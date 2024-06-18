<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Income;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class IncomeDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboardMetrics()
    {
        $user = auth()->guard('api')->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $incomes = Income::where('id_user', $user->id)->orderBy('date', 'desc')->take(4)->get();

        $transactionsChart = Transaction::where('id_user', $user->id)->where('status', 1)->whereYear('transaction_date', $currentYear)->get();
        $incomesChart = Income::where('id_user', $user->id)->whereYear('date', $currentYear)->orderBy('date', 'desc')->get();

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
        $data = array_fill(0, count($labels), 0);

        foreach ($transactionsChart as $transaction) {
            $transactionMonth = date('F', strtotime($transaction->transaction_date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $data[$index] += $transaction->total;
            }
        }

        foreach ($incomesChart as $income) {
            $incomeMonth = date('F', strtotime($income->date));
            $index = array_search($incomeMonth, $labels);

            if ($index !== false) {
                $data[$index] += $income->nominal;
            }
        }

        $totalPemasukan = Income::where('id_user', $user->id)
            ->sum('nominal') +
            Transaction::where('id_user', $user->id)->where('status', 1)->sum('total');

        $totalPemasukanBulanIni = Income::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal') +
            Transaction::where('id_user', $user->id)->where('status', 1)
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('total');

        $currentWeekStartDate = now()->startOfWeek()->format('Y-m-d');
        $currentWeekEndDate = now()->endOfWeek()->format('Y-m-d');

        $collection = collect($incomesChart);

        $filteredIncomes = $collection->filter(function ($income) use ($currentWeekStartDate, $currentWeekEndDate) {
            return $income['date'] >= $currentWeekStartDate && $income['date'] <= $currentWeekEndDate;
        });

        $collection2 = collect($transactionsChart);

        $filteredTransactions = $collection2->filter(function ($transaction) use ($currentWeekStartDate, $currentWeekEndDate) {
            return $transaction['date'] >= $currentWeekStartDate && $transaction['date'] <= $currentWeekEndDate;
        });

        $totalPemasukanMingguIni = $filteredIncomes->sum('nominal') + $filteredTransactions->sum('total');

        return response()->json([
            'message' => 'success',
            'data' => [
                'totalPemasukan'=> $totalPemasukan,
                'totalPemasukanBulanIni'=> $totalPemasukanBulanIni,
                'totalPemasukanMingguIni' => $totalPemasukanMingguIni,
                'incomesData' => $incomes,
                'chartData' => [
                    'labels' => $labels,
                    'datasets' => $data
                ]
            ],
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
}
