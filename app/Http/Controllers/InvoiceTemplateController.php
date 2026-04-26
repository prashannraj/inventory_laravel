<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;

class InvoiceTemplateController extends Controller
{
    public function index()
    {
        $templates = InvoiceTemplate::all();
        return view('invoice-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('invoice-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string|in:default,classic,modern,thermal',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'show_logo' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            InvoiceTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        InvoiceTemplate::create($request->all());

        return redirect()->route('invoice-templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(InvoiceTemplate $invoiceTemplate)
    {
        return view('invoice-templates.edit', compact('invoiceTemplate'));
    }

    public function update(Request $request, InvoiceTemplate $invoiceTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout' => 'required|string|in:default,classic,modern,thermal',
            'header_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'show_logo' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->is_default) {
            InvoiceTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $invoiceTemplate->update($request->all());

        return redirect()->route('invoice-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(InvoiceTemplate $invoiceTemplate)
    {
        if ($invoiceTemplate->is_default) {
            return back()->with('error', 'Cannot delete the default template.');
        }
        $invoiceTemplate->delete();
        return redirect()->route('invoice-templates.index')->with('success', 'Template deleted successfully.');
    }
}
