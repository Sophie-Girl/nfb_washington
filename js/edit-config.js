(function ($, Drupal) {
    Drupal.behaviors.wash_sem_config_edit = {
        attach: function(context, settings) {
            window.onload = function () {
                var vissueid = 'go';
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/config',
                        data: { issueid:vissueid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-pp-api-key').value = issue[0];
                        document.getElementById('edit-congress-number').value = issue[1];
                        document.getElementById('edit-seminar').value = issue[2];
                        document.getElementById('edit-staff-email').value = issue[3];
                        document.getElementById('edit-issue-number').value = issue[4];
                    });
                }

            }
        }
    })(jQuery, Drupal);
