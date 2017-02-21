$(document).ready(function(){

    $('.cp-provider').click(function () {
        cleanSelection();
        $(this).addClass('cp-selected');
        $('#provider_cp').val($(this).attr('data-provider'));
    });

    $('#button_confirm').click(function () {
        if ($('#provider_cp').val() != '') {
            $('#cp-form').submit();
        } else {
            alert('Seleccione un establecimiento para realizar el pago');
        }
    });

});

function cleanSelection() {
    var all = $('.cp-provider');

    for (var x = 0; x <= all.length; x++) {
        $(all[x]).removeClass('cp-selected');
    }
}
