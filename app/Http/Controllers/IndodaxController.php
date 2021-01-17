<?php

namespace App\Http\Controllers;

use App\Indodax\Indodax;
use Illuminate\Http\Request;

class IndodaxController extends Controller
{
    protected $indodax;

    public function __construct()
    {
        $this->indodax = new Indodax;
    }

    public function index()
    {
        $info = $this->indodax->getInfo();
        $transaction = $this->indodax->transactionHistory();
        dd($info, $transaction);
        return response()->json($info);
    }
}
