<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Jobs\HairStylingJob;
use Illuminate\Support\Str;

class HairStylingController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function processImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        // Завантаження зображення у storage/app/public/uploads
        $imageFile = $request->file('image');
        $originalName = $imageFile->getClientOriginalName();
        $imagePath = $imageFile->store('uploads', 'public');
        $imageFullPath = storage_path("app/public/" . $imagePath);

        Session::put('uploaded_image_name', $originalName);

        // Унікальний ID для результату
        $trackingId = (string) Str::uuid();

        $pythonScriptPath = 'D:/study-1/KURSOVA4/praktik/scripts/hair_swap.py';
        $pythonExecutable = 'D:/Python/python.exe';

        HairStylingJob::dispatch(
            $imageFullPath,
            $pythonScriptPath,
            $pythonExecutable,
            $trackingId
        )->onQueue('default');

        return response()->json([
            'success' => true,
            'trackingId' => $trackingId
        ]);
    }

    public function showResult($id)
    {
        $resultPath = 'results/' . $id . '.json';

        if (!Storage::disk('public')->exists($resultPath)) {
            abort(404, 'Результат ще не готовий.');
        }

        $jsonString = Storage::disk('public')->get($resultPath);
        $data = json_decode($jsonString, true);

        if (!$data || !isset($data['recommended_hairstyles'])) {
            return view('result_error', ['id' => $id]);
        }

        // Очистити сесію
        Session::forget('uploaded_image_name');

        return view('result', compact('data'));
    }
}
