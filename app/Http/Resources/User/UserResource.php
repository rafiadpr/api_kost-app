<?php

namespace App\Http\Resources\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // 'photo_url' => !empty($this->photo) ? Storage::disk('public')->url($this->photo) : asset('no-image/no-image-profile.png'),
            'photo_url' => !empty($this->photo) && Storage::disk('public')->exists($this->photo)
                ? Storage::disk('public')->url($this->photo)
                : asset('no-image/no-image-profile.png'),
            'updated_security' => $this->updated_security,
            'phone_number' => $this->phone_number,
            'status' => (string) $this->status,
            'user_roles_id' => (string) $this->user_roles_id,
            'access' => isset($this->role->access) ? json_decode($this->role->access) : [],
        ];
    }
}
