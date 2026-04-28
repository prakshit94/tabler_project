<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\PartyAddress;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Party::query()->with('addresses');

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('gstin', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $parties = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        if ($request->ajax()) {
            return view('erp.parties._table', compact('parties', 'view'))->render();
        }

        return view('erp.parties.index', compact('parties', 'view'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:customer,vendor',
            'gstin' => 'nullable|string|max:20',
            'state' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $party = Party::create($validated);

        // Optional: Add initial address if provided
        if ($request->filled('address')) {
            $party->addresses()->create([
                'type' => 'billing',
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
            ]);
        }

        return redirect()->back()->with('success', 'Party created successfully');
    }

    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:customer,vendor',
            'gstin' => 'nullable|string|max:20',
            'state' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $party->update($validated);

        return redirect()->back()->with('success', 'Party updated successfully');
    }

    public function destroy(Party $party)
    {
        $party->delete();
        return redirect()->back()->with('success', 'Party moved to trash');
    }

    public function restore($id)
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $party->restore();
        return redirect()->back()->with('success', 'Party restored successfully');
    }

    public function forceDelete($id)
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $party->forceDelete();
        return redirect()->back()->with('success', 'Party permanently deleted');
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
                Party::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected parties moved to trash';
                break;
            case 'restore':
                Party::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected parties restored';
                break;
            case 'force-delete':
                Party::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected parties permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }
}
