function octoPart_success(response) {
    'use strict';
    $('#description_select').modal('show');
    $('#description').val(response.results[0].snippet);
}

function octoPart() {
    'use strict';

    let url : string = 'http://octopart.com/api/v3/parts/search?',
        part : string = $('#name').val();

    url += '&apikey=e418fbe2';

    $.ajax({
        url: url,

        // The name of the callback parameter
        jsonp: "callback",

        // Tell jQuery we're expecting JSONP
        dataType: "jsonp",

        //
        data: {
            q: part
        },

        // Work with the response
        success: octoPart_success
    });
}