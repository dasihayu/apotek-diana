<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Notifications\StockAlert;
use App\Events\ProductReachedLowStock;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "dashboard";

        $total_suppliers = Supplier::count();
        $total_medicines = Purchase::count();
        $available_medicines = Purchase::where('quantity', '>', 5)->count();
        $total_medicines_outStock = Purchase::where('quantity', 0)->count();
        $total_medicines_runningOutStock = Purchase::where('quantity', '<=', 5)->count();
        $total_purchases = Purchase::where('expiry_date', '=', Carbon::now())->count();

        $total_categories = Category::count();
        $total_suppliers = Supplier::count();
        // Mengambil total penjualan
        $total_sales = Sales::sum('total_price');
        $formatted_total_sales = number_format($total_sales, 0, ',', '.');

        // Penjualan kemarin
        $yesterday_sales = Sales::whereDate('created_at', Carbon::now()->yesterday()->format('Y-m-d'))->sum('total_price');
        $formatted_yesterday_sales = number_format($yesterday_sales, 0, ',', '.');

        // Penjualan tujuh hari terakhir
        $last_sevenDays = Sales::where('created_at', '>=', Carbon::now()->subDays(7))->sum('total_price');
        $formatted_last_sevenDays = number_format($last_sevenDays, 0, ',', '.');

        // dd($yesterday_sales);

        $pieChart = app()->chartjs
            ->name('pieChart')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['Total Purchases', 'Total Suppliers', 'Total Sales'])
            ->datasets([
                [
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#7bb13c'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB', '#7bb13c'],
                    'data' => [$total_purchases, $total_suppliers, $total_sales]
                ]
            ])
            ->options([]);

        // dd($pieChart );

        $total_expired_products = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();
        $latest_sales = Sales::whereDate('created_at', '=', Carbon::now())->get();
        $today_sales = Sales::whereDate('created_at', '=', Carbon::now())->sum('total_price');
        $formatted_today_sales = number_format($today_sales, 0, ',', '.');
        return view('home', compact(
            'title',
            'pieChart',
            'total_expired_products',
            'formatted_today_sales',
            'latest_sales',
            'total_categories',
            'total_purchases',
            'total_medicines',
            'total_medicines_outStock',
            'total_medicines_runningOutStock',
            'available_medicines',
            'total_suppliers',
            'formatted_last_sevenDays',
            'formatted_yesterday_sales',
            'formatted_total_sales'
        ));
    }
}
