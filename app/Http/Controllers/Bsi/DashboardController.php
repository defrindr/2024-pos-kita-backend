<?php

namespace App\Http\Controllers\Bsi;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboardMetrics()
    {

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $umkmsData = User::where('id_role', 2)->get();

        $totalUMKM = $umkmsData->count();

        $lastMonthTotalUMKMData =  User::where('id_role', 2)
            ->whereMonth('created_at', "<", $currentMonth)
            ->whereYear('created_at', "<=", $currentYear)
            ->get();

        $lastMonthTotalUMKMCount = $lastMonthTotalUMKMData->count();

        $percentageChangeTotalUMKM = 0;

        if ($lastMonthTotalUMKMCount != 0) {
            $percentageChangeTotalUMKM = (($totalUMKM - $lastMonthTotalUMKMCount) / $lastMonthTotalUMKMCount) * 100;
        }

        $umkmLicensed = $umkmsData->where('evaluation.is_licensed', 1)->count();

        $lastMonthUMKMLicensedData =  $lastMonthTotalUMKMData->where('evaluation.is_licensed', 1);

        $lastMonthUMKMLicensedCount = $lastMonthUMKMLicensedData->count();

        $percentageChangeUMKMLicensed = 0;

        if ($lastMonthUMKMLicensedCount != 0) {
            $percentageChangeUMKMLicensed = (($umkmLicensed - $lastMonthUMKMLicensedCount) / $lastMonthUMKMLicensedCount) * 100;
        }

        $UMKMNotInGroup = User::where('id_role', 2)->doesntHave('groups')->count();

        $lastMonthUMKMNotInGroupData = User::where('id_role', 2)
            ->whereMonth('created_at', "<", $currentMonth)
            ->whereYear('created_at', "<=", $currentYear)
            ->doesntHave('groups')->get();

        $lastMonthUMKMNotInGroupCount = $lastMonthUMKMNotInGroupData->count();

        $percentageChangeUMKMNotInGroup = 0;

        if ($lastMonthUMKMNotInGroupCount != 0) {
            $percentageChangeUMKMNotInGroup = (($UMKMNotInGroup - $lastMonthUMKMNotInGroupCount) / $lastMonthUMKMNotInGroupCount) * 100;
        }


        return response([
            'message' => 'success',
            'data' => [
                'totalUMKM' => [
                    'current_month_total_UMKM' => $totalUMKM,
                    'last_month_total_UMKM' => $lastMonthTotalUMKMCount,
                    'percentage_change' => $percentageChangeTotalUMKM

                ],
                'licencedUMKM' => [
                    'current_month_total_UMKM' => $umkmLicensed,
                    'last_month_total_UMKM' => $lastMonthUMKMLicensedCount,
                    'percentage_change' => $percentageChangeUMKMLicensed
                ],
                'UMKMNotInGroup' => [
                    'current_month_UMKM_not_in_group' => $UMKMNotInGroup,
                    'last_month_UMKM_not_in_group' => $lastMonthUMKMNotInGroupCount,
                    'percentage_change' => $percentageChangeUMKMNotInGroup
                ]
            ],
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
