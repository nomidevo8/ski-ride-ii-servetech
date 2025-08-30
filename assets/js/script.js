jQuery(document).ready(function ($) {
    // Initialize bs-stepper
    window.stepper = new window.Stepper(document.querySelector('#srs-booking-form'), {
        linear: false, // allow navigation
        animation: true
    });

    // Form submission handler
    $('#srs-booking-form-inner').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: srs_ajax.ajax_url, // Localized in PHP
            type: 'POST',
            data: {
                action: 'srs_submit_booking',
                form_data: formData
            },
            success: function (res) {
                alert('✅ Booking Submitted: ' + res.data);
            },
            error: function () {
                alert('❌ Something went wrong!');
            }
        });
    });
});
