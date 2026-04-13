<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Pro - Compact Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; letter-spacing: -0.01em; }
        .glass { background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(20px) saturate(180%); border: 1px solid rgba(255, 255, 255, 0.2); }
        .inner-glass { background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.05); }
        .text-glow { text-shadow: 0 0 12px rgba(255, 255, 255, 0.4); }
        #map { height: 160px; border-radius: 1.5rem; margin-top: 1rem; border: 1px solid rgba(255,255,255,0.1); }
        
        /* Custom Scrollbar untuk Suggestion Box */
        #suggestion-box::-webkit-scrollbar { width: 5px; }
        #suggestion-box::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-cover bg-center transition-all duration-1000 relative" 
      style="background-image: url('{{ $backgroundImage ?? 'https://images.unsplash.com/photo-1504608524841-42fe6f032b4b' }}')">
    
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

    <div class="relative z-10 glass p-6 rounded-[2.5rem] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] w-full max-w-[21.5rem] text-white">
        
        <form action="/" method="GET" class="relative mb-5 group">
            <div class="relative">
                <input type="text" name="city" id="city-input" autocomplete="off" placeholder="Search city..." 
                       class="w-full py-2.5 px-5 rounded-full bg-white/10 border border-white/10 focus:outline-none focus:ring-1 focus:ring-white/40 placeholder-white/50 text-xs">
                
                <div id="suggestion-box" class="absolute left-0 right-0 mt-2 hidden glass rounded-2xl overflow-hidden z-50 text-left shadow-2xl max-h-48 overflow-y-auto">
                    </div>
            </div>
            
            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-white text-indigo-600 p-2 rounded-full hover:scale-105 transition shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </form>

        @if(isset($weatherData['current']))
            <div class="text-center">
                <div class="mb-1">
                    <h2 class="text-xl font-bold text-glow">{{ $weatherData['location']['name'] }}</h2>
                    <p class="text-[9px] font-semibold opacity-50 uppercase tracking-[0.2em]">{{ $weatherData['location']['region'] }}, {{ $weatherData['location']['country'] }}</p>
                </div>
                
                <div class="flex flex-col items-center">
                    <img src="https:{{ $weatherData['current']['condition']['icon'] }}" alt="Icon" class="w-20 h-20 drop-shadow-lg">
                    <div class="text-6xl font-bold tracking-tighter text-glow -mt-4">{{ round($weatherData['current']['temp_c']) }}°</div>
                    <p class="text-xs font-bold mt-1 uppercase tracking-wider opacity-80">{{ $weatherData['current']['condition']['text'] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2.5 mt-6">
                    <div class="inner-glass p-3 rounded-2xl">
                        <p class="text-[8px] opacity-50 uppercase font-bold tracking-widest mb-0.5">Humidity</p>
                        <p class="text-xl font-bold">{{ $weatherData['current']['humidity'] }}%</p>
                    </div>
                    <div class="inner-glass p-3 rounded-2xl">
                        <p class="text-[8px] opacity-50 uppercase font-bold tracking-widest mb-0.5">Wind Speed</p>
                        <p class="text-xl font-bold">{{ round($weatherData['current']['wind_kph']) }} <span class="text-[10px] font-normal opacity-70">km/h</span></p>
                    </div>
                </div>
            </div>

            <div id="map" class="shadow-inner"></div>

            @if(isset($weatherData['forecast']['forecastday']))
                <div class="mt-6 pt-5 border-t border-white/10">
                    <h3 class="text-[9px] font-bold opacity-30 uppercase tracking-[0.2em] text-center mb-4 italic">Next 3 Days</h3>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($weatherData['forecast']['forecastday'] as $day)
                            <div class="inner-glass py-2.5 px-1 rounded-2xl text-center border border-white/5">
                                <p class="text-[9px] font-bold opacity-50 mb-1">
                                    {{ \Carbon\Carbon::parse($day['date'])->format('D') }}
                                </p>
                                <img src="https:{{ $day['day']['condition']['icon'] }}" class="w-8 h-8 mx-auto mb-1">
                                <p class="text-sm font-bold">{{ round($day['day']['avgtemp_c']) }}°</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        @elseif(isset($weatherData['error']))
            <div class="text-center py-6 px-4 inner-glass rounded-2xl border-red-500/30 animate-pulse">
                <p class="text-xs font-medium text-red-200 italic">Oops! Kota tidak ditemukan.</p>
            </div>
        @else
            <div class="text-center py-10 opacity-30 italic text-sm tracking-widest">
                <p>Awaiting search...</p>
            </div>
        @endif
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- 1. LOGIC PETA (Leaflet) ---
        @if(isset($weatherData['location']))
            var lat = {{ $weatherData['location']['lat'] }};
            var lon = {{ $weatherData['location']['lon'] }};
            var cityName = "{{ $weatherData['location']['name'] }}";

            var map = L.map('map', { zoomControl: false }).setView([lat, lon], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            L.marker([lat, lon]).addTo(map)
                .bindPopup(cityName)
                .openPopup();
        @endif

        // --- 2. LOGIC DYNAMIC SEARCH (Autocomplete) ---
        const cityInput = document.getElementById('city-input');
        const suggestionBox = document.getElementById('suggestion-box');
        const apiKey = "{{ env('WEATHER_API_KEY') }}"; 

        cityInput.addEventListener('input', async function() {
            const query = this.value;

            if (query.length < 3) {
                suggestionBox.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`https://api.weatherapi.com/v1/search.json?key=${apiKey}&q=${query}`);
                const cities = await response.json();

                if (cities.length > 0) {
                    suggestionBox.innerHTML = ''; 
                    cities.forEach(city => {
                        const item = document.createElement('div');
                        item.className = 'px-5 py-2 hover:bg-white/20 cursor-pointer text-xs border-b border-white/5 last:border-none';
                        item.innerHTML = `<strong>${city.name}</strong>, <span class="opacity-60">${city.country}</span>`;
                        
                        item.onclick = () => {
                            cityInput.value = city.name;
                            suggestionBox.classList.add('hidden');
                            cityInput.closest('form').submit(); 
                        };
                        suggestionBox.appendChild(item);
                    });
                    suggestionBox.classList.remove('hidden');
                } else {
                    suggestionBox.classList.add('hidden');
                }
            } catch (error) {
                console.error("Gagal memuat saran:", error);
            }
        });

        document.addEventListener('click', function(e) {
            if (!cityInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('hidden');
            }
        });
    </script>
</body>
</html>