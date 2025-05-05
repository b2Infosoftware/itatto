<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationTemplateResource extends JsonResource
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
            'organisation_id' => $this->organisation_id,
            'name' => $this->name,
            'entity' => $this->entity,
            'type' => $this->type,
            'channel' => $this->channel,
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}
