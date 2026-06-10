<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;

class PaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with('order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($r) use ($q) {
                $r->where('order_number', 'like', "%$q%")
                  ->orWhere('transaction_id', 'like', "%$q%");
            });
        }

        $payments = $query->latest()->paginate(25);

        return view('admin.payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with('order','invoice')->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,paid,failed,refunded']);
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => $request->status]);

        // sync order payment status
        if ($payment->order) {
            $payment->order->update(['payment_status' => $request->status]);
        }

        // if marked paid, generate invoice if missing
        if ($request->status === 'paid') {
            try {
                $service = app(\App\Services\PaymentServiceInterface::class);
                $service->createInvoiceForPayment($payment);
            } catch (\Exception $e) {
                // ignore invoice creation errors here
            }
        }

        return redirect()->back()->with('success', 'Payment status updated.');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        if ($invoice->pdf_path && \Storage::disk('public')->exists($invoice->pdf_path)) {
            return response()->download(storage_path('app/public/' . $invoice->pdf_path));
        }

        if (class_exists(\PDF::class)) {
            $payment = $invoice->payment;
            $order = $invoice->order;
            $pdf = \PDF::loadView('invoices.template', compact('invoice', 'payment', 'order'));
            return $pdf->download($invoice->invoice_number . '.pdf');
        }

        abort(404);
    }
}
