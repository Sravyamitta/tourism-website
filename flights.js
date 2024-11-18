$(document).ready(function() {
    $('input[name="tripType"]').change(function() {
        if ($('input[name="tripType"]:checked').val() === 'roundTrip') {
            $('#returnDateDiv').show();
        } else {
            $('#returnDateDiv').hide();
        }
    });

    $('#flightsForm').submit(function(event) {
        event.preventDefault();
        
        let tripType = $('input[name="tripType"]:checked').val();
        let origin = $('#origin').val();
        let destination = $('#destination').val();
        let departureDate = $('#departureDate').val();
        let returnDate = $('#returnDate').val();
        let adults = $('#adults').val();
        let children = $('#children').val();
        let infants = $('#infants').val();

        $.ajax({
            url: 'search-flights.php',
            type: 'POST',
            data: {
                tripType: tripType,
                origin: origin,
                destination: destination,
                departureDate: departureDate,
                returnDate: returnDate,
                adults: adults,
                children: children,
                infants: infants
            },
            success: function(response) {
                $('#flightsResult').html(response);
            }
        });
    });
});
