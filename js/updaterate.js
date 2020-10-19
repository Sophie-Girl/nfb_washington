(function ($, Drupal) {
    Drupal.behaviors.nfb_washington_update_rate_issue = {
        attach: function(context, settings){
            $('#nfb-select-rep').once.onchange(function () {

                $.ajax({
                    type: 'POST',
                    url: '/nfb_washington/ajax/rating',
                    data: { route: vroute },
                }).done(function (data) {
                    var meeting = data;
                    console.log(toString(meeting));
                    if(meetng[0] != "no_result")
                    {
                        if(meeting[0] != "null") {document.getElementById('edit-nfb-contact-name').value = meeting[0];}
                        if(meeting[1] != "null") {document.getElementById("edit-nfb-civicrm-phone_1").value = meeting[1];}
                        if(meeting[2] != "null") {document.getElementById('edit-meeting-location').value = meeting[2];}
                        if(meeting[6] != "null"){document.getElementById('edit-in-memory-template-id').value = setting[6];}
                        if(meeting[7] != "null"){document.getElementById('edit-attendance').value = meeting[7];}
                        if(meeting[8] != "null"){document.getElementById('edit-moc-contact').value = meeting[8];}
                    }

                });});

        }
    }
})(jQuery, Drupal);