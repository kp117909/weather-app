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
</head>
@php
    $count = 0;
    $countCurr = 0;
    $countBreak = 0 ;
@endphp
<body class="antialiased">
    @include('navigation')
    <div class="container">
        <table id = "tableMain" class="table">
            <thead>
            </thead>
            <tbody>
            <tr>
                @php
                    $count = 0;
                    $countBreak = 0;
                    $countCurr = 0;
                @endphp
                @foreach($apiData as $weather)
                    @php
                        $count++;
                        $countBreak++;
                        $celsius = $weather['list'][0]['main']['temp'] - 273.15;
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
                            Temperature {{ $celsius }} °C<br>
                            {{ $weather['list'][0]['dt_txt'] }} <br>
                            {{ $dayName }}
                        </div>
                        @php
                            $celsius = $weather['list'][$count]['main']['temp'] - 273.15;
                            $countCurr++;
                        @endphp
                    </td>
                    @if ($count % 5 == 0)
            </tr><tr>
                @endif
                @endforeach
            </tr>

            @foreach(array_slice($apiData, 0, 10) as $weather)
                @if ($countBreak >= 10)
                    @break
                @endif
                <div class="modal fade" id="weatherModal{{ $weather['city']['id'] }}" tabindex="-1" aria-labelledby="weatherModalLabel{{ $weather['city']['id'] }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="weatherModalLabel{{ $weather['city']['id'] }}">{{ $weather['city']['name'] }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Main Weather: {{ $weather['list'][$count]['weather'][0]['main'] }}</p>
                                <p>Description: {{ $weather['list'][$count]['weather'][0]['description'] }}</p>
                                <p>Temperature: {{ $celsius }} °C</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $countBreak = 0;
                    $countCurr = 0;
                @endphp
            @endforeach
            </tbody>
        </table>
    </div>

<script>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
