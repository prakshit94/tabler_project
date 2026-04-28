<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use App\Models\Party;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $parties = Party::all();
        $partyId = $request->party_id;
        
        $query = LedgerEntry::query();
        if ($partyId) {
            $query->where('party_id', $partyId);
        }

        $entries = $query->with('party')->latest()->paginate(20)->withQueryString();
        
        $balance = 0;
        if ($partyId) {
            $balance = LedgerEntry::where('party_id', $partyId)->sum(\DB::raw("CASE WHEN type = 'credit' THEN amount ELSE -amount END"));
        }

        return view('erp.ledgers.index', compact('entries', 'parties', 'partyId', 'balance'));
    }
}
