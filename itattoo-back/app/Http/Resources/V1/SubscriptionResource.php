<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'plan_id' => $this->plan_id,
            'started_at' => $this->started_at,
            'ends_at' => $this->ends_at,
            'canceled_at' => $this->canceled_at,
            'is_trial' => $this->is_trial,
            'plan' => new PlanResource($this->plan),
        ];
    }
}
