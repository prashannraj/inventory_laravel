<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::withCount('values')->latest()->paginate(10);
        return view('attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:attributes',
            'active' => 'boolean',
        ]);

        Attribute::create($data);
        return redirect()->route('attributes.index')->with('success', 'Attribute created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        $attribute->load('values');
        return view('attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        return view('attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'active' => 'boolean',
        ]);

        $attribute->update($data);
        return redirect()->route('attributes.index')->with('success', 'Attribute updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('attributes.index')->with('success', 'Attribute deleted successfully.');
    }

    /**
     * Display values for the attribute.
     */
    public function values(Attribute $attribute)
    {
        $values = $attribute->values;
        return view('attributes.values', compact('attribute', 'values'));
    }

    /**
     * Store a new value for the attribute.
     */
    public function storeValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $attribute->values()->create($data);
        return back()->with('success', 'Value added successfully.');
    }
}
