<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebAuthController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () { return redirect()->route('dashboard'); });

    // Access Control Management (Admin only)
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::post('users/{user}/restore', [\App\Http\Controllers\Web\UserWebController::class, 'restore'])->name('users.restore');
        Route::delete('users/{user}/force-delete', [\App\Http\Controllers\Web\UserWebController::class, 'forceDelete'])->name('users.force-delete');
        Route::post('users/bulk-action', [\App\Http\Controllers\Web\UserWebController::class, 'bulkAction'])->name('users.bulk-action');
        Route::resource('users', \App\Http\Controllers\Web\UserWebController::class);

        // Roles
        Route::post('roles/{role}/restore', [\App\Http\Controllers\Web\RoleWebController::class, 'restore'])->name('roles.restore');
        Route::delete('roles/{role}/force-delete', [\App\Http\Controllers\Web\RoleWebController::class, 'forceDelete'])->name('roles.force-delete');
        Route::post('roles/bulk-action', [\App\Http\Controllers\Web\RoleWebController::class, 'bulkAction'])->name('roles.bulk-action');
        Route::resource('roles', \App\Http\Controllers\Web\RoleWebController::class);

        // Permissions
        Route::post('permissions/{permission}/restore', [\App\Http\Controllers\Web\PermissionWebController::class, 'restore'])->name('permissions.restore');
        Route::delete('permissions/{permission}/force-delete', [\App\Http\Controllers\Web\PermissionWebController::class, 'forceDelete'])->name('permissions.force-delete');
        Route::post('permissions/bulk-action', [\App\Http\Controllers\Web\PermissionWebController::class, 'bulkAction'])->name('permissions.bulk-action');
        Route::resource('permissions', \App\Http\Controllers\Web\PermissionWebController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // ERP Management
    Route::prefix('erp')->name('erp.')->group(function () {
        // Brands
        Route::patch('brands/{brand}/restore', [\App\Http\Controllers\ERP\BrandController::class, 'restore'])->name('brands.restore');
        Route::delete('brands/{brand}/force-delete', [\App\Http\Controllers\ERP\BrandController::class, 'forceDelete'])->name('brands.force-delete');
        Route::post('brands/bulk-action', [\App\Http\Controllers\ERP\BrandController::class, 'bulkAction'])->name('brands.bulk-action');
        Route::resource('brands', \App\Http\Controllers\ERP\BrandController::class);

        // Categories
        Route::patch('categories/{category}/restore', [\App\Http\Controllers\ERP\CategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('categories/{category}/force-delete', [\App\Http\Controllers\ERP\CategoryController::class, 'forceDelete'])->name('categories.force-delete');
        Route::post('categories/bulk-action', [\App\Http\Controllers\ERP\CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
        Route::resource('categories', \App\Http\Controllers\ERP\CategoryController::class);

        // Sub-Categories
        Route::patch('sub-categories/{sub_category}/restore', [\App\Http\Controllers\ERP\SubCategoryController::class, 'restore'])->name('sub-categories.restore');
        Route::delete('sub-categories/{sub_category}/force-delete', [\App\Http\Controllers\ERP\SubCategoryController::class, 'forceDelete'])->name('sub-categories.force-delete');
        Route::post('sub-categories/bulk-action', [\App\Http\Controllers\ERP\SubCategoryController::class, 'bulkAction'])->name('sub-categories.bulk-action');
        Route::resource('sub-categories', \App\Http\Controllers\ERP\SubCategoryController::class);

        // Tax Rates
        Route::patch('tax-rates/{tax_rate}/restore', [\App\Http\Controllers\ERP\TaxRateController::class, 'restore'])->name('tax-rates.restore');
        Route::delete('tax-rates/{tax_rate}/force-delete', [\App\Http\Controllers\ERP\TaxRateController::class, 'forceDelete'])->name('tax-rates.force-delete');
        Route::post('tax-rates/bulk-action', [\App\Http\Controllers\ERP\TaxRateController::class, 'bulkAction'])->name('tax-rates.bulk-action');
        Route::resource('tax-rates', \App\Http\Controllers\ERP\TaxRateController::class);

        // HSN Codes
        Route::patch('hsn-codes/{hsn_code}/restore', [\App\Http\Controllers\ERP\HsnCodeController::class, 'restore'])->name('hsn-codes.restore');
        Route::delete('hsn-codes/{hsn_code}/force-delete', [\App\Http\Controllers\ERP\HsnCodeController::class, 'forceDelete'])->name('hsn-codes.force-delete');
        Route::post('hsn-codes/bulk-action', [\App\Http\Controllers\ERP\HsnCodeController::class, 'bulkAction'])->name('hsn-codes.bulk-action');
        Route::resource('hsn-codes', \App\Http\Controllers\ERP\HsnCodeController::class);

        // Warehouses
        Route::patch('warehouses/{warehouse}/restore', [\App\Http\Controllers\ERP\WarehouseController::class, 'restore'])->name('warehouses.restore');
        Route::delete('warehouses/{warehouse}/force-delete', [\App\Http\Controllers\ERP\WarehouseController::class, 'forceDelete'])->name('warehouses.force-delete');
        Route::post('warehouses/bulk-action', [\App\Http\Controllers\ERP\WarehouseController::class, 'bulkAction'])->name('warehouses.bulk-action');
        Route::resource('warehouses', \App\Http\Controllers\ERP\WarehouseController::class);

        // Crops
        Route::patch('crops/{crop}/restore', [\App\Http\Controllers\ERP\CropController::class, 'restore'])->name('crops.restore');
        Route::delete('crops/{crop}/force-delete', [\App\Http\Controllers\ERP\CropController::class, 'forceDelete'])->name('crops.force-delete');
        Route::post('crops/bulk-action', [\App\Http\Controllers\ERP\CropController::class, 'bulkAction'])->name('crops.bulk-action');
        Route::resource('crops', \App\Http\Controllers\ERP\CropController::class);

        // Irrigation Types
        Route::patch('irrigation-types/{irrigation_type}/restore', [\App\Http\Controllers\ERP\IrrigationTypeController::class, 'restore'])->name('irrigation-types.restore');
        Route::delete('irrigation-types/{irrigation_type}/force-delete', [\App\Http\Controllers\ERP\IrrigationTypeController::class, 'forceDelete'])->name('irrigation-types.force-delete');
        Route::post('irrigation-types/bulk-action', [\App\Http\Controllers\ERP\IrrigationTypeController::class, 'bulkAction'])->name('irrigation-types.bulk-action');
        Route::resource('irrigation-types', \App\Http\Controllers\ERP\IrrigationTypeController::class);

        // Land Units
        Route::patch('land-units/{land_unit}/restore', [\App\Http\Controllers\ERP\LandUnitController::class, 'restore'])->name('land-units.restore');
        Route::delete('land-units/{land_unit}/force-delete', [\App\Http\Controllers\ERP\LandUnitController::class, 'forceDelete'])->name('land-units.force-delete');
        Route::post('land-units/bulk-action', [\App\Http\Controllers\ERP\LandUnitController::class, 'bulkAction'])->name('land-units.bulk-action');
        Route::resource('land-units', \App\Http\Controllers\ERP\LandUnitController::class);

        // Account Types
        Route::patch('account-types/{account_type}/restore', [\App\Http\Controllers\ERP\AccountTypeController::class, 'restore'])->name('account-types.restore');
        Route::delete('account-types/{account_type}/force-delete', [\App\Http\Controllers\ERP\AccountTypeController::class, 'forceDelete'])->name('account-types.force-delete');
        Route::post('account-types/bulk-action', [\App\Http\Controllers\ERP\AccountTypeController::class, 'bulkAction'])->name('account-types.bulk-action');
        Route::resource('account-types', \App\Http\Controllers\ERP\AccountTypeController::class);

        // Parties
        Route::patch('parties/{party}/restore', [\App\Http\Controllers\ERP\PartyController::class, 'restore'])->name('parties.restore');
        Route::delete('parties/{party}/force-delete', [\App\Http\Controllers\ERP\PartyController::class, 'forceDelete'])->name('parties.force-delete');
        Route::post('parties/bulk-action', [\App\Http\Controllers\ERP\PartyController::class, 'bulkAction'])->name('parties.bulk-action');
        Route::get('parties/search-by-mobile', [\App\Http\Controllers\ERP\PartyController::class, 'searchByMobile'])->name('parties.search-by-mobile');
        Route::resource('parties', \App\Http\Controllers\ERP\PartyController::class);

        // Customer Profile & Ordering
        Route::get('parties/{party}/profile', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'show'])->name('parties.profile');
        Route::post('parties/{party}/cart', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'addToCart'])->name('parties.cart.add');
        Route::delete('parties/{party}/cart-clear', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'clearCart'])->name('parties.cart.clear');
        Route::delete('parties/{party}/cart/{product}', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'removeFromCart'])->name('parties.cart.remove');
        Route::patch('parties/{party}/cart/{product}', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'updateCart'])->name('parties.cart.update');
        Route::post('parties/{party}/place-order', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'placeOrder'])->name('parties.place-order');
        Route::post('parties/{party}/addresses', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'storeAddress'])->name('parties.addresses.store');
        Route::put('parties/{party}/addresses/{address}', [\App\Http\Controllers\ERP\CustomerProfileController::class, 'updateAddress'])->name('parties.addresses.update');

        // Villages
        Route::get('villages/search', [\App\Http\Controllers\ERP\VillageController::class, 'search'])->name('villages.search');

        // Products
        Route::get('products/get-subcategories', [\App\Http\Controllers\ERP\ProductController::class, 'getSubCategories'])->name('products.get-subcategories');
        Route::patch('products/{product}/restore', [\App\Http\Controllers\ERP\ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{product}/force-delete', [\App\Http\Controllers\ERP\ProductController::class, 'forceDelete'])->name('products.force-delete');
        Route::post('products/bulk-action', [\App\Http\Controllers\ERP\ProductController::class, 'bulkAction'])->name('products.bulk-action');
        Route::resource('products', \App\Http\Controllers\ERP\ProductController::class);

        // Inventory
        Route::get('inventory', [\App\Http\Controllers\ERP\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/movements', [\App\Http\Controllers\ERP\InventoryController::class, 'movements'])->name('inventory.movements');
        Route::post('inventory/adjust', [\App\Http\Controllers\ERP\InventoryController::class, 'adjustStock'])->name('inventory.adjust');

        // Orders
        Route::patch('orders/{order}/status', [\App\Http\Controllers\ERP\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('orders', \App\Http\Controllers\ERP\OrderController::class);

        // Invoices
        Route::resource('invoices', \App\Http\Controllers\ERP\InvoiceController::class);

        // Payments
        Route::resource('payments', \App\Http\Controllers\ERP\PaymentController::class);

        // Stock Transfers
        Route::resource('stock-transfers', \App\Http\Controllers\ERP\StockTransferController::class);

        // Returns
        Route::resource('returns', \App\Http\Controllers\ERP\ReturnController::class);

        // Ledgers
        Route::get('ledgers', [\App\Http\Controllers\ERP\LedgerController::class, 'index'])->name('ledgers.index');

        // Sync Logs
        Route::get('sync-logs', [\App\Http\Controllers\ERP\SyncLogController::class, 'index'])->name('sync-logs.index');

        // Product Prices
        Route::get('product-prices', [\App\Http\Controllers\ERP\ProductPriceController::class, 'index'])->name('product-prices.index');
        Route::post('product-prices', [\App\Http\Controllers\ERP\ProductPriceController::class, 'store'])->name('product-prices.store');
    });

    // Tabler Template Pages Routes
Route::get('/2-step-verification-code', function () { return view('pages.2-step-verification-code'); });
Route::get('/2-step-verification', function () { return view('pages.2-step-verification'); });
Route::get('/accordion', function () { return view('pages.accordion'); });
Route::get('/activity', function () { return view('pages.activity'); });
Route::get('/alerts', function () { return view('pages.alerts'); });
Route::get('/all-elements', function () { return view('pages.all-elements'); });
Route::get('/auth-lock', function () { return view('pages.auth-lock'); });
Route::get('/avatars', function () { return view('pages.avatars'); });
Route::get('/badges', function () { return view('pages.badges'); });
Route::get('/blank', function () { return view('pages.blank'); });
Route::get('/buttons', function () { return view('pages.buttons'); });
Route::get('/card-actions', function () { return view('pages.card-actions'); });
Route::get('/card-gradients', function () { return view('pages.card-gradients'); });
Route::get('/cards-masonry', function () { return view('pages.cards-masonry'); });
Route::get('/cards', function () { return view('pages.cards'); });
Route::get('/carousel', function () { return view('pages.carousel'); });
Route::get('/changelog', function () { return view('pages.changelog'); });
Route::get('/charts', function () { return view('pages.charts'); });
Route::get('/chat', function () { return view('pages.chat'); });
Route::get('/colorpicker', function () { return view('pages.colorpicker'); });
Route::get('/colors', function () { return view('pages.colors'); });
Route::get('/cookie-banner', function () { return view('pages.cookie-banner'); });
Route::get('/dashboard-crypto', function () { return view('pages.dashboard-crypto'); });
Route::get('/datagrid', function () { return view('pages.datagrid'); });
Route::get('/datatables', function () { return view('pages.datatables'); });
Route::get('/dropdowns', function () { return view('pages.dropdowns'); });
Route::get('/dropzone', function () { return view('pages.dropzone'); });
Route::get('/email-inbox', function () { return view('pages.email-inbox'); });
Route::get('/emails', function () { return view('pages.emails'); });
Route::get('/empty', function () { return view('pages.empty'); });
Route::get('/error-404', function () { return view('pages.error-404'); });
Route::get('/error-500', function () { return view('pages.error-500'); });
Route::get('/error-maintenance', function () { return view('pages.error-maintenance'); });
Route::get('/faq', function () { return view('pages.faq'); });
Route::get('/flags', function () { return view('pages.flags'); });
Route::get('/forgot-password', function () { return view('pages.forgot-password'); });
Route::get('/form-elements', function () { return view('pages.form-elements'); });
Route::get('/form-layout', function () { return view('pages.form-layout'); });
Route::get('/fullcalendar', function () { return view('pages.fullcalendar'); });
Route::get('/gallery', function () { return view('pages.gallery'); });
Route::get('/icons', function () { return view('pages.icons'); });
Route::get('/illustrations', function () { return view('pages.illustrations'); });

Route::get('/inline-player', function () { return view('pages.inline-player'); });
Route::get('/invoice', function () { return view('pages.invoice'); });
Route::get('/job-listing', function () { return view('pages.job-listing'); });
Route::get('/layout-boxed', function () { return view('pages.layout-boxed'); });
Route::get('/layout-combo', function () { return view('pages.layout-combo'); });
Route::get('/layout-condensed', function () { return view('pages.layout-condensed'); });
Route::get('/layout-fluid-vertical', function () { return view('pages.layout-fluid-vertical'); });
Route::get('/layout-fluid', function () { return view('pages.layout-fluid'); });
Route::get('/layout-horizontal', function () { return view('pages.layout-horizontal'); });
Route::get('/layout-navbar-dark', function () { return view('pages.layout-navbar-dark'); });
Route::get('/layout-navbar-overlap', function () { return view('pages.layout-navbar-overlap'); });
Route::get('/layout-navbar-sticky', function () { return view('pages.layout-navbar-sticky'); });
Route::get('/layout-rtl', function () { return view('pages.layout-rtl'); });
Route::get('/layout-vertical-right', function () { return view('pages.layout-vertical-right'); });
Route::get('/layout-vertical-transparent', function () { return view('pages.layout-vertical-transparent'); });
Route::get('/layout-vertical', function () { return view('pages.layout-vertical'); });
Route::get('/license', function () { return view('pages.license'); });
Route::get('/lightbox', function () { return view('pages.lightbox'); });
Route::get('/lists', function () { return view('pages.lists'); });
Route::get('/logs', function () { return view('pages.logs'); });
Route::get('/map-fullsize', function () { return view('pages.map-fullsize'); });
Route::get('/maps-vector', function () { return view('pages.maps-vector'); });
Route::get('/maps', function () { return view('pages.maps'); });
Route::get('/markdown', function () { return view('pages.markdown'); });
Route::get('/modals', function () { return view('pages.modals'); });
Route::get('/music', function () { return view('pages.music'); });
Route::get('/navigation', function () { return view('pages.navigation'); });
Route::get('/offcanvas', function () { return view('pages.offcanvas'); });
Route::get('/onboarding', function () { return view('pages.onboarding'); });
Route::get('/page-loader', function () { return view('pages.page-loader'); });
Route::get('/pagination', function () { return view('pages.pagination'); });
Route::get('/patterns', function () { return view('pages.patterns'); });
Route::get('/pay', function () { return view('pages.pay'); });
Route::get('/payment-providers', function () { return view('pages.payment-providers'); });
Route::get('/photogrid', function () { return view('pages.photogrid'); });
Route::get('/placeholder', function () { return view('pages.placeholder'); });
Route::get('/pricing-table', function () { return view('pages.pricing-table'); });
Route::get('/pricing', function () { return view('pages.pricing'); });
Route::get('/profile', function () { return view('pages.profile'); });
Route::get('/progress', function () { return view('pages.progress'); });
Route::get('/prose', function () { return view('pages.prose'); });
Route::get('/scroll-spy', function () { return view('pages.scroll-spy'); });
Route::get('/search-results', function () { return view('pages.search-results'); });
Route::get('/segmented-control', function () { return view('pages.segmented-control'); });
Route::get('/settings-plan', function () { return view('pages.settings-plan'); });
Route::get('/settings', function () { return view('pages.settings'); });
Route::get('/sign-in-cover', function () { return view('pages.sign-in-cover'); });
Route::get('/sign-in-illustration', function () { return view('pages.sign-in-illustration'); });
Route::get('/sign-in-link', function () { return view('pages.sign-in-link'); });
Route::get('/sign-in', function () { return view('pages.sign-in'); });
Route::get('/sign-up', function () { return view('pages.sign-up'); });
Route::get('/signatures', function () { return view('pages.signatures'); });
Route::get('/social-icons', function () { return view('pages.social-icons'); });
Route::get('/sortable', function () { return view('pages.sortable'); });
Route::get('/stars-rating', function () { return view('pages.stars-rating'); });
Route::get('/steps', function () { return view('pages.steps'); });
Route::get('/tables', function () { return view('pages.tables'); });
Route::get('/tabs', function () { return view('pages.tabs'); });
Route::get('/tags', function () { return view('pages.tags'); });
Route::get('/tasks-list', function () { return view('pages.tasks-list'); });
Route::get('/tasks', function () { return view('pages.tasks'); });
Route::get('/terms-of-service', function () { return view('pages.terms-of-service'); });
Route::get('/text-features', function () { return view('pages.text-features'); });
Route::get('/toasts', function () { return view('pages.toasts'); });
Route::get('/tour', function () { return view('pages.tour'); });
Route::get('/trial-ended', function () { return view('pages.trial-ended'); });
Route::get('/turbo-loader', function () { return view('pages.turbo-loader'); });
Route::get('/typography', function () { return view('pages.typography'); });
Route::get('/uptime', function () { return view('pages.uptime'); });
Route::get('/users', function () { return view('pages.users'); });
Route::get('/widgets', function () { return view('pages.widgets'); });
Route::get('/wizard', function () { return view('pages.wizard'); });
Route::get('/wysiwyg', function () { return view('pages.wysiwyg'); });

});
