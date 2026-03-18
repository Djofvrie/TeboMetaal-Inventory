<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockItem;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockController extends Controller
{
    public function dashboard()
    {
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalVariants = ProductVariant::count();
        $totalStockItems = StockItem::count();
        $totalLengthMm = StockItem::selectRaw('SUM(length_mm * quantity) as total')->value('total') ?? 0;
        $totalLengthM = round($totalLengthMm / 1000, 1);
        $totalPieces = StockItem::sum('quantity');

        // Low stock: variants where total quantity <= threshold
        $lowStockVariants = ProductVariant::with(['product.category'])
            ->withSum('stockItems', 'quantity')
            ->get()
            ->filter(fn ($v) => ($v->stock_items_sum_quantity ?? 0) <= $v->low_stock_threshold)
            ->sortBy('stock_items_sum_quantity')
            ->take(50);

        // Recent mutations
        $recentMutations = StockMutation::with(['variant.product.category', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalCategories',
            'totalProducts',
            'totalVariants',
            'totalStockItems',
            'totalLengthM',
            'totalPieces',
            'lowStockVariants',
            'recentMutations'
        ));
    }

    public function index()
    {
        $categories = Category::with(['products.variants.stockItems'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.stock.index', compact('categories'));
    }

    public function edit(ProductVariant $variant)
    {
        $variant->load(['product.category', 'stockItems', 'mutations' => function ($query) {
            $query->latest()->limit(20);
        }]);

        return view('admin.stock.edit', compact('variant'));
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'action' => 'required|in:add,remove,correct',
            'length_mm' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:255',
            'stock_item_id' => 'nullable|integer|exists:stock_items,id',
        ]);

        return DB::transaction(function () use ($variant, $validated) {
            $lengthMm = $validated['length_mm'];
            $quantity = $validated['quantity'];
            $action = $validated['action'];

            if ($action === 'add') {
                // Add stock: find existing item with same length or create new
                $stockItem = StockItem::firstOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'length_mm' => $lengthMm,
                    ],
                    ['quantity' => 0]
                );
                $stockItem->increment('quantity', $quantity);

                StockMutation::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'addition',
                    'length_mm' => $lengthMm,
                    'quantity' => $quantity,
                    'note' => $validated['note'] ?? null,
                    'user_id' => auth()->id(),
                ]);
            } elseif ($action === 'remove') {
                // Remove stock
                $stockItem = StockItem::where('product_variant_id', $variant->id)
                    ->where('length_mm', $lengthMm)
                    ->where('quantity', '>=', $quantity)
                    ->lockForUpdate()
                    ->first();

                if (!$stockItem) {
                    return back()->with('error', 'Onvoldoende voorraad voor deze afmeting.');
                }

                $stockItem->decrement('quantity', $quantity);

                if ($stockItem->fresh()->quantity <= 0) {
                    $stockItem->delete();
                }

                StockMutation::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'removal',
                    'length_mm' => $lengthMm,
                    'quantity' => $quantity,
                    'note' => $validated['note'] ?? null,
                    'user_id' => auth()->id(),
                ]);
            } elseif ($action === 'correct') {
                // Correction: update existing stock item directly
                if (!empty($validated['stock_item_id'])) {
                    $stockItem = StockItem::where('id', $validated['stock_item_id'])
                        ->where('product_variant_id', $variant->id)
                        ->lockForUpdate()
                        ->first();
                } else {
                    $stockItem = StockItem::firstOrCreate(
                        [
                            'product_variant_id' => $variant->id,
                            'length_mm' => $lengthMm,
                        ],
                        ['quantity' => 0]
                    );
                }

                if (!$stockItem) {
                    return back()->with('error', 'Voorraadregel niet gevonden.');
                }

                $oldQuantity = $stockItem->quantity;
                $oldLength = $stockItem->length_mm;
                $stockItem->update(['length_mm' => $lengthMm, 'quantity' => $quantity]);

                if ($quantity <= 0) {
                    $stockItem->delete();
                }

                StockMutation::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'correction',
                    'length_mm' => $lengthMm,
                    'quantity' => $quantity - $oldQuantity,
                    'note' => $validated['note'] ?? "Correctie: {$oldLength}mm {$oldQuantity}x → {$lengthMm}mm {$quantity}x",
                    'user_id' => auth()->id(),
                ]);
            }

            return back()->with('success', 'Voorraad bijgewerkt.');
        });
    }

    public function inventory(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'inventory_data' => 'required|string',
            'note' => 'nullable|string|max:255',
        ]);

        // Parse "6000 x 10" lines into items
        $lines = preg_split('/\r?\n/', trim($validated['inventory_data']));
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            if (!preg_match('/^(\d+)\s*x\s*(\d+)$/i', $line, $matches)) {
                return back()->withInput()->with('error', "Ongeldig formaat: \"{$line}\". Gebruik: lengte_mm x aantal (bijv. 6000 x 10)");
            }

            $items[] = [
                'length_mm' => (int) $matches[1],
                'quantity' => (int) $matches[2],
            ];
        }

        if (empty($items)) {
            return back()->withInput()->with('error', 'Voer minimaal één voorraadregel in.');
        }

        return DB::transaction(function () use ($variant, $items, $validated) {
            // Delete all current stock for this variant
            StockItem::where('product_variant_id', $variant->id)->delete();

            // Create new stock items
            foreach ($items as $item) {
                if ($item['quantity'] > 0) {
                    StockItem::create([
                        'product_variant_id' => $variant->id,
                        'length_mm' => $item['length_mm'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            }

            // Record inventory mutation
            StockMutation::create([
                'product_variant_id' => $variant->id,
                'type' => 'inventory',
                'length_mm' => 0,
                'quantity' => collect($items)->sum('quantity'),
                'note' => $validated['note'] ?? 'Volledige inventarisatie',
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Inventarisatie opgeslagen.');
        });
    }

    public function threshold(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $variant->update($validated);

        return back()->with('success', 'Drempel opgeslagen.');
    }

    public function importForm()
    {
        return view('admin.stock.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        if (count($rows) < 2) {
            return back()->with('error', 'Het CSV-bestand bevat geen gegevensrijen.');
        }

        // Validate header
        $header = array_map('trim', array_map('strtolower', $rows[0]));
        $expectedHeader = ['categorie', 'product', 'afmeting', 'wanddikte', 'kwaliteit', 'lengte_mm', 'aantal'];

        if ($header !== $expectedHeader) {
            return back()->with('error', 'Ongeldige CSV-header. Verwacht: ' . implode(',', $expectedHeader));
        }

        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($rows, &$imported, &$errors) {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip empty rows
                if (count($row) < 7 || empty(trim($row[0]))) {
                    continue;
                }

                $categoryName = trim($row[0]);
                $productName = trim($row[1]);
                $dimension = trim($row[2]);
                $wallThickness = floatval(str_replace(',', '.', trim($row[3])));
                $quality = trim($row[4]) ?: null;
                $lengthMm = intval(trim($row[5]));
                $quantity = intval(trim($row[6]));

                if ($lengthMm <= 0 || $quantity <= 0) {
                    $errors[] = "Regel " . ($i + 1) . ": ongeldige lengte of aantal.";
                    continue;
                }

                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName), 'sort_order' => 0]
                );

                // Find or create product
                $product = Product::firstOrCreate(
                    ['name' => $productName, 'category_id' => $category->id],
                    ['dimension' => $dimension, 'sort_order' => 0]
                );

                // Find or create variant
                $variant = ProductVariant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'wall_thickness' => $wallThickness,
                        'quality' => $quality,
                    ]
                );

                // Add stock (merge with existing same-length items)
                $stockItem = StockItem::firstOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'length_mm' => $lengthMm,
                    ],
                    ['quantity' => 0]
                );
                $stockItem->increment('quantity', $quantity);

                // Log mutation
                StockMutation::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'addition',
                    'length_mm' => $lengthMm,
                    'quantity' => $quantity,
                    'note' => 'CSV-import',
                    'user_id' => auth()->id(),
                ]);

                $imported++;
            }
        });

        $message = $imported . ' regel(s) succesvol geïmporteerd.';
        if (count($errors) > 0) {
            $message .= ' ' . count($errors) . ' regel(s) overgeslagen: ' . implode(' ', $errors);
        }

        return redirect()->route('admin.stock.import')->with('success', $message);
    }
}
