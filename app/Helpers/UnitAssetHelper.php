<?php

namespace App\Helpers;

use App\Models\UnitAssetModel;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UnitAssetHelper
{
    private $unitAssetModel;

    public function __construct()
    {
        $this->unitAssetModel = new UnitAssetModel();
    }

    public function create(array $payload): array
    {
        try {
            $unitAsset = $this->unitAssetModel->store($payload);

            return [
                'status' => true,
                'data' => $unitAsset
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
            $this->unitAssetModel->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $unitAssets = $this->unitAssetModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $unitAssets
        ];
    }

    public function getById(string $id): array
    {
        $unitAsset = $this->unitAssetModel->getById($id);
        if (empty($unitAsset)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $unitAsset
        ];
    }

    public function update(array $payload, string $id): array
    {
        try {

            $this->unitAssetModel->edit($payload, $id);

            $unitAsset = $this->getById($id);

            return [
                'status' => true,
                'data' => $unitAsset['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}
