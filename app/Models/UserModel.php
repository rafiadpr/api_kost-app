<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserModel extends Authenticatable implements CrudInterface, JWTSubject, MustVerifyEmail
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;
    use Notifiable, CanResetPassword;

    protected $table = 'user_auth';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'user_roles_id',
        'phone_number',
        'status'
    ];

    protected $attributes = [
        'user_roles_id' => 1, // memberi nilai default = 1 pada kolom user_roles_id
        'status' => 1
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function role()
    {
        return $this->hasOne(RoleModel::class, 'id', 'user_roles_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
                'email' => $this->email,
                'updated_security' => $this->updated_security
            ]
        ];
    }

    public function isHasRole($permissionName)
    {
        $tokenPermission = explode('|', $permissionName);
        $userPrivilege = json_decode($this->role->access ?? '{}', TRUE);

        foreach ($tokenPermission as $val) {
            $permission = explode('.', $val);
            $feature = $permission[0] ?? '-';
            $activity = $permission[1] ?? '-';
            if (isset($userPrivilege[$feature][$activity]) && $userPrivilege[$feature][$activity] === true) {
                return true;
            }
        }

        return false;
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function resetPassword(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['name'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if (!empty($filter['email'])) {
            $user->where('email', 'LIKE', '%' . $filter['email'] . '%');
        }

        if ($filter['phone_number'] != '') {
            $user->where('phone_number', '=', $filter['phone_number']);
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

    public function store(array $payload)
    {
        return $this->create($payload);
    }
}
