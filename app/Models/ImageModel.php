<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageModel extends Model
{
    use HasFactory;

    protected $table = 'unit_images';

    protected $fillable = [
        'url',
        'unit_id'
    ];

    public function unit()
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
    }
}
