(function ($, Drupal) {
    Drupal.behaviors.wash_sem_note_edit = {
        attach: function(context, settings) {
            window.onload = function () {
                var vmeeint_id = $('#edit-note-value').val();
                if (vmeeitngid != "new") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/ajax/rating',
                        data: { meetingid:vmeeitngid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-issue-1-ranking').value = issue[0];
                        document.getElementById('edit-issue-1-comment').value = issue[1];
                        document.getElementById('edit-issue-2-ranking').value = issue[2];
                        document.getElementById('edit-issue-2-comment').value = issue[3];
                        document.getElementById('edit-issue-3-ranking').value = issue[4];
                        document.getElementById('edit-issue-3-comment').value = issue[5];
                    });
                }
                else{
                    document.getElementById('edit-rating-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-rating-value js-form-item-rating-value")['0'].style.display = 'none';
                }
            }
        }
    }
})(jQuery, Drupal);