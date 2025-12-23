<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->orderByDesc('id')->paginate(20);
        return view('staff.products.index', compact('products'));
    }

    public function create()
    {
        return view('staff.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'], // 4MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'is_active' => (bool)($data['is_active'] ?? true),
            'image_path' => $imagePath,
        ]);

        return redirect()->route('staff.products.index')->with('success', 'เพิ่มสินค้าแล้ว');
    }

    public function edit(Product $product)
    {
        return view('staff.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $newPath = $product->image_path;

        if (($data['remove_image'] ?? false) && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $newPath = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $newPath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'is_active' => (bool)($data['is_active'] ?? false),
            'image_path' => $newPath,
        ]);

        return redirect()->route('staff.products.index')->with('success', 'บันทึกสินค้าแล้ว');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('staff.products.index')->with('success', 'ลบสินค้าแล้ว');
    }
}
