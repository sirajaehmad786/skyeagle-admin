<!-- resources/views/city-form.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>City Select with Google API</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCd3ClpSHFj4CMH6j8LJ-AIlnafrpSQyzU&libraries=places"></script>
</head>
<body>

<form>
    <label for="city">City:</label>
    <input type="text" id="city" placeholder="Enter city" autocomplete="off">

    <br><br>

    <label>State:</label>
    <input type="text" id="state" readonly>

    <label>Country:</label>
    <input type="text" id="country" readonly>
</form>

<script>
    function initAutocomplete() {
        let input = document.getElementById('city');
        let autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['(cities)'] // only cities
        });

        autocomplete.addListener('place_changed', function () {
            let place = autocomplete.getPlace();
            let state = '';
            let country = '';

            if (!place.address_components) return;

            place.address_components.forEach(function (component) {
                let types = component.types;
                if (types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (types.includes('country')) {
                    country = component.long_name;
                }
            });

            document.getElementById('state').value = state;
            document.getElementById('country').value = country;
        });
    }

    google.maps.event.addDomListener(window, 'load', initAutocomplete);
</script>

</body>
</html>
