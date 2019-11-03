<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Manufacturer;
use App\Http\Resources\Manufacturer as ManufacturerResource;

use Validator;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->get('filters', []);

        if(isset($filters['beerType'])) {
            $beerType = $filters['beerType'];
            $manufacturers = Manufacturer::whereHas('beers', function($query) use ($beerType) {
                $query->whereHas('type', function($query) use ($beerType) {
                    $query->where('name', $beerType);
                });
             })->get();
        } else {
            $manufacturers = Manufacturer::all();
        }
        return ManufacturerResource::collection($manufacturers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $validator = $this->getValidator($requestData);

        if ($validator->fails()) {
            return $validator->errors()->toJson();
        }

        $manufacturer = Manufacturer::create($requestData);
        return new ManufacturerResource($manufacturer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ManufacturerResource(Manufacturer::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $validator = $this->getValidator($requestData);

        if ($validator->fails()) {
            return $validator->errors()->toJson();
        }

        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->fill($requestData);
        $manufacturer->save();

        return new ManufacturerResource($manufacturer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return response()->json([
            'isDeleted' => $manufacturer->delete()
        ]);
    }

    public function getValidator(array $fields)
    {
        return Validator::make($fields, [
            'name' => 'required|max:255|unique:manufacturers,name',
          ]);
    }
}
