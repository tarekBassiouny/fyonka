<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionAPIResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'description' => $this->description,
            'amount' => $this->amount,
            'type_id' => $this->transactionType?->id,
            'subtype_id' => $this->subtype?->id,
            'store_id' => $this->store?->id,
        ];
    }
}
