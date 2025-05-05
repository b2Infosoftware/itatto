<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'name'=>$this->name,
            'scheduled_date'=>$this->scheduled_date,
            'scheduled_time'=>$this->scheduled_time,
            'timezone'=>$this->timezone,
            'delivered_on'=>$this->delivered_on,
            'is_active'=>$this->is_active,
            'is_birthday'=>$this->is_birthday,
            'filters'=>$this->filters,
            'type'=>$this->type,
            'email_subject'=>$this->email_subject,
            'message'=>$this->message,
        ];
    }
}
