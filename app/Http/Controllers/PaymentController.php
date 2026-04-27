<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show the payment gateway selection page.
     */
    public function index()
    {
        return view('payments.index');
    }

    /**
     * Show payment success page.
     */
    public function success(Request $request)
    {
        $payment = $request->session()->get('payment');
        
        if (!$payment && Auth::check()) {
            // Try to get the latest successful payment for the user
            $payment = Payment::where('user_id', Auth::id())
                ->completed()
                ->latest()
                ->first();
        }

        return view('payments.success', compact('payment'));
    }

    /**
     * Show payment failed page.
     */
    public function failed(Request $request)
    {
        $error = $request->session()->get('error', 'Payment failed or cancelled.');
        return view('payments.failed', compact('error'));
    }

    /**
     * Show payment history with pagination.
     */
    public function history(Request $request)
    {
        $query = Payment::latest();

        // Filter by user if not admin
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'failed', 'refunded'])) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->has('gateway') && in_array($request->gateway, ['esewa', 'khalti'])) {
            $query->where('payment_gateway', $request->gateway);
        }

        $payments = $query->paginate(20);

        return view('payments.history', compact('payments'));
    }
}
