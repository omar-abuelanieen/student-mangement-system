<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
{
    return [
        'id'          => $this->id,
        'title'       => $this->title,
        'section'     => $this->section,
        'custom_name' => $this->custom_name,
        'capacity'    => $this->capacity,
        'level'       => new LevelResource($this->whenLoaded('level')),
    ];
}
}
