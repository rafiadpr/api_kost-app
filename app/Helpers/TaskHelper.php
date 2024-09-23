<?php

namespace App\Helpers;

use App\Models\TaskModel;
use Throwable;

class TaskHelper
{
    private $task;

    public function __construct()
    {
        $this->task = new TaskModel();
    }

    public function create(array $payload): array
    {
        try {

            $user = $this->task->store($payload);

            return [
                'status' => true,
                'data' => $user
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->task->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $task = $this->task->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $task
        ];
    }


    public function getById(string $id): array
    {
        $task = $this->task->getById($id);
        if (empty($task)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $task
        ];
    }

    public function update(array $payload, string $id): array
    {
        try {

            $this->task->edit($payload, $id);

            $task = $this->getById($id);

            return [
                'status' => true,
                'data' => $task['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}