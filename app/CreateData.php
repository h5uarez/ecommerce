<?php

namespace App;

use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

trait CreateData
{

    public function createData($color = false, $size = false, $quantity = 15)
    {
        $brand = Brand::factory()->create();

        $category = Category::factory()->create([]);

        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size,
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => $quantity,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        if ($color && !$size) {
            $product->quantity = null;
            $Color = Color::factory()->create();
            $product->colors()
                ->attach($Color->id, ['quantity' => 15]);
        } elseif ($size && $color) {
            $product->quantity = null;
            $Color = Color::factory()->create();
            $Size = Size::factory()->create([
                'product_id' => $product->id
            ]);
            $Size->colors()
                ->attach($Color->id, ['quantity' => 15]);
        }

        return $product;
    }
}
