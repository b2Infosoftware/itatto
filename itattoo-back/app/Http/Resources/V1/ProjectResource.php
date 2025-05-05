<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'category_id' => $this->category_id,
            'customer_id' => $this->customer_id,
            'staff_id' => $this->staff_id,
            'category_name' => $this->category->name,
            'completed_at' => $this->completed_at,
            'signed' => (bool) $this->document,
            'signed_document' => new SignedDocumentResource($this->whenLoaded('document')),
        ];
    }
}
