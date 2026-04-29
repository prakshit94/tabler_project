<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Party;
use App\Models\PartyAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'active');
        $query = Party::query()->with(['addresses' => function($q) {
            $q->where('is_default', true);
        }]);

        if ($view === 'trash') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('party_code', 'like', '%' . $request->search . '%')
                  ->orWhere('gstin', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $parties = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        $crops_master = Crop::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return view('erp.parties._table', compact('parties', 'view'))->render();
        }

        return view('erp.parties.index', compact('parties', 'view', 'crops_master'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'type' => 'required|in:customer,vendor,farmer,buyer,dealer',
            'category' => 'required|in:individual,business',
            'mobile' => 'required|string|max:20|unique:parties,mobile',
            'email' => 'nullable|email|max:255',
            'phone_number_2' => 'nullable|string|max:20',
            'relative_phone' => 'nullable|string|max:20',
            'aadhaar_last4' => 'nullable|string|max:4',
            'gstin' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric',
            'credit_valid_till' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:255',
            'ledger_group' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'land_area' => 'nullable|numeric',
            'land_unit' => 'nullable|string',
            'irrigation_type' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Handle crops from master list
        if ($request->filled('crops_master')) {
            $validated['crops'] = Crop::whereIn('id', $request->crops_master)->pluck('name')->toArray();
        }

        if ($request->filled('tags_input')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags_input));
        }

        DB::transaction(function() use ($request, $validated) {
            $party = Party::create($validated);

            if ($request->filled('crops_master')) {
                $party->crops_list()->sync($request->crops_master);
            }

            // Add initial address
            if ($request->filled('address_line1') || $request->filled('village')) {
                $party->addresses()->create([
                    'type' => 'both',
                    'address_line1' => $request->address_line1,
                    'village' => $request->village,
                    'taluka' => $request->taluka,
                    'district' => $request->district,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                    'is_default' => true,
                    'address' => $request->address_line1
                ]);
            }
        });

        $party = Party::where('mobile', $request->mobile)->first();
        return redirect()->route('erp.parties.profile', $party->id)->with('success', 'Farmer created successfully');
    }

    public function update(Request $request, Party $party)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'type' => 'required|in:customer,vendor,farmer,buyer,dealer',
            'mobile' => 'required|string|max:20|unique:parties,mobile,' . $party->id,
        ]);

        $party->update($validated);
        return redirect()->back()->with('success', 'Farmer updated successfully');
    }

    public function destroy(Party $party)
    {
        $party->delete();
        return redirect()->back()->with('success', 'Farmer moved to trash');
    }

    public function restore($id)
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $party->restore();
        return redirect()->back()->with('success', 'Farmer restored successfully');
    }

    public function forceDelete($id)
    {
        $party = Party::onlyTrashed()->findOrFail($id);
        $party->forceDelete();
        return redirect()->back()->with('success', 'Farmer permanently deleted');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) return redirect()->back()->with('error', 'No items selected');

        switch ($action) {
            case 'delete':
                Party::whereIn('id', $ids)->get()->each->delete();
                $msg = 'Selected records moved to trash';
                break;
            case 'restore':
                Party::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                $msg = 'Selected records restored';
                break;
            case 'force-delete':
                Party::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                $msg = 'Selected records permanently deleted';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $msg);
    }

    public function show(Party $party)
    {
        return redirect()->route('erp.parties.profile', $party->id);
    }

    public function searchByMobile(Request $request)
    {
        $request->validate(['mobile' => 'required|digits:10']);
        $party = Party::where('mobile', $request->mobile)->first();

        if ($party) return redirect()->route('erp.parties.profile', $party->id);

        return redirect()->route('erp.parties.index')->with([
            'open_create_modal' => true,
            'searched_mobile' => $request->mobile,
            'info' => 'Farmer not found with mobile: ' . $request->mobile . '. You can register a new one below.'
        ]);
    }
}
