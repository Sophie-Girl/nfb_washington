(function ($, Drupal) {
    Drupal.behaviors.coms_request_wash = {
        attach: function (context, settings) {
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

            }

        }
    }
})(jQuery, Drupal)
