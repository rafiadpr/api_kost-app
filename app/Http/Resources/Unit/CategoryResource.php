<?php

namespace App\Http\Resources\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
       return [
           'id' => $this->id,
           'name' => $this->name,
           'type' => $this->type
       ];
    }
}
