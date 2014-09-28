$(function() {
    $('table tbody tr:not([th]):odd').addClass('odd');
    $('table tbody tr:not([th]):even').addClass('even');

    $('.changePage').live('click', function(){
        $.ajax({
            url: $(this).attr('href') + '&action=changePage',
            type: 'get',
            dataType: 'html',
            success: function(data) {
                $('#contents').html(data);

                $('table tbody tr:not([th]):odd').addClass('odd');
                $('table tbody tr:not([th]):even').addClass('even');
            }
        });
        return false;
    });
});

function request(to, params) {
    $.ajax({
        url: to,
        type: 'post',
        data: params,
        dataType: 'json',
        success: function(data) {
            if(data.error) {
                formError(data.error);
            } else if(data.redirect) {
                window.location = data.redirect;
            }
        }
    });
}

function formError(text) {
    $('#error').show();
    $('#errorText').text(text);
}

function getFieldCounter(name) {
    return localStorage.getItem('field_' + name);
}

function incrementCounter(name) {
    var count = localStorage.getItem('field_' + name);

    if(!count || count == null) {
        count = 1;
    } else {
        count++;
    }

    localStorage.setItem('field_' + name);
}

function addDomain(key, values) {
    localStorage.setItem('domain_' + key, values.toString() );
}

function removeDomain(key) {
    localStorage.removeItem('domain_' + key);
}

function getDomain(key) {
    return localStorage.getItem('domain_' + key);
}