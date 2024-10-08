<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends JsonResourceWithPhoto
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->file_object,
            'birth_date' => $this->birth_date,
            'balance' => $this->balance,
        ];
    }
}
