<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->query('city', 'Surabaya'); 

        $weatherRes = Http::get("http://api.weatherapi.com/v1/forecast.json", [
            'key' => env('WEATHER_API_KEY'), 
            'q' => $city,
            'days' => 3,
            'aqi' => 'yes'
        ]);

        $imageRes = Http::get("https://api.unsplash.com/photos/random", [
            'client_id' => env('UNSPLASH_ACCESS_KEY'), 
            'query' => $city . " city",
            'orientation' => 'landscape'
        ]);

        $weatherData = $weatherRes->json();
        
        $imageData = $imageRes->json();
        $backgroundImage = $imageData['urls']['regular'] ?? null;

        return view('weather', compact('weatherData', 'backgroundImage'));
    }
}