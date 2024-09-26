<?php

namespace App\Http\Resources\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'unit_id' => $this->unit_id,
                'name' => $this->name,
                'description' => $this->description,
            ]
        ];
    }
}
