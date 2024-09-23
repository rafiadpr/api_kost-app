<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repository\CrudInterface;
use App\Http\Traits\Uuid;

class TaskModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = [
        'user_auth_id',
        'description',
        'status',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected $attributes = [
        'status' => 1, // memberi nilai default = 1 pada kolom status
    ];

    protected $table = 'employee_task';

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
        $user = $this->query();

        if (!empty($filter['user_auth_id'])) {
            $user->where('user_auth_id', '=', $filter['user_auth_id']);
        }

        $sort = $sort ?: 'id DESC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }


    public function getById(string $id)
    {
        return $this->find($id);
    }
}
