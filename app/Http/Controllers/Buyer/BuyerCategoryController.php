<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerCategoryController extends ApiController
{
	public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general');
        $this->middleware('can:view,buyer');
    }

    public function index(Buyer $buyer)
    {
         $categories = $buyer->transactions()->with('product.categories')->get()
         					 ->pluck('product.categories') // To retrive specific column
         					 ->collapse() // To remove extra array[] element
         					 ->unique('id') // To retrive unique category
         					 ->values(); // To remove emty array
        return $this->showAll($categories);
    }

}
