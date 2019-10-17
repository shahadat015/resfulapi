<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionSellerController extends ApiController
{
	public function __construct()
    {
        parent::__construct();
        
        $this->middleware('scope:read-general');

        $this->middleware('can:view,transaction');
    }

    public function index(Transaction $transaction)
    {
        $seller = $transaction->product->seller;
        return $this->showOne($seller);
    }

}
