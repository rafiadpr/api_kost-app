<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CustomerResource extends JsonResource
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
                'email' => $this->email,
                // 'photo_url' => !empty($this->photo) ? Storage::disk('public')->url($this->photo) : asset('no-image/no-image-profile.png'),
                'photo_url' => !empty($this->photo) && Storage::disk('public')->exists($this->photo)
                    ? Storage::disk('public')->url($this->photo)
                    : asset('no-image/no-image-profile.png'),
                'phone_number' => $this->phone_number,
            ],
        ];
    }
}
