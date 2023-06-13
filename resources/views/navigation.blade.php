<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Weather App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('favorites')}}">Favorites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome')}}">Weathers</a>
                </li>
            </ul>
            <div class="d-flex ms-auto">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" id="searchInput">
                <button class="btn btn-outline-light" type="button" id="searchButton">Search</button>
            </div>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function() {
        $('#searchButton').click(function() {
            var searchValue = $('#searchInput').val().trim().toLowerCase();

            if (searchValue === '') {
                // Jeśli pole wyszukiwania jest puste, pokaż wszystkie wartości i zresetuj ukrycie
                $('#tableMain tbody tr').show();
                $('#tableMain tbody td').show();
            } else {
                $('#tableMain tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();

                    if (rowText.includes(searchValue)) {
                        $(this).show().filter(function() {
                            return $(this).find('td:empty').length === 0;
                        }).find('td').each(function() {
                            var cellText = $(this).text().toLowerCase();
                            if (cellText.includes(searchValue)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });


</script>


