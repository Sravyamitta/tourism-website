$(document).ready(function() {
    // Load account info
    $.ajax({
        url: 'load-account-info.php',
        success: function(response) {
            $('#accountInfo').html(response);
        }
    });
});
