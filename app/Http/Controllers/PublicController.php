<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockItem;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        return view('public.index', compact('categories'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('products.variants')
            ->firstOrFail();

        return view('public.category', compact('category'));
    }

    public function product(Product $product)
    {
        $product->load(['category', 'variants.stockItems']);

        return view('public.product', compact('product'));
    }

    public function variant(ProductVariant $variant)
    {
        $variant->load(['product.category', 'stockItems']);

        return view('public.variant', compact('variant'));
    }

    public function deduct(Request $request, ProductVariant $variant)
    {
        $mode = $request->input('mode', 'cut');

        if ($mode === 'full') {
            return $this->deductFullPieces($request, $variant);
        }

        $validated = $request->validate([
            'length_mm' => 'required|integer|min:1',
            'source_length' => 'required|integer|min:1',
        ]);

        $requestedLengthMm = (int) $validated['length_mm'];
        $sourceLengthMm = (int) $validated['source_length'];

        if ($sourceLengthMm < $requestedLengthMm) {
            return back()->withInput()->with('error', "Het gekozen stuk ({$sourceLengthMm} mm) is korter dan de gewenste lengte ({$requestedLengthMm} mm).");
        }

        return DB::transaction(function () use ($variant, $requestedLengthMm, $sourceLengthMm) {
            $piece = StockItem::where('product_variant_id', $variant->id)
                ->where('length_mm', $sourceLengthMm)
                ->where('quantity', '>', 0)
                ->lockForUpdate()
                ->first();

            if (!$piece) {
                return back()->withInput()->with('error', 'Dit stuk is niet meer beschikbaar.');
            }

            $piece->decrement('quantity');
            if ($piece->fresh()->quantity <= 0) {
                $piece->delete();
            }

            // Create remainder if piece was longer
            if ($sourceLengthMm > $requestedLengthMm) {
                $remainingLength = $sourceLengthMm - $requestedLengthMm;
                $existing = StockItem::where('product_variant_id', $variant->id)
                    ->where('length_mm', $remainingLength)
                    ->first();

                if ($existing) {
                    $existing->increment('quantity');
                } else {
                    StockItem::create([
                        'product_variant_id' => $variant->id,
                        'length_mm' => $remainingLength,
                        'quantity' => 1,
                    ]);
                }
            }

            StockMutation::create([
                'product_variant_id' => $variant->id,
                'type' => 'removal',
                'length_mm' => $requestedLengthMm,
                'quantity' => 1,
                'note' => "Afgesneden {$requestedLengthMm} mm van stuk van {$sourceLengthMm} mm",
                'user_id' => auth()->id(),
            ]);

            $message = "{$requestedLengthMm} mm afgeboekt van stuk van {$sourceLengthMm} mm.";
            if ($sourceLengthMm > $requestedLengthMm) {
                $remaining = $sourceLengthMm - $requestedLengthMm;
                $message .= " Reststuk: {$remaining} mm.";
            }

            return back()->with('success', $message);
        });
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $products = collect();

        if (strlen($q) >= 2) {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $q);
            $products = Product::with(['category', 'variants.stockItems'])
                ->where(function ($query) use ($escaped) {
                    $query->where('name', 'like', "%{$escaped}%")
                        ->orWhere('dimension', 'like', "%{$escaped}%")
                        ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$escaped}%"));
                })
                ->orderBy('name')
                ->limit(50)
                ->get();
        }

        return view('public.search', compact('products', 'q'));
    }

    private function deductFullPieces(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'source_length' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
        ]);

        $sourceLengthMm = (int) $validated['source_length'];
        $quantity = (int) $validated['quantity'];

        return DB::transaction(function () use ($variant, $sourceLengthMm, $quantity) {
            $piece = StockItem::where('product_variant_id', $variant->id)
                ->where('length_mm', $sourceLengthMm)
                ->where('quantity', '>=', $quantity)
                ->lockForUpdate()
                ->first();

            if (!$piece) {
                return back()->withInput()->with('error', "Niet genoeg stuks van {$sourceLengthMm} mm beschikbaar.");
            }

            $piece->decrement('quantity', $quantity);
            if ($piece->fresh()->quantity <= 0) {
                $piece->delete();
            }

            StockMutation::create([
                'product_variant_id' => $variant->id,
                'type' => 'removal',
                'length_mm' => $sourceLengthMm,
                'quantity' => $quantity,
                'note' => "{$quantity}x heel stuk van {$sourceLengthMm} mm afgeboekt",
                'user_id' => auth()->id(),
            ]);

            $label = $quantity === 1 ? "1 stuk" : "{$quantity} stuks";
            return back()->with('success', "{$label} van {$sourceLengthMm} mm afgeboekt.");
        });
    }
}
