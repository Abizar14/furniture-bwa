<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(request()->ajax()) {
            $products = Product::query();

            return DataTables::of($products)
            ->addColumn('action', function($item) {
                return
                '<a href=" '. route('dashboard.product.gallery.index', $item->id) .' " class="bg-blue-500 text-white rounded-md px-3 py-1 m-2">
                    Gallery
                </a>
                <a href=" '. route('dashboard.product.edit', $item->id) .' " class="bg-gray-500 text-white rounded-md px-3 py-1 m-2">
                    Edit
                </a>
                <form class="inline-block" action= "'. route('dashboard.product.destroy', $item->id) .'" method="POST">
                    <button class="bg-red-500 text-white rounded-md px-2 py-1 m-2">
                        Delete
                    </button>
                '. method_field('delete'). csrf_field() .'
                </form>'
                ;
            })
            ->editColumn('price', function($item){
                return 'Rp.' . number_format($item->price);
            })
            ->rawColumns(['action'])
            ->make();
        }
        return view('pages.dashboard.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dashboard.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        Product::create($data);
        $response = [
            'title' => 'Success!',
            'text' => 'Data berhasil ditambahkan.',
            'icon' => 'success'
        ];


        return redirect()->route('dashboard.product.index');
        // Mengembalikan respon JSON berisi data SweetAlert
        // return response()->json($response);
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
    public function edit(Product $product) 
    {
        return view('pages.dashboard.product.edit', [
            'item' => $product
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $product->update($data);

        return redirect()->route('dashboard.product.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('dashboard.product.index');
    }
}
