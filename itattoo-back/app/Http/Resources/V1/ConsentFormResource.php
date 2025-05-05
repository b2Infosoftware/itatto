<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsentFormResource extends JsonResource
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
            'category_id' => $this->category_id,
            'organisation_id' => $this->organisation_id,
            'name' => $this->name,
            'logo' => $this->logo,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'opening_text' => $this->opening_text,
            'statements' => $this->statements,
            'closing_text' => $this->closing_text,
            'sign_title' => $this->sign_title,
            'needs_signature' => $this->needs_signature,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'use_custom_consent' => $this->use_custom_consent,
            'text' => $this->text,
            'infant_consent' => $this->infant_consent,
            'category' => new CategoryResource($this->whenLoaded('category'), $this->category),
        ];
    }
}
