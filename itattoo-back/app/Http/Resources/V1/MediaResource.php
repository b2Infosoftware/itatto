<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            // 'customer_id' => $this->customer_id,
            // 'staff_id' => $this->staff_id,
            'project_id' => $this->project_id,
            'media_type' => $this->media_type,
            'type' => $this->type,
            'size' => $this->size,
            'full_path' => config('filesystems.disks.r2.url') . $this->path,
        ];
    }
}
