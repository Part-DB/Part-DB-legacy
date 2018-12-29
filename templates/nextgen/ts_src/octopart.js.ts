/*
 *
 * Part-DB Version 0.4+ "nextgen"
 * Copyright (C) 2016 - 2018 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

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