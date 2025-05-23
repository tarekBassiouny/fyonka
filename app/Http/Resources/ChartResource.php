<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'months' => $this['months'],
            'stores' => $this['stores'],
            'net_income' => $this['net_income'],
            'income' => $this['income'],
            'outcome' => $this['outcome'],
        ];
    }
}
