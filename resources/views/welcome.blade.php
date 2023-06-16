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
        <div class="container">
            <div class="row p-3">
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <select id="weatherSelect" class="form-select chosen-select" aria-label="Select Weather">
                        <option value="none" selected disabled>Select Weather</option>
                            @foreach($weatherData as $weather)
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
                <tbody id="weather-table">
                <tr id = "weather-row">
                    @foreach($apiData as $weather)
                        @php
                            $date = $weather['list'][0]['dt_txt'];
                            $dayName = date('l', strtotime($date));
                        @endphp
                        <td class="weather-cell">
                            <h2 class="center">
                                {{ $weather['city']['name'] }}
                                <a href="#" class="favorite-icon"  data-bs-toggle="tooltip" title="Delete" data-bs-placement="top" data-city-id="{{ $weather['city']['id'] }}">
                                    <i class="fa-solid fa-delete-left"></i>
                                </a>
                                <a href="#" class="charts" data-bs-toggle="modal"  data-bs-target="#weatherModal{{$weather['city']['id']}}">
                                    <i class="fa-solid fa-chart-line"></i>
                                </a>
                            </h2>
                            <div>
                                {{ $weather['list'][0]['weather'][0]['main'] }}
                                <img src="https://openweathermap.org/img/w/{{$weather['list'][0]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"><br>
                                Temperature {{ $weather['list'][0]['main']['temp'] }} °C<br>
                                {{ $weather['list'][0]['dt_txt'] }} <br>
                                {{ $dayName }}
                            </div>
                        </td>
                    @endforeach
                </tr>

                @foreach($apiData as $weather)
                    <div class="modal fade fav" data-id="{{ $weather['city']['id'] }}"  id="weatherModal{{ $weather['city']['id'] }}" tabindex="-1" aria-labelledby="weatherModalLabel{{ $weather['city']['id'] }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="weatherModalLabel{{ $weather['city']['id'] }}">{{ $weather['city']['name'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Main Weather: {{ $weather['list'][0]['weather'][0]['main'] }}</p>
                                    <p>Description: {{ $weather['list'][0]['weather'][0]['description'] }}      <img src="https://openweathermap.org/img/w/{{$weather['list'][0]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"></p>
                                    <p>Temperature: {{ $weather['list'][0]['main']['temp'] }} °C</p>
                                    <p>Humidity: {{ $weather['list'][0]['main']['humidity'] }}</p>
                                </div>
                                <canvas id="weatherChart" ></canvas>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
            </table>
            <button type="button" id = "addFavToSql" class="btn btn-secondary">Add Fav to Sql | // DEV  <i class="fas fa-star"></i> </button>
        </div>

        <div class="modal fade" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLa bel" aria-hidden="true">
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
                        <p><strong>Humidity:</strong> <span id="modalCityHumadity"></span></p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" id = "addFavorite" class="btn btn-secondary">Add To Favorite <i class="fas fa-star"></i> </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(window).on('resize', function() {
                initializeChosenSelect();
            });

            function initializeChosenSelect() {
                $('.chosen-select').chosen();
            }

            $(document).ready(function() {
                window.addEventListener('load', rearrangeCells);
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


            $('#addFavToSql').click(function(event) {
                event.preventDefault();

                $.ajax({
                    url: '{{ route("favorites.addToSql") }}',
                    method: 'GET',
                    data: {
                    },
                    success: function(response) {
                        Swal.fire({
                            title: response.message,
                            text: "Weather App",
                            icon: success,
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

                        setTimeout(function() {
                            location.reload();
                        }, 1250);
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

            // Remove from favorite
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
                        if (response.type === 'delete'){
                            $(event.target).closest('td').remove();
                            rearrangeCells();
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

            function rearrangeCells() {
                var screenWidth = document.documentElement.clientWidth || document.body.clientWidth;
                var maxCellsPerRow = 5;

                if (screenWidth <= 200) {
                  maxCellsPerRow =  1;
                } else if (screenWidth <= 500) {
                    maxCellsPerRow = 2;
                } else if (screenWidth <= 900) {
                    maxCellsPerRow = 3;
                }

                var $weatherTable = document.getElementById('weather-table');
                var $weatherCells = Array.from($weatherTable.querySelectorAll('.weather-cell'));

                $weatherTable.innerHTML = '';

                for (var i = 0; i < $weatherCells.length; i += maxCellsPerRow) {
                    var $row = document.createElement('tr');
                    var cells = $weatherCells.slice(i, i + maxCellsPerRow);

                    cells.forEach(function(cell) {
                        $row.appendChild(cell);
                    });

                    $weatherTable.appendChild($row);
                }
            }

            window.addEventListener('DOMContentLoaded', rearrangeCells);
            window.addEventListener('resize', rearrangeCells);
            window.addEventListener('load', rearrangeCells);
        </script>
    </div>

    <div class="footer">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Charts
        var modals = document.querySelectorAll('.modal.fav');
        var weatherData = {!! json_encode($favData) !!};

        modals.forEach(function(modal) {
            var weatherChart = modal.querySelector('#weatherChart');
            var id = modal.getAttribute('data-id');
            var filteredWeatherData = weatherData.filter(function(data) {
                return parseInt(data.id_city) === parseInt(id);
            }).map(function(data) {
                var date = new Date(data.created_at);
                var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                var dayName = daysOfWeek[new Date(date).getDay()];
                var hour = new Date(date).getHours();
                var min = new Date(date).getMinutes();
                var formattedMin = min < 10 ? "0" + min : min;
                var dateString = dayName + " " + hour + ":" + formattedMin
                return {
                    id: data.id_city,
                    name: data.name,
                    temp: data.temp,
                    humidity: data.humidity,
                    date: dateString,
                    icon: data.icon
                };
            });

            var labels = [];
            var temperatures = [];
            var humidities = [];
            filteredWeatherData.forEach(function(data) {
                labels.push(data.date);
                temperatures.push(data.temp);
                humidities.push(data.humidity);
            });

            var ctx = weatherChart.getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Temperatures',
                        data: temperatures,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Humidities',
                        data: humidities,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Value'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'x'
                            }
                        }
                    }
                }
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>



    </body>
</html>
