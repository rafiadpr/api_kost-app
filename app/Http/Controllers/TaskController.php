<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TaskHelper;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskResource;
use App\Http\Requests\Task\CreateRequest;
use App\Http\Requests\Task\UpdateRequest;

class TaskController extends Controller
{
    private $task;

    public function __construct()
    {
        $this->task = new TaskHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'description' => $request->description ?? '',
        ];
        $tasks = $this->task->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->json(new TaskCollection($tasks['data']));
    }

    public function store(CreateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only([
            'user_auth_id',
            'description',
            'status'
        ]);

        // dd($payload);

        $task = $this->task->create($payload);

        if (!$task['status']) {
            return response()->json($task['error']);
        }

        return response()->json([
            'message' => 'task berhasil ditambah',
            'data' => new TaskResource($task['data']),
        ], 200);
    }

    public function show($id)
    {
        $task = $this->task->getById($id);

        if (!($task['status'])) {
            return response()->json(['Data task tidak ditemukan'], 404);
        }

        return response()->json(['data' => new TaskResource($task['data'])], 200);
    }

    public function update(UpdateRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only([
            'user_auth_id',
            'description',
            'status',
            'id',
        ]);

        $task = $this->task->update($payload, $request->id);

        if (!$task['status']) {
            return response()->json($task['error']);
        }

        return response()->json([
            'message' => 'task berhasil diubah',
            'data' => new TaskResource($task['data']),
        ], 200);
    }

    public function destroy($id)
    {
        $task = $this->task->delete($id);

        if (!$task) {
            return response()->json(['Mohon maaf task tidak ditemukan']);
        }

        return response()->json([
            'message' => 'task berhasil dihapus',
            'data' => $task,
        ], 200);
    }
}
