<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get paginate products
     */
    public function index($pagination = null)
    {
        $pagination = isset($pagination) ? $pagination : 10;
        $products = Product::paginate($pagination);

        return response()->json(['products' => $products]);
    }

    /**
     * Get an especific product by id
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['product' => $product]);
    }

    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'images' => 'array',
            'images.*' => 'max:2048',
            'main_image_id' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // create the product
        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
        ]);

        if ($request->has('images')) {
            foreach ($request->input('images') as $key => $imagePath) {
                $image = $product->images()->create(['image_path' => $imagePath]);
                if( $request->has('main_image_id') &&  $key == $request->input('main_image_id')){
                    $product->main_image_id = $image->id;
                    $product->save();
                }
            }
        }

        return response()->json(['product' => $product], 201);
    }

    public function update(Request $request, $id)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'description' => 'string',
            'price' => 'numeric',
            'images' => 'array',
            'images.*' => 'max:2048',
            'main_image_id' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // search the product
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Update the product
        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
        ]);

        ProductImage::where('product_id', '=', $product->id)->delete();//delete old images

        if ($request->has('images')) {//insert new images
            foreach ($request->input('images') as $key => $imagePath) {
                $image = $product->images()->create(['image_path' => $imagePath]);
                if( $request->has('main_image_id') &&  $key == $request->input('main_image_id')){
                    $product->main_image_id = $image->id;
                    $product->save();
                }
            }
        }


        return response()->json(['product' => $product]);
    }

    public function destroy($id)
    {
        // search the product
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // delete the producto
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
