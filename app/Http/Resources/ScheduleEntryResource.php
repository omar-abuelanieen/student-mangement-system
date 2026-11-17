<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray(Request $request): array
{
    return [
        'id'         => $this->id,
        'day'        => $this->day->value,
        'start_time' => substr($this->start_time, 0, 5),
        'end_time'   => substr($this->end_time, 0, 5),
        'classroom'  => new ClassroomResource($this->whenLoaded('classroom')),
        'subject'    => new SubjectResource($this->whenLoaded('subject')),
        'teacher'    => new TeacherResource($this->whenLoaded('teacher')),
    ];
}
}
