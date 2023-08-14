<?php

namespace App\Http\Controllers\API;

use Midtrans\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function callback() {
        // Set Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$clientKey = config('services.midtrans.clientKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // Buat Instace midtrans notifications
        $notification = new Notification();

        // Assign ke variable untuk memudahan codingan
        $status 	= $notification->transaction_status;
        $type           = $notification->payment_type;
        $fraud 	   	    = $notification->fraud_status;
        $order_id       = $notification->order_id;

        // Get Transaction id
        $order = explode('-', $order_id);

        // Cari Transaksi Berdasarkan ID
        $transactions = Transactions::findOrFail($order[1]);

    // Handle Notification Status Midtrans
    if ($status == 'capture') {
        if ($type == 'credit_card'){
            if($fraud == 'challenge') {
                $transactions->status = 'PENDING';
            } else {
                 $transactions->status = 'SUCCESS';
            }
        }
    } 
    else if ($status == 'settlement') {
        $transactions->status = "SUCCESS";
    }

    else if ($status == 'pending') {
        $transactions->status = "PENDING";
    }

    else if ($status == 'deny') {
        $transactions->status = "PENDING";
    }

    else if ($status == 'expire') {
        $transactions->status = "CANCELLED";
    }

    else if ($status == 'cancel') {
        $transactions->status = "CANCELLED";
    }

    // Simpan Transaksi
    $transactions->save();

    // return response midtrans
    return response()->json([
        'meta' => [
            'code' => 200,
            'message' => 'Midtrans Notification Success'
        ]
        ]);
    
    
    }
}
