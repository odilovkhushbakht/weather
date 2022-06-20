<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityCollection;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'Integer|min:10|max:100'
        ]);
        if ($validator->fails()) {
            return [
                'Ошибка' => 'Валидация не пройдена',
                'limit' => 'Лимит параметра limit min 10 max 100',
            ];
        }
        $limitPage = $validator->validated();        

        return new CityCollection(City::where('country', 'ru')->orderBy('name')->paginate($limitPage['limit']));
    }
}
