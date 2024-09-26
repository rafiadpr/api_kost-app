<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Unit\UnitCollection;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Requests\Unit\UnitRequest;
use App\Helpers\UnitHelper;
use App\Models\UnitModel;
use App\Models\ImageModel;

class UnitController extends Controller
{
    private $unit;
    public function __construct()
    {
        $this->unit = new UnitHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'price' => $request->price ?? '',
        ];
        $units = $this->unit->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->json(new UnitCollection($units['data']));
    }

    public function show($id)
    {
        $unit = $this->unit->getById($id);

        if (!($unit['name'])) {
            return response()->json(['Data unit tidak ditemukan'], 404);
        }
        return response()->json(['data' => new UnitResource($unit['data'])], 200);
    }

    public function store(Request $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }

        $payload = $request->only(['unit_category_id', 'name', 'price', 'details']);
        $payload['images'] = $request->file('images');
        $unit = $this->unit->create($payload);

        if (!$unit['status']) {
            return response()->json($unit['error'], 400);
        }

        return response()->json([
            'message' => 'unit berhasil ditambah',
            'data' => new UnitResource($unit['data']),
        ], 200);
    }

    public function update(UnitRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only(['unit_category_id', 'name', 'price', 'details', 'details_deleted']);
        $unit = $this->unit->update($payload, $request->id);

        if (!$unit['status']) {
            return response()->json($unit['error'], 400);
        }

        return response()->json([
            'message' => 'unit berhasil diubah',
            'data' => new UnitResource($unit['data']),
        ], 200);
    }

    public function destroy($id)
    {
        $unit = $this->unit->delete($id);

        if (!$unit) {
            return response()->json(['Mohon maaf data pengguna tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'unit berhasil dihapus',
            'data' => $unit,
        ], 200);
    }
}
