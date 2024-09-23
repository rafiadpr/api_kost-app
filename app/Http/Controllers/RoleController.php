<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $role;

    public function __construct()
    {
        $this->role = new RoleHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'access' => $request->access ?? '',
        ];
        $roles = $this->role->getAll($filter, 5, $request->sort ?? '');

        return response()->json(new RoleCollection($roles['data']));
    }

    public function show(string $id)
    {
        $role = $this->role->getById($id);

        if (!($role['status'])) {
            return response()->json(['Data role tidak ditemukan'], 404);
        }
        return response()->json(new RoleResource($role['data']), 200);
    }

    public function store(CreateRequest $request)
    {
        // dd($request->all());
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }
        $payload = $request->only(['access', 'name']);
        $role = $this->role->create($payload);

        if (!$role['status']) {
            return response()->json($role['error']);
        }

        return response()->json([
            'message' => 'role berhasil ditambah',
            'data' => new RoleResource($role['data']),
        ], 200);
    }

    public function update(UpdateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only(['access', 'name']);
        $role = $this->role->update($payload, $request->id);

        if (!$role['status']) {
            return response()->json($role['error'], 400);
        }

        return response()->json([
            'message' => 'role berhasil diubah',
            'data' => new RoleResource($role['data']),
        ], 200);
    }

    public function destroy(string $id)
    {
        $role = $this->role->delete($id);

        if (!$role) {
            return response()->json(['Mohon maaf data role tidak ditemukan']);
        }

        return response()->json([
            'message' => 'role berhasil dihapus',
            'data' => $role,
        ], 200);
    }
}
