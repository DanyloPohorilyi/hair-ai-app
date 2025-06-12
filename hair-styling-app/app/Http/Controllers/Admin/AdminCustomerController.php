<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(10); // Пагінація по 10 користувачів
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email',
            'password' => 'required|min:6|confirmed',
        ]);

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'photo' => 'default.png',
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Користувач створений!');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Валідація даних
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:customer,email,' . $customer->user_id . ',user_id',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Оновлення даних
        $customer->name = $request->name;
        $customer->email = $request->email;

        if ($request->filled('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->is_admin = $request->has('is_admin');

        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Користувач оновлений!');
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Користувач видалений!');
    }
}
