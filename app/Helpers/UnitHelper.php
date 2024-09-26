<?php

namespace App\Helpers;

use App\Models\UnitModel;
use App\Models\UnitDetailModel;
use App\Models\ImageModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class UnitHelper
{
    const UNIT_PHOTO_DIRECTORY = 'foto-unit';
    private $unit;
    private $unitDetail;
    private $image;

    public function __construct()
    {
        $this->unit = new UnitModel();
        $this->unitDetail = new UnitDetailModel();
        $this->image = new ImageModel();
    }

    private function uploadAndGetPayload(array $payload)
    {
        // Handle multiple images
        if (!empty($payload['images']) && is_array($payload['images'])) {
            $uploadedImages = [];
            foreach ($payload['images'] as $imageFile) {
                if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = $this->generateFileName($imageFile, 'UNIT_' . date('Ymdhis'));
                    $filePath = $imageFile->storeAs(self::UNIT_PHOTO_DIRECTORY, $fileName, 'public');
                    $uploadedImages[] = $filePath;  // Store file paths in an array
                }
            }
            $payload['images'] = $uploadedImages;  // Return the file paths
        } else {
            unset($payload['images']);
        }

        return $payload;
    }

    public function create(array $payload): array
    {
        DB::beginTransaction(); // Begin transaction
    
        try {
            $payload = $this->uploadAndGetPayload($payload);
            $unit = $this->unit->store($payload);
    
            // Insert or update the unit details
            $this->insertUpdateDetail($payload['details'] ?? [], $unit->id);
    
            // Insert the uploaded image URLs into the database
            if (!empty($payload['images'])) {
                foreach ($payload['images'] as $imagePath) {
                    $this->image->create([
                        'url' => $imagePath,  // Store image path, not file object
                        'unit_id' => $unit->id
                    ]);
                }
            }
    
            DB::commit(); // Commit transaction
    
            return [
                'status' => true,
                'data' => $unit
            ];
        } catch (Throwable $th) {
            DB::rollBack(); // Rollback transaction on error
    
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
    public function delete(string $unitId)
    {
        DB::beginTransaction(); // Begin transaction

        try {
            $this->unit->drop($unitId);
            $this->unitDetail->dropByUnitId($unitId);

            DB::commit(); // Commit transaction

            return [
                'status' => true,
                'data' => $unitId
            ];
        } catch (Throwable $th) {
            DB::rollBack(); // Rollback transaction on error

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $unit = $this->unit->getAll($filter, $itemPerPage, $sort);

        // dd($unit);

        return [
            'status' => true,
            'data' => $unit
        ];
    }

    public function getById(string $id): array
    {
        $unit = $this->unit->getById($id);
        // dd($unit);
        if (empty($unit)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $unit
        ];
    }

    private function deleteDetail(array $details)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            if (isset($val['is_deleted']) && $val['is_deleted']) {
                $this->unitDetail->drop($val['id']);
            }
        }
    }

    public function update(array $payload): array
    {
        DB::beginTransaction(); // Begin transaction

        try {
            $payload = $this->uploadAndGetPayload($payload);
            $this->unit->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $unit = $this->getById($payload['id']);

            DB::commit(); // Commit transaction

            return [
                'status' => true,
                'data' => $unit['data']
            ];
        } catch (Throwable $th) {
            DB::rollBack(); // Rollback transaction on error

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function insertUpdateDetail(array $details, string $unitId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['unit_id'] = $unitId;
                $this->unitDetail->store($val);
            }

            // Update
            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->unitDetail->edit($val, $val['id']);
            }
        }
    }

    private function generateFileName($file, $prefix)
    {
        $extension = $file->getClientOriginalExtension();
        return $prefix . '.' . $extension;
    }
}
