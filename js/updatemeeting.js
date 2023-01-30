(function ($, Drupal) {
    Drupal.behaviors.wash_sem_meeitng = {
        attach: function(context, settings) {
            window.onload = function () {
                var db = document.getElementsByClassName("menu__item menu__item--donate");
                db['0'].style.display = "none";
                db['1'].style.display = "none";
                // Connell,Sophi:  remove all other donate buttons per Outreach
                var ml = document.getElementsByClassName("menu menu--parent menu--main menu--level-0");
                ml['0'].style.display = "none";
                var nav = document.getElementById("block-primary-navigation");
                nav.style.display = "none";
                /*   ml['1'].style.display = "none";
                   ml['2'].style.display = "none";
                   ml['3'].style.display = "none";
                   ml['4'].style.display = "none"; */
                // Remove top menu
                document.getElementById('edit-meeting-value').style.display = 'none';
                document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-meeting-value js-form-item-meeting-value")['0'].style.display = 'none';
                var vmeetingid = $('#edit-meeting-value').val();
                if (vmeetingid != "new" && vmeetingid.substr(0,3) != "new") {
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
                        document.getElementById('edit-attendance').value = issue[6];
                    });
                }
                    document.getElementById('edit-meeting-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-meeting-value js-form-item-meeting-value")['0'].style.display = 'none';

            }
        }
    }
})(jQuery, Drupal);