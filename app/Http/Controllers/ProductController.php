<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products   = Product::with('categories', 'variants')->latest()->get();
        $categories = Category::orderBy('name')->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:150',
            'categories'           => 'nullable|array',
            'categories.*'         => 'exists:categories,id',
            'description'          => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'unit'                 => 'nullable|string|max:50',
            'tax'                  => 'nullable|numeric|min:0|max:100',
            'image'                => 'nullable|image|max:2048',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required_with:variants|string|max:100',
            'variants.*.attribute' => 'nullable|string|max:100',
            'variants.*.unit'      => 'nullable|string|max:50',
            'variants.*.price'     => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->categories()->sync($data['categories'] ?? []);

        foreach ($request->input('variants', []) as $variant) {
            if (!empty($variant['name'])) {
                $product->variants()->create($variant);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load('variants', 'categories');
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:150',
            'categories'           => 'nullable|array',
            'categories.*'         => 'exists:categories,id',
            'description'          => 'nullable|string',
            'price'                => 'required|numeric|min:0',
            'unit'                 => 'nullable|string|max:50',
            'tax'                  => 'nullable|numeric|min:0|max:100',
            'image'                => 'nullable|image|max:2048',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required_with:variants|string|max:100',
            'variants.*.attribute' => 'nullable|string|max:100',
            'variants.*.unit'      => 'nullable|string|max:50',
            'variants.*.price'     => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->categories()->sync($data['categories'] ?? []);

        $product->variants()->delete();
        foreach ($request->input('variants', []) as $variant) {
            if (!empty($variant['name'])) {
                $product->variants()->create($variant);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $products = Product::whereIn('id', $request->input('ids', []))->get();
        foreach ($products as $product) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $product->delete();
        }
        return back()->with('success', count($products).' product(s) deleted.');
    }
}
