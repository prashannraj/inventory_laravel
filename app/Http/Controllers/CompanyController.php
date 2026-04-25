<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');
        return view('company.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'required|string',
            'company_address' => 'required|string',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'currency_symbol' => 'required|string|max:5',
            'currency_code' => 'required|string|max:3',
            'tax_number' => 'nullable|string',
            'invoice_prefix' => 'required|string|max:10',
            'invoice_footer_text' => 'nullable|string',
        ]);

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('company', 'public');
            Setting::set('company_logo', $path);
        }

        foreach ($data as $key => $value) {
            if ($key !== 'company_logo') {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('company.edit')->with('success', 'Company settings updated successfully.');
    }
}
