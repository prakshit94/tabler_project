<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\PartyAddress;
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

        // Master data for Edit Profile modal
        $account_types = \App\Models\AccountType::where('is_active', true)->orderBy('name')->get();
        $land_units = \App\Models\LandUnit::all();
        $irrigation_types = \App\Models\IrrigationType::all();
        $crops_master = \App\Models\Crop::orderBy('name')->get();

        return view('erp.parties.profile', compact(
            'party', 'orders', 'products', 'cart', 'cartTotal', 'warehouses', 'categories',
            'account_types', 'land_units', 'irrigation_types', 'crops_master'
        ));
    }

    public function addToCart(Request $request, Party $party)
    {
        $productId = $request->product_id;
        $quantity = (int) ($request->quantity ?? 1);

        if ($quantity <= 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Quantity must be at least 1']);
            }
            return redirect()->back()->with('error', 'Quantity must be at least 1');
        }
        
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

        if ($request->ajax()) {
            $cartTotal = array_reduce($cart, function($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            
            $cartHtml = view('erp.parties._cart_content', compact('party', 'cart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cartCount' => count($cart),
                'cartTotal' => number_format($cartTotal, 2),
                'cartHtml' => $cartHtml
            ]);
        }

        return redirect()->to(route('erp.parties.profile', $party->id) . '#v-pills-products')->with('success', 'Product added to cart');
    }

    public function removeFromCart(Request $request, Party $party, $productId)
    {
        $cart = session()->get("cart.{$party->id}", []);
        if(isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put("cart.{$party->id}", $cart);
        }

        if ($request->ajax()) {
            $cartTotal = array_reduce($cart, function($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            
            $cartHtml = view('erp.parties._cart_content', compact('party', 'cart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'cartCount' => count($cart),
                'cartTotal' => number_format($cartTotal, 2),
                'cartHtml' => $cartHtml
            ]);
        }

        return redirect()->back()->with(['success' => 'Product removed from cart', 'cart_open' => true]);
    }

    public function updateCart(Request $request, Party $party, $productId)
    {
        $quantity = (int) $request->quantity;
        if ($quantity <= 0) {
            return response()->json(['success' => false, 'message' => 'Quantity must be at least 1']);
        }

        $cart = session()->get("cart.{$party->id}", []);
        if(isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put("cart.{$party->id}", $cart);
        }

        if ($request->ajax()) {
            $cartTotal = array_reduce($cart, function($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            
            $cartHtml = view('erp.parties._cart_content', compact('party', 'cart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cartCount' => count($cart),
                'cartTotal' => number_format($cartTotal, 2),
                'cartHtml' => $cartHtml
            ]);
        }

        return redirect()->back()->with(['success' => 'Cart updated', 'cart_open' => true]);
    }

    public function clearCart(Request $request, Party $party)
    {
        session()->forget("cart.{$party->id}");

        if ($request->ajax()) {
            $cart = [];
            $cartHtml = view('erp.parties._cart_content', compact('party', 'cart'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared',
                'cartCount' => 0,
                'cartTotal' => '0.00',
                'cartHtml' => $cartHtml
            ]);
        }

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
            'shipping_address_id' => 'required|exists:party_addresses,id',
            'billing_address_id' => 'required|exists:party_addresses,id',
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
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
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

            // Manual activity log
            activity()
                ->performedOn($order)
                ->causedBy(auth()->user())
                ->withProperties(['total_amount' => $order->total_amount])
                ->log('Order placed for customer ' . $party->name);

            return redirect()->route('erp.parties.profile', $party->id)
                ->with('success', 'Order #' . $order->order_number . ' placed successfully!')
                ->with('active_tab', 'v-pills-profile-tab');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function storeAddress(Request $request, Party $party)
    {
        $validated = $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'label' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'village' => 'nullable|string',
            'taluka' => 'nullable|string',
            'district' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'post_office' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable',
        ]);

        $validated['is_default'] = $request->boolean('is_default');

        if ($request->has('is_default') && $request->is_default) {
            $party->addresses()->update(['is_default' => false]);
        }

        // Populate the mandatory 'address' field for compatibility
        $validated['address'] = implode(', ', array_filter([
            $validated['address_line1'],
            $validated['address_line2'],
            $validated['village'],
            $validated['taluka'],
            $validated['district'],
            $validated['state'],
            $validated['pincode']
        ]));

        $party->addresses()->create($validated);

        return redirect()->back()->with('success', 'Address added successfully.')
            ->with('active_tab', $request->input('active_tab'));
    }

    public function updateAddress(Request $request, Party $party, PartyAddress $address)
    {
        $validated = $request->validate([
            'type' => 'required|in:billing,shipping,both',
            'label' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'village' => 'nullable|string',
            'taluka' => 'nullable|string',
            'district' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'post_office' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        if ($request->has('is_default') && $request->is_default) {
            $party->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        } else {
            $validated['is_default'] = false;
        }

        $validated['address'] = implode(', ', array_filter([
            $validated['address_line1'],
            $validated['address_line2'],
            $validated['village'],
            $validated['taluka'],
            $validated['district'],
            $validated['state'],
            $validated['pincode']
        ]));

        $address->update($validated);

        return redirect()->back()->with('success', 'Address updated successfully.')
            ->with('active_tab', $request->input('active_tab'));
    }

    public function destroyAddress(Party $party, PartyAddress $address)
    {
        if ($address->party_id !== $party->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $address->delete();

        return redirect()->back()->with('success', 'Address deleted successfully.');
    }
}
