<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UnitModel;
use App\Models\ImageModel;

class UnitController extends Controller
{
    public function addUnit(Request $request)
    {
        $this->validate($request, [
            'unit_category_id' => 'required|string|max:855',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',  // Correct 'double' to 'numeric'
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  // Add validation for images
        ]);

        $unit = new UnitModel;
        $unit->unit_category_id = $request->unit_category_id;
        $unit->name = $request->name;
        $unit->price = $request->price;
        $unit->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imagefile) {
                $image = new ImageModel;
                $path = $imagefile->store('/images/resource', ['disk' => 'my_files']);
                $image->url = $path;
                $image->unit_id = $unit->id;
                $image->save();
            }
        }

        return redirect()->back()->with('success', 'Unit and images uploaded successfully.');
    }
}
