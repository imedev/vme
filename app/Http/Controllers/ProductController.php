<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Jobs\ProductCsvProcess;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductController
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    public $productService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @return \Illuminate\Bus\Batch
     * @throws \Throwable
     */
    public function importProducts()
    {
        $path = resource_path("/importData/legacy_products.csv");
        $data = file($path);

        $batch = $this->productService->importProducts($data);


        return $batch;

    }

    /**
     * @return \Illuminate\Bus\Batch|null
     */
    public function batch()
    {
        $batchId = request('id');

        $batch = $this->productService->batch($batchId);
        return $batch;
    }

    /**
     * @return array|\Illuminate\Bus\Batch|null
     */
    public function batchInProgress()
    {
        $batches = $this->productService->batchInProgress();
        return $batches;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProducts()
    {
        return $this->productService->paginate("10", ['*'], $pageName = 'page', 1);
    }

    /**
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function saveProduct(StoreProductRequest $request)
    {
        $filePath = "";
        if ($request->img) {
            $img = $request->img;
            $folderPath = public_path("/images/products/");//path location

            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $uniqid = uniqid('', true);
            $file = $folderPath . $uniqid . '.' . $image_type;
            file_put_contents($file, $image_base64);

            $filePath = config("app.url") . "/images/products/" . $uniqid . '.' . $image_type;//path location

        }


        $product = $this->productService->store($request->validated(), $filePath);
        return response()->json($product);
    }

    /**
     * @param UpdateProductRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateProduct(UpdateProductRequest $request, $id)
    {
        $filePath = "";

        if ($request->img) {
            $img = $request->img;
            $folderPath = public_path("/images/products/");//path location

            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $uniqid = uniqid('', true);
            $file = $folderPath . $uniqid . '.' . $image_type;
            file_put_contents($file, $image_base64);

            $filePath = config("app.url") . "/images/products/" . $uniqid . '.' . $image_type;//path location

        }


        $product = $this->productService->update($id, $request->validated(), $filePath);
        return response()->json($product);
    }

    /**
     * @param DeleteProductRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\DeleteResourceFailedException
     */
    public function deleteProduct(DeleteProductRequest $request, $id)
    {

        $prod = $this->productService->delete($id);
        return response()->json($prod);
    }


    /**
     * @param Request $request
     * @return Product[]|\App\Models\Product[][]|ProductService|\Illuminate\Database\Eloquent\Builder[][]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection[]|mixed
     */
    public function filterProducts(Request $request)
    {

        $brand = $request->query('brand');
        $price = $request->query('price');

        $productsFilter = $this->productService->filterProducts($brand, $price);

        return $productsFilter;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateProducts(Request $request)
    {
        $page = (int)$request->input('page', '1');
        $page = $page < 1 ? 1 : $page;
        $perPage = (int)$request->input('per_page', 10);
        $brand = $request->query('brand');
        $price = $request->query('price');
        return $this->productService->filterProducts($brand, $price, $page, $perPage);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchProducts(Request $request)
    {
        $searchQuery = $request->query('search');
        return $this->productService->searchProducts($searchQuery);


    }

    /**
     * @param Request $request
     * @return Product[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function sortProducts(Request $request)
    {
        $sortQuery = $request->query('sort');
        $sortByQuery = $request->query('sortBy');
        return $this->productService->sortProducts($sortQuery, $sortByQuery);

    }


    /**
     * @param Request $request
     */
    public function mailProducts(Request $request)
    {

        $brand = $request->query('brand');
        $price = $request->query('price');

        $productsFilter = $this->productService->mailProducts($brand, $price);

        return $productsFilter;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications()
    {
        $productsNotifications = $this->productService->getNotifications();

        return response()->json($productsNotifications);

    }


}
