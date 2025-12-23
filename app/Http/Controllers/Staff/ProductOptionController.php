<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OptionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOptionController extends Controller
{
    public function edit(Product $product)
    {
        $groups = OptionGroup::with(['options' => function ($q) {
                $q->where('is_active', true)->orderBy('sort')->orderBy('id');
            }])
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        // options ที่ผูกอยู่แล้วกับสินค้า
        $attached = $product->options()->get()->keyBy('id');

        return view('staff.products.options_edit', compact('product', 'groups', 'attached'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'options' => ['array'],
            'options.*.enabled' => ['nullable','boolean'],
            'options.*.is_allowed' => ['nullable','boolean'],
            'options.*.is_default' => ['nullable','boolean'],
            'options.*.price_override' => ['nullable','numeric','min:0'],
            'options.*.max_qty' => ['nullable','integer','min:1'],
            'options.*.sort' => ['nullable','integer','min:0'],
        ]);

        $optionsInput = $data['options'] ?? [];

        DB::transaction(function () use ($product, $optionsInput) {
            $sync = [];

            foreach ($optionsInput as $optionId => $row) {
                $enabled = (bool)($row['enabled'] ?? false);
                if (!$enabled) {
                    continue;
                }

                $sync[$optionId] = [
                    'is_allowed' => (bool)($row['is_allowed'] ?? true),
                    'is_default' => (bool)($row['is_default'] ?? false),
                    'price_override' => ($row['price_override'] ?? null) === '' ? null : ($row['price_override'] ?? null),
                    'max_qty' => ($row['max_qty'] ?? null) === '' ? null : ($row['max_qty'] ?? null),
                    'sort' => $row['sort'] ?? 0,
                ];
            }

            $product->options()->sync($sync);
        });

        return redirect()
            ->route('staff.products.options.edit', $product->id)
            ->with('success', 'บันทึก Options ของสินค้าแล้ว');
    }
}
