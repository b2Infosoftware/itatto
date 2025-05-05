<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExclusiveOfferResource extends JsonResource
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
            'offer_name' => $this->offer_name,
            'offer_details' => $this->offer_details,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'discount' => new DiscountResource($this->whenLoaded('discount')),
            'vips' => VipResource::collection($this->whenLoaded('vips')),
        ];
    }
}
