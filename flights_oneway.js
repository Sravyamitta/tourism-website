document.addEventListener("DOMContentLoaded", function () {
    const tripTypeSelect = document.getElementById("tripType");
    const returnDateLabel = document.getElementById("returnDateLabel");
    const returnDateInput = document.getElementById("returnDate");
    const passengerIcon = document.getElementById("passengerIcon");
    const passengerForm = document.getElementById("passengerForm");
    const flightForm = document.getElementById("flightForm");

    const validCities = [
        "Austin", "Dallas", "Houston", "San Antonio", "Fort Worth", "Los Angeles", "San Francisco", "San Diego", "San Jose", "Sacramento"
    ];

    const minDate = new Date("2024-09-01");
    const maxDate = new Date("2024-12-01");

    tripTypeSelect.addEventListener("change", function () {
        if (tripTypeSelect.value === "roundTrip") {
            returnDateLabel.style.display = "block";
            returnDateInput.style.display = "block";
            returnDateInput.required = true;
        } else {
            returnDateLabel.style.display = "none";
            returnDateInput.style.display = "none";
            returnDateInput.required = false;
        }
    });

    passengerIcon.addEventListener("click", function () {
        passengerForm.style.display = passengerForm.style.display === "none" ? "block" : "none";
    });

    function isValidDate(date) {
        const parsedDate = new Date(date);
        return parsedDate >= minDate && parsedDate <= maxDate;
    }

    function isValidCity(city) {
        return validCities.map(c => c.toLowerCase()).includes(city.toLowerCase());
    }

    flightForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const tripType = tripTypeSelect.value;
        const origin = document.getElementById("origin").value.trim();
        const destination = document.getElementById("destination").value.trim();
        const departureDate = document.getElementById("departureDate").value;
        const returnDate = returnDateInput.value;
        const adults = parseInt(document.getElementById("adults").value);
        const children = parseInt(document.getElementById("children").value);
        const infants = parseInt(document.getElementById("infants").value);

        if (!origin || !isValidCity(origin)) {
            alert("Please enter a valid origin city in Texas or California.");
            return;
        }

        if (!destination || !isValidCity(destination)) {
            alert("Please enter a valid destination city in Texas or California.");
            return;
        }

        if (!departureDate || !isValidDate(departureDate)) {
            alert("Please enter a valid departure date between September 1, 2024, and December 1, 2024.");
            return;
        }

        if (tripType === "roundTrip") {
            if (!returnDate || !isValidDate(returnDate)) {
                alert("Please enter a valid return date between September 1, 2024, and December 1, 2024.");
                return;
            }

            if (new Date(departureDate) >= new Date(returnDate)) {
                alert("Return Date must be after the departure date.");
                return;
            }
        }

        if (!adults || adults < 1 || adults > 4) {
            alert("Please enter the number of adults (between 1 and 4).");
            return;
        }

        if (children < 0 || children > 4) {
            alert("Please enter the number of children (between 0 and 4).");
            return;
        }

        if (infants < 0 || infants > 4) {
            alert("Please enter the number of infants (between 0 and 4).");
            return;
        }

        const totalPassengers = adults + children + infants;

        fetch('flights.xml')
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(data, "text/xml");
                const flights = xmlDoc.getElementsByTagName("flight");
                const flightList = document.getElementById("flightList");
                flightList.innerHTML = ""; // Clear previous results

                const requestedDepartureDate = new Date(departureDate);
                let foundFlights = false;

                for (let i = 0; i < flights.length; i++) {
                    const flight = flights[i];
                    const flightOrigin = flight.getElementsByTagName("origin")[0].textContent.toLowerCase();
                    const flightDestination = flight.getElementsByTagName("destination")[0].textContent.toLowerCase();
                    const flightDepartureDate = new Date(flight.getElementsByTagName("departure-date")[0].textContent);
                    const availableSeats = parseInt(flight.getElementsByTagName("available-seats")[0].textContent);
                    
                    if (flightOrigin === origin.toLowerCase() && flightDestination === destination.toLowerCase()) {
                        const dateDifference = Math.abs(flightDepartureDate - requestedDepartureDate) / (1000 * 3600 * 24);
                        console.log(dateDifference);
                        if (dateDifference <= 3 && availableSeats >= totalPassengers) {
                            foundFlights = true;

                            const flightId = flight.getElementsByTagName("flight-id")[0].textContent;
                            const arrivalDate = flight.getElementsByTagName("arrival-date")[0].textContent;
                            const departureTime = flight.getElementsByTagName("departure-time")[0].textContent;
                            const arrivalTime = flight.getElementsByTagName("arrival-time")[0].textContent;
                            const price = flight.getElementsByTagName("price")[0].textContent;

                            const flightItem = document.createElement("div");
                            flightItem.classList.add("flight-item");
                            flightItem.innerHTML = `
                                <p>Flight ID: ${flightId}</p>
                                <p>Origin: ${origin}</p>
                                <p>Destination: ${destination}</p>
                                <p>Departure Date: ${flightDepartureDate.toISOString().split('T')[0]}</p>
                                <p>Arrival Date: ${arrivalDate}</p>
                                <p>Departure Time: ${departureTime}</p>
                                <p>Arrival Time: ${arrivalTime}</p>
                                <p>Available Seats: ${availableSeats}</p>
                                <p>Price: $${price}</p>
                            `;
                            flightList.appendChild(flightItem);
                        }
                    }
                }

                if (!foundFlights) {
                    const noFlightsMessage = document.createElement("p");
                    noFlightsMessage.textContent = "No available flights found for the requested date or within 3 days before and after.";
                    flightList.appendChild(noFlightsMessage);
                }
            })
            .catch(error => {
                console.error('Error loading the XML file:', error);
            });
    });
});