<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{__('Weather Data')}}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://kit.fontawesome.com/3133d360bd.js" crossorigin="anonyous"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

    </head>
    <body class="antialiased">

    @include('navigation')
    <div class="container">
        <div class="container">
            <div class="row p-3">
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <select id="weatherSelect" class="form-select chosen-select" aria-label="Select Weather">
                        <option value="none" selected disabled>{{__('Select Weather')}}</option>
                            @foreach($weatherData as $weather)
                                <option value="{{ $weather['id'] }}" data-lon="{{ $weather['coord']['lon'] }}" data-lat="{{ $weather['coord']['lat'] }}">{{ $weather['name'] }} [{{$weather['coord']['lat']}}, {{$weather['coord']['lon']}}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <button id="getCoordinatesButton" data-url="{{ route('getCurrentCity') }}" class="btn btn-primary">Get Data From Api Here</button>
                </div>
            </div>

            <div id="weather-table" class="row">
                @foreach($apiData as $weather)
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title d-flex justify-content-between align-items-center">
                                    <span>{{ $weather['city']['name'] }} <img src="https://openweathermap.org/img/w/{{$weather['list'][0]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"></span>
                                    <a href="#" class="remove-from-fav" data-url = "{{ route("favorites.add") }}" data-city-id="{{ $weather['city']['id'] }}">
                                        <i class="fa-solid fa-delete-left"></i>
                                    </a>
                                </h2>
                                <div class="card-text">
                                    <p>{{__('Main Weather:')}} {{ $weather['list'][0]['weather'][0]['main'] }}</p>
                                    <p>{{__('Description:')}} {{ $weather['list'][0]['weather'][0]['description'] }}</p>
                                    <p>{{__('Temperature:')}} {{ $weather['list'][0]['main']['temp'] }} °C</p>
                                    <p>{{__('Humidity:')}} {{ $weather['list'][0]['main']['humidity'] }}</p>
                                </div>
                                <div class="card-footer">
                                    {{__('Show Detailed Statistic:')}}
                                        <a href="#" class="charts" data-bs-toggle="modal" data-bs-target="#weatherModal{{$weather['city']['id']}}">
                                            <i class="fa-solid fa-chart-line fa-xl"></i>
                                        </a>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade fav" data-id="{{ $weather['city']['id'] }}"  id="weatherModal{{ $weather['city']['id'] }}" tabindex="-1" aria-labelledby="weatherModalLabel{{ $weather['city']['id'] }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="weatherModalLabel{{ $weather['city']['id'] }}">{{ $weather['city']['name'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p> {{__('Main Weather:')}} {{ $weather['list'][0]['weather'][0]['main'] }}</p>
                                    <p> {{__('Description:')}} {{ $weather['list'][0]['weather'][0]['description'] }}      <img src="https://openweathermap.org/img/w/{{$weather['list'][0]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"></p>
                                    <p> {{__('Temperature:')}} {{ $weather['list'][0]['main']['temp'] }} {{__('°C')}}</p>
                                    <p> {{__('Humidity:')}} {{ $weather['list'][0]['main']['humidity'] }}</p>
                                </div>
                                <canvas id="weatherChart" ></canvas>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" data-url = "{{ route("favorites.addToSql") }}" id = "addFavToSql" class="btn btn-secondary">{{__('Add Fav to Sql | // DEV')}}  <i class="fas fa-star"></i> </button>
        </div>

        <div class="modal fade" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLa bel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="weatherModalLabel">{{__('Weather Data')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{__('ID:')}}</strong> <span id="modalId"></span></p>
                        <p><strong>{{__('Longitude:')}}</strong> <span id="modalLon"></span></p>
                        <p><strong>{{__('Latitude:')}}</strong> <span id="modalLat"></span></p>
                        <p><strong>{{__('Name:')}}</strong> <span id="modalCityName"></span></p>
                        <p><strong>{{__('Temp:')}}</strong> <span id="modalCityTemp"></span></p>
                        <p><strong>{{__('Humidity:')}}</strong> <span id="modalCityHumadity"></span></p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id = "addFavorite"  data-url = "{{ route("favorites.add") }}" class="btn btn-secondary">{{__('Add To Favorite ')}}<i class="fas fa-star"></i> </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="footer">
        <div id="app" data-weather-data="{{ json_encode($favData) }}"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

    @vite(['resources/js/app.js'])


    </body>
</html>
