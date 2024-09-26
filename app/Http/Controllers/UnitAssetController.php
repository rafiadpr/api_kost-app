<?php

namespace App\Http\Controllers;

use App\Helpers\UnitAssetHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\AssetRequest;
use App\Http\Resources\Unit\AssetCollection;
use App\Http\Resources\Unit\AssetResource;
use Illuminate\Http\Request;

class UnitAssetController extends Controller
{
    private $unitAsset;

    public function __construct()
    {
        $this->unitAsset = new UnitAssetHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'description' => $request->description ?? '',
        ];
        $unitAssets = $this->unitAsset->getAll($filter, 5, $request->sort ?? '');

        return response()->json(new AssetCollection($unitAssets['data']));
    }

    public function show(string $id)
    {
        $unitAsset = $this->unitAsset->getById($id);

        if (!($unitAsset['status'])) {
            return response()->json(['Data unit asset tidak ditemukan'], 404);
        }
        return response()->json(new AssetResource($unitAsset['data']), 200);
    }

    public function store(AssetRequest $request)
    {
        // dd($request->all());
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }
        $payload = $request->only(['description', 'name', 'unit_id']);
        $unitAsset = $this->unitAsset->create($payload);

        if (!$unitAsset['status']) {
            return response()->json($unitAsset['error']);
        }

        return response()->json([
            'message' => 'unit asset berhasil ditambah',
            'data' => new AssetResource($unitAsset['data']),
        ], 200);
    }

    public function update(AssetRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only(['description', 'name', 'unit_id']);
        $unitAsset = $this->unitAsset->update($payload, $request->id);

        if (!$unitAsset['status']) {
            return response()->json($unitAsset['error'], 400);
        }

        return response()->json([
            'message' => 'unit asset berhasil diubah',
            'data' => new AssetResource($unitAsset['data']),
        ], 200);
    }

    public function destroy(string $id)
    {
        $unitAsset = $this->unitAsset->delete($id);

        if (!$unitAsset) {
            return response()->json(['Mohon maaf data unit asset tidak ditemukan']);
        }

        return response()->json([
            'message' => 'unit asset berhasil dihapus',
            'data' => $unitAsset,
        ], 200);
    }
}
