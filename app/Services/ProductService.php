<?php

namespace App\Services;

use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Exceptions\CreateResourceFailedException;
use App\Exceptions\DeleteResourceFailedException;
use App\Exceptions\GeneralErrorException;
use App\Exceptions\UpdateResourceFailedException;
use App\Exports\ProductsExport;
use App\Jobs\NotifyByMailUserOfCompletedExport;
use App\Jobs\ProductCsvProcess;
use App\Mail\filtredProdut;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Excel;

/**
 * Class ProductService.
 */
class ProductService extends BaseService
{
    /**
     * ProductService constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }


    /**
     * @param array $data
     * @param $file
     * @return Product
     * @throws CreateResourceFailedException
     * @throws \Throwable
     */
    public function store(array $data = [], $file): Product
    {
        DB::beginTransaction();

        try {
            $product = $this->createProduct([
                'name' => $data["name"],
                'barcode' => $data["barcode"],
                'brand' => $data["brand"] ?? null,
                'price' => $data["price"],
                'image_url' => $file,
                'date_added' => now(),


            ]);

        } catch (Exception $e) {
            DB::rollBack();

            throw new CreateResourceFailedException(__('There was a problem creating this Product. Please try again.' . $e->getMessage()));
        }

        event(new ProductCreated($product));

        DB::commit();


        return $product;
    }

    /**
     * @param $id
     * @param array $data
     * @param $file
     * @return Product
     * @throws UpdateResourceFailedException
     * @throws \Throwable
     */
    public function update($id, array $data = [], $file): Product
    {

        $product = $this->model::find($id);
        if (!$product) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $data["name"],
                'brand' => $data["brand"] ?? null,
                'price' => $data["price"],
                'image_url' => $file,

            ]);

        } catch (Exception $e) {
            DB::rollBack();

            throw new UpdateResourceFailedException(__('There was a problem updating this Product. Please try again.' . $e->getMessage()));
        }

        event(new ProductUpdated($product));

        DB::commit();

        return $product;
    }


    /**
     * @param $id
     * @return Product
     * @throws DeleteResourceFailedException
     * @throws Exception
     */
    public function delete($id): Product
    {

        $product = $this->model::find($id);
        if (!$product) {
            abort(404);
        }


        if ($this->deleteById($product->id)) {
            event(new ProductDeleted($product));

            return $product;
        }

        throw new DeleteResourceFailedException('There was a problem deleting this Product. Please try again.');
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function createProduct(array $data = [])
    {
        return $this->model::create([
            'name' => $data["name"],
            'barcode' => $data["barcode"],
            'brand' => $data["brand"] ?? null,
            'price' => $data["price"],
            'image_url' => $data["image_url"] ?? null,
            'date_added' => now(),
        ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Bus\Batch
     * @throws \Throwable
     */
    public function importProducts($data)
    {

        // Chunking file
        $chunks = array_chunk($data, 10);

        $header = [];

        // Add Job Batch
        $batch = Bus::batch([])->dispatch();

        foreach ($chunks as $key => $chunk) {
            $datachunk = array_map('str_getcsv', $chunk);

            // add header
            if ($key === 0) {
                $header = $datachunk[0];
                unset($datachunk[0]);
            }


            // Add job to batch
            $batch->add(new ProductCsvProcess($datachunk, $header));
        }

        return $batch;

    }

    /**
     * @param $brand
     * @param $price
     * @param string $page
     * @param string $perPage
     * @return Product|Product[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function filterProducts($brand, $price, $page = 1, $perPage = 10)
    {
        $productsFilter = $this->model::query();

        // filter brand
        if ($brand !== "all" || $brand !== "") {
            $productsFilter->where("brand", "LIKE", $brand);
        }

        // filter price
        switch ($price) {
            case 'lt3' :
                $productsFilter->where("price", "<", 3);
                break;
            case 'bt3-6':
                $productsFilter->whereBetween("price", [3, 6]);
                break;

            case 'bt6-9' :
                $productsFilter->whereBetween("price", [6, 9]);
                break;

            case 'gt9':
                $productsFilter->where("price", ">", 9);
                break;

        }
        $getData = $productsFilter->paginate($perPage, ['*'], $pageName = 'page', $page);

        return $getData;
    }

    /**
     * @param $batchId
     * @return \Illuminate\Bus\Batch|null
     */
    public function batch($batchId)
    {
        return Bus::findBatch($batchId);
    }

    /**
     * @return array|\Illuminate\Bus\Batch|null
     */
    public function batchInProgress()
    {
        $batches = DB::table('job_batches')
            ->where('pending_jobs', '>', 0)
            ->get();
        if (count($batches) > 0) {
            return Bus::findBatch($batches[0]->id);
        }

        return [];
    }

    /**
     * @param $searchQuery
     * @return Product[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function searchProducts($searchQuery)
    {

        $searchWd = '%' . $searchQuery . '%';
        $queried = $this->model::where("brand", "LIKE", $searchWd)
            ->orWhere("name", "LIKE", $searchWd)
            ->orWhere("barcode", "=", $searchQuery)
            ->get();

        return $queried;

    }

    /**
     * @param $sortQuery
     * @param $sortByQuery
     * @return Product[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function sortProducts($sortQuery, $sortByQuery)
    {
        $queries = $this->model::query()->orderBy($sortQuery, $sortByQuery)->get();

        return $queries;

    }

    public function mailProducts($brand, $price): void
    {
        (new ProductsExport($brand, $price))
            ->store('filtredProducts.csv', 'public', Excel::CSV)->chain([
                new NotifyByMailUserOfCompletedExport(),
            ]);

    }

    public function getNotifications()
    {

        $count = DB::table("notifications")->count();
        $list = DB::table("notifications")->get();

        return [
            "count" => $count,
            "list" => $list
        ];

    }


}
