<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DropdownResource;
use App\Models\Store;
use App\Models\TransactionSubtype;
use App\Models\TransactionType;

class DropdownController extends Controller
{
    public function index(): DropdownResource
    {
        $data = [
            'types' => TransactionType::select('id', 'name')->get(),
            'subtypes' => TransactionSubtype::select('id', 'name', 'transaction_type_id')->get(),
            'stores' => Store::select('id', 'name')->get(),
        ];

        return new DropdownResource($data);
    }
}
