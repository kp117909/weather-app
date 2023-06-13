<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function showWeather()
    {
        $data = file_get_contents('C:\Users\kpola\PhpstormProjects\cityList.json');

        $weatherData = json_decode($data, true);

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        $count = 0;

        foreach ($weatherData as $data) {
            if ($count >= 5) {
                break;
            }

            if (isset($data['coord']['lon']) && isset($data['coord']['lat'])) {
                $lat = $data['coord']['lat'];
                $lon = $data['coord']['lon'];

                $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&appid=$apiKey";
               // $apiUrl = "https://api.openweathermap.org/data/2.5/weather?slat=$lat&lon=$lon&appid=$apiKey";
                $apiResponse = file_get_contents($apiUrl);
                $apiData[] = json_decode($apiResponse, true);

                $count++;
            }
        }
//
//        var_dump($apiData);


        return view('welcome', compact('apiData'));
    }
}

