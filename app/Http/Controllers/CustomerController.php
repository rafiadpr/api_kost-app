<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Customer\CreateRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Resources\Customer\CustomerCollection;
use App\Http\Resources\Customer\CustomerResource;
use App\Models\CustomerModel;
use App\Helpers\CustomerHelper;

class CustomerController extends Controller
{
    private $customer;
    public function __construct()
    {
        $this->customer = new CustomerHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
            'phone_number' => $request->phone_number ?? '',
        ];
        
        // Query the CustomerModel directly without a helper
        $customers = CustomerModel::where(function($query) use ($filter) {
            if (!empty($filter['name'])) {
                $query->where('name', 'like', '%' . $filter['name'] . '%');
            }
            if (!empty($filter['email'])) {
                $query->where('email', 'like', '%' . $filter['email'] . '%');
            }
            if (!empty($filter['phone_number'])) {
                $query->where('phone_number', $filter['phone_number']);
            }
        })
        ->paginate($request->per_page ?? 25);

        return response()->json(new CustomerCollection($customers));
    }

    public function store(CreateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }

        $payload = $request->only([
            'email',
            'name',
            'password',
            'password_confirmation',
            'photo',
            'phone_number',
        ]);
        $customer = $this->customer->create($payload);

        if (!$customer['status']) {
            return response()->json($customer['error'], 400);
        }

        // $email = $payload['email'];

        // Mail::send('generate.auth.send-credential', ['email' => $payload['email'], 'password' => $payload['password']], function ($message) use ($email) {
        //     $message->to($email)->subject('Your Email & Password');
        // });

        return response()->json(new CustomerResource($customer['data']));
    }

    public function show($id)
    {
        $customer = $this->customer->getById($id);

        if (!($customer['status'])) {
            return response()->json(['Data customer tidak ditemukan'], 404);
        }

        return response()->json(new CustomerResource($customer['data']));
    }

    public function update(UpdateRequest $request)
    {
        // dd($request->all());
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'phone_number']);
        // dd($payload);
        $customer = $this->customer->update($payload, $request->id);

        if (!$customer['status']) {
            return response()->json($customer['error'], 400);
        }

        return response()->json(new CustomerResource($customer['data']));
    }

    public function destroy($id)
    {
        $customer = $this->customer->delete($id);

        if (!$customer) {
            return response()->json(['Mohon maaf data pengguna tidak ditemukan'], 404);
        }

        return response()->json(['data' => $customer, 'message' => 'customer berhasil dihapus', 'settings' => []], 200);
    }
}
