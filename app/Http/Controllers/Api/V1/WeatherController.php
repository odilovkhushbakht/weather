<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $params = [
            'data' => $request->all(),
        ];
        $resArray = App::make('App\Service\Weather', $params)->getWeather();
        return response()->json($resArray);
    }
}
