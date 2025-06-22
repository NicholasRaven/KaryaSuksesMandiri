<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $suppliers = Supplier::query();

        if($search) {
            $suppliers->where('name','Like','%'. $search. '%')
                    ->orWhere('jenis_barang','Like','%'. $search. '%')
                    ->orWhere('phone_number','like','%'. $search. '%')
                    ->orWhere('email','like','%'. $search. '%');
        }

        $suppliers = $suppliers->paginate($perPage);

        return view('suppliers.index', compact('suppliers', 'perPage', 'search'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|min:3|max:60|regex:/^[A-Za-z\s]+$/',
            'jenis_barang'=>'nullable|string|min:3|max:60',
            'phone_number'=>'nullable|digits_between:10,15|regex:/^[0-9]+$/',
            'email'=>'required|string|email|min:3|max:100|unique:suppliers',
            'address'=>'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'=>'required|string|min:3|max:60|regex:/^[A-Za-z\s]+$/',
            'jenis_barang'=>'nullable|string|min:3|max:60',
            'phone_number'=>'nullable|digits_between:10,15|regex:/^[0-9]+$/',
            'email'=>'required|string|email|min:3|max:100|unique:suppliers',
            'address'=>'nullable|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus!');
    }
}
