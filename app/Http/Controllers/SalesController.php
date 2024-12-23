<?php

namespace App\Http\Controllers;

use App\Events\MedicineOutStock;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Events\PurchaseOutStock;


class SalesController extends Controller
{

    public function index(Request $request)
    {
        $title = "sales";

        // Ambil kata kunci dari input pencarian
        $search = $request->input('search');

        // Query produk berdasarkan kata kunci pencarian
        $products = Product::when($search, function ($query, $search) {
            $query->whereHas('purchase', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })->get();

        // Ambil semua sales
        $sales = Sales::with('product')->latest()->get();

        return view('sales.sales', compact(
            'title',
            'products',
            'sales'
        ));
    }




    public function store(Request $request)
    {
        try {
            $orderData = json_decode($request->input('order_data'), true);
            $paymentMethod = $request->input('payment_method');
            
            

            foreach ($orderData as $order) {
                $this->validate($request, [
                    'order_data.*.productId' => 'required|exists:products,id',
                    'order_data.*.quantity' => 'required|integer|min:1'
                ]);

                $sold_product = Product::find($order['productId']);
                $purchased_item = Purchase::find($sold_product->purchase->id);

                if (!empty($request->edit_id)) {
                    $sales_quantity = Sales::find($request->edit_id)->quantity;
                    $purchased_item->increment('quantity', $sales_quantity);
                }

                $new_quantity = ($purchased_item->quantity) - ($order['quantity']);
                $notification = '';

                if (!($new_quantity < 0)) {
                    Sales::updateOrCreate(
                        ['id' => $request->edit_id],
                        [
                            'product_id' => $order['productId'],
                            'quantity' => $order['quantity'],
                            'total_price' => ($order['quantity']) * ($sold_product->price),
                            'payment_method' => $paymentMethod
                        ]
                    );

                    $purchased_item->update([
                        'quantity' => $new_quantity,
                    ]);

                    $notification = array(
                        'message' => "Medicine sold successfully!!",
                        'alert-type' => 'success'
                    );

                    if ($new_quantity <= 1 || $new_quantity == 0) {
                        event(new MedicineOutStock($purchased_item));
                        $notification = array(
                            'message' => "Medicine is running out of stock!!!",
                            'alert-type' => 'error'
                        );
                    }
                } elseif ($order['quantity'] > $purchased_item->quantity) {
                    $notification = array(
                        'message' => "Medicine request quantity can not be greater than available quantity!!!  " . ' Available Quantity is ' . ($purchased_item->quantity),
                        'alert-type' => 'error'
                    );

                    if (!empty($request->edit_id)) {
                        $sales_quantity = Sales::find($request->edit_id)->quantity;
                        $purchased_item->decrement('quantity', $sales_quantity);
                    }
                    return response()->json($notification);
                }
            }

            return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function destroy(Request $request)
    {
        $sale = Sales::find($request->id);
        $sale->delete();
        $notification = array(
            'message' => "Sales has been deleted",
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
}
