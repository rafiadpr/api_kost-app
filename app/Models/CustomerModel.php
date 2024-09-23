<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;

class CustomerModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    protected $table = "customer";
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'photo',
    ];
    protected $casts = [
        'id' => 'string',
    ];
    public $timestamp = true;
    
    // protected $attributes = [
    //     'membership_status' => 0, // memberi nilai default = 1 pada kolom membership
    //     'status' => 1, // memberi nilai default = 1 pada kolom status
    // ];

    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }

    // public function getJWTCustomClaims(): array
    // {
    //     return [
    //         'user' => [
    //             'id' => $this->id,
    //             'email' => $this->email,
    //             'updated_security' => $this->updated_security
    //         ]
    //     ];
    // }

    public function transaction()
    {
        return $this->hasMany(TransactionModel::class, 'customer_id', 'id'); // Adjust the foreign key name if necessary
    }

    public function getAll(array $filter, int $itemPerPage, string $sort)
    {
        $customer = $this->query();

        if (!empty($filter['name'])) {
            $customer->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if ($filter['email'] != '') {
            $customer->where('email', '=', $filter['email']);
        }

        if ($filter['phone_number'] != '') {
            $customer->where('phone_number', '=', $filter['phone_number']);
        }

        if (!empty($filter['customer_id'])) {
            $customerIds = explode(',', $filter['customer_id']);
            $customer->whereIn('id', $customerIds);
        }

        $sort = $sort ?: 'id DESC';
        $customer->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $customer->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }
}
