<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Beer;
use App\Http\Resources\Beer as BeerResource;

use Validator;

class BeerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $relationFields = [
            'typeName' => ['beer_types', 'name', 'type_id'],
            'manufacturerName' => ['manufacturers', 'name', 'manufacturer_id']
          ];

        $filters = $request->get('filters', []);
        $query = Beer::with($this->getRelations());

        $selectArr = ['beers.*'];

        foreach($filters as $filterField => $filterValue) {
            if(isset($relationFields[$filterField])) {
                $relationField = $relationFields[$filterField];
                $selectArr[]= "{$relationField[0]}.{$relationField[1]}";
                $query
                    ->leftJoin($relationField[0], "beers.{$relationField[2]}", '=', "{$relationField[0]}.id")
                    ->where("{$relationField[0]}.{$relationField[1]}", $filterValue);
            }
        }

        $query->selectRaw(implode(', ', $selectArr));

        return BeerResource::collection($query->get());
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

        $beer = Beer::create($requestData);
        $beer->load($this->getRelations());
        return new BeerResource($beer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new BeerResource(Beer::with($this->getRelations())->find($id));
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
        $validator = $this->getValidator($requestData, 'update');

        if ($validator->fails()) {
            return $validator->errors()->toJson();
        }

        $beer = Beer::findOrFail($id);
        $beer->fill($requestData);
        $beer->save();

        $beer->load($this->getRelations());

        return new BeerResource($beer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $beer = Beer::findOrFail($id);

        return response()->json([
            'isDeleted' => $beer->delete()
        ]);
    }

    public function getValidator(array $fields, string $scenario = 'store')
    {
        $rules =  [
            'name' => 'nullable|max:255|unique:beers,name',
            'description' => 'nullable|max:65535',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'type_id' => 'nullable|exists:beer_types,id',
        ];

        if($scenario == 'store'){
            $rules['name'] = 'required|max:255|unique:beers,name';
        }
        return Validator::make($fields, $rules);
    }

    public function getRelations(): array
    {
        return ['type:id,name', 'manufacturer:id,name'];
    }
}
