<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\OptionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionGroupController extends Controller
{
    public function index()
    {
        $groups = OptionGroup::withCount('options')->orderBy('sort')->orderBy('id')->paginate(30);
        return view('staff.option_groups.index', compact('groups'));
    }

    public function create()
    {
        $options = Option::orderBy('sort')->orderBy('name')->get();
        return view('staff.option_groups.create', compact('options'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'sort' => ['nullable','integer','min:0'],
            'option_ids' => ['array'],
            'option_ids.*' => ['integer','exists:options,id'],
            'option_order' => ['array'],
            'option_order.*' => ['integer','exists:options,id'],
        ]);

        return DB::transaction(function () use ($data) {
            $group = OptionGroup::create([
                'name' => $data['name'],
                'sort' => $data['sort'] ?? 0,
            ]);

            $ids = $data['option_ids'] ?? [];
            $order = $data['option_order'] ?? [];

            if (!empty($order)) {
                $order = array_values(array_filter($order, fn($id) => in_array($id, $ids)));
                $idsNotInOrder = array_values(array_diff($ids, $order));
                $ids = array_merge($order, $idsNotInOrder);
            }

            if (!empty($ids)) {
                $sync = [];
                $i = 0;
                foreach ($ids as $id) {
                    $sync[$id] = ['sort' => $i++];
                }
                $group->options()->sync($sync);
            }

            return redirect()->route('staff.option-groups.index')->with('success', 'สร้างกลุ่มแล้ว');
        });
    }

    public function edit(OptionGroup $option_group)
    {
        $group = $option_group->load('options');
        $options = Option::orderBy('sort')->orderBy('name')->get();
        $selected = $group->options->pluck('id')->all();

        return view('staff.option_groups.edit', compact('group', 'options', 'selected'));
    }

    public function update(Request $request, OptionGroup $option_group)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'sort' => ['nullable','integer','min:0'],
            'option_ids' => ['array'],
            'option_ids.*' => ['integer','exists:options,id'],
            'option_order' => ['array'],
            'option_order.*' => ['integer','exists:options,id'],
        ]);

        return DB::transaction(function () use ($option_group, $data) {
            $option_group->update([
                'name' => $data['name'],
                'sort' => $data['sort'] ?? 0,
            ]);

            $ids = $data['option_ids'] ?? [];
            $order = $data['option_order'] ?? [];

            if (!empty($order)) {
                $order = array_values(array_filter($order, fn($id) => in_array($id, $ids)));
                $idsNotInOrder = array_values(array_diff($ids, $order));
                $ids = array_merge($order, $idsNotInOrder);
            }

            $sync = [];
            $i = 0;
            foreach ($ids as $id) {
                $sync[$id] = ['sort' => $i++];
            }
            $option_group->options()->sync($sync);

            return redirect()->route('staff.option-groups.edit', $option_group->id)->with('success', 'บันทึกแล้ว');
        });
    }

    public function destroy(OptionGroup $option_group)
    {
        $option_group->delete();
        return redirect()->route('staff.option-groups.index')->with('success', 'ลบกลุ่มแล้ว');
    }
}
