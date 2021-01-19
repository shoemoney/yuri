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

    public function summaries()
    {
        return response()->json($this->indodax->summaries());
    }

    public function getInfo()
    {
        return response()->json($this->indodax->getInfo());
    }

    public function trades($pair_id)
    {
        return response()->json($this->indodax->trades($pair_id));
    }
}
