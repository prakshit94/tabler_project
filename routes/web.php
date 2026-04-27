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
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
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
