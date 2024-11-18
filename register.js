$(document).ready(function() {
    $('#registerForm').submit(function(event) {
        event.preventDefault();
        
        let phone = $('#phone').val();
        let password = $('#password').val();
        let confirmPassword = $('#confirmPassword').val();
        let firstName = $('#firstName').val();
        let lastName = $('#lastName').val();
        let dob = $('#dob').val();
        let email = $('#email').val();
        
        let phonePattern = /^\d{3}-\d{3}-\d{4}$/;
        let dobPattern = /^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])-\d{4}$/;
        let emailPattern = /^[^@]+@[^@]+\.[^@]{2,}$/;

        if (!phone.match(phonePattern)) {
            $('#message').text('Invalid phone number format. It should be ddd-ddd-dddd.');
            return;
        }
        
        if (password.length < 8) {
            $('#message').text('Password must be at least 8 characters.');
            return;
        }
        
        if (password !== confirmPassword) {
            $('#message').text('Passwords do not match.');
            return;
        }
        
        if (!dob.match(dobPattern)) {
            $('#message').text('Invalid date of birth format. It should be MM-DD-YYYY.');
            return;
        }
        
        if (!email.match(emailPattern)) {
            $('#message').text('Invalid email format.');
            return;
        }

        // Further validation (e.g., check if phone number already exists)
        $.ajax({
            url: 'register.php',
            type: 'POST',
            data: {
                phone: phone,
                password: password,
                firstName: firstName,
                lastName: lastName,
                dob: dob,
                email: email,
                gender: $('input[name="gender"]:checked').val()
            },
            success: function(response) {
                $('#message').text(response);
            }
        });
    });
});
