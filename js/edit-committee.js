(function ($, Drupal) {
    Drupal.behaviors.wash_sem_comm_edit = {
        attach: function(context, settings) {
            window.onload = function () {
                var vcommitteeid = $('#edit-committee-value').val();
                if (vcommitteeid != "add") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/committee',
                        data: { issueid:vissueid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-committee-name').value = issue[0];
                        document.getElementById('edit-committee-id').value = issue[1];

                    });
                }
                else{
                    document.getElementById('edit-committee-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-committee-value js-form-item-committee-value")['0'].style.display = 'none';
                }
            }
        }
    }
})(jQuery, Drupal);
