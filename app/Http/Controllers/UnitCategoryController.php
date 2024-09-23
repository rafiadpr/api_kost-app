<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\UnitCategoryHelper;
use App\Http\Resources\Unit\CategoryCollection;
use App\Http\Resources\Unit\CategoryResource;
use App\Http\Requests\Unit\CategoryRequest;

class UnitCategoryController extends Controller
{
    private $category;

    public function __construct()
    {
        $this->category = new UnitCategoryHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
        ];
        $categories = $this->category->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->json(new CategoryCollection($categories['data']));
    }

    public function show($id)
    {
        $category = $this->category->getById($id);

        if (!($category['status'])) {
            return response()->json(['Data category tidak ditemukan'], 404);
        }

        return response()->json(new CategoryResource($category['data']), 200);
    }

    public function store(CategoryRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors(), 422);
        }

        $payload = $request->only(['name', 'type']);
        $category = $this->category->create($payload);

        if (!$category['status']) {
            return response()->json($category['error']);
        }

        return response()->json([
            'message' => 'category berhasil ditambah',
            'data' => new CategoryResource($category['data']),
        ], 200);
    }

    public function update(CategoryRequest $request)
    {

        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->errors());
        }

        $payload = $request->only(['name', 'id', 'type']);
        $category = $this->category->update($payload, $request->id);

        if (!$category['status']) {
            return response()->json($category['error']);
        }

        return response()->json([
            'message' => 'category berhasil diubah',
            'data' => new CategoryResource($category['data']),
        ], 200);
    }

    public function destroy($id)
    {
        $category = $this->category->delete($id);

        if (!$category) {
            return response()->json(['Mohon maaf category tidak ditemukan']);
        }

        return response()->json([
            'message' => 'category berhasil dihapus',
            'data' => $category,
        ], 200);
    }
}
