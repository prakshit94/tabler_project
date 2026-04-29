<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\IrrigationType;
use App\Models\LandUnit;
use App\Models\AccountType;
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
        $account_types = AccountType::where('is_active', true)->orderBy('name')->get();
        $land_units = LandUnit::where('is_active', true)->orderBy('name')->get();
        $irrigation_types = IrrigationType::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return view('erp.parties._table', compact('parties', 'view'))->render();
        }

        return view('erp.parties.index', compact('parties', 'view', 'crops_master', 'account_types', 'land_units', 'irrigation_types'));
    }

    public function store(Request $request)
    {
        // Only keep required information validations
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'type' => 'required|exists:account_types,slug',
            'category' => 'required|in:individual,business',
            'mobile' => 'required|string|max:20|unique:parties,mobile',
        ]);

        // Merge all other optional fields from the request
        $data = array_merge($request->all(), $validated);

        // Handle crops from master list
        if ($request->filled('crops_master')) {
            $data['crops'] = Crop::whereIn('id', $request->crops_master)->pluck('name')->toArray();
        }

        if ($request->filled('tags_input')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags_input));
        }

        DB::transaction(function() use ($request, $data) {
            $party = Party::create($data);

            if ($request->filled('crops_master')) {
                $party->crops_list()->sync($request->crops_master);
            }

            // Add initial address (Optional)
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
                    'address' => $request->address_line1 ?? $request->village
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
            'type' => 'required|exists:account_types,slug',
            'category' => 'required|in:individual,business',
            'mobile' => 'required|string|max:20|unique:parties,mobile,' . $party->id,
        ]);

        $data = array_merge($request->all(), $validated);

        // Handle crops from master list
        if ($request->filled('crops_master')) {
            $data['crops'] = \App\Models\Crop::whereIn('id', $request->crops_master)->pluck('name')->toArray();
            $party->crops_list()->sync($request->crops_master);
        }

        if ($request->filled('tags_input')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags_input));
        }

        $party->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully');
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
