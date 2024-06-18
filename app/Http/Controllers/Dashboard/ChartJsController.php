<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChartJsController extends Controller
{
    // Dashboard
    public function pendapatanPerBulanSatuTahun()
    {
        $user = auth()->guard('api')->user();

        $incomes = Transaction::where('id_user', $user->id)->where('status', 1)->get();

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
        $data = array_fill(0, count($labels), 0);

        // Calculate the sum of data by month.
        foreach ($incomes as $income) {
            $transactionMonth = date('F', strtotime($income->transaction_date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $data[$index] += $income->total;
            }
        }

        return response([
            'response' => 'success',
            'data' => [
                'labels' => $labels,
                'datasets' => $data
            ]
        ], 200);
    }

    public function pendapatanPerHariSatuMinggu()
    {
        $user = auth()->guard('api')->user();

        $incomes = Transaction::where('id_user', $user->id)->where('status', 1)->get();

        // Initialize arrays for labels and data with 0 values for each day of the week.
        $labels = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];
        $data = array_fill(0, count($labels), 0);

        // Calculate the sum of data by day of the week.
        foreach ($incomes as $income) {
            $transactionDayOfWeek = date('w', strtotime($income->transaction_date));
            $data[$transactionDayOfWeek] += $income->amount;
        }

        return response([
            'response' => 'success',
            'data' => [
                'labels' => $labels,
                'datasets' => $data
            ]
        ], 200);
    }

    public function pengeluaranPerBulanSatuTahun()
    {
        $user = auth()->guard('api')->user();

        $expenses = Expense::where('id_user', $user->id)->get();

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
        $data = array_fill(0, count($labels), 0);

        // Calculate the sum of data by month.
        foreach ($expenses as $expense) {
            $transactionMonth = date('F', strtotime($expense->date));
            $index = array_search($transactionMonth, $labels);

            if ($index !== false) {
                $data[$index] += $expense->nominal;
            }
        }

        return response([
            'response' => 'success',
            'data' => [
                'labels' => $labels,
                'datasets' => $data
            ]
        ], 200);
    }

    public function peningkatanPesananPerBulanSatuTahun()
    {
        $user = auth()->guard('api')->user();

        $incomes = Transaction::where('id_user', $user->id)->where('status', 1)->get();

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

        // Calculate the number of orders per month.
        foreach ($incomes as $income) {
            $transactionMonth = date('n', strtotime($income->transaction_date));
            $data[$transactionMonth - 1]++;
        }

        return response([
            'response' => 'success',
            'data' => [
                'labels' => $labels,
                'datasets' => $data
            ]
        ], 200);
    }
}
