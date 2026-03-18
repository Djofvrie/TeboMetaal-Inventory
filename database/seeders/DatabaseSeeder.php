<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@tebo.nl',
            'password' => bcrypt('password'),
        ]);

        $this->seedKoker();
        $this->seedBuis();
        $this->seedDikwandigeBuis();
        $this->seedStrip();
        $this->seedHoeklijn();
        $this->seedVierkant();
        $this->seedPlaat();
        $this->seedAs();
    }

    // ─── Koker ──────────────────────────────────────────────────────────

    private function seedKoker(): void
    {
        $category = Category::create([
            'name' => 'Koker',
            'slug' => 'koker',
            'icon' => 'koker',
            'sort_order' => 1,
        ]);

        $products = [
            ['60x60', [4]],
            ['70x70', [4, 5]],
            ['80x80', [4, 5, 6, 8, 10]],
            ['90x90', [4, 5, 6, 8]],
            ['100x100', [4, 5, 6, 8, 10, 12.5]],
            ['110x110', [6, 8, 10, 12.5]],
            ['120x120', [4, 5, 6, 8, 10, 12.5]],
            ['140x140', [4, 5, 6, 8, 10, 12.5]],
            ['150x150', [4, 5, 6, 8, 10, 12.5, 16]],
            ['160x160', [4, 5, 6, 8, 10, 12.5]],
            ['180x180', [4, 5, 6, 8, 10, 12.5, 16]],
            ['200x200', [5, 6, 8, 10, 12.5, 16]],
            ['220x220', [6, 8, 10, 12.5, 16]],
            ['250x250', [6, 8, 10, 12.5, 16]],
            ['260x260', [6, 8, 10, 12.5]],
            ['300x300', [6, 8, 10, 12.5, 16, 20]],
            ['350x350', [6, 8, 10, 12.5, 16]],
            ['400x400', [6, 8, 10, 12.5, 16, 20]],
            ['500x500', [12.5, 16, 20]],
        ];

        foreach ($products as $sortOrder => [$dim, $walls]) {
            $label = str_replace('x', ' x ', $dim);
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Koker {$label}",
                'dimension' => $dim,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, $walls);
        }
    }

    // ─── Buis ───────────────────────────────────────────────────────────

    private function seedBuis(): void
    {
        $category = Category::create([
            'name' => 'Buis',
            'slug' => 'buis',
            'icon' => 'buis',
            'sort_order' => 2,
        ]);

        $products = [
            ['Ø21.3', [2.0, 2.6, 3.2]],
            ['Ø26.9', [2.0, 2.6, 3.2]],
            ['Ø33.7', [2.6, 3.2, 4.0]],
            ['Ø42.4', [2.6, 3.2, 4.0]],
            ['Ø48.3', [2.6, 3.2, 4.0, 5.0]],
            ['Ø60.3', [2.9, 3.2, 4.0, 5.0]],
            ['Ø76.1', [2.9, 3.2, 4.0, 5.0]],
            ['Ø88.9', [3.2, 4.0, 5.0, 6.3]],
            ['Ø101.6', [3.6, 4.0, 5.0, 6.3]],
            ['Ø114.3', [3.6, 4.0, 5.0, 6.3]],
            ['Ø139.7', [4.0, 5.0, 6.3, 8.0]],
            ['Ø168.3', [4.0, 5.0, 6.3, 8.0]],
            ['Ø219.1', [5.0, 6.3, 8.0, 10.0]],
            ['Ø273.0', [5.0, 6.3, 8.0, 10.0]],
            ['Ø323.9', [5.6, 6.3, 8.0, 10.0]],
            ['Ø355.6', [6.3, 8.0, 10.0, 12.5]],
            ['Ø406.4', [6.3, 8.0, 10.0, 12.5]],
        ];

        foreach ($products as $sortOrder => [$dim, $walls]) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Buis {$dim}",
                'dimension' => $dim,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, $walls);
        }
    }

    // ─── Dikwandige buis ────────────────────────────────────────────────

    private function seedDikwandigeBuis(): void
    {
        $category = Category::create([
            'name' => 'Dikwandige buis',
            'slug' => 'dikwandige-buis',
            'icon' => 'dikwandige-buis',
            'sort_order' => 3,
        ]);

        $products = [
            ['Ø33.7', [5.0, 6.3, 8.0]],
            ['Ø42.4', [5.0, 6.3, 8.0]],
            ['Ø48.3', [6.3, 8.0, 10.0]],
            ['Ø60.3', [6.3, 8.0, 10.0, 12.5]],
            ['Ø76.1', [6.3, 8.0, 10.0, 12.5]],
            ['Ø88.9', [8.0, 10.0, 12.5, 16.0]],
            ['Ø101.6', [8.0, 10.0, 12.5, 16.0]],
            ['Ø114.3', [8.0, 10.0, 12.5, 16.0]],
            ['Ø139.7', [10.0, 12.5, 16.0, 20.0]],
            ['Ø168.3', [10.0, 12.5, 16.0, 20.0]],
            ['Ø219.1', [12.5, 16.0, 20.0, 25.0]],
        ];

        foreach ($products as $sortOrder => [$dim, $walls]) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Dikw. Buis {$dim}",
                'dimension' => $dim,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, $walls);
        }
    }

    // ─── Strip ──────────────────────────────────────────────────────────

    private function seedStrip(): void
    {
        $category = Category::create([
            'name' => 'Strip',
            'slug' => 'strip',
            'icon' => 'strip',
            'sort_order' => 4,
        ]);

        $products = [
            [20, [3, 4, 5]],
            [25, [3, 4, 5]],
            [30, [3, 4, 5, 6]],
            [40, [4, 5, 6, 8]],
            [50, [5, 6, 8, 10]],
            [60, [5, 6, 8, 10]],
            [80, [6, 8, 10]],
            [100, [8, 10, 12]],
            [120, [10, 12]],
            [150, [10, 12, 15]],
            [200, [12, 15, 20]],
        ];

        foreach ($products as $sortOrder => [$width, $thicknesses]) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Strip {$width} mm",
                'dimension' => (string) $width,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, $thicknesses);
        }
    }

    // ─── Hoeklijn ───────────────────────────────────────────────────────

    private function seedHoeklijn(): void
    {
        $category = Category::create([
            'name' => 'Hoeklijn',
            'slug' => 'hoeklijn',
            'icon' => 'hoeklijn',
            'sort_order' => 5,
        ]);

        $products = [
            ['20x20', [3]],
            ['25x25', [3]],
            ['30x30', [3, 4]],
            ['40x40', [3, 4, 5]],
            ['50x50', [4, 5, 6]],
            ['60x60', [5, 6, 8]],
            ['70x70', [6, 7, 8]],
            ['80x80', [6, 8, 10]],
            ['100x100', [8, 10, 12]],
            ['120x120', [10, 12, 15]],
            ['150x150', [12, 15, 18]],
            ['200x200', [16, 18, 20]],
        ];

        foreach ($products as $sortOrder => [$dim, $walls]) {
            $label = str_replace('x', ' x ', $dim);
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Hoeklijn {$label}",
                'dimension' => $dim,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, $walls);
        }
    }

    // ─── Vierkant ───────────────────────────────────────────────────────

    private function seedVierkant(): void
    {
        $category = Category::create([
            'name' => 'Vierkant',
            'slug' => 'vierkant',
            'icon' => 'vierkant',
            'sort_order' => 6,
        ]);

        $sizes = [10, 12, 14, 16, 20, 25, 30, 35, 40, 50, 60, 70, 80];

        foreach ($sizes as $sortOrder => $size) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Vierkant {$size} mm",
                'dimension' => (string) $size,
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, [0]);
        }
    }

    // ─── Plaat ──────────────────────────────────────────────────────────

    private function seedPlaat(): void
    {
        $category = Category::create([
            'name' => 'Plaat',
            'slug' => 'plaat',
            'icon' => 'plaat',
            'sort_order' => 7,
        ]);

        $thicknesses = [1, 1.5, 2, 3, 4, 5, 6, 8, 10, 12, 15, 20, 25];

        foreach ($thicknesses as $sortOrder => $thickness) {
            $dimLabel = ($thickness == (int) $thickness) ? (int) $thickness : $thickness;
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "Plaat {$dimLabel} mm",
                'dimension' => (string) $dimLabel,
                'sort_order' => $sortOrder + 1,
            ]);

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'wall_thickness' => 0,
                'quality' => 'S235',
            ]);

            // Plaat uses sheet lengths (standard 2000mm width implied)
            $sheetLengths = [2000, 3000, 6000];
            $count = rand(2, 5);
            for ($i = 0; $i < $count; $i++) {
                StockItem::create([
                    'product_variant_id' => $variant->id,
                    'length_mm' => $sheetLengths[array_rand($sheetLengths)],
                    'quantity' => rand(1, 10),
                ]);
            }
        }
    }

    // ─── As ─────────────────────────────────────────────────────────────

    private function seedAs(): void
    {
        $category = Category::create([
            'name' => 'As',
            'slug' => 'as',
            'icon' => 'as',
            'sort_order' => 8,
        ]);

        $diameters = [10, 12, 14, 16, 20, 25, 30, 35, 40, 50, 60, 70, 80, 100, 120, 150, 200, 250, 300];

        foreach ($diameters as $sortOrder => $d) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => "As Ø{$d} mm",
                'dimension' => "Ø{$d}",
                'sort_order' => $sortOrder + 1,
            ]);

            $this->createVariantsWithStock($product, [0]);
        }
    }

    // ─── Helpers ────────────────────────────────────────────────────────

    /**
     * Create product variants with random demo stock items.
     */
    private function createVariantsWithStock(Product $product, array $wallThicknesses): void
    {
        $allQualities = ['S235', 'S275', 'S355'];

        foreach ($wallThicknesses as $wt) {
            // Randomly assign 1-3 qualities per wall thickness, S235 always included
            $qualities = ['S235'];
            if (rand(0, 1)) {
                $qualities[] = 'S275';
            }
            if (rand(0, 2) === 0) {
                $qualities[] = 'S355';
            }

            foreach ($qualities as $quality) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'wall_thickness' => $wt,
                    'quality' => $quality,
                ]);

                $this->createDemoStock($variant);
            }
        }
    }

    /**
     * Create random demo stock items for a variant.
     */
    private function createDemoStock(ProductVariant $variant): void
    {
        $lengths = [500, 1000, 2000, 3000, 6000];
        // Pick 2-4 unique lengths
        $selectedLengths = collect($lengths)->shuffle()->take(rand(2, 4));

        foreach ($selectedLengths as $length) {
            StockItem::create([
                'product_variant_id' => $variant->id,
                'length_mm' => $length,
                'quantity' => rand(1, 10),
            ]);
        }
    }
}
