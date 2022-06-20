<?php

namespace App\Service;

use App\Models\City;
use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class Weather implements WeatherInterface
{

    private $cityName = 'Пермь';
    private $typeTemp = 'metric';
    private $data = [];


    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function validate()
    {
        $validator = Validator::make($this->data, [
            'typeTemp' => 'String',
            'cityName' => 'required|String|max:50',
        ]);
        if ($validator->fails()) {
            return false;
        }
        $paramArray = $validator->validated();
        if (isset($paramArray['typeTemp']) and $paramArray['typeTemp'] == "f") {
            $this->typeTemp = 'imperial';
        } else {
            $this->typeTemp = 'metric';
        }

        if (!empty(trim($paramArray['cityName']))) {
            $this->cityName = trim($paramArray['cityName']);
        }
    }

    private function sendRequest()
    {
        $cityArray = City::where('name', $this->cityName)->get()->toArray();
        if (empty($cityArray)) {
            return ['Ошибка', 'Город не найден.'];
        }
        $params = [
            'lat' => $cityArray[0]['lat'],
            'lon' => $cityArray[0]['lon'],
            'lang' => 'ru',
            'cnt' => 1,
            'units' => $this->typeTemp,
            'appid' => env('WEATHER_API_KEY'),
        ];
        //$urlApi = 'http://api.openweathermap.org/data/2.5/weather';
        $urlApi = 'http://api.openweathermap.org/data/2.5/forecast';
        $response = Http::get($urlApi, $params);

        if ($response->status() == 200) {
            return $response->json();
        } else {
            return ['Ошибка', 'Повторите запрос.'];
        }
    }

    private function initArray()
    {
        $res = [];
        $urlImg = 'http://openweathermap.org/img/wn/';
        $res['city'] = $this->data['city']['name'];
        $res['temp'] = $this->data['list'][0]['main']['temp'];
        $res['humidity'] = $this->data['list'][0]['main']['humidity'];
        $res['pressure'] = $this->data['list'][0]['main']['pressure'];
        $res['speed'] = $this->data['list'][0]['wind']['speed'];
        $res['pop'] = $this->data['list'][0]['pop'];
        $res['pressure'] = $this->data['list'][0]['pop'];
        $res['desc'] = $this->data['list'][0]['weather'][0]['description'];
        $res['typeTemp'] = $this->typeTemp;

        switch ($this->data['list'][0]['weather'][0]['main']) {
            case 'Clouds':
                $urlImg .= 'cloud@2x.png';
                break;
            case 'Thunderstorm':
                $urlImg .= 'strom@2x.png';
                break;
            case 'Rain':
                $urlImg .= 'rain@2x.png';
                break;
            case 'Clear':
                $urlImg .= 'sun@2x.png';
                break;
            case 'Snow':
                $urlImg .= 'snow.png';
                break;
        }

        $res['icon'] = $urlImg;
        return $res;
    }

    public function getWeather(): array
    {
        if ($this->validate()) {
            return [
                'Ошибка' => 'Валидация не пройдена',
                'typeTemp' => 'Тип измерения тепература значения принимает "f" или "c" ',
                'cityName' => 'Имя города',
            ];
        }

        $response = $this->sendRequest();

        if (!isset($response['city']['name'])) {
            return $response;
        }
        $this->data = $response;

        return $this->initArray();
    }
}
