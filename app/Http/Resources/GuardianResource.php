<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'full_name' => $this->user->full_name,
            'email'     => $this->user->email,
            'students'  => $this->whenLoaded('students', fn () => $this->students->map(fn ($student) => [
                'id'           => $student->id,
                'full_name'    => $student->user->full_name,
                'relationship' => $student->pivot->relationship,
            ])->all()),
        ];
    }
}
