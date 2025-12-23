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
        $groups = OptionGroup::with(['options' => function($q){
                $q->where('options.is_active', true)
                  ->orderBy('option_group_items.sort');
            }])
            ->orderBy('sort')->orderBy('id')->get();

        $attachedOptions = $product->options()
            ->where('options.is_active', true)
            ->get()
            ->keyBy('id');

        $product->load(['optionGroups' => function($q){
            $q->orderBy('product_option_groups.sort');
        }]);

        $productGroups = $product->optionGroups->keyBy('id');

        return view('staff.products.options_edit', compact(
            'product','groups','attachedOptions','productGroups'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'groups' => ['array'],
            'groups.*.enabled' => ['nullable','boolean'],
            'groups.*.min_select' => ['nullable','integer','min:0'],
            'groups.*.max_select' => ['nullable','integer','min:0'],
            'groups.*.sort' => ['nullable','integer','min:0'],
            'options' => ['array'],
            'options.*.enabled' => ['nullable','boolean'],
            'options.*.is_allowed' => ['nullable','boolean'],
            'options.*.is_default' => ['nullable','boolean'],
            'options.*.price_override' => ['nullable','numeric','min:0'],
            'options.*.max_qty' => ['nullable','integer','min:1'],
            'options.*.sort' => ['nullable','integer','min:0'],
        ]);

        DB::transaction(function() use ($product, $data) {
            $syncGroups = [];
            foreach (($data['groups'] ?? []) as $groupId => $row) {
                if (!($row['enabled'] ?? false)) continue;
                $min = (int)($row['min_select'] ?? 0);
                $max = (int)($row['max_select'] ?? 0);
                if ($max > 0 && $min > $max) $min = $max;

                $syncGroups[$groupId] = [
                    'is_enabled' => true,
                    'min_select' => $min,
                    'max_select' => $max,
                    'sort' => (int)($row['sort'] ?? 0),
                ];
            }
            $product->optionGroups()->sync($syncGroups);

            $syncOptions = [];
            foreach (($data['options'] ?? []) as $optionId => $row) {
                if (!($row['enabled'] ?? false)) continue;
                $syncOptions[$optionId] = [
                    'is_allowed' => true,
                    'is_default' => false,
                    'price_override' => null,
                    'max_qty' => null,
                    'sort' => 0,
                ];
            }
            $product->options()->sync($syncOptions);
        });

        return redirect()
            ->route('staff.products.options.edit', $product)
            ->with('success', 'บันทึก Options/Groups เรียบร้อย');
    }
}
