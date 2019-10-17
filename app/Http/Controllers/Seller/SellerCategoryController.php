<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');
        $this->middleware('can:view,seller');
    }
    
    public function index(Seller $seller)
    {
        $categories = $seller->products()->whereHas('categories')->with('categories')->get()->pluck('categories')->collapse()->unique('id')->values();

        return $this->showAll($categories);
    }
}
