(function ($, Drupal) {
    Drupal.behaviors.wash_sem_update_meeting = {
        attach: function(context, settings) {
            window.onload = function () {
                var vissueid = $('#edit-issue-value').val();
                if (vissueid != "create") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/issue',
                        data: {issueid: vissueid},
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-issue-name').value = issue[0];
                       document.getElementById('edit-bill-id').value = issue[1];
                       document.getElementById('edit-bill-slug').value = issue[2];
                       document.getElementById('edit-primary-issue').value = issue[3];
                       document.getElementById('edit-derivative-issue').value = issue[4];
                    });
                }
            }
        }
    }
})(jQuery, Drupal);
