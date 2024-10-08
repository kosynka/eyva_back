<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EmployeeResource extends JsonResourceWithPhoto
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'photo' => $this->file_object,
            'description' => $this->employee_description,
        ];
    }
}
