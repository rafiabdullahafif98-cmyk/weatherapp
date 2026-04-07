<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Memproses permintaan cuaca dan menampilkan view.
     */
    public function index(Request $request)
    {
        // 1. Ambil input kota dari user (Request)
        $city = $request->query('city', 'Surabaya'); // Lokasi default Surabaya

        // 2. Request ke WeatherAPI (Endpoint: forecast.json)
        // Kita mengambil paket lengkap: Cuaca Saat Ini & Forecast 3 Hari
        $weatherRes = Http::get("http://api.weatherapi.com/v1/forecast.json", [
            'key' => env('WEATHER_API_KEY'), // Authentication via API Key
            'q' => $city,
            'days' => 3, // Mengambil perkiraan untuk 3 hari
            'aqi' => 'yes' // Mengambil data kualitas udara
        ]);

        // 3. Request ke Unsplash API untuk Background Foto (Optional Service Integration)
        $imageRes = Http::get("https://api.unsplash.com/photos/random", [
            'client_id' => env('UNSPLASH_ACCESS_KEY'), // Authentication via Access Key
            'query' => $city . " city",
            'orientation' => 'landscape'
        ]);

        // 4. Menerima REST Response dalam format JSON
        $weatherData = $weatherRes->json();
        
        // Mengambil URL gambar latar belakang dari response Unsplash
        $imageData = $imageRes->json();
        $backgroundImage = $imageData['urls']['regular'] ?? null;

        // 5. Mengirimkan data ke View (Representasi Resource)
        return view('weather', compact('weatherData', 'backgroundImage'));
    }
}