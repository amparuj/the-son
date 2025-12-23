<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSubmission;
use App\Models\OrderSubmissionItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getOrCreateOpenDineInOrder(Table $table, ?int $userId): Order
    {
        $existing = Order::query()
            ->where('channel', 'DINE_IN')
            ->where('table_id', $table->id)
            ->where('status', 'OPEN')
            ->latest('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return Order::create([
            'order_no' => $this->generateOrderNo(),
            'channel' => 'DINE_IN',
            'table_id' => $table->id,
            'status' => 'OPEN',

            'subtotal' => 0,
            'discount_type' => 'AMOUNT', // NOT NULL
            'discount_value' => 0,
            'total' => 0,

            'opened_at' => now(),
            'paid_at' => null,

            'created_by' => $userId,
        ]);
    }

    public function createOpenDeliveryOrder(array $payload, ?int $userId): Order
    {
        return Order::create([
            'order_no' => $this->generateOrderNo(),
            'channel' => 'DELIVERY',
            'table_id' => null,
            'status' => 'OPEN',

            'subtotal' => 0,
            'discount_type' => 'AMOUNT', // NOT NULL
            'discount_value' => 0,
            'total' => 0,

            'opened_at' => now(),
            'paid_at' => null,

            'created_by' => $userId,
        ]);
    }

    /**
     * Staff adds items first (no monitor yet). Submit to monitor via submitPendingStaffItems().
     */
    public function addItemStaff(Order $order, Product $product, int $qty, ?string $note, int $userId): void
    {
        $this->addItemInternal($order, $product, $qty, $note, 'STAFF', $userId, null);
    }

    /**
     * QR adds can be submitted in a batch by controller; if you pass $submissionId, it will attach.
     */
    public function addItemFromQr(Order $order, Product $product, int $qty, ?string $note, ?int $submissionId = null): void
    {
        $this->addItemInternal($order, $product, $qty, $note, 'QR', null, $submissionId);
    }

    /**
     * QR creates 1 line item per selection set (แบบ B) + store option snapshots.
     *
     * @param array $snapshots [
     *   ['option_id'=>int,'name'=>string,'price'=>float,'qty'=>int],
     *   ...
     * ]
     */
    public function addItemFromQrLine(
        Order $order,
        Product $product,
        int $qty,
        ?string $note,
        array $snapshots,
        ?int $submissionId = null
    ): void {
        if ($order->status !== 'OPEN') {
            throw new \RuntimeException('Order is not OPEN.');
        }
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Qty must be >= 1');
        }

        DB::transaction(function () use ($order, $product, $qty, $note, $snapshots, $submissionId) {

            $addonPerUnit = 0.0;
            foreach ($snapshots as $s) {
                $addonPerUnit += ((float)($s['price'] ?? 0)) * ((int)($s['qty'] ?? 1));
            }

            $unit = (float)$product->price + $addonPerUnit;
            $lineTotal = $unit * $qty;

            /** @var \App\Models\OrderItem $item */
            $item = OrderItem::create([
                'order_id' => $order->id,
                'order_submission_id' => $submissionId,

                'product_name' => $product->name,
                'unit_price' => $unit,
                'qty' => $qty,
                'line_total' => $lineTotal,
                'note' => $note,

                'created_via' => 'QR',
                'created_by' => null,

                'status' => 'OPEN',
                'done_at' => null,
            ]);

            // store option snapshots
            foreach ($snapshots as $s) {
                OrderItemOption::create([
                    'order_item_id' => $item->id,
                    'option_id' => (int)($s['option_id'] ?? 0),
                    'option_name_snapshot' => (string)($s['name'] ?? ''),
                    'option_price_snapshot' => (float)($s['price'] ?? 0),
                    'qty' => (int)($s['qty'] ?? 1),
                ]);
            }

            $this->recalcTotals($order);
        });
    }


    public function createSubmission(Order $order, string $source, ?int $userId): OrderSubmission
    {
        return OrderSubmission::create([
            'order_id' => $order->id,
            'source' => $source,
            'channel' => $order->channel,
            'table_id' => $order->table_id,
            'created_by' => $userId,
            'submitted_at' => now(),

            // ถ้า order_submissions ของคุณมี 2 คอลัมน์นี้ ให้คงไว้
            'status' => 'OPEN',
            'done_at' => null,
        ]);
    }

    public function addSubmissionItem(
        OrderSubmission $submission,
        string $productName,
        float $unitPrice,
        int $qty,
        float $lineTotal,
        ?string $note
    ): void {
        OrderSubmissionItem::create([
            'order_submission_id' => $submission->id,
            'product_name' => $productName,
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'line_total' => $lineTotal,
            'note' => $note,
        ]);
    }

    /**
     * Create 1 submission containing all pending STAFF items (not yet attached to any submission).
     */
    public function submitPendingStaffItems(Order $order, int $userId): ?OrderSubmission
    {
        $pending = OrderItem::query()
            ->where('order_id', $order->id)
            ->whereNull('order_submission_id')
            ->where('created_via', 'STAFF')
            ->where('status', 'OPEN')
            ->orderBy('id')
            ->get();

        if ($pending->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($order, $userId, $pending) {
            $submission = $this->createSubmission($order, 'STAFF', $userId);

            foreach ($pending as $item) {
                $item->order_submission_id = $submission->id;
                $item->save();

                $this->addSubmissionItem(
                    $submission,
                    $item->product_name,
                    (float) $item->unit_price,
                    (int) $item->qty,
                    (float) $item->line_total,
                    $item->note
                );
            }

            return $submission;
        });
    }

    private function addItemInternal(
        Order $order,
        Product $product,
        int $qty,
        ?string $note,
        string $via,
        ?int $userId,
        ?int $submissionId
    ): void {
        if ($order->status !== 'OPEN') {
            throw new \RuntimeException('Order is not OPEN.');
        }

        if ($qty <= 0) {
            throw new \InvalidArgumentException('Qty must be >= 1');
        }

        DB::transaction(function () use ($order, $product, $qty, $note, $via, $userId, $submissionId) {
            $unit = (float) $product->price;
            $lineTotal = $unit * $qty;

            OrderItem::create([
                'order_id' => $order->id,
                'order_submission_id' => $submissionId,

                'product_name' => $product->name,
                'unit_price' => $unit,
                'qty' => $qty,
                'line_total' => $lineTotal,
                'note' => $note,

                'created_via' => $via,
                'created_by' => $userId,

                'status' => 'OPEN',
                'done_at' => null,
            ]);

            $this->recalcTotals($order);
        });
    }

    public function updateItemQty(OrderItem $item, int $qty): void
    {
        if ($item->order->status !== 'OPEN') {
            throw new \RuntimeException('Order is not OPEN.');
        }

        if ($item->status === 'DONE') {
            throw new \RuntimeException('This item is DONE and cannot be edited.');
        }

        if ($qty <= 0) {
            throw new \InvalidArgumentException('Qty must be >= 1');
        }

        DB::transaction(function () use ($item, $qty) {
            $item->qty = $qty;
            $item->line_total = (float) $item->unit_price * $qty;
            $item->save();

            $this->recalcTotals($item->order);
        });
    }

    public function removeItem(OrderItem $item): void
    {
        $order = $item->order;

        if ($order->status !== 'OPEN') {
            throw new \RuntimeException('Order is not OPEN.');
        }

        if ($item->status === 'DONE') {
            throw new \RuntimeException('This item is DONE and cannot be deleted.');
        }

        DB::transaction(function () use ($item, $order) {
            $item->delete();
            $this->recalcTotals($order);
        });
    }

    public function applyDiscount(Order $order, string $type, float $value): void
    {
        if ($order->status !== 'OPEN') {
            throw new \RuntimeException('Order is not OPEN.');
        }

        if (!in_array($type, ['AMOUNT', 'PERCENT'], true)) {
            throw new \InvalidArgumentException('Invalid discount type.');
        }

        if ($value < 0) {
            throw new \InvalidArgumentException('Discount value must be >= 0.');
        }

        DB::transaction(function () use ($order, $type, $value) {
            $order->discount_type = $type;
            $order->discount_value = $value;
            $this->recalcTotals($order);
        });
    }

    private function recalcTotals(Order $order): void
    {
        $subtotal = (float) OrderItem::query()
            ->where('order_id', $order->id)
            ->sum('line_total');

        $discount = 0.0;

        if ($order->discount_type === 'AMOUNT') {
            $discount = min((float) $order->discount_value, $subtotal);
        } elseif ($order->discount_type === 'PERCENT') {
            $pct = max(0.0, min(100.0, (float) $order->discount_value));
            $discount = round($subtotal * ($pct / 100.0), 2);
        }

        $order->subtotal = $subtotal;
        $order->total = max(0.0, $subtotal - $discount);
        $order->save();
    }

    private function generateOrderNo(): string
    {
        return 'ORD-' . now()->format('Ymd-His') . '-' . random_int(1000, 9999);
    }
}
