<?php

namespace App\Http\Resources;

use App\Models\Program;
use Illuminate\Http\Request;

class CategoryResource extends JsonResourceWithPhoto
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $programs = null;

        if ($this->relationLoaded('services')) {
            $programs = Program::with(['photos'])
                ->whereHas('programServices.service.categoryServices', function ($query) {
                    $query->where('category_id', $this->id);
                })
                ->isEnabled()
                ->get();
        }

        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $this->file_object,
            'routes' => CategoryResource::collection($this->whenLoaded('routes')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'programs' => $programs != null ? ProgramResource::collection($programs) : [],
        ];
    }
}
