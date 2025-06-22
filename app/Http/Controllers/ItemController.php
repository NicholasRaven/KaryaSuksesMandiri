<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $items = Item::with('supplier');

        if ($search) {
            $items->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'. $search .'%')
                     ->orWhere('unit_type', 'like', '%'. $search .'%');
            });
        }

        $items = $items->paginate($perPage);
        $suppliers = Supplier::all();

        return view('items.index', compact('items', 'perPage', 'search'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'unit_type' => 'required|string|in:Pcs,Set,m,Roll,L,kg,gr,Box,Kaleng,Botol,Paket',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        Item::create([
            'name' => $request->name,
            'unit_type' => $request->unit_type,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function show(Item $item)
    {
        $suppliers = Supplier::all();
        return view('items.show', compact('item', 'suppliers'));
    }

    public function edit(Item $item)
    {
        $suppliers = Supplier::all();
        return view('items.edit', compact('item','suppliers'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'unit_type' => 'required|string|in:Pcs,Set,m,Roll,L,kg,gr,Box,Kaleng,Botol,Paket',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $item->update([
            'name' => $request->name,
            'unit_type' => $request->unit_type,
            'supplier_id' => $request->supplier_id,
        ]);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus!');
    }
}
