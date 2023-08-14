<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionsItem;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MyTransactionController extends Controller
{
    public function index() {
        if(request()->ajax()) {
            $query = Transactions::with(['user'])->where('users_id', Auth::user()->id);

            return DataTables::of($query)
            ->addColumn('action', function($item) {
                return
                '<a href=" '. route('dashboard.my-transaction.show', $item->id) .' " class="bg-blue-500 text-white rounded-md px-3 py-1 m-2">
                    Show
                </a>'
                ;
            })
            ->editColumn('price_total', function($item){
                return 'Rp. ' . number_format($item->price_total);
            })
            ->rawColumns(['action'])
            ->make();
        }
        return view('pages.dashboard.transaction.index', [

        ]);
    }

        public function show(Transactions $myTransaction) {

            if(request()->ajax()) {
                $myTransaction = TransactionsItem::with(['product'])->where('transactions_id', $myTransaction->id);
    
                return DataTables::of($myTransaction)
                ->editColumn('product.price', function($item){
                    return 'Rp. ' . number_format($item->product->price);
                })
                ->rawColumns(['action'])
                ->make();
            }
            return view('pages.dashboard.transaction.show', [
                'transaction' => $myTransaction
            ]);
        }
    }

