<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');
        if (empty($q)) {
            return response()->json([]);
        }

        $villages = Village::where('village_name', 'like', "%{$q}%")
            ->orWhere('taluka_name', 'like', "%{$q}%")
            ->orWhere('district_name', 'like', "%{$q}%")
            ->orWhere('pincode', 'like', "{$q}%")
            ->limit(20)
            ->get();

        return response()->json($villages);
    }
}
