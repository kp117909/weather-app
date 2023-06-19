import './bootstrap';

$(window).on('resize', function() {
    initializeChosenSelect();
});

function initializeChosenSelect() {
    $('.chosen-select').chosen();
}

$(document).ready(function() {
    $('.chosen-select').chosen();


    $('#getCoordinatesButton').click(function(event) {
        event.preventDefault();

        var url = $(this).data('url');
        var selectedOption = $('#weatherSelect option:selected');
        var selectedId = selectedOption.val();
        var selectedLon = selectedOption.data('lon');
        var selectedLat = selectedOption.data('lat');
        $.ajax({
            url: url,
            type: 'GET',
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
                $('#weatherModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });

    });

    // Function to add all fav to sql data
    $('#addFavToSql').click(function(event) {
        event.preventDefault();

        var url = $(this).data('url');

        $.ajax({
            url: url,
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
        var url = $(this).data('url');
        $.ajax({
            url: url,
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

    $('.remove-from-fav').click(function(event) {
        event.preventDefault();
        var cityId = $(this).data('city-id');

        var url = $(this).data('url');

        $.ajax({
            url: url,
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

    // Charts
    var modals = document.querySelectorAll('.modal.fav');
    const appElement = document.getElementById('app');
    const weatherData = JSON.parse(appElement.getAttribute('data-weather-data'));

    console.log(weatherData)

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
});


