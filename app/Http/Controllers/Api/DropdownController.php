<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\TransactionSubtype;
use App\Models\TransactionType;

class DropdownController extends Controller
{
    public function index()
    {
        return response()->json([
            'types' => TransactionType::select('id', 'name')->get(),
            'subtypes' => TransactionSubtype::select('id', 'name', 'transaction_type_id')->get(),
            'stores' => Store::select('id', 'name')->get(),
        ]);
    }
}
