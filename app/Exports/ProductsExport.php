<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;

class ProductsExport implements FromQuery, ShouldQueue
{


    use Exportable;

    private  $brand;
    private  $price;

    public function __construct( $brand,  $price)
    {
        $this->brand = $brand;
        $this->price = $price;
    }

    public function query()
    {
        $productsFilter = Product::query();

        // filter brand
        if ($this->brand !== "all" || $this->brand !== "") {
            $productsFilter->where("brand", "LIKE", $this->brand);
        }

        // filter price
        switch ($this->price) {
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
        return $productsFilter;
    }
}
