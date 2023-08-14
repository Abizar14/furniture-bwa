<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transactions;
use App\Models\TransactionsItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(request()->ajax()) {
            $transaction = Transactions::query();

            return DataTables::of($transaction)
            ->addColumn('action', function($item) {
                return
                '<a href=" '. route('dashboard.transaction.show', $item->id) .' " class="bg-blue-500 text-white rounded-md px-3 py-1 m-2">
                    Show
                </a>
                <a href=" '. route('dashboard.transaction.edit', $item->id) .' " class="bg-gray-500 text-white rounded-md px-3 py-1 m-2">
                    Edit
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
    public function show(Transactions $transaction)
    {
        if(request()->ajax()) {
            $transaction = TransactionsItem::with(['product'])->where('transactions_id', $transaction->id);

            return DataTables::of($transaction)
            ->editColumn('product.price', function($item){
                return 'Rp. ' . number_format($item->product->price);
            })
            ->rawColumns(['action'])
            ->make();
        }
        return view('pages.dashboard.transaction.show', [
            'transaction' => $transaction
        ]);
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transactions $transaction)
    {
        return view('pages.dashboard.transaction.edit', [
            'item' => $transaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionRequest $request, Transactions $transaction)
    {
        $data = $request->all();
        
        $transaction->update($data);

        return redirect()->route('dashboard.transaction.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
