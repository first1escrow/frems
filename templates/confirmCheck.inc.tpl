
<script type="text/javascript">
$(document).ready(function() {
    $.ajax({
        url: '/includes/confirmCheck.php',
        type: 'POST',
        async: false,
        success: function(response) {
            $('#hr-confirm-list').hide();
            if (response == 'Y') {
                $('#hr-confirm-list').show();
            }
        }
    });
});
</script>
