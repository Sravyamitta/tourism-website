$(document).ready(function() {
    $('#contactForm').submit(function(event) {
        event.preventDefault();
        
        let comment = $('#comment').val();

        $.ajax({
            url: 'submit-comment.php',
            type: 'POST',
            data: {
                comment: comment
            },
            success: function(response) {
                $('#contactMessage').html(response);
            }
        });
    });
});
