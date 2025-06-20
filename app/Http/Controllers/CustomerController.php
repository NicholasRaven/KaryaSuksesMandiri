<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $customers = Customer::query();

        if ($search) {
            $customers->where('name', 'like', '%'. $search. '%')
                    ->orWhere('email', 'like', '%'. $search. '%')
                    ->orWhere('phone_number', 'like', '%'. $search. '%');
        }

        $customers = $customers->paginate($perPage);

        return view('customers.index', compact('customers', 'perPage', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|min:3|max:50',
            'email'=>'required|string|email|min:3|max:255|unique:customers',
            'phone_number'=>'nullable|string|regex:/^[0-9]{10,15}$/',
            'type'=>'required|string|in:Individu,Perusahaan',
            'address'=>'nullable|string'
        ],
        [
            'email.email' => 'Format email tidak valid (harus mengandung @)',
            'name.min' => 'Nama pelanggan minimal 3 karakter.',
            'name.max' => 'Nama pelanggan maksimal 50 karakter.',
            'phone_number.integer' => 'Nomor telepon harus berupa angka.',
            'phone_number.min' => 'Nomor telepon minimal 10 digit.',
            'phone_number.max' => 'Nomor telepon maksimal 15 digit.',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'=>'required|string|min:3|max:50',
            'email'=>'required|string|email|max:255|unique:customers,email,'.$customer->id,
            'phone_number'=>'nullable|string|regex:/^[0-9]{10,15}$/',
            'type'=>'required|string|in:Individu,Perusahaan',
            'address'=>'nullable|string',
        ],
        [
            'email.email' => 'Format email tidak valid (harus mengandung @)',
            'name.min' => 'Nama pelanggan minimal 3 karakter.',
            'name.max' => 'Nama pelanggan maksimal 50 karakter.',
            'phone_number.integer' => 'Nomor telepon harus berupa angka.',
            'phone_number.min' => 'Nomor telepon minimal 10 digit.',
            'phone_number.max' => 'Nomor telepon maksimal 15 digit.',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbaharui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}
