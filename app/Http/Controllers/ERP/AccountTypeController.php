<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountTypeController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = AccountType::query();

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $account_types = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.account_types._table', compact('account_types', 'view'))->render();
        }

        return view('erp.account_types.index', compact('account_types', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_types,name',
            'color_class' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        AccountType::create($validated);

        return redirect()->back()->with('success', 'Account Type created successfully');
    }

    public function update(Request $request, AccountType $accountType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_types,name,' . $accountType->id,
            'color_class' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $accountType->update($validated);

        return redirect()->back()->with('success', 'Account Type updated successfully');
    }

    public function destroy(AccountType $accountType)
    {
        $accountType->delete();
        return redirect()->back()->with('success', 'Account Type moved to trash');
    }

    public function restore($id)
    {
        $accountType = AccountType::onlyTrashed()->findOrFail($id);
        $accountType->restore();
        return redirect()->back()->with('success', 'Account Type restored successfully');
    }

    public function forceDelete($id)
    {
        $accountType = AccountType::onlyTrashed()->findOrFail($id);
        $accountType->forceDelete();
        return redirect()->back()->with('success', 'Account Type permanently deleted');
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
                AccountType::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected items moved to trash';
                break;
            case 'restore':
                AccountType::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected items restored';
                break;
            case 'force-delete':
                AccountType::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected items permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
