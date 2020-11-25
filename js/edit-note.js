(function ($, Drupal) {
    Drupal.behaviors.wash_sem_note_edit = {
        attach: function(context, settings) {
            window.onload = function () {
                var vnoteid = $('#edit-note-value').val();
                if (vnoteid != "create") {
                    $.ajax({
                        type: 'POST',
                        url: '/nfb_washington/admin/ajax/note',
                        data: { noteid:vnoteid },
                    }).done(function (data) {
                        var issue = data;
                        document.getElementById('edit-note-type').value = issue[0];
                        document.getElementById('edit-note-year').value = issue[1];
                        document.getElementById('edit-note-text').value = issue[2];
                    });
                }
                else{
                    document.getElementById('edit-note-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-note-value js-form-item-note-value")['0'].style.display = 'none';
                }
            }
        }
    }
})(jQuery, Drupal);