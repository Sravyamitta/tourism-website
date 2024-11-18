document.addEventListener("DOMContentLoaded", () => {
    fetchUserDetails();

    const updateForm = document.getElementById("updateForm");
    updateForm.addEventListener("submit", (event) => {
        event.preventDefault();
        updateUserDetails();
    });
});

function fetchUserDetails() {
    fetch("get_user_details.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("firstName").value = data.user.firstName;
                document.getElementById("lastName").value = data.user.lastName;
                document.getElementById("dob").value = data.user.dob;
                document.querySelector(`input[name="gender"][value="${data.user.gender}"]`).checked = true;
                document.getElementById("email").value = data.user.email;
                document.getElementById("phone").value = data.user.phone;
            } else {
                alert("Failed to fetch user details.");
            }
        })
        .catch(error => console.error("Error:", error));
}

function updateUserDetails() {
    const formData = new FormData(document.getElementById("updateForm"));

    fetch("update_user_details.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("User details updated successfully.");
            } else {
                alert("Failed to update user details.");
            }
        })
        .catch(error => console.error("Error:", error));
}
