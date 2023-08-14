<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transactions;
use App\Models\TransactionsItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Midtrans\Transaction;
use Midtrans\Config;
use Midtrans\Snap;

class FrontendController extends Controller
{
    public function index(Request $request) {
        $products = Product::with(['productGalleries'])->latest()->get();
        return view('pages.frontend.index', [
            'products' => $products
        ]);
    }

    public function details(Request $request, $slug) {
        $products = Product::with(['productGalleries'])->where('slug', $slug)->firstOrFail();
        $recommendations = Product::with(['productGalleries'])->inRandomOrder()->limit(4)->get();
        return view('pages.frontend.details', [
            'products' => $products,
            'recommendations' => $recommendations
        ]);
    }

    public function cartAdd(Request $request, $id) {
        Cart::create([
            'users_id' => Auth::user()->id,
            'products_id' => $id
        ]);

        return redirect('cart');
    }
    
    public function cart(Request $request) {
        $carts = Cart::with(['product.productGalleries'])->where('users_id', Auth::user()->id)->get(); // untuk relasinya menggunakan Nested Relasi

        return view('pages.frontend.cart', [
            'carts' => $carts
        ]);
    }

    public function cartDelete(Request $request, $id) {
        $item = Cart::findOrFail($id);

        $item->delete();

        return redirect('cart');
    }

    public function checkout(CheckoutRequest $request) {
        
        $data = $request->all();

        // Get Carts Data
        $carts = Cart::with(['product'])->where('users_id', Auth::user()->id)->get();

        // Add to Transaction Data
        $data['users_id'] = Auth::user()->id;
        $data['price_total'] = $carts->sum('product.price'); 

        // Create Data Transaction
        $transaction = Transactions::create($data);

        // Create Transaction Item
        foreach($carts as $cart) {
            $items[] = TransactionsItem::create([
                'transactions_id' => $transaction->id,
                'users_id' => $cart->users_id,
                'products_id' => $cart->products_id

            ]);
        }

        // Delete Cart After Transaction
        Cart::where('users_id', Auth::user()->id)->delete();

        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$clientKey = config('services.midtrans.clientKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Setup Variable Midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => 'LUX-' . $transaction->id,
                'gross_amount' => (int) $transaction->price_total
            ],
            'customer_details'    => [
                'first_name'        =>  $transaction->name,
                'email' => $transaction->email
            ],
            'enabled_payments'   => [
                'gopay', 'bank_transfer'
            ],
            'vtweb'              => []
        ];

        // Payment Proses
        try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            
            $transaction->payment_url = $paymentUrl;
            $transaction->save();
            // Redirect to Snap Payment Page
            return redirect($paymentUrl);
          }
          catch (Exception $e) {
            echo $e->getMessage();
          }


    }

    public function success(Request $request) {
        return view('pages.frontend.success');
    }
}
