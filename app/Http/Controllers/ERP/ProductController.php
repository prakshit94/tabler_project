<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HsnCode;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Product::query()
            ->with(['brand', 'category', 'subCategory', 'taxRate', 'images'])
            ->withSum('stocks as stock_count', 'quantity')
            ->withSum('stocks as reserved_count', 'reserved_qty')
            ->withSum('stocks as committed_count', 'committed_qty')
            ->withSum('stocks as in_transit_count', 'in_transit_qty');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();
        
        $brands = Brand::all();
        $categories = Category::all();
        $taxRates = TaxRate::all();
        $hsnCodes = HsnCode::all();

        if ($request->ajax()) {
            return view('erp.products._table', compact('products', 'view'))->render();
        }

        return view('erp.products.index', compact('products', 'brands', 'categories', 'taxRates', 'hsnCodes', 'view'));
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        $taxRates = TaxRate::all();
        $hsnCodes = HsnCode::all();
        return view('erp.products.create', compact('brands', 'categories', 'taxRates', 'hsnCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'hsn_code_id' => 'nullable|exists:hsn_codes,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0
                ]);
            }
        }

        return redirect()->route('erp.products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $brands = Brand::all();
        $categories = Category::all();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $taxRates = TaxRate::all();
        $hsnCodes = HsnCode::all();
        return view('erp.products.edit', compact('product', 'brands', 'categories', 'subCategories', 'taxRates', 'hsnCodes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'hsn_code_id' => 'nullable|exists:hsn_codes,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => !$hasPrimary && $index === 0
                ]);
            }
        }

        return redirect()->route('erp.products.index')->with('success', 'Product updated successfully');
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'subCategory', 'taxRate', 'hsnCode', 'stocks.warehouse']);
        
        if (request()->ajax()) {
            return view('erp.products._show_modal_content', compact('product'))->render();
        }

        return view('erp.products.show', compact('product'));
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $image->delete();
        // If deleted was primary, promote next available image
        if ($wasPrimary) {
            $next = $product->images()->first();
            if ($next) $next->update(['is_primary' => true]);
        }
        return response()->json(['success' => true]);
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product moved to trash');
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->back()->with('success', 'Product restored successfully');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return redirect()->back()->with('success', 'Product permanently deleted');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No items selected');
        }

        switch ($action) {
            case 'delete':
                Product::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected products moved to trash';
                break;
            case 'restore':
                Product::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected products restored';
                break;
            case 'force-delete':
                Product::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected products permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function getSubCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategories = SubCategory::where('category_id', $categoryId)->get();
        return response()->json($subCategories);
    }
}
