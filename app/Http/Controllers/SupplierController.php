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
            'name'=>'required|string|max:255',
            'jenis_barang'=>'nullable|string|max:255',
            'phone_number'=>'nullable|string|max:20',
            'email'=>'nullable|string|email|max:255|unique:suppliers',
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
            'name'=>'required|string|max:255',
            'jenis_barang'=>'nullable|string|max:255',
            'phone_number'=>'nullable|string|max:20',
            'email'=>'nullable|string|email|max:255|unique:suppliers,email,'. $supplier->id,
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
