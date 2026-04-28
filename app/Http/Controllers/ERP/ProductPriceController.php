<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function index(Request $request)
    {
        $productId = $request->product_id;
        $query = ProductPrice::query();
        if ($productId) {
            $query->where('product_id', $productId);
        }
        $prices = $query->with('product')->latest()->paginate(20)->withQueryString();
        $products = Product::all();
        
        return view('erp.product-prices.index', compact('prices', 'products', 'productId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:purchase,selling',
            'price' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
        ]);

        ProductPrice::create($validated);
        
        // Optionally update the product's main price
        $product = Product::find($validated['product_id']);
        if ($validated['type'] == 'selling') {
            $product->update(['selling_price' => $validated['price']]);
        } else {
            $product->update(['purchase_price' => $validated['price']]);
        }

        return redirect()->back()->with('success', 'Price updated successfully');
    }
}
