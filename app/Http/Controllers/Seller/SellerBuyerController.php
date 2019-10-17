<?php

namespace App\Http\Controllers\Seller;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerBuyerController extends ApiController
{
	public function __construct()
    {
        parent::__construct();
    }
    
    public function index(Seller $seller)
    {
        $this->allowedAdminAction();
        
        $buyers = $seller->products()->whereHas('transactions')->with('transactions.buyer')->get()->pluck('transactions')->collapse()->pluck('buyer')->unique('id')->values();

        // $buyers = Buyer::whereHas('transactions.product.seller', function ($query) use ($seller) {
		//     $query->where('id', $seller->id);
		// })->get();

        return $this->showAll($buyers);
    }

}
