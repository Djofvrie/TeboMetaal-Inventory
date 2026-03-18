<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products.variants'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'dimension' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'variants' => 'nullable|array',
            'variants.*.wall_thickness' => 'required|numeric|min:0',
            'variants.*.quality' => 'nullable|string|max:255',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
            'variants.*.drawer' => 'nullable|integer|min:1|max:20',
        ]);

        return DB::transaction(function () use ($validated) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'dimension' => $validated['dimension'],
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    $product->variants()->create([
                        'wall_thickness' => $variantData['wall_thickness'],
                        'quality' => $variantData['quality'] ?? null,
                        'drawer' => $variantData['drawer'] ?? null,
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 0,
                    ]);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product aangemaakt.');
        });
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $product->load('variants');
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'dimension' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.wall_thickness' => 'required|numeric|min:0',
            'variants.*.quality' => 'nullable|string|max:255',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
            'remove_variants' => 'nullable|array',
            'remove_variants.*' => 'exists:product_variants,id',
        ]);

        return DB::transaction(function () use ($product, $validated) {
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'dimension' => $validated['dimension'],
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            // Remove variants marked for deletion
            if (!empty($validated['remove_variants'])) {
                $product->variants()
                    ->whereIn('id', $validated['remove_variants'])
                    ->each(function ($variant) {
                        if ($variant->stockItems()->exists() || $variant->mutations()->exists()) {
                            return; // Skip variants with stock data
                        }
                        $variant->delete();
                    });
            }

            // Update or create variants
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        $product->variants()
                            ->where('id', $variantData['id'])
                            ->update([
                                'wall_thickness' => $variantData['wall_thickness'],
                                'quality' => $variantData['quality'] ?? null,
                                'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 0,
                            ]);
                    } else {
                        $product->variants()->create([
                            'wall_thickness' => $variantData['wall_thickness'],
                            'quality' => $variantData['quality'] ?? null,
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 0,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product bijgewerkt.');
        });
    }

    public function destroy(Product $product)
    {
        $hasStock = $product->variants()->whereHas('stockItems')->exists();
        if ($hasStock) {
            return back()->with('error', 'Kan product niet verwijderen: er is nog voorraad gekoppeld aan varianten.');
        }

        DB::transaction(function () use ($product) {
            $product->variants()->delete();
            $product->delete();
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product verwijderd.');
    }
}
