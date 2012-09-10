$(document).ready( function() {

        // confirmator

        $('a[data-confirm], button[data-confirm], input[data-confirm]').live('click', function (e) {
                 if (!confirm($(this).attr('data-confirm'))) {
                         e.preventDefault();
                         e.stopImmediatePropagation();
                         return false;
                 }
         });
});