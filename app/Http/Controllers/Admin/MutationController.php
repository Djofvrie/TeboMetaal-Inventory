<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StockItem;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMutation::with(['variant.product.category', 'user'])
            ->orderByDesc('created_at');

        // Filter by mutation type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('variant.product', function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            });
        }

        $mutations = $query->paginate(50)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        $types = ['addition', 'removal', 'correction', 'inventory'];

        return view('admin.mutations.index', compact('mutations', 'categories', 'types'));
    }

    public function revert(StockMutation $mutation)
    {
        if (!$mutation->variant) {
            return back()->with('error', 'Kan mutatie niet ongedaan maken: variant is verwijderd.');
        }

        return DB::transaction(function () use ($mutation) {
            $variant = $mutation->variant;
            $lengthMm = $mutation->length_mm;
            $quantity = abs($mutation->quantity);

            if (in_array($mutation->type, ['addition', 'inventory'])) {
                // Reverse an addition: remove stock
                $stockItem = StockItem::where('product_variant_id', $variant->id)
                    ->where('length_mm', $lengthMm)
                    ->first();

                if ($stockItem) {
                    $stockItem->decrement('quantity', min($quantity, $stockItem->quantity));
                    if ($stockItem->fresh()->quantity <= 0) {
                        $stockItem->delete();
                    }
                }
            } elseif ($mutation->type === 'removal') {
                // Reverse a removal: add stock back
                $stockItem = StockItem::firstOrCreate(
                    ['product_variant_id' => $variant->id, 'length_mm' => $lengthMm],
                    ['quantity' => 0]
                );
                $stockItem->increment('quantity', $quantity);
            } elseif ($mutation->type === 'correction') {
                // For corrections, we can't reliably reverse - just log the revert
            }

            StockMutation::create([
                'product_variant_id' => $variant->id,
                'type' => 'correction',
                'length_mm' => $lengthMm,
                'quantity' => in_array($mutation->type, ['addition', 'inventory']) ? -$quantity : $quantity,
                'note' => 'Ongedaan gemaakt: ' . ($mutation->note ?: $mutation->type),
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Mutatie ongedaan gemaakt.');
        });
    }
}
