<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   TransactionResource extends JsonResource
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
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date?->toDateString(),
            'is_temp' => $this->is_temp,

            'store' => new StoreResource($this->store),
            'type' => new TransactionTypeResource($this->transactionType),
            'subtype' => new TransactionSubtypeResource($this->subtype),
        ];
    }
}
