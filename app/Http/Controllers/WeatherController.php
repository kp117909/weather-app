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

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        $count = 0;

        $client = new Client();

        foreach ($weatherData as $data) {
            if ($count >= 100) {
                break;
            }

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

                $count++;
            }
        }

        $fav = Favorite::pluck('city_id')->all();

        return view('welcome', compact('apiData'  , 'fav'));
    }

    public function showFavorites($optional = null)
    {
        $data = file_get_contents('C:\Users\kpola\PhpstormProjects\cityList.json');

        $weatherData = json_decode($data, true);

        $apiKey = 'bdad5cfd1ab8a2389236eb82d988d992';
        $apiData = [];

        $count = 0;
        $fav = Favorite::pluck('city_id')->all();
        foreach ($weatherData as $data) {
            if ($count >= 50) {
                break;
            }
            if (in_array($data['id'], $fav)){
                if (isset($data['coord']['lon']) && isset($data['coord']['lat'])) {
                    $lat = $data['coord']['lat'];
                    $lon = $data['coord']['lon'];

                    $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&appid=$apiKey";
                    $apiResponse = file_get_contents($apiUrl);
                    $apiData[] = json_decode($apiResponse, true);

                    $count++;
                }
            }
        }

        return view('favorites', compact('apiData'));
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

