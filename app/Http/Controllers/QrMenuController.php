<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;
use Illuminate\Http\Request;

class QrMenuController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function showMenu(Table $table)
    {
        abort_unless($table->is_active, 404);

        $products = Product::query()
            ->where('is_active', true)
            ->with([
                // options ที่อนุญาตสำหรับสินค้านี้ (ไว้ใช้ราคา override + allow list)
                'options' => function($q){
                    $q->where('options.is_active', true)
                      ->wherePivot('is_allowed', true)
                      ->orderBy('product_option.sort');
                },
                // group ที่เปิดใช้กับสินค้านี้ + ตั้ง min/max ได้
                'optionGroups' => function($q){
                    $q->wherePivot('is_enabled', true)
                      ->orderBy('product_option_groups.sort');
                },
                // options ภายใน group (ตามลำดับที่ staff จัดใน group)
                'optionGroups.options' => function($q){
                    $q->where('options.is_active', true)
                      ->orderBy('option_group_items.sort');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('qr.menu', [
            'table' => $table,
            'products' => $products,
        ]);
    }

    public function submit(Request $request, Table $table)
    {
        abort_unless($table->is_active, 404);

        // Only accept items with qty >= 1
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.note' => ['nullable', 'string', 'max:100'],
            'items.*.option_ids' => ['array'],
            'items.*.option_ids.*' => ['integer', 'exists:options,id'],
        ]);

        $order = $this->orders->getOrCreateOpenDineInOrder($table, null);

        // Monitor: record this QR submit as 1 submission (may contain multiple items)
        $submission = $this->orders->createSubmission($order, 'QR', null);

        foreach ($data['items'] as $it) {
            $product = Product::with([
                'options' => function($q){
                    $q->where('options.is_active', true)->wherePivot('is_allowed', true);
                },
                'optionGroups' => function($q){
                    $q->wherePivot('is_enabled', true)->orderBy('product_option_groups.sort');
                },
                'optionGroups.options' => function($q){
                    $q->where('options.is_active', true)->orderBy('option_group_items.sort');
                },
            ])->findOrFail($it['product_id']);

            $qty = (int) $it['qty'];
            $note = $it['note'] ?? null;
            $optionIds = $it['option_ids'] ?? [];

            // Validate: option must be allowed for this product
            $allowed = $product->options->keyBy('id');
            $snapshots = [];
            $addonPerUnit = 0.0;

            foreach ($optionIds as $oid) {
                $oid = (int) $oid;
                if (!$allowed->has($oid)) {
                    abort(422, 'มีตัวเลือกที่ไม่ได้รับอนุญาตสำหรับเมนูนี้');
                }
                $opt = $allowed->get($oid);
                $price = (float) ($opt->pivot->price_override ?? $opt->base_price);
                $snapshots[] = [
                    'option_id' => $opt->id,
                    'name' => $opt->name,
                    'price' => $price,
                    'qty' => 1,
                ];
                $addonPerUnit += $price;
            }


            // Validate: group min/max ต่อสินค้า (บังคับเลือก 1 = min=1 max=1)
            $selectedIds = collect($optionIds)->map(fn($x)=>(int)$x)->unique()->values();
            $groups = $product->optionGroups;

            foreach ($groups as $g) {
                $min = (int) $g->pivot->min_select;
                $max = (int) $g->pivot->max_select;

                $groupOptionIds = $g->options->pluck('id');
                $count = $selectedIds->intersect($groupOptionIds)->count();

                if ($min > 0 && $count < $min) {
                    abort(422, "เลือกตัวเลือกในกลุ่ม '{$g->name}' ไม่ครบตามเงื่อนไข");
                }
                if ($max > 0 && $count > $max) {
                    abort(422, "เลือกตัวเลือกในกลุ่ม '{$g->name}' เกินจำนวนสูงสุด");
                }
            }

            // Create 1 line item (แบบ B) + snapshot options
            $this->orders->addItemFromQrLine($order, $product, $qty, $note, $snapshots, $submission->id);

            $unit = (float) $product->price + $addonPerUnit;
            $lineTotal = $unit * $qty;

            // For monitoring, store line with total including options (note contains customer text)
            $this->orders->addSubmissionItem($submission, $product->name, $unit, $qty, $lineTotal, $note);
        }

        return redirect()->route('qr.menu', $table->public_uuid)
            ->with('success', "ส่งรายการเรียบร้อย (บิล: {$order->order_no})");
    }
}
