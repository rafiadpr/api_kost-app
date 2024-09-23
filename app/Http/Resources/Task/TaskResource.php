<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
                'user_auth_id' => $this->user_auth_id,
                'description' => $this->description,
                'status' => $this->status,
            ]
        ];
    }
}
