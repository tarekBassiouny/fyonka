<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'revenue' => $this['revenue'],
            'gross_profit' => $this['gross_profit'],
            'net_margin' => $this['net_margin'],
            'expenses' => $this['expenses'],
        ];
    }
}
