(function ($, Drupal) {
    Drupal.behaviors.wash_sem_update_meeting = {
        attach: function(context, settings) {
            window.onload = function () {
                var issueid;
                issueid =  $('#edit-route-id-field').val();
                $.ajax({
                    type: 'POST',
                    url: '/nfb_washington/admin/ajax/issue',
                    data: { route: issueid },
                }).done(function (data) {

                });
                }
        }
    }
})(jQuery, Drupal);
