<?php

namespace App\Jobs;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class ProductCsvProcess
 * @package App\Jobs
 */
class ProductCsvProcess implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    public $data;
    /**
     * @var
     */
    public $header;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data   = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->data as $sale) {
            $productData = array_combine($this->header, $sale);
            Product::firstOrCreate([
                'name' => $productData["name"],
                'barcode' => $productData["barcode"],
                'brand' => $productData["brand"],
                'price' => $productData["price"],
                'image_url' => $productData["image_url"],
                'date_added' => Carbon::createFromFormat("d/m/Y H:i:s", $productData["date_added"]),
            ]);
        }
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
        dump($exception);
        // Send user notification of failure, etc...
    }
}
