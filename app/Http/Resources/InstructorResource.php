<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InstructorResource extends JsonResourceWithPhoto
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->employee_description,
            'photo' => $this->file_object,
        ];
    }
}
