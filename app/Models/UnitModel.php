<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repository\CrudInterface;

class UnitModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $timestamps = true;
    public $incrementing = false;
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

    public function category()
    {
        return $this->belongsTo(UnitCategoryModel::class, 'unit_category_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(UnitDetailModel::class, 'unit_id', 'id');
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $content = $this->query();

        if (!empty($filter['unit_category_id'])) {
            $content->where('unit_category_id', '=', $filter['unit_category_id']);
        }

        if (!empty($filter['unit_id'])) {
            $contentIds = explode(',', $filter['unit_id']);
            $content->whereIn('id', $contentIds);
        }

        // if (!empty($filter['unit_category_id'])) {
        //     $contentIds = explode(',', $filter['unit_category_id']);
        //     $content->whereIn('id', $contentIds);
        // }

        $sort = $sort ? : 'unit_category_id DESC';
        $content->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $content->paginate($itemPerPage)->appends('sort', $sort);
    }


    public function getById(string $id)
    {
        return $this->find($id);
    }
}
