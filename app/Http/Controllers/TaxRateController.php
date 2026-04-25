<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::latest()->paginate(10);
        return view('tax-rates.index', compact('taxRates'));
    }

    public function create()
    {
        return view('tax-rates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'active' => 'boolean',
        ]);

        TaxRate::create($data);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate created successfully.');
    }

    public function edit(TaxRate $taxRate)
    {
        return view('tax-rates.edit', compact('taxRate'));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'active' => 'boolean',
        ]);

        $taxRate->update($data);
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate)
    {
        if ($taxRate->products()->count() > 0) {
            return back()->with('error', 'Cannot delete tax rate used by products.');
        }
        $taxRate->delete();
        return redirect()->route('tax-rates.index')->with('success', 'Tax rate deleted successfully.');
    }
}
