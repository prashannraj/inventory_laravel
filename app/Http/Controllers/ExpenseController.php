<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['store', 'user'])->latest()->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $stores = Store::where('active', true)->get();
        return view('expenses.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'expense_no' => 'EXP-' . strtoupper(Str::random(8)),
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'store_id' => $request->store_id,
            'category' => $request->category,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $stores = Store::where('active', true)->get();
        return view('expenses.edit', compact('expense', 'stores'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
