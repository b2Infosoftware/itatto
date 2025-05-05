<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'is_artist' => $this->is_artist,
            'price' => $this->price,
            'months' => $this->months,
            'is_marketing_modul' => $this->is_marketing_modul,
            'is_sms' => $this->is_sms,
        ];
    }
}
