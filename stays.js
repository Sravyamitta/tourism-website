(document).ready(function() {
    fetchUserDetails();

    const updateForm = $('#updateForm');
    updateForm.on('submit', (event) => {
        event.preventDefault();
        updateUserDetails();
    });
});

function fetchUserDetails() {
    $.ajax({
        url: "get_user_details.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            if (data.success) {
                $('#firstName').val(data.user.firstName);
                $('#lastName').val(data.user.lastName);
                $('#dob').val(data.user.dob);
                $(`input[name="gender"][value="${data.user.gender}"]`).prop('checked', true);
                $('#email').val(data.user.email);
                $('#phone').val(data.user.phone);
            } else {
                alert("Failed to fetch user details.");
            }
        },
        error: function(error) {
            console.error("Error:", error);
        }
    });
}

function updateUserDetails() {
    const formData = new FormData(document.getElementById("updateForm"));

    $.ajax({
        url: "update_user_details.php",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
            if (data.success) {
                alert("User details updated successfully.");
            } else {
                alert("Failed to update user details.");
            }
        },
        error: function(error) {
            console.error("Error:", error);
        }
    });
}
