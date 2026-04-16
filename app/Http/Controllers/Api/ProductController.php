<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //
    public function index($from, $to)
    {
        $offset = $from - 1;
        $limit = min($to - $from + 1, 20);
        return ProductResource::collection(Product::latest()->skip($offset)->take($limit)->get());
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('images'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'product_name' => 'required|string|max:255',
        'product_desc' => 'nullable|string',
        'product_price' => 'required|numeric',
        'category' => 'nullable|string',
        'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // 🔥 Handle image upload
    if ($request->hasFile('product_image')) {
        $path = $request->file('product_image')->store('products', 'public');
        $validated['product_image'] = $path; // save path only
    }

    $product = Product::create($validated);

    if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }
    }

    return response()->json(['data' => new ProductResource($product->load('images'))], 201);
}


public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'product_name' => 'sometimes|nullable|string|max:255',
        'product_desc' => 'sometimes|nullable|string',
        'product_price' => 'sometimes|nullable|numeric|min:0',
        'category' => 'sometimes|nullable|string',
        'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'product_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    if ($request->hasFile('product_image')) {
        if ($product->product_image) {
            \Storage::disk('public')->delete($product->product_image);
        }

        $path = $request->file('product_image')->store('products', 'public');
        $validated['product_image'] = $path;
    }

    $product->update($validated);

    if ($request->hasFile('product_images')) {
        foreach ($request->file('product_images') as $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }
    }

    return new ProductResource($product->load('images'));
}

    public function uploadImages(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        foreach ($request->file('product_images') as $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }

        $product->load('images');
        return new ProductResource($product);
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json(['message' => 'Image does not belong to this product'], 403);
        }

        \Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image deleted'], 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }

    public function getByCategory($category, $from, $to)
    {
        $offset = max($from - 1, 0);
        $limit = min(max($to - $from + 1, 1), 20);

        $products = Product::where('category', $category)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        return ProductResource::collection($products);
    }

    public function search($query, $from, $to)
    {
        $offset = max($from - 1, 0);
        $limit = min(max($to - $from + 1, 1), 20);

        $terms = preg_split('/\s+/', trim($query));
        $terms = array_filter($terms, fn($term) => $term !== '');

        $productsQuery = Product::query();

        foreach ($terms as $term) {
            $productsQuery->where(function ($q) use ($term) {
                $q->where('product_name', 'LIKE', "%{$term}%")
                  ->orWhere('product_desc', 'LIKE', "%{$term}%");
            });
        }

        $products = $productsQuery
            ->orderByRaw(
                "(CASE WHEN product_name LIKE ? THEN 1 ELSE 0 END) + (CASE WHEN product_desc LIKE ? THEN 1 ELSE 0 END) DESC",
                ["%{$query}%", "%{$query}%"]
            )
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        return ProductResource::collection($products);
    }
}
