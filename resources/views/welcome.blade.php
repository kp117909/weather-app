<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Weather Data</title>
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
        @foreach(array_slice($apiData, 0, 10) as $weather)
        <h1 class = "center">{{ $weather['city']['name']}}</h1>
        <table class="table">
            <thead>
            </thead>
            <tbody>
                @php
                    $count++;
                @endphp
                    <tr data-bs-toggle="modal" data-bs-tsarget="#weatherModal{{$weather['city']['id']}}">
                        @foreach($weather['list'] as $weatherCurr)
                            @if ($countBreak >= 7)
                                @break
                            @endif
                            @php
                                $countBreak++;
                                $celsius = $weather['list'][$countCurr]['main']['temp'] - 273.15;
                                $date = $weather['list'][$countCurr]['dt_txt'];
                                $dayName = date('l', strtotime($date));
                            @endphp
                        <td>
                            <div>
                                {{ $weather['list'][$countCurr]['weather'][0]['main'] }}
                                <img src="https://openweathermap.org/img/w/{{$weather['list'][$countCurr]['weather'][0]['icon']}}.png" alt="Weather Icon" class="img-fluid"><br>
                                Temperature {{ $celsius }} °C<br>
                                {{$weather['list'][$countCurr]['dt_txt']}} <br>
                                {{$dayName}}
                            </div>
                            @php
                                $celsius = $weather['list'][$count]['main']['temp'] - 273.15;
                                $countCurr++;
                            @endphp
                        </td>
                        @endforeach
                            @php
                                $countBreak = 0;
                                $countCurr = 0;
                            @endphp
                    </tr>


                <div class="modal fade" id="weatherModal{{$weather['city']['id']}}" tabindex="-1" aria-labelledby="weatherModalLabel{{$weather['city']['id']}}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="weatherModalLabel{{$weather['city']['id']}}">{{$weather['city']['name']}}</h5>
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
            </tbody>
        </table>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
