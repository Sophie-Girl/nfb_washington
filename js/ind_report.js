(function ($, Drupal) {
    Drupal.behaviors.wash_sem_ind_report = {
        attach: function(context, settings) {
            window.onload = function () {
                    document.getElementById('edit-member-value').style.display = 'none';
                    document.getElementsByClassName("form-item js-form-item form-type-textfield js-form-type-textfield form-item-member-value js-form-item-member-value")['0'].style.display = 'none';
            }
        }
    }
})(jQuery, Drupal);
