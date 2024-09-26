<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repository\CrudInterface;

class UnitDetailModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;
    public $timestamps = true;

    protected $fillable = [
        'unit_id',
        'is_available',
        'description'
    ];

    protected $table = 'unit_detail';
 
    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }
 
    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }
 
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();
 
        if (!empty($filter['is_available'])) {
            $user->where('is_available', 'LIKE', '%' . $filter['is_available'] . '%');
        }
 
        if (!empty($filter['unit_id'])) {
            $user->where('unit_id', 'LIKE', '%' . $filter['unit_id'] . '%');
        }
 
        $sort = $sort ?: 'unit_category.index ASC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;
 
        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }
 
    public function getById(string $id)
    {
        return $this->find($id);
    }
 
    public function store(array $payload)
    {
        return $this->create($payload);
    }
 
}
