<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = $request->query('category');
        if(!empty($category)){
            $filteredProducts = Product::whereHas('category', function($query) use ($category) {
                $query->where('title', $category);
            })->get();
        }
        if(empty($category)){
            $filteredProducts = Product::all();
        }
        $products = ProductResource::collection($filteredProducts);
        $categories = Category::all();
        return response(compact('products', 'categories'), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $formInput = $request->except('image');

        $image = $request->file('image');
        if(!empty($image)){
            $path = $image->store('images');
            $formInput['image'] = $path;
        }

        Product::create($formInput);
        $products = ProductResource::collection(Product::all());
        return response(compact('products'), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = new ProductResource(Product::find($id));
        return response(compact('product'), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        $formInput = $request->except('image');

        $image = $request->file('image');
        if(!empty($image)){
            $path = $image->store('images');
            $formInput['image'] = $path;
        }

        $product->update($formInput);
        $products = ProductResource::collection(Product::all());
        return response(compact('products'), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::destroy($id);
        $products = ProductResource::collection(Product::all());
        return response(compact('products'), Response::HTTP_OK);
    }
}
