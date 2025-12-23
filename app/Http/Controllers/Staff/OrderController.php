<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function dashboard()
    {
        $tables = Table::query()->orderBy('number')->get();

        $openByTable = Order::query()
            ->where('status', 'OPEN')
            ->whereNotNull('table_id')
            ->get()
            ->keyBy('table_id');

        $openDeliveries = Order::query()
            ->where('status', 'OPEN')
            ->where('channel', 'DELIVERY')
            ->latest('opened_at')
            ->get();

        return view('staff.orders.dashboard', [
            'tables' => $tables,
            'openByTable' => $openByTable,
            'openDeliveries' => $openDeliveries,
        ]);
    }

    public function openDineIn(Table $table)
    {
        if (!$table->is_active) abort(404);

        $order = $this->orders->getOrCreateOpenDineInOrder($table, auth()->id());

        return redirect()->route('staff.orders.show', $order);
    }

    public function openDelivery(Request $request)
    {
        // Delivery details are optional but recommended
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:100'],
        ]);

        $order = $this->orders->openDelivery(auth()->id(), $data);

        return redirect()->route('staff.orders.show', $order);
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items', 'delivery']);

        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('staff.orders.show', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function addItem(Request $request, Order $order)
    {
        if (!$order->isOpen()) {
            return back()->withErrors(['order' => 'บิลนี้ปิดแล้ว']);
        }

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'note' => ['nullable', 'string', 'max:100'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $this->orders->addItemStaff($order, $product, (int) $data['qty'], $data['note'] ?? null, auth()->id());

        return back()->with('success', 'เพิ่มรายการแล้ว');
    }

    public function updateItemQty(Request $request, OrderItem $item)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $this->orders->updateItemQty($item, (int) $data['qty']);

        return back()->with('success', 'อัปเดตจำนวนแล้ว');
    }

    public function removeItem(OrderItem $item)
    {
        try {
            $this->orders->removeItem($item);
            return back()->with('success', 'ลบรายการแล้ว');
        } catch (\Throwable $e) {
            return back()->withErrors(['item' => $e->getMessage()]);
        }
    }

    public function applyDiscount(Request $request, Order $order)
    {
        if (!$order->isOpen()) {
            return back()->withErrors(['order' => 'บิลนี้ปิดแล้ว']);
        }

        $data = $request->validate([
            'discount_type' => ['required', 'in:NONE,AMOUNT,PERCENT'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $value = (float) ($data['discount_value'] ?? 0);
        if ($data['discount_type'] === 'NONE') $value = 0;

        $this->orders->applyDiscount($order, $data['discount_type'], $value);

        return back()->with('success', 'อัปเดตส่วนลดแล้ว');
    }

    public function updateDelivery(Request $request, Order $order)
    {
        if ($order->channel !== 'DELIVERY') abort(404);

        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:100'],
        ]);

        $this->orders->updateDeliveryDetails($order, $data);

        return back()->with('success', 'บันทึกข้อมูลจัดส่งแล้ว');
    }

    public function submitPending(Order $order)
    {
        if (!$order->isOpen()) {
            return back()->withErrors(['order' => 'บิลนี้ปิดแล้ว']);
        }

        $submission = $this->orders->submitPendingStaffItems($order, auth()->id());

        if (!$submission) {
            return redirect()->route('staff.monitor.submissions', ['status' => 'OPEN'])
                ->with('success', 'ไม่มีรายการใหม่ให้ส่ง');
        }

        return redirect()->route('staff.monitor.submissions', ['status' => 'OPEN'])
            ->with('success', 'ส่งรายการไปหน้า Monitor แล้ว');
    }

    public function submitPendingStay(Order $order)
    {
        if (!$order->isOpen()) {
            return back()->withErrors(['order' => 'บิลนี้ปิดแล้ว']);
        }

        $submission = $this->orders->submitPendingStaffItems($order, auth()->id());

        return back()->with('success', $submission ? 'ส่งรายการแล้ว' : 'ไม่มีรายการใหม่ให้ส่ง');
    }

}
