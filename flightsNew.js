document.addEventListener("DOMContentLoaded", function () {
  const tripTypeSelect = document.getElementById("tripType");
  const returnDateLabel = document.getElementById("returnDateLabel");
  const returnDateInput = document.getElementById("returnDate");
  const flightForm = document.getElementById("flightForm");
  let d_count = 0;
  let r_count = 0;
  const validCities = [
    "Austin",
    "Dallas",
    "Houston",
    "San Antonio",
    "Fort Worth",
    "Los Angeles",
    "San Francisco",
    "San Diego",
    "San Jose",
    "Sacramento",
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

  function isValidDate(date) {
    const parsedDate = new Date(date);
    return parsedDate >= minDate && parsedDate <= maxDate;
  }

  function isValidCity(city) {
    return validCities.map((c) => c.toLowerCase()).includes(city.toLowerCase());
  }

  flightForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const tripType = tripTypeSelect.value;
    const origin = document.getElementById("origin").value.trim();
    const destination = document.getElementById("destination").value.trim();
    const departureDate = document.getElementById("departureDate").value;
    // const returnDate = returnDateInput.value;
    const returnDate = document.getElementById("returnDate").value;
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
      alert(
        "Please enter a valid departure date between September 1, 2024, and December 1, 2024."
      );
      return;
    }

    if (tripType === "roundTrip") {
      if (!returnDate || !isValidDate(returnDate)) {
        alert(
          "Please enter a valid return date between September 1, 2024, and December 1, 2024."
        );
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

    let tripInfo = `
            Trip Type: ${tripType}
            Origin: ${origin}
            Destination: ${destination}
            Departure Date: ${departureDate}
        `;

    if (tripType === "roundTrip") {
      tripInfo += `\nReturn Date: ${returnDate}`;
    }

    tripInfo += `
            Adults: ${adults}
            Children: ${children}
            Infants: ${infants}
        `;

    alert(tripInfo);
    if (tripType === "oneWay") {
      fetch("flights.xml")
        .then((response) => response.text())
        .then((data) => {
          const parser = new DOMParser();
          const xmlDoc = parser.parseFromString(data, "text/xml");
          const flights = xmlDoc.getElementsByTagName("Flight");
          const flightList = document.getElementById("flightList");
          flightList.innerHTML = ""; // Clear previous results

          const requestedDepartureDate = new Date(departureDate);
          let foundFlights = false;

          for (let i = 0; i < flights.length; i++) {
            const flight = flights[i];
            const flightOrigin = flight
              .getElementsByTagName("Origin")[0]
              .textContent.toLowerCase();
            const flightDestination = flight
              .getElementsByTagName("Destination")[0]
              .textContent.toLowerCase();
            const flightDepartureDate = new Date(
              flight.getElementsByTagName("DepartureDate")[0].textContent
            );
            const availableSeats = parseInt(
              flight.getElementsByTagName("AvailableSeats")[0].textContent
            );

            if (
              flightOrigin === origin.toLowerCase() &&
              flightDestination === destination.toLowerCase()
            ) {
              const dateDifference =
                Math.abs(flightDepartureDate - requestedDepartureDate) /
                (1000 * 3600 * 24);
              if (dateDifference <= 3 && availableSeats >= totalPassengers) {
                foundFlights = true;

                const flightId =
                  flight.getElementsByTagName("FlightID")[0].textContent;
                const arrivalDate =
                  flight.getElementsByTagName("ArrivalDate")[0].textContent;
                const departureTime =
                  flight.getElementsByTagName("DepartureTime")[0].textContent;
                const arrivalTime =
                  flight.getElementsByTagName("ArrivalTime")[0].textContent;
                const price =
                  flight.getElementsByTagName("Price")[0].textContent;
                console.log(adults);
                const totalPrice =
                  price * (adults + 0.7 * children + 0.1 * infants);

                const flightItem = document.createElement("div");
                flightItem.classList.add("flight-item");
                flightItem.innerHTML = `
                                <p>Flight ID: ${flightId}</p>
                                <p>Origin: ${origin}</p>
                                <p>Destination: ${destination}</p>
                                <p>Departure Date: ${
                                  flightDepartureDate
                                    .toISOString()
                                    .split("T")[0]
                                }</p>
                                <p>Arrival Date: ${arrivalDate}</p>
                                <p>Departure Time: ${departureTime}</p>
                                <p>Arrival Time: ${arrivalTime}</p>
                                <p>Available Seats: ${availableSeats}</p>
                                <p>Price: $${price}</p>
                                <p>Total: $${totalPrice}</p>
                                <button class="add-to-cart" data-flight-id="${flightId}">Add to Cart</button>
                            `;
                flightList.appendChild(flightItem);
              }
            }
          }

          if (!foundFlights) {
            const noFlightsMessage = document.createElement("p");
            noFlightsMessage.textContent =
              "No available flights found for the requested date or within 3 days before and after.";
            flightList.appendChild(noFlightsMessage);
          }

          document.querySelectorAll(".add-to-cart").forEach((button) => {
            button.addEventListener("click", function () {
              const flightId = this.getAttribute("data-flight-id");
              addToCart(flightId);
            });
          });
        })
        .catch((error) => {
          console.error("Error loading the XML file:", error);
        });
      function addToCart(flightId) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        if (cart.length >= 1) {
          alert("You can only add one flight to the cart at a time!");
          return;
        }
        // Retrieve the flight details
        const flightItem = document
          .querySelector(`.add-to-cart[data-flight-id="${flightId}"]`)
          .closest(".flight-item");

        const origin = flightItem
          .querySelector("p:nth-child(2)")
          .textContent.split(": ")[1];
        const destination = flightItem
          .querySelector("p:nth-child(3)")
          .textContent.split(": ")[1];
        const departureDate = flightItem
          .querySelector("p:nth-child(4)")
          .textContent.split(": ")[1];
        const arrivalDate = flightItem
          .querySelector("p:nth-child(5)")
          .textContent.split(": ")[1];
        const departureTime = flightItem
          .querySelector("p:nth-child(6)")
          .textContent.split(": ")[1];
        const arrivalTime = flightItem
          .querySelector("p:nth-child(7)")
          .textContent.split(": ")[1];
        const price = parseFloat(
          flightItem
            .querySelector("p:nth-child(10)")
            .textContent.split(": $")[1]
        );

        // Add flight details to the cart
        const flightData = {
          id: flightId,
          origin: origin,
          destination: destination,
          departureDate: departureDate,
          arrivalDate: arrivalDate,
          departureTime: departureTime,
          arrivalTime: arrivalTime,
          price: price,
          adults: parseInt(document.getElementById("adults").value),
          children: parseInt(document.getElementById("children").value),
          infants: parseInt(document.getElementById("infants").value),
          oneWay: true,
        };

        // Check if the flight is already in the cart
        if (!cart.some((flight) => flight.id === flightId)) {
          cart.push(flightData);
          localStorage.setItem("cart", JSON.stringify(cart));
          alert("Flight added to cart!");
        } else {
          alert("Flight is already in the cart!");
        }
      }
    } else if (tripType === "roundTrip") {
      fetch("flights.xml")
        .then((response) => response.text())
        .then((data) => {
          const parser = new DOMParser();
          const xmlDoc = parser.parseFromString(data, "text/xml");
          const flights = xmlDoc.getElementsByTagName("Flight");
          const flightList = document.getElementById("flightList");
          flightList.innerHTML = ""; // Clear previous results

          const requestedDepartureDate = new Date(departureDate);
          const requestedReturnDate = new Date(returnDate);
          let foundDepartingFlights = false;
          let foundReturningFlights = false;

          for (let i = 0; i < flights.length; i++) {
            console.log(flights[i]);
            const flight = flights[i];
            const flightOrigin = flight
              .getElementsByTagName("Origin")[0]
              .textContent.toLowerCase();
            const flightDestination = flight
              .getElementsByTagName("Destination")[0]
              .textContent.toLowerCase();
            const flightDepartureDate = new Date(
              flight.getElementsByTagName("DepartureDate")[0].textContent
            );
            const flightArrivalDate = new Date(
              flight.getElementsByTagName("ArrivalDate")[0].textContent
            );
            const availableSeats = parseInt(
              flight.getElementsByTagName("AvailableSeats")[0].textContent
            );

            if (
              flightOrigin === origin.toLowerCase() &&
              flightDestination === destination.toLowerCase()
            ) {
              const dateDifference =
                Math.abs(flightDepartureDate - requestedDepartureDate) /
                (1000 * 3600 * 24);
              if (dateDifference <= 3 && availableSeats >= totalPassengers) {
                foundDepartingFlights = true;
                console.log("Filter1");
                console.log(flight);
                displayFlight(flight, "Departing Flight");
              }
            }

            if (
              flightOrigin === destination.toLowerCase() &&
              flightDestination === origin.toLowerCase()
            ) {
              const dateDifference =
                Math.abs(flightDepartureDate - requestedReturnDate) /
                (1000 * 3600 * 24);
              if (dateDifference <= 3 && availableSeats >= totalPassengers) {
                foundReturningFlights = true;
                console.log("Filter2");
                console.log(flight);
                displayFlight(flight, "Returning Flight");
              }
            }
          }

          if (!foundDepartingFlights) {
            const noDepartingFlightsMessage = document.createElement("p");
            noDepartingFlightsMessage.textContent =
              "No available departing flights found for the requested date or within 3 days before and after.";
            flightList.appendChild(noDepartingFlightsMessage);
          }

          if (!foundReturningFlights) {
            const noReturningFlightsMessage = document.createElement("p");
            noReturningFlightsMessage.textContent =
              "No available returning flights found for the requested date or within 3 days before and after.";
            flightList.appendChild(noReturningFlightsMessage);
          }

          document.querySelectorAll(".add-to-cart").forEach((button) => {
            button.addEventListener("click", function () {
              const flightId = this.getAttribute("data-flight-id");
              addToCart2(flightId);
            });
          });
          function addToCart2(flightId) {
           
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            const flightItem = document
              .querySelector(`.add-to-cart[data-flight-id="${flightId}"]`)
              .closest(".flight-item");
            console.log(flightItem);
            const flightType = flightItem.querySelector("h3:nth-child(1)").textContent;
            console.log(flightType);
            if (
              flightType === "Departing Flight"
            ) {
              d_count += 1;
              console.log(d_count);
              if (d_count > 1) {
                alert(
                  "You can only add one departing flight to the cart at a time!"
                );
                return;
              }
            } else {
              r_count += 1;
              if (r_count > 1) {
                alert(
                  "You can only add one return flight to the cart at a time!"
                );
                return;
              }
            }

            // Retrieve the flight details

            console.log(flightItem);
            const origin = flightItem
              .querySelector("p:nth-child(3)")
              .textContent.split(": ")[1];
            const destination = flightItem
              .querySelector("p:nth-child(4)")
              .textContent.split(": ")[1];
            const departureDate = flightItem
              .querySelector("p:nth-child(5)")
              .textContent.split(": ")[1];
            const arrivalDate = flightItem
              .querySelector("p:nth-child(6)")
              .textContent.split(": ")[1];
            const departureTime = flightItem
              .querySelector("p:nth-child(7)")
              .textContent.split(": ")[1];
            const arrivalTime = flightItem
              .querySelector("p:nth-child(8)")
              .textContent.split(": ")[1];
            const price = parseFloat(
              flightItem
                .querySelector("p:nth-child(11)")
                .textContent.split(": $")[1]
            );
            console.log(price);
            // Add flight details to the cart
            const flightData = {
              id: flightId,
              origin: origin,
              destination: destination,
              departureDate: departureDate,
              arrivalDate: arrivalDate,
              departureTime: departureTime,
              arrivalTime: arrivalTime,
              price: price,
              adults: parseInt(document.getElementById("adults").value),
              children: parseInt(document.getElementById("children").value),
              infants: parseInt(document.getElementById("infants").value),
              oneWay: false,
            };

            // Check if the flight is already in the cart
            if (!cart.some((flight) => flight.id === flightId)) {
              cart.push(flightData);
              localStorage.setItem("cart", JSON.stringify(cart));
              alert("Flight added to cart!");
            } else {
              alert("Flight is already in the cart!");
            }
          }
        })
        .catch((error) => {
          console.error("Error loading the XML file:", error);
        });
    }
    function displayFlight(flight, type) {
      const flightId = flight.getElementsByTagName("FlightID")[0].textContent;
      const origin = flight.getElementsByTagName("Origin")[0].textContent;
      const destination =
        flight.getElementsByTagName("Destination")[0].textContent;
      const departureDate =
        flight.getElementsByTagName("DepartureDate")[0].textContent;
      const arrivalDate =
        flight.getElementsByTagName("ArrivalDate")[0].textContent;
      const departureTime =
        flight.getElementsByTagName("DepartureTime")[0].textContent;
      const arrivalTime =
        flight.getElementsByTagName("ArrivalTime")[0].textContent;
      const price = flight.getElementsByTagName("Price")[0].textContent;
      const availableSeats = parseInt(
        flight.getElementsByTagName("AvailableSeats")[0].textContent
      );
      const totalPrice = price * (adults + 0.7 * children + 0.1 * infants);

      const flightItem = document.createElement("div");
      flightItem.classList.add("flight-item");
      flightItem.innerHTML = `
                <h3>${type}</h3>
                <p>Flight ID: ${flightId}</p>
                <p>Origin: ${origin}</p>
                <p>Destination: ${destination}</p>
                <p>Departure Date: ${departureDate}</p>
                <p>Arrival Date: ${arrivalDate}</p>
                <p>Departure Time: ${departureTime}</p>
                <p>Arrival Time: ${arrivalTime}</p>
                <p>Available Seats: ${availableSeats}</p>
                <p>Price: $${price}</p>
                <p>Total: $${totalPrice}</p>
                <button class="add-to-cart" data-flight-id="${flightId}">Add to Cart</button>
            `;
      document.getElementById("flightList").appendChild(flightItem);
    }
  });
});
