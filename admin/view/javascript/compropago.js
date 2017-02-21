$(document).ready(function(){
    $('.cp-provider').click(function (elem) {
        $(this).toggleClass('cp-selected');
    });

    $('#send-config').click(function () {
        setSelectedProviders();
        $('#form').submit();
    });
});

function setSelectedProviders() {
    var all_selected = $('.cp-provider.cp-selected');
    var concat = '';

    for (var x = 0; x <= all_selected.length; x++) {
        if (concat == '') {
            concat = $(all_selected[x]).attr('data-provider');
        } else {
            concat = concat + ',' + $(all_selected[x]).attr('data-provider');
        }
    }

    $('#compropago_active_providers').val(concat);
}