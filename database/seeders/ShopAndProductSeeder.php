<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shop; 
use App\Models\Product; 

class ShopAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::create(['name' => 'My Test Shop']);

        Product::create(['name'=>'Product A','price'=>100,'stock'=>10]);
        Product::create(['name'=>'Product B','price'=>200,'stock'=>5]);
        Product::create(['name'=>'Product C','price'=>50,'stock'=>20]);
    }
}
