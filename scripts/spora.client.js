function desaIncidencia() {
    $.post('?action=saveIncidence', {text: $('#textIncidencia').val()}, function() {
        $('#textIncidencia').text('');
    });

    return false;
}

function desaDireccio() {
    $.post('?action=saveDirection', {
        carrer: $('#carrer').attr('data-id'),
        barri: $('#barri').val(),
        numero: $('#numero').val(),
        planta: $('#planta').val(),
        porta: $('#porta').val()
    }, function() {
        window.location = $('#urlback').attr('href');
    });

    return false;
}

function cercaNumero() {
    $.post('?action=searchNum', {
            carrer: $('#carrer').val(),
            barri: $('#barri').val(),
            text: $('#numero').val()
    }, function(data) {
        if(data.hasOwnProperty('c') && data.hasOwnProperty('start')) {
            window.location = '?page=numero&c=' + data.c + '&start=' + data.start;
        }
    }, 'json');
}

function initParams() {
    window.queryParams = new Array();

    var hash,
        hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        window.queryParams.push(hash[0]);
        window.queryParams[hash[0]] = hash[1];
    }
}

$(function() {
    $.mobile.activeBtnClass = '';
    $.mobile.ajaxLinksEnabled = false;
    $.mobile.ajaxFormsEnabled = false;
    $.mobile.defaultTransition = '';
    $.mobile.loadingMessage = '';

    $('.opcioActuacio').live('change', function() {
        var pieces = $(this).attr('id').split('_', 2);

        if(pieces.length >= 2) {
            $.post('?action=saveActuacio', {
                direccio: pieces[0],
                actuacio: pieces[1],
                set: $(this).is(':checked')
            });
        }
    });
});