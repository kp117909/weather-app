<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class WeatherController extends Controller
{
    public function showWeather()
    {
        $data = file_get_contents('C:\Users\kpola\PhpstormProjects\cityList.json');
        $weatherData = json_decode($data, true);

        $filteredWeatherData = [];
        foreach ($weatherData as $weather) {
            if ($weather['country'] === 'PL') {
                $filteredWeatherData[] = $weather;
            }
        }
        $fav = Favorite::pluck('city_id')->all();
        $weatherData = $filteredWeatherData;
        $client = new Client();

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        foreach ($filteredWeatherData as $data) {
            if (in_array($data['id'], $fav)){
                if (isset($data['coord']['lon']) && isset($data['coord']['lat'])) {
                    $lat = $data['coord']['lat'];
                    $lon = $data['coord']['lon'];

                    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast";
                    $response = $client->request('GET', $apiUrl, [
                        'query' => [
                            'lat' => $lat,
                            'lon' => $lon,
                            'appid' => $apiKey,
                            'units' => 'metric'
                        ]
                    ]);
                    $apiData[] = json_decode($response->getBody(), true);
                }
            }
        }
        return view('welcome', compact('weatherData'  , 'fav', 'apiData'));
    }

    public function showFavorites($optional = null)
    {
        $data = file_get_contents('C:\Users\kpola\PhpstormProjects\cityList.json');
        $weatherData = json_decode($data, true);

        $fav = Favorite::pluck('city_id')->all();

        $client = new Client();

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        foreach ($weatherData as $data) {
            if (in_array($data['id'], $fav)){
                if (isset($data['coord']['lon']) && isset($data['coord']['lat'])) {
                    $lat = $data['coord']['lat'];
                    $lon = $data['coord']['lon'];

                    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast";
                    $response = $client->request('GET', $apiUrl, [
                        'query' => [
                            'lat' => $lat,
                            'lon' => $lon,
                            'appid' => $apiKey,
                            'units' => 'metric'
                        ]
                    ]);
                    $apiData[] = json_decode($response->getBody(), true);
                }
            }
        }

        return view('favorites', compact('apiData'));
    }

    public function getCurrentCity(Request $request)
    {
        $selectedId = $request->input('selectedId');
        $lon = $request->input('lon');
        $lat = $request->input('lat');

        $client = new Client();

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        if (isset($lon) && isset($lat)) {
            $apiUrl = "https://api.openweathermap.org/data/2.5/forecast";
            $response = $client->request('GET', $apiUrl, [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]
            ]);
            $apiData[] = json_decode($response->getBody(), true);
        }

        return response()->json($apiData);
    }

    public function addFavorite(Request $request)
    {
        $cityId = $request->input('city_id');

        if (Favorite::where('city_id', $cityId)->exists()) {
            Favorite::where('city_id', $cityId)->delete();
            return response()->json(['message' => 'City removed from favorites.', 'type' => 'delete', 'icon'=> 'success']);
        }

        $favoriteCount = Favorite::count();
        if ($favoriteCount >= 10) {
            return response()->json(['message' => 'You can only have a maximum of 10 favorite cities.', 'type' => 'error', 'icon' => 'error']);
        }

        Favorite::create([
            'city_id' => $cityId,
        ]);

        return response()->json(['message' => 'City added to favorites.', 'type' => 'add', 'icon' => 'success']);
    }
}

