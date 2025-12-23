<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['table', 'items', 'delivery', 'payments']);

        return view('staff.orders.checkout', [
            'order' => $order,
        ]);
    }

    public function pay(Request $request, Order $order)
    {
        if (!$order->isOpen()) {
            return back()->withErrors(['order' => 'บิลนี้ปิดแล้ว']);
        }

        $data = $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'in:CASH,TRANSFER,QR,CARD'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.ref_no' => ['nullable', 'string', 'max:50'],
        ]);

        $totalPaid = 0.0;
        $cashPaid = 0.0;
        foreach ($data['payments'] as $p) {
            $amt = (float) $p['amount'];
            $totalPaid += $amt;
            if ($p['method'] === 'CASH') $cashPaid += $amt;
        }

        if ($totalPaid + 0.00001 < (float) $order->total) {
            return back()->withErrors(['payments' => 'ยอดชำระเงินน้อยกว่ายอดบิล']);
        }

        $change = 0.0;
        // Change only makes sense if cash was part of payment
        if ($cashPaid > 0) {
            // Theoretical change: paid - total, but do not exceed cashPaid (avoid nonsense)
            $change = max(0.0, $totalPaid - (float) $order->total);
        }

        DB::transaction(function () use ($order, $data) {
            // lock order to avoid double pay
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            if ($order->status !== 'OPEN') {
                throw new \RuntimeException('Order already closed.');
            }

            // clear existing payments (optional). Safer: disallow if any exists.
            // Here we disallow re-pay to avoid accounting ambiguity.
            if ($order->payments()->count() > 0) {
                throw new \RuntimeException('Payments already recorded for this order.');
            }

            foreach ($data['payments'] as $p) {
                Payment::create([
                    'order_id' => $order->id,
                    'method' => $p['method'],
                    'amount' => (float) $p['amount'],
                    'ref_no' => $p['ref_no'] ?? null,
                    'paid_at' => now(),
                    'created_by' => auth()->id(),
                ]);
            }

            $order->status = 'PAID';
            $order->paid_at = now();
            $order->save();
        });

        return redirect()->route('staff.orders.show', $order)
            ->with('success', 'ชำระเงินและปิดบิลเรียบร้อย')
            ->with('change', number_format($change, 2));
    }
}
