<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'unit_category_id',
        'name',
        'price'
    ];

    public function images()
    {
        return $this->hasMany(ImageModel::class, 'unit_id');
    }
}
