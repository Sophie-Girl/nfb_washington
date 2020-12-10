(function ($, Drupal) {
    Drupal.behaviors.wash_sem_meeitng = {
        attach: function(context, settings) {
            window.onload = function () {
                var vmeetingid = $('#edit-meeting-value').val();
                if (vmeetingid != "new") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/ajax/meeting',
                        data: { meetingid:vmeetingid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-meeting-location').value = issue[0];
                        document.getElementById('edit-meeting-time').value = issue[1];
                        document.getElementById('edit-meeting-day').value = issue[2];
                        document.getElementById('edit-nfb-contact-name').value = issue[3];
                        document.getElementById('edit-nfb-civicrm-phone-1').value = issue[4];
                        document.getElementById('edit-moc-contact').value = issue[5];
                        document.getElementById('editattendance').value = issue[6];
                    });
                }
                    document.getElementById('edit-meeting-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-meeting-value js-form-item-meeting-value")['0'].style.display = 'none';

            }
        }
    }
})(jQuery, Drupal);