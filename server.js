const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const xmlbuilder = require('xmlbuilder');
const xml2js = require('xml2js');
const app = express();
const port = 3000;

// Middleware to parse JSON bodies
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Serve static files from the current directory
app.use(express.static(__dirname));

// Existing endpoint to save contact data
app.post('/saveContactData', (req, res) => {
    const { firstName, lastName, phone, email, comment, gender } = req.body;

    // Create XML data
    const xml = xmlbuilder.create('ContactData')
        .ele('Contact')
        .ele('FirstName', firstName)
        .up()
        .ele('LastName', lastName)
        .up()
        .ele('Phone', phone)
        .up()
        .ele('Email', email)
        .up()
        .ele('Comment', comment)
        .up()
        .ele('Gender', gender)
        .end({ pretty: true });

    // Append XML to the file
    fs.readFile('contactData.xml', 'utf8', (err, data) => {
        let existingData = '<Contacts>'; // Wrap all entries in a root element

        if (!err) {
            existingData += data.replace('</ContactData>', ''); // Remove the closing tag to append new entries
        }
        existingData += xml.replace('<ContactData>', '').replace('</ContactData>', '') + '</Contacts>';

        fs.writeFile('contactData.xml', existingData, (err) => {
            if (err) {
                console.error('Error saving XML file:', err);
                return res.json({ success: false });
            }
            res.json({ success: true });
        });
    });
});

// New endpoint to handle cart updates
app.post('/cart', (req, res) => {
    const cartItem = req.body;
    
    
    if (!cartItem["hotelId"] || !cartItem["hotelName"] || !cartItem["numberOfRooms"] ||
        !cartItem["checkinDate"] || !cartItem["checkoutDate"] || !cartItem["city"] || cartItem["price"] ==0) {
        return res.status(400).json({ message: 'Invalid cart item data.' });
    }

    // Read existing cart data
    fs.readFile('cart.json', 'utf8', (err, data) => {
        let existingCart = [];

        if (!err && data) {
            existingCart = JSON.parse(data);
        }

        // Add new cart item
        existingCart.push(cartItem);

        // Write updated cart data
        fs.writeFile('cart.json', JSON.stringify(existingCart, null, 2), (err) => {
            if (err) {
                console.error('Error saving cart data:', err);
                return res.status(500).json({ message: 'Error saving cart data.' });
            }
            res.json({ message: 'Cart updated successfully.' });
        });
    });
});

app.post('/removeFromCart', (req, res) => {
    const item = req.body;
    hotelId = item["hotelId"]
    console.log(hotelId);
    // Read the existing cart data
    fs.readFile('cart.json', 'utf8', (err, data) => {
        if (err) {
            console.error('Error reading cart file:', err);
            return res.status(500).json({ success: false });
        }

        let cartData = JSON.parse(data);
       
        cartData = cartData.filter(booking => booking.hotelId !== hotelId);
        
        
        // Write the updated cart data back to the file
        fs.writeFile('cart.json', JSON.stringify(cartData, null, 2), (err) => {
            if (err) {
                console.error('Error saving cart data:', err);
                return res.status(500).json({ message: 'Error saving cart data.' });
            }
            res.json({ message: 'Cart updated successfully.' });
        });
    });
});

app.post('/bookFlight', (req, res) => {
    const flightId = req.body.flightId;
    const seats = req.body.seats;
    console.log(flightId);
    // Read the XML file
    fs.readFile("flights.xml", (err, data) => {
        if (err) {
            return res.status(500).send('Error reading XML file.');
        }

        // Parse the XML file
        xml2js.parseString(data, (err, result) => {
            if (err) {
                return res.status(500).send('Error parsing XML file.');
            }
            
            // Find the flight and update the available seats
            const flight = result.Flights.Flight.find(f => f.FlightID[0] === flightId);
            
            if (flight && flight.AvailableSeats[0] > 0) {
                flight.AvailableSeats[0] = (parseInt(flight.AvailableSeats[0], 10) - parseInt(seats)).toString();
                console.log(flight);
                result.Flights.Flight.find(f => f.FlightID[0]=== flightId).AvailableSeats[0] = flight.AvailableSeats[0];
                console.log(result.Flights.Flight.find(f => f.FlightID[0]=== flightId).AvailableSeats[0]);
                // Convert JSON back to XML
                const builder = new xml2js.Builder();
                const xml = builder.buildObject(result);

                // Write the updated XML back to the file
                fs.writeFile("flights.xml", xml, (err) => {
                    if (err) {
                        return res.status(500).send('Error writing XML file.');
                    }
                    res.send('Flight booked successfully.');
                });
            } else {
                res.status(400).send('No available seats or flight not found.');
            }
        });
    });
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
