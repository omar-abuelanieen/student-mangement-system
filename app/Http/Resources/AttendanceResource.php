<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
{
    return [
        'id'        => $this->id,
        'date'      => $this->date->toDateString(),
        'status'    => $this->status->value,
        'marked_at' => $this->marked_at->format('Y-m-d H:i:s.v'),
        'student'   => new StudentResource($this->whenLoaded('student')),
    ];
}
}
