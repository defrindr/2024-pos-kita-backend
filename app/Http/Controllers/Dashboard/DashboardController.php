<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pengeluaran()
    {
        $user = auth()->guard('api')->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $currentMonthExpenses = Expense::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        $lastMonthExpenses = Expense::where('id_user', $user->id)
            ->whereMonth('date', $lastMonth)
            ->whereYear('date', $lastYear)
            ->sum('nominal');

        $percentageChange = 0;

        if ($lastMonthExpenses != 0) {
            $percentageChange = (($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100;
        }

        return response([
            'message' => "success",
            'data' => [
                'current_month_expenses' => $currentMonthExpenses,
                'last_month_expenses' => $lastMonthExpenses,
                'percentage_change' => $percentageChange
            ]
        ], 200);
    }


    public function labaBersih()
    {
        $user = auth()->guard('api')->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Menghitung laba bersih bulan ini
        $totalPendapatanBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total');

        $totalPengeluaranBulanIni = Expense::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        $labaBersihBulanIni = $totalPendapatanBulanIni - $totalPengeluaranBulanIni;

        // Menghitung laba bersih bulan kemarin
        $totalPendapatanBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->sum('total');

        $totalPengeluaranBulanKemarin = Expense::where('id_user', $user->id)
            ->whereMonth('date', $lastMonth)
            ->whereYear('date', $lastYear)
            ->sum('nominal');

        $labaBersihBulanKemarin = $totalPendapatanBulanKemarin - $totalPengeluaranBulanKemarin;

        $percentageChange = 0;

        if ($labaBersihBulanKemarin != 0) {
            $percentageChange = (($labaBersihBulanIni - $labaBersihBulanKemarin) / $labaBersihBulanKemarin) * 100;
        }

        return response([
            'message' => "success",
            'data' => [
                'laba_bersih_bulan_ini' => $labaBersihBulanIni,
                'laba_bersih_bulan_kemarin' => $labaBersihBulanKemarin,
                'percentage_change' => $percentageChange,
            ]
        ], 200);
    }

    public function pesananBaru()
    {
        $user = auth()->guard('api')->user();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $totalPesananBaruBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->count();

        $totalPesananBaruBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastYear)
            ->count();

        $percentageChange = 0;

        if ($totalPesananBaruBulanKemarin != 0) {
            $percentageChange = (($totalPesananBaruBulanIni - $totalPesananBaruBulanKemarin) / $totalPesananBaruBulanKemarin) * 100;
        }

        return response([
            'message' => "success",
            'data' => [
                'total_pesanan_baru_bulan_ini' => $totalPesananBaruBulanIni,
                'total_pesanan_baru_bulan_kemarin' => $totalPesananBaruBulanKemarin,
                'persentage_change' => $percentageChange
            ]
        ], 200);
    }

    public function barangTerjual()
    {
        $user = auth()->guard('api')->user();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        $totalTerjualBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->with('transactionItems')
            ->get()
            ->pluck('transactionItems')
            ->flatten()
            ->sum('quantity');

        $totalTerjualBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastYear)
            ->with('transactionItems')
            ->get()
            ->pluck('transactionItems')
            ->flatten()
            ->sum('quantity');

        $percentageChange = 0;

        if ($totalTerjualBulanKemarin != 0) {
            $percentageChange = (($totalTerjualBulanIni - $totalTerjualBulanKemarin) / $totalTerjualBulanKemarin) * 100;
        }

        return response([
            'message' => "success",
            'data' => [
                'total_terjual_barang_bulan_ini' => $totalTerjualBulanIni,
                'total_terjual_barang_bulan_kemarin' => $totalTerjualBulanKemarin,
                'percentage_change' => $percentageChange
            ]
        ], 200);
    }


    public function itemTerpopuler()
    {
        $user = auth()->guard('api')->user();

        $transactions = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->with('transactionItems.product') // Eager load product details
            ->get();

        // Flatten the transaction items into a single collection
        $transactionItems = $transactions->pluck('transactionItems')->flatten();

        // Group transaction items by the product ID and sum the quantities
        $popularItems = $transactionItems->groupBy('id_product')
            ->map(function ($items, $idProduct) {
                $totalQuantity = $items->sum('quantity');
                $product = $items->first()->product; // Get product details
                return [
                    'id_product' => $idProduct,
                    'product' => $product, // Include product details
                    'total_quantity' => $totalQuantity,
                ];
            });

        // Sort the items by total quantity in descending order to get the most popular items
        $popularItems = $popularItems->sortByDesc('total_quantity');

        return response([
            'message' => 'success',
            'data' => $popularItems->values(), // Re-index the keys for the response
        ], 200);
    }

    public function sortProductByStock(Request $request)
    {
        $user = auth()->guard('api')->user();

        $products = Product::where('id_user', $user->id)->orderBy('stock', $request->sortBy)->take(8)->get(); //'desc'
        return response([
            'message' => "success",
            'data' => $products
        ], 200);
    }

    public function dashboardMetrics()
    {
        $user = auth()->guard('api')->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Calculate current month expenses
        $currentMonthExpenses = Expense::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        // Calculate last month expenses
        $lastMonthExpenses = Expense::where('id_user', $user->id)
            ->whereMonth('date', $lastMonth)
            ->whereYear('date', $lastYear)
            ->sum('nominal');

        $percentageChangeExpenses = 0;

        if ($lastMonthExpenses != 0) {
            $percentageChangeExpenses = (($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100;
        }

        // Calculate laba bersih for current month
        $totalPendapatanBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total');

        $totalPengeluaranBulanIni = Expense::where('id_user', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('nominal');

        $labaBersihBulanIni = $totalPendapatanBulanIni - $totalPengeluaranBulanIni;

        // Calculate laba bersih for last month
        $totalPendapatanBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->sum('total');

        $totalPengeluaranBulanKemarin = Expense::where('id_user', $user->id)
            ->whereMonth('date', $lastMonth)
            ->whereYear('date', $lastYear)
            ->sum('nominal');

        $labaBersihBulanKemarin = $totalPendapatanBulanKemarin - $totalPengeluaranBulanKemarin;

        $percentageChangeLabaBersih = 0;

        if ($labaBersihBulanKemarin != 0) {
            $percentageChangeLabaBersih = (($labaBersihBulanIni - $labaBersihBulanKemarin) / $labaBersihBulanKemarin) * 100;
        }

        // Calculate total pesanan baru for current month
        $totalPesananBaruBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->count();

        // Calculate total pesanan baru for last month
        $totalPesananBaruBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastYear)
            ->count();

        $percentageChangePesananBaru = 0;

        if ($totalPesananBaruBulanKemarin != 0) {
            $percentageChangePesananBaru = (($totalPesananBaruBulanIni - $totalPesananBaruBulanKemarin) / $totalPesananBaruBulanKemarin) * 100;
        }

        // Calculate total terjual barang for current month
        $totalTerjualBulanIni = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->with('transactionItems')
            ->get()
            ->pluck('transactionItems')
            ->flatten()
            ->sum('quantity');

        // Calculate total terjual barang for last month
        $totalTerjualBulanKemarin = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastYear)
            ->with('transactionItems')
            ->get()
            ->pluck('transactionItems')
            ->flatten()
            ->sum('quantity');

        $percentageChangeTotalTerjual = 0;

        if ($totalTerjualBulanKemarin != 0) {
            $percentageChangeTotalTerjual = (($totalTerjualBulanIni - $totalTerjualBulanKemarin) / $totalTerjualBulanKemarin) * 100;
        }

        // Calculate popular items
        $transactions = Transaction::where('id_user', $user->id)
            ->where('status', '1')
            ->with('transactionItems.product')
            ->get();

        $transactionItems = $transactions->pluck('transactionItems')->flatten();

        // Group transaction items by the product ID and sum the quantities
        $popularItems = $transactionItems->groupBy('id_product')
            ->map(function ($items, $idProduct) {
                $totalQuantity = $items->sum('quantity');
                $product = $items->first()->product;
                return [
                    'id_product' => $idProduct,
                    'product' => $product,
                    'total_quantity' => $totalQuantity,
                ];
            });

        // Sort the items by total quantity in descending order to get the most popular items
        $popularItems = $popularItems->sortByDesc('total_quantity')->values();

        $incomesPerBulanSatuTahun = Transaction::where('id_user', $user->id)->where('status', 1)->get();

        // Initialize arrays for labels and data with 0 values for each month.
        $pendapatanPerBulanSatuTahunLabels = [
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
        $pendapatanPerBulanSatuTahunData = array_fill(0, count($pendapatanPerBulanSatuTahunLabels), 0);
        $peningkatanPesananPerBulanSatuTahunData = array_fill(0, count($pendapatanPerBulanSatuTahunLabels), 0);

        // Calculate the sum of pendapatanPerBulanSatuTahunData by month.
        foreach ($incomesPerBulanSatuTahun as $incomePerBulanSatuTahun) {
            $transactionMonth = date('F', strtotime($incomePerBulanSatuTahun->transaction_date));
            $index = array_search($transactionMonth, $pendapatanPerBulanSatuTahunLabels);

            if ($index !== false) {
                $pendapatanPerBulanSatuTahunData[$index] += $incomePerBulanSatuTahun->total;
            }

            $transactionMonthPeningkatanPesanan = date('n', strtotime($incomePerBulanSatuTahun->transaction_date));
            $peningkatanPesananPerBulanSatuTahunData[$transactionMonthPeningkatanPesanan - 1]++;
        }

        $incomesPerHariSatuMinggu = Transaction::where('id_user', $user->id)->where('status', 1)->get();

        // Initialize arrays for labels and data with 0 values for each day of the week.
        $pendapatanPerHariSatuMingguLabels = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];
        $pendapatanPerHariSatuMingguData = array_fill(0, count($pendapatanPerHariSatuMingguLabels), 0);

        // Calculate the sum of data by day of the week.
        foreach ($incomesPerHariSatuMinggu as $incomePerHariSatuMinggu) {
            $transactionDayOfWeek = date('w', strtotime($incomePerHariSatuMinggu->transaction_date));
            $pendapatanPerHariSatuMingguData[$transactionDayOfWeek] += $incomePerHariSatuMinggu->amount;
        }

        return response([
            'message' => 'success',
            'data' => [
                'pengeluaran' => [
                    'current_month_expenses' => $currentMonthExpenses,
                    'last_month_expenses' => $lastMonthExpenses,
                    'percentage_change' => $percentageChangeExpenses
                ],
                'laba_bersih' => [
                    'laba_bersih_bulan_ini' => $labaBersihBulanIni,
                    'laba_bersih_bulan_kemarin' => $labaBersihBulanKemarin,
                    'percentage_change' => $percentageChangeLabaBersih
                ],
                'pesanan_baru' => [
                    'total_pesanan_baru_bulan_ini' => $totalPesananBaruBulanIni,
                    'total_pesanan_baru_bulan_kemarin' => $totalPesananBaruBulanKemarin,
                    'percentage_change' => $percentageChangePesananBaru
                ],
                'total_terjual_barang' => [
                    'total_terjual_barang_bulan_ini' => $totalTerjualBulanIni,
                    'total_terjual_barang_bulan_kemarin' => $totalTerjualBulanKemarin,
                    'percentage_change' => $percentageChangeTotalTerjual
                ],
                'popular_items' => $popularItems->take(8),
                'pendapatanPerBulanSatuTahun' => [
                    'labels' => $pendapatanPerBulanSatuTahunLabels,
                    'datasets' => $pendapatanPerBulanSatuTahunData
                ],
                'pendapatanPerHariSatuMinggu' => [
                    'labels' => $pendapatanPerHariSatuMingguLabels,
                    'datasets' => $pendapatanPerHariSatuMingguData
                ],
                'peningkatanPesananPerBulanSatuTahun' => [
                    'labels' => $pendapatanPerBulanSatuTahunLabels,
                    'datasets' => $peningkatanPesananPerBulanSatuTahunData
                ]
            ],
        ], 200);
    }
}
