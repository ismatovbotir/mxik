<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'mxik' => $this->id,
            'new_mxik_code' => $this->new_mxik_code,
            'status' => $this->status,
            'name' => $this->name,
            'description' => $this->description,
            'gtin' => $this->gtin,
            'label' => $this->label,
            'use_package' => $this->use_package,
            'packages' => PackageCodeResource::collection($this->whenLoaded('packageCodes')),
        ];
    }
}
