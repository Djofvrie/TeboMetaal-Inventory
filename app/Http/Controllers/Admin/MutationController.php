<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StockMutation;
use Illuminate\Http\Request;

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
}
