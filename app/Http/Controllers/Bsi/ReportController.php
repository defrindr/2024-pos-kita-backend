<?php

namespace App\Http\Controllers\Bsi;

use App\Models\User;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $umkms = User::where('id_role', 2)->orderBy('updated_at', 'desc')->get();

        // $umkms->load('incomes');

        $umkms->each(function ($umkm) {
            $umkm->incomesTotal = $umkm->incomes->sum('total');
            $umkm->expensesTotal = $umkm->expenses->sum('nominal');

            $lastIncome = $umkm->incomes->max('created_at'); // Get the max updated_at date for incomes
            $lastExpense = $umkm->expenses->max('date');

            if ($lastIncome > $lastExpense) {
                $umkm->lastTransaction = $lastIncome;
            } else {
                $umkm->lastTransaction = $lastIncome;
            }
            $umkm->unsetRelation('incomes');
            $umkm->unsetRelation('expenses');
        });

        return response([
            'message' => "success",
            'data' => $umkms
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
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $umkm = User::where('id_role', 2)->where('id', $id)->with(['incomes', 'expenses', 'incomes.transactionItems'])->first();

        if (!$umkm) {
            return response([
                'message' => "There is no UMKM with that id",
                'data' => null
            ], 400);
        }

        // Group incomes and expenses by date
        $groupedIncomes = $umkm->incomes->groupBy(function ($item) {
            return $item->transaction_date;
        });

        $groupedExpenses = $umkm->expenses->groupBy(function ($item) {
            return $item->date;
        });

        $uniqueDates = collect(array_merge($groupedIncomes->keys()->toArray(), $groupedExpenses->keys()->toArray()))->unique();

        $sortedDates = $uniqueDates->sortByDesc(function ($date) {
            return strtotime($date);
        });

        $combinedData = $sortedDates->map(function ($date) use ($groupedIncomes, $groupedExpenses, $umkm) {
            return [
                'date' => $date,
                'sold' => $umkm->incomes->where('transaction_date', $date)->flatMap->transactionItems->sum('quantity'),
                'order' => $groupedIncomes->has($date) ? $groupedIncomes[$date]->count() : 0,
                'income' => $groupedIncomes->has($date) ? $groupedIncomes[$date]->sum('total') : 0,
                'expense' => $groupedExpenses->has($date) ? $groupedExpenses[$date]->sum('nominal') : 0,
                'net_profit' => ($groupedIncomes->has($date) ? $groupedIncomes[$date]->sum('total') : 0) - ($groupedExpenses->has($date) ? $groupedExpenses[$date]->sum('nominal') : 0),
            ];
        });

        $umkm->combinedData = $combinedData->values();

        $umkm->makeHidden(['incomesTotal', 'expensesTotal', 'incomes', 'expenses', 'profile_photo_url']);

        // Next

        $incomes = Transaction::where('id_user', $id)->whereYear('transaction_date', "=", $currentYear)->where('status', 1)->get();
        $expenses = Expense::where('id_user', $id)->whereYear('created_at', "=", $currentYear)->get();

        // Initialize arrays for labels and data with 0 values for each month.
        $labels = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
        $dataIncomes = array_fill(0, count($labels), 0);

        // Calculate the sum of data by month.
        foreach ($incomes as $income) {
            $transactionMonth = date('F', strtotime($income->transaction_date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $dataIncomes[$index] += $income->total;
            }
        }

        $dataExpenses = array_fill(0, count($labels), 0);

        foreach ($expenses as $expense) {
            $transactionMonth = date('F', strtotime($expense->date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $dataExpenses[$index] += $expense->nominal;
            }
        }


        return response([
            'message' => "success",
            'data' => [
                'UMKM' => $umkm,
                'pendapatanPerBulanSatuTahun' => [
                    'labels' => $labels,
                    'datasets' => $dataIncomes
                ],
                'pengeluaranPerBulanSatuTahun' => [
                    'labels' => $labels,
                    'datasets' => $dataExpenses
                ]
            ]
        ], 200);
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
