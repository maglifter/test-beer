<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\BeerType;
use App\Http\Resources\BeerType as BeerTypeResource;

use Validator;

class BeerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BeerTypeResource::collection(BeerType::all());
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

        $beerType = BeerType::create($requestData);
        return new BeerTypeResource($beerType);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new BeerTypeResource(BeerType::find($id));
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

        $beerType = BeerType::findOrFail($id);
        $beerType->fill($requestData);
        $beerType->save();

        return new BeerTypeResource($beerType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $beerType = BeerType::findOrFail($id);

        return response()->json([
            'isDeleted' => $beerType->delete()
        ]);
    }

    public function getValidator(array $fields)
    {
        return Validator::make($fields, [
            'name' => 'required|max:255|unique:beer_types,name',
          ]);
    }
}
