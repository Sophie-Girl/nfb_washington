(function ($, Drupal) {
    Drupal.behaviors.wash_sem_update_meeting = {
        attach: function(context, settings) {
            window.onload = function () {
                var vissueid = $('#edit-issue-value').val();
                console.log(vissueid);
                if (vissueid != "create") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/issue',
                        data: {issueid: vissueid},
                    }).done(function (ajax_array) {
                        var issue = ajax_array;

                        document.getElementById('edit-issue-name').value = issue[0];
                       document.getElementById('edit-bill-id').value = issue[1];
                       document.getElementById('edit-bill-slug').value = issue[2];
                       document.getElementById('edit-primary-issue').value = issue[3];
                       document.getElementById('edit-derivative-issue').value = issue[4];
                    });
                }
                else{
                    document.getElementById('edit-issue-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-issue-value-field js-form-item-issue-value-field")['0'].style.display = 'none';
                }
            }
        }
    }
})(jQuery, Drupal);
