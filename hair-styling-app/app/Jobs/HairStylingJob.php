<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HairStylingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePath;
    protected $scriptPath;
    protected $pythonPath;
    protected $trackingId;

    public function __construct($imagePath, $scriptPath, $pythonPath, $trackingId)
    {
        $this->imagePath = $imagePath;
        $this->scriptPath = $scriptPath;
        $this->pythonPath = $pythonPath;
        $this->trackingId = $trackingId;
    }

    public function handle()
    {
        ini_set('max_execution_time', 600);

        $scriptDir = dirname($this->scriptPath);
        $scriptFile = basename($this->scriptPath);

        $command = "cd " . escapeshellarg($scriptDir)
            . " && " . escapeshellcmd($this->pythonPath)
            . " " . escapeshellarg($scriptFile)
            . " " . escapeshellarg($this->imagePath);

        Log::info("Executing Python script: " . $command);

        $output = shell_exec($command);

        if (!$output) {
            Log::error("Empty output from Python script.");
            return;
        }

        Log::info("Python script raw output: " . $output);

        $lines = explode("\n", trim($output));
        $lastLine = trim(end($lines));

        $data = json_decode($lastLine, true);

        if (!$data || !isset($data['recommended_hairstyles'])) {
            Log::error("Invalid JSON structure or missing 'recommended_hairstyles'.");
            Storage::disk('public')->put('results/' . $this->trackingId . '_error_raw_output.txt', $output);
            return;
        }

        $processedHairstyles = [];
        $generatedStorageFolder = 'generated_hairstyles';
        $originalHalfCreated = false;
        $originalFilePath = '';

        foreach ($data['recommended_hairstyles'] as $index => $hairstyleData) {
            if (isset($hairstyleData['generated_image_url'])) {
                $imageUrl = $hairstyleData['generated_image_url'];
                Log::info("Attempting to download image from: " . $imageUrl);

                try {
                    $imageContent = file_get_contents($imageUrl);

                    if ($imageContent === false) {
                        Log::error("Failed to download image content from URL: " . $imageUrl);
                        $hairstyleData['generated_image_local_path'] = null;
                        $processedHairstyles[] = $hairstyleData;
                        continue;
                    }

                    $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (empty($extension)) {
                        $extension = 'png';
                    }
                    $filename = $this->trackingId . '_' . Str::slug($hairstyleData['hairstyle']) . '_' . $index . '.' . $extension;
                    $filePath = $generatedStorageFolder . '/' . $filename;

                    Storage::disk('public')->put($filePath, $imageContent);
                    Log::info("Image saved locally: " . $filePath);

                    $fullPath = storage_path('app/public/' . $filePath);

                    [$width, $height] = getimagesize($fullPath);
                    $src = imagecreatefromstring($imageContent);
                    $halfWidth = $width / 2;

                    $cropped = imagecreatetruecolor($halfWidth, $height);
                    imagecopy($cropped, $src, 0, 0, $halfWidth, 0, $halfWidth, $height);
                    imagepng($cropped, $fullPath);
                    imagedestroy($cropped);

                    if (!$originalHalfCreated) {
                        $originalFileName = $this->trackingId . '_original_half.png';
                        $originalFilePath = $generatedStorageFolder . '/' . $originalFileName;
                        $originalFullPath = storage_path('app/public/' . $originalFilePath);

                        $leftHalf = imagecreatetruecolor($halfWidth, $height);
                        imagecopy($leftHalf, $src, 0, 0, 0, 0, $halfWidth, $height);
                        imagepng($leftHalf, $originalFullPath);
                        imagedestroy($leftHalf);

                        $originalHalfCreated = true;
                        $data['original_image'] = asset('storage/' . $originalFilePath);
                        Log::info("Original (left half) image saved at: " . $originalFilePath);
                    }

                    imagedestroy($src);

                    $publicUrl = asset('storage/' . $filePath);
                    $hairstyleData['generated_image'] = $publicUrl;
                    unset($hairstyleData['generated_image_url']);
                } catch (\Exception $e) {
                    Log::error("Error downloading or saving image " . $imageUrl . ": " . $e->getMessage());
                    $hairstyleData['generated_image_local_path'] = null;
                    unset($hairstyleData['generated_image_url']);
                }
            }
            $processedHairstyles[] = $hairstyleData;
        }

        $data['recommended_hairstyles'] = $processedHairstyles;

        $resultPath = 'results/' . $this->trackingId . '.json';
        Storage::disk('public')->put($resultPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        Log::info("Final JSON result saved to: " . $resultPath);
    }
}
