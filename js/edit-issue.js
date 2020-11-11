(function ($, Drupal) {
    Drupal.behaviors.wash_sem_update_meeting = {
        attach: function(context, settings) {
            window.onload = function () {
                var vissueid = $('#edit-route-id-field').val();
                if (vissueid != "create") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/issue',
                        data: {route: issueid},
                    }).done(function (data) {

                    });
                }
            }
        }
    }
})(jQuery, Drupal);
