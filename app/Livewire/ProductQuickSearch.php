<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductQuickSearch extends Component
{
    public $search = '';
    public $suggestions = [];

    public function mount() {}

    public function searchProducts()
    {
        $value = $this->search;
        if (is_numeric($value)) {
            $product = Product::where('barcode', $value)->first();
            if ($product) {
                $this->addProductToInvoice($product);
                $this->search = '';
                $this->suggestions = [];
                return;
            }
        }

        if(empty($value)) {
            $this->suggestions = [];
            return;
        }

        $this->suggestions = Product::where('name', 'like', "%{$value}%")
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->addProductToInvoice($product);
            $this->search = '';
            $this->suggestions = [];
        }
    }

    private function addProductToInvoice($product)
    {
        $this->dispatch('productSelectedFromSearch', $product->id);
    }

    public function render()
    {
        return view('livewire.product-quick-search');
    }
}
