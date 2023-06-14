<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Weather Data</title>
        <link href= "{{url('style.css')}}" rel = "stylesheet"/>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://kit.fontawesome.com/3133d360bd.js" crossorigin="anonyous"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

    </head>
    <body class="antialiased">
    @include('navigation')
    <div class="container">
        @php
            $count = 0;
        @endphp
        <div class="container">
            <div class="row p-3">
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <select id="weatherSelect" class="form-select chosen-select" aria-label="Select Weather">
                        <option value="" selected disabled>Select Weather</option>
                            @foreach($weatherData as $weather)
{{--                                @if($count > 500)--}}
{{--                                    @break--}}
{{--                                @endif--}}
                                @php
                                    $count++
                                @endphp
                                <option value="{{ $weather['id'] }}" data-lon="{{ $weather['coord']['lon'] }}" data-lat="{{ $weather['coord']['lat'] }}">{{ $weather['name'] }} [{{$weather['coord']['lat']}}, {{$weather['coord']['lon']}}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <button id="getCoordinatesButton" class="btn btn-primary">Get Data From Api</button>
                </div>
            </div>

            <table id = "tableMain" class="table">
                <thead>
                </thead>
                <tbody>
                <tr>
                    @php
                        $count = 0;
                        $countCurr = 0;
                    @endphp
                    @foreach($apiData as $weather)
                        @php
                            $count++;
                            $date = $weather['list'][0]['dt_txt'];
                            $dayName = date('l', strtotime($date));
                        @endphp
                        <td>
                            <h2 class="center">
                                {{ $weather['city']['name'] }}
                                <a href="#" class="favorite-icon" data-city-id="{{ $weather['city']['id'] }}">
                                    <i class="fas fa-star"></i>
                                </a>
                            </h2>
                            <div data-bs-toggle="modal" data-bs-target="#weatherModal{{$weather['city']['id']}}">
                                {{ $weather['list'][0]['weather'][0]['main'] }}
                                <img src="https://openweathermap.org/img/w/{{$weather['list'][0]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"><br>
                                Temperature {{ $weather['list'][0]['main']['temp'] }} °C<br>
                                {{ $weather['list'][0]['dt_txt'] }} <br>
                                {{ $dayName }}
                            </div>
                            @php
                                $countCurr++;
                            @endphp
                        </td>
                        @if ($count % 5 == 0)
                </tr><tr>
                    @endif
                    @endforeach
                </tr>

                @foreach($apiData as $weather)
                    <div class="modal fade" id="weatherModal{{ $weather['city']['id'] }}" tabindex="-1" aria-labelledby="weatherModalLabel{{ $weather['city']['id'] }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="weatherModalLabel{{ $weather['city']['id'] }}">{{ $weather['city']['name'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Main Weather: {{ $weather['list'][0]['weather'][0]['main'] }}</p>
                                    <p>Description: {{ $weather['list'][0]['weather'][0]['description'] }}</p>
                                    <p>Temperature: {{ $weather['list'][0]['main']['temp'] }} °C</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $countCurr = 0;
                    @endphp
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="weatherModalLabel">Weather Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>ID:</strong> <span id="modalId"></span></p>
                        <p><strong>Longitude:</strong> <span id="modalLon"></span></p>
                        <p><strong>Latitude:</strong> <span id="modalLat"></span></p>
                        <p><strong>Name:</strong> <span id="modalCityName"></span></p>
                        <p><strong>Temp:</strong> <span id="modalCityTemp"></span></p>
                        <p><strong>Humadity:</strong> <span id="modalCityHumadity"></span></p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id = "addFavorite" class="btn btn-secondary">Add To Favorite <i class="fas fa-star"></i> </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                $('.chosen-select').chosen();

                $('#getCoordinatesButton').click(function() {
                    var selectedOption = $('#weatherSelect option:selected');
                    var selectedId = selectedOption.val();
                    var selectedLon = selectedOption.data('lon');
                    var selectedLat = selectedOption.data('lat');

                    $.ajax({
                        url: '{{route("getCurrentCity")}}', // Tutaj podaj odpowiedni URL do Twojego kontrolera WeatherController
                        type: 'GET', // Określ metodę żądania, na przykład POST lub GET
                        data: {
                            id: selectedId,
                            lon: selectedLon,
                            lat: selectedLat
                        },
                        success: function(response) {

                            $('#modalId').text(response[0].city.id);
                            $('#modalLon').text(response[0].city.coord.lat);
                            $('#modalLat').text(response[0].city.coord.lon);
                            $('#modalCityName').text(response[0].city.name);
                            $('#modalCityTemp').text(response[0].list[0].main.temp);
                            $('#modalCityHumadity').text(response[0].list[0].main.humidity);
                            @foreach($fav as $favorites)
                                if({{$favorites}} === response[0].city.id){
                                    $('#addFavorite').html('Remove From Favorite <i class="fas fa-star"></i>');
                                }else{
                                  $('#addFavorite').html('Add To Favorite <i class="far fa-star"></i>');
                                }
                            @endforeach

                            $('#weatherModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });

                });
            });

            $('#addFavorite').click(function(event) {
                event.preventDefault();

                var cityId = $('#modalId').text();

                $.ajax({
                    url: '{{ route("favorites.add") }}',
                    method: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        Swal.fire({
                            title: response.message,
                            text: "Weather App",
                            icon: response.icon,
                            showConfirmButton: false
                        })
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: xhr.responseJSON.message,
                            text: "Weather App",
                            icon: "warning",
                            showConfirmButton: false
                        })
                    }
                });
            });

            $('.favorite-icon').click(function(event) {
                event.preventDefault();

                var cityId = $(this).data('city-id');

                $.ajax({
                    url: '{{ route("favorites.add") }}',
                    method: 'GET',
                    data: {
                        city_id: cityId
                    },
                    success: function(response) {
                        if(response.type === "add"){
                            $(event.target).removeClass('fa-regular fa-star').addClass('fas fa-star');
                        }else if(response.type === 'delete')
                            $(event.target).closest('td').remove();
                        else{
                            $(event.target).removeClass('fas fa-star').addClass('fa-regular fa-star');
                        }
                        Swal.fire({
                            title: response.message,
                            text: "Weather App",
                            icon: response.icon,
                            showConfirmButton: false
                        })
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: xhr.responseJSON.message,
                            text: "Weather App",
                            icon: "warning",
                            showConfirmButton: false
                        })
                    }
                });
            });

        </script>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

    </body>
</html>
