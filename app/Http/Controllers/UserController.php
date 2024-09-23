<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\UserModel;
use App\Helpers\UserHelper;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new UserHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
            'phone_number' => $request->phone_number ?? '',
        ];
        $users = $this->user->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->json(new UserCollection($users['data']));
    }

    public function show($id)
    {
        $user = $this->user->getById($id);

        if (!($user['name'])) {
            return response()->json(['Data user tidak ditemukan'], 404);
        }
        return response()->json(['data' => new UserResource($user['data'])], 200);
    }

    public function store(CreateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'user_roles_id', 'phone_number']);
        // dd($payload);
        $user = $this->user->create($payload);


        if (!$user['status']) {
            return response()->json($user['error'], 400);
        }

        return response()->json([
            'message' => 'task berhasil ditambah',
            'data' => new UserResource($user['data']),
        ], 200);
    }

    public function update(UpdateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'user_roles_id', 'phone_number']);
        $user = $this->user->update($payload, $request->id);

        // dd($payload);

        if (!$user['status']) {
            return response()->json($user['error'], 400);
        }

        return response()->json(new UserResource($user['data']));
    }

    public function destroy($id)
    {
        $user = $this->user->delete($id);

        if (!$user) {
            return response()->json(['Mohon maaf data pengguna tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'user berhasil dihapus',
            'data' => $user,
        ], 200);
    }
}
