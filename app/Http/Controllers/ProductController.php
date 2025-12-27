<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->productService->getPublicProducts($request->all()));
    }

    public function adminIndex(): JsonResponse
    {
        return response()->json($this->productService->getAdminProducts());
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->productService->getProduct($id));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->only(['name', 'price', 'original_price', 'information', 'status', 'stock', 'store_id', 'category']),
            $request->file('image'),
            $request->user()?->store_id
        );

        return response()->json($product, 201);
    }

    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        $product = $this->productService->updateProduct(
            $id,
            $request->only(['name', 'price', 'original_price', 'information', 'status', 'stock', 'store_id', 'category']),
            $request->file('image')
        );

        return response()->json($product);
    }

    public function destroy($id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted']);
    }

    public function categories(): JsonResponse
    {
        return response()->json($this->productService->getCategories());
    }
}
