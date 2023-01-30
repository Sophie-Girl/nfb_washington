(function ($, Drupal) {
    Drupal.behaviors.wash_sem_note_edit = {
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
                var vmeetingid = $('#edit-rating-value').val();
                if (vmeetingid != "new" && vmeetingid.substr(0,3) != "new") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/ajax/rating',
                        data: { meetingid:vmeetingid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-issue-1-ranking').value = issue[0];
                        document.getElementById('edit-issue-1-comment').value = issue[1];
                        if(issue[2]){
                        document.getElementById('edit-issue-2-ranking').value = issue[2];
                        document.getElementById('edit-issue-2-comment').value = issue[3];}
                        if(issue[4]){
                        document.getElementById('edit-issue-3-ranking').value = issue[4];
                        document.getElementById('edit-issue-3-comment').value = issue[5];}
                        if(issue[6])
                        {document.getElementById('edit-issue-4-ranking').value = issue[6];
                            document.getElementById('edit-issue-4-comment').value = issue[7];}
                        if(issue[8])
                        {document.getElementById('edit-issue-4-ranking').value = issue[8];
                            document.getElementById('edit-issue-4-comment').value = issue[9];}


                    });
                }

                    document.getElementById('edit-rating-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-rating-value js-form-item-rating-value")['0'].style.display = 'none';

            }
        }
    }
})(jQuery, Drupal);