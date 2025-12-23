<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        $options = Option::orderBy('sort')->orderBy('id')->paginate(50);
        return view('staff.options.index', compact('options'));
    }

    public function create()
    {
        return view('staff.options.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'base_price' => ['nullable','numeric','min:0'],
            'is_active' => ['nullable','boolean'],
            'sort' => ['nullable','integer','min:0'],
        ]);

        $data['base_price'] = $data['base_price'] ?? 0;
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['sort'] = $data['sort'] ?? 0;

        Option::create($data);

        return redirect()->route('staff.options.index')->with('success', 'สร้าง Option แล้ว');
    }

    public function edit(Option $option)
    {
        return view('staff.options.edit', compact('option'));
    }

    public function update(Request $request, Option $option)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'base_price' => ['nullable','numeric','min:0'],
            'is_active' => ['nullable','boolean'],
            'sort' => ['nullable','integer','min:0'],
        ]);

        $data['base_price'] = $data['base_price'] ?? 0;
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['sort'] = $data['sort'] ?? 0;

        $option->update($data);

        return redirect()->route('staff.options.index')->with('success', 'บันทึกแล้ว');
    }

    public function destroy(Option $option)
    {
        // หาก option ถูกผูกกับ product แล้ว จะลบได้/ไม่ได้ขึ้นกับ FK (ใน migration เราใช้ cascade/restrict ตามที่คุณตั้ง)
        $option->delete();
        return redirect()->route('staff.options.index')->with('success', 'ลบแล้ว');
    }
}
