<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelReaderImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {

            if ($key == 0) {
                continue;
            }

            $barcode = $row[1];
            $name = $row[2];
            $category_id = Category::where('name', is_numeric($row[7]) ? '' : $row[7])->first()?->id;

            if(!$category_id) {
                $category_id = Category::first()->id;
            }
 
            $product = Product::where('barcode', $barcode)->first();
            if ($product) {
                continue;
            }

            $product = new Product();
            $product->name = $name;
            $product->barcode = $barcode;
            $product->purchase_price = 0;
            $product->retail_price = 0;
            $product->category_id = $category_id;
            $product->wholesale_price = 0;
            $product->stock = 0;
            $product->save();
        }
    }
}
