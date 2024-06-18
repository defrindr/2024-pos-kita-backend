<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->guard('api')->user();
        // $currentMonth = now()->format('Y-m');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $incomes = Income::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        $expenses = Expense::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        $transactions = Transaction::where('id_user', $user->id)->where('status', 1)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total');

        $totalPemasukanBulanIni = $incomes + $transactions;
        $totalPengeluaranBulanIni = $expenses + 0;
        $totalLabaBulanIni = $totalPemasukanBulanIni - $expenses;

        $transactionsChart = Transaction::where('id_user', $user->id)->where('status', 1)->whereYear('transaction_date', $currentYear)->get();
        $incomesChart = Income::where('id_user', $user->id)->whereYear('date', $currentYear)->orderBy('date', 'desc')->get();
        $expensesChart = Expense::where('id_user', $user->id)->whereYear('date', $currentYear)->orderBy('date', 'desc')->get();

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

        $incomeData = array_fill(0, count($labels), 0);

        foreach ($transactionsChart as $transaction) {
            $transactionMonth = date('F', strtotime($transaction->transaction_date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $incomeData[$index] += $transaction->total;
            }
        }

        foreach ($incomesChart as $income) {
            $incomeMonth = date('F', strtotime($income->date));
            $index = array_search($incomeMonth, $labels);

            if ($index !== false) {
                $incomeData[$index] += $income->nominal;
            }
        }

        $expenseData = array_fill(0, count($labels), 0);

        foreach ($expensesChart as $expense) {
            $expenseMonth = date('F', strtotime($expense->date));
            $index = array_search($expenseMonth, $labels);

            if ($index !== false) {
                $expenseData[$index] += $expense->nominal;
            }
        }


        return response([
            'message' => "success",
            'data' => [
                'totalPemasukanBulanIni'=> $totalPemasukanBulanIni,
                'totalPengeluaranBulanIni'=> $totalPengeluaranBulanIni,
                'totalLabaBulanIni' => $totalLabaBulanIni,
                'incomeChartData' => [
                    'labels' => $labels,
                    'datasets' => $incomeData
                ],
                'expenseChartData' => [
                    'labels' => $labels,
                    'datasets' => $expenseData
                ]
            ]
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
