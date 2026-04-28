<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerProfileController extends Controller
{
    public function show(Party $party, Request $request)
    {
        // Past Order History
        $orders = Order::where('party_id', $party->id)
            ->with(['items.product', 'warehouse'])
            ->latest()
            ->paginate(5, ['*'], 'orders_page');

        // Product Catalog with Filtering & AJAX
        $productsQuery = Product::query()->with(['brand', 'category', 'stocks']);
        
        if ($request->filled('search')) {
            $q = $request->search;
            $productsQuery->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->category);
        }

        if ($request->filled('sort')) {
            $sort = explode('|', $request->sort);
            if (count($sort) == 2) {
                $productsQuery->orderBy($sort[0], $sort[1]);
            }
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(12, ['*'], 'products_page');

        if ($request->ajax()) {
            return view('erp.parties._product_table', compact('party', 'products'));
        }

        // Cart Data
        $cart = session()->get("cart.{$party->id}", []);
        $cartTotal = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $warehouses = Warehouse::all();
        $categories = \App\Models\Category::all();

        return view('erp.parties.profile', compact('party', 'orders', 'products', 'cart', 'cartTotal', 'warehouses', 'categories'));
    }

    public function addToCart(Request $request, Party $party)
    {
        $productId = $request->product_id;
        $quantity = (int) ($request->quantity ?? 1);
        
        $product = Product::with('taxRate')->findOrFail($productId);
        $cart = session()->get("cart.{$party->id}", []);
        
        // Calculate Tax (Dummy logic based on percentage if available)
        $taxPercent = $product->taxRate->rate ?? 0;
        $unitPrice = (float) $product->selling_price;
        $taxAmount = ($unitPrice * $taxPercent) / 100;
        
        if(isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $unitPrice,
                "tax" => $taxAmount,
                "discount" => 0, // Placeholder for future discount logic
                "sku" => $product->sku
            ];
        }
        
        session()->put("cart.{$party->id}", $cart);
        return redirect()->to(route('erp.parties.profile', $party->id) . '#v-pills-products')->with('success', 'Product added to cart');
    }

    public function removeFromCart(Request $request, Party $party, $productId)
    {
        $cart = session()->get("cart.{$party->id}", []);
        if(isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put("cart.{$party->id}", $cart);
        }
        return redirect()->back()->with(['success' => 'Product removed from cart', 'cart_open' => true]);
    }

    public function clearCart(Party $party)
    {
        session()->forget("cart.{$party->id}");
        return redirect()->back()->with(['success' => 'Cart cleared', 'cart_open' => true]);
    }

    public function placeOrder(Request $request, Party $party)
    {
        $cart = session()->get("cart.{$party->id}", []);
        if(empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'payment_method' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalSubtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($cart as $item) {
                $totalSubtotal += $item['price'] * $item['quantity'];
                $totalTax += ($item['tax'] ?? 0) * $item['quantity'];
                $totalDiscount += ($item['discount'] ?? 0) * $item['quantity'];
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'party_id' => $party->id,
                'warehouse_id' => $request->warehouse_id,
                'type' => 'sale',
                'order_date' => now(),
                'sub_total' => $totalSubtotal,
                'tax_amount' => $totalTax,
                'discount' => $totalDiscount,
                'total_amount' => ($totalSubtotal + $totalTax) - $totalDiscount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            foreach ($cart as $id => $details) {
                $order->items()->create([
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'unit_price' => $details['price'],
                    'tax_amount' => ($details['tax'] ?? 0) * $details['quantity'],
                    'discount' => ($details['discount'] ?? 0) * $details['quantity'],
                    'total_price' => ($details['price'] + ($details['tax'] ?? 0) - ($details['discount'] ?? 0)) * $details['quantity'],
                ]);
            }

            DB::commit();
            session()->forget("cart.{$party->id}");

            return redirect()->to(route('erp.parties.profile', $party->id) . '#v-pills-orders')->with('success', 'Order #' . $order->order_number . ' placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
