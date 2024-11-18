document.addEventListener("DOMContentLoaded", function () {
    const cruisesForm = document.getElementById("cruisesForm");

    const validDestinations = ["Alaska", "Bahamas", "Europe", "Mexico"];
    const minDate = new Date("2024-09-01");
    const maxDate = new Date("2024-12-01");

    // Validate date within the range
    function isValidDate(date) {
        const parsedDate = new Date(date);
        return parsedDate >= minDate && parsedDate <= maxDate;
    }

    // Validate and submit the form
    cruisesForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        // Gather form data
        const destination = document.getElementById("destination").value;
        const departingBetween = document.getElementById("departingBetween").value;
        const durationMin = parseInt(document.getElementById("durationMin").value);
        const durationMax = parseInt(document.getElementById("durationMax").value);
        const numPassengers = parseInt(document.getElementById("numPassengers").value);

        // Validation checks
        if (!validDestinations.includes(destination)) {
            alert("Please select a valid destination (Alaska, Bahamas, Europe, or Mexico).");
            return;
        }

        if (!departingBetween || !isValidDate(departingBetween)) {
            alert("Please enter a valid departing date between September 1, 2024, and December 1, 2024.");
            return;
        }

        if (isNaN(durationMin) || durationMin < 3 || durationMin > 10) {
            alert("Please enter a valid minimum duration between 3 and 10 days.");
            return;
        }

        if (isNaN(durationMax) || durationMax < 3 || durationMax > 10) {
            alert("Please enter a valid maximum duration between 3 and 10 days.");
            return;
        }

        if (durationMin > durationMax) {
            alert("Minimum duration cannot be greater than maximum duration.");
            return;
        }

        if (isNaN(numPassengers) || numPassengers < 1 || numPassengers > 2) {
            alert("Please enter a valid number of passengers (maximum 2).");
            return;
        }

        // Display the information entered by the user
        let cruiseInfo = `
            Destination: ${destination}
            Departing Between: ${departingBetween}
            Duration: ${durationMin} to ${durationMax} days
            Number of Passengers: ${numPassengers}
        `;

        alert(cruiseInfo);
    });
});
