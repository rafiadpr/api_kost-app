<?php

namespace App\Http\Resources\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UnitResource extends JsonResource
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
                'name' => $this->name,
                'price' => $this->price,
                // Map through the images and return their URLs
                'images' => $this->images->map(function ($image) {
                    return Storage::disk('public')->exists($image->url)
                        ? Storage::disk('public')->url($image->url)
                        : asset('no-image/no-image-profile.png');
                }),
                'details' => UnitDetailResource::collection($this->details),
            ]
        ];
    }
}
