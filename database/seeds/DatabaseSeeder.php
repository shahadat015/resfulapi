<?php

use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	//DB::statement('SET FOREIGN_KEY_CHECKS=0');
    	Schema::disableForeignKeyConstraints();

    	User::truncate();
    	Category::truncate();
    	Product::truncate();
    	Transaction::truncate();
    	DB::table('category_product')->truncate();

        User::flushEventListeners();
        Category::flushEventListeners();
        Product::flushEventListeners();
        Transaction::flushEventListeners();

        factory(User::class, 10)->create();
        factory(Category::class, 5)->create();
        factory(Product::class, 50)->create()->each(function($product) {
        	$categories = Category::all()->random(mt_rand(1,3))->pluck('id');
        	$product->categories()->attach($categories);
        });
        factory(Transaction::class, 50)->create();
    }
}
