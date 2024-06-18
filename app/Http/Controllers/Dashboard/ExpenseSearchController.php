<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Request $request)
    {
        $user = auth()->guard('api')->user();

        $bulan = $request->input('bulan');

        // Map month names to their corresponding numeric values
        $monthMap = [
            'Januari' => '01',
            'Februari' => '02',
            'Maret' => '03',
            'April' => '04',
            'Mei' => '05',
            'Juni' => '06',
            'Juli' => '07',
            'Agustus' => '08',
            'September' => '09',
            'Oktober' => '10',
            'November' => '11',
            'Desember' => '12',
        ];

        // Check if the provided month exists in the mapping
        if (array_key_exists($bulan, $monthMap)) {

            $bulanNumeric = $monthMap[$bulan];

            // Use the numeric month value in the query
            $expenses = Expense::where('id_user', $user->id)
                ->whereRaw('MONTH(date) = ?', [$bulanNumeric])
                ->orderBy('date', 'desc')
                ->get();

            return response([
                'message' => 'success',
                'data' => $expenses,
            ], 200);
        } else {
            return response([
                'message' => 'Invalid month name',
                'data' => [],
            ], 400);
        }
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
