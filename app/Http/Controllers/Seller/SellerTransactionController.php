<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerTransactionController extends ApiController
{
	public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');

        $this->middleware('can:view,seller');
    }
    
    public function index(Seller $seller)
    {
        $transactions = $seller->products()->whereHas('transactions')->with('transactions')->get()->pluck('transactions')->collapse()->unique('id')->values();

        return $this->showAll($transactions);
    }

}
