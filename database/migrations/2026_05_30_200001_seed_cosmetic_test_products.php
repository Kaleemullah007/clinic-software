<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure "Cosmetics" category exists (or create it)
        $catId = DB::table('categories')->where('name', 'Cosmetics')->value('id');
        if (!$catId) {
            $catId = DB::table('categories')->insertGetId([
                'name'            => 'Cosmetics',
                'price'           => 0,
                'benchmark_price' => 0,
                'status'          => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // 2. Seed products
        $products = [
            [
                'name'            => 'Moisturizing Day Cream',
                'description'     => 'Hydrating day cream for all skin types. SPF 15 protection.',
                'price'           => 1200.00,
                'has_variations'  => false,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Sunscreen SPF 50',
                'description'     => 'Broad-spectrum UVA/UVB protection. Lightweight formula.',
                'price'           => 1800.00,
                'has_variations'  => true,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Anti-Aging Serum',
                'description'     => 'Retinol + Vitamin C brightening serum. Reduces fine lines.',
                'price'           => 3500.00,
                'has_variations'  => false,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Gentle Face Wash',
                'description'     => 'Sulphate-free foaming cleanser suitable for sensitive skin.',
                'price'           => 850.00,
                'has_variations'  => false,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Hydrating Toner',
                'description'     => 'Alcohol-free toner with hyaluronic acid. Balances skin pH.',
                'price'           => 1100.00,
                'has_variations'  => false,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Lip Balm',
                'description'     => 'SPF 20 moisturising lip balm. Available in multiple flavours.',
                'price'           => 350.00,
                'has_variations'  => true,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
            [
                'name'            => 'Under Eye Cream',
                'description'     => 'Reduces dark circles and puffiness. Peptide-infused formula.',
                'price'           => 2200.00,
                'has_variations'  => false,
                'track_inventory' => true,
                'category_id'     => $catId,
                'status'          => true,
            ],
        ];

        foreach ($products as $p) {
            // Avoid duplicates
            if (DB::table('products')->where('name', $p['name'])->exists()) continue;

            $productId = DB::table('products')->insertGetId(array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Seed inventory for each product
            DB::table('inventory')->insertOrIgnore([
                'product_id'   => $productId,
                'variation_id' => null,
                'quantity'     => rand(15, 80),
                'cost_price'   => round($p['price'] * 0.6, 2),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Seed variations for products that have them
            if ($p['has_variations']) {
                $variations = match ($p['name']) {
                    'Sunscreen SPF 50' => [
                        ['name' => '50 ml',  'price' => 1800.00],
                        ['name' => '100 ml', 'price' => 3200.00],
                        ['name' => '200 ml', 'price' => 5500.00],
                    ],
                    'Lip Balm' => [
                        ['name' => 'Strawberry', 'price' => 350.00],
                        ['name' => 'Vanilla',    'price' => 350.00],
                        ['name' => 'Mint',       'price' => 350.00],
                    ],
                    default => [],
                };

                foreach ($variations as $v) {
                    if (DB::table('product_variations')->where('product_id', $productId)->where('name', $v['name'])->exists()) continue;

                    $varId = DB::table('product_variations')->insertGetId([
                        'product_id' => $productId,
                        'name'       => $v['name'],
                        'price'      => $v['price'],
                        'status'     => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('inventory')->insertOrIgnore([
                        'product_id'   => $productId,
                        'variation_id' => $varId,
                        'quantity'     => rand(10, 50),
                        'cost_price'   => round($v['price'] * 0.6, 2),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $names = [
            'Moisturizing Day Cream', 'Sunscreen SPF 50', 'Anti-Aging Serum',
            'Gentle Face Wash', 'Hydrating Toner', 'Lip Balm', 'Under Eye Cream',
        ];

        $ids = DB::table('products')->whereIn('name', $names)->pluck('id');
        DB::table('inventory')->whereIn('product_id', $ids)->delete();
        DB::table('product_variations')->whereIn('product_id', $ids)->delete();
        DB::table('products')->whereIn('name', $names)->delete();
    }
};
