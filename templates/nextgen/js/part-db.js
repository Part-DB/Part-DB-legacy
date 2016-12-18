/*jslint browser: true*/
/*global $, jQuery, alert, x3dom, console*/

var BASE = "";

function setEnv(path) {
    'use strict';
    BASE = path;
}

function startLoading() {
    'use strict';
}

function openLink(page) {
    'use strict';
    $("#main").load(page + " #content");
    //window.history.pushState(null, "", page);
}

function registerLinks() {
    'use strict';
    $("a").not(".link-anchor").not(".link-external").click(function (event) {
        event.preventDefault();
        var a = $(this),
            href = a.attr("href");
        startLoading();
        $("#main").load(href + " #content");
        return false;
    });
}

//Called when Form submit was submited
function showFormResponse(responseText, statusText, xhr, $form) {
    'use strict';
    $("#main").html($(responseText).find("#content")).fadeIn('slow');
}

function registerForm() {
    'use strict';
    
    var data = {
        success:  showFormResponse
    };
    $('form').ajaxForm(data);
}

function submitForm(form) {
    'use strict';
    var data = {
        success:  showFormResponse
    };
    $(form).ajaxSubmit(data);
}

function registerHoverImages(form) {
    'use strict';
    $('img[rel=popover]').popover({
        html: true,
        trigger: 'hover',
        placement: 'auto',
        container: 'body',
        content: function () {
            return '<img class="img-responsive" src="' + this.src + '" />';
        }
    });
}

function onNodeSelected(event, data) {
    'use strict';
    $("#main").load(data.href + " #content");
}

function tree_fill() {
    'use strict';
    $.getJSON(BASE + 'api_json.php?mode="tree_category"', function (tree) {
        $('#tree-categories').treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: onNodeSelected}).treeview('collapseAll', { silent: true });
    });
    
    $.getJSON(BASE + 'api_json.php?mode="tree_devices"', function (tree) {
        $('#tree-devices').treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: onNodeSelected}).treeview('collapseAll', { silent: true });
    });
    
    $.getJSON(BASE + 'api_json.php?mode="tree_tools"', function (tree) {
        $('#tree-tools').treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: onNodeSelected}).treeview('collapseAll', { silent: true });
    });
}

function bbcode_edit() {
    // Create the editor
    $("textarea").sceditor({
        // Options go here

        // Option 1
        plugins: "bbcode",

        emoticonsEnabled: false,
        runWithoutWysiwygSupport: true
    });
}

$(document).ready(function () {
    'use strict';
    var page = window.location.pathname;
    
    //Only load start page when on index.php (and no content is loaded already)!
    if (page.indexOf(".php") === -1 || page.indexOf("index.php") !== -1) {
        openLink("startup.php");
    }
    tree_fill();
    registerForm();
    registerLinks();

    //bbcode_edit();
    
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    }).tooltip('show');

    
});

function makeSortTable() {
    'use strict';
    
    if (!$.fn.DataTable.isDataTable('.table-sortable')) {
        $('.table-sortable').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false,
            "searching":   false,
            "order": [],
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false
            }]
        });
        
    }
}

function makeFileInput() {
    'use strict';
    $(".file").fileinput();
}

//Make back in the browser go back in history
window.onpopstate = function (event) {
    'use strict';
    var page = location.href;
    //Go back only when the the target isnt the empty index.
    if (page.indexOf(".php") !== -1 && page.indexOf("index.php") === -1) {
        $("#main").load(location.href + " #content");
    }
};


$(document).ajaxComplete(function (event, xhr, settings) {
    'use strict';
    makeSortTable();
    registerLinks();
    registerForm();
    makeFileInput();
    registerHoverImages();
    
    if ($("x3d").length) {
        x3dom.reload();
    }
        
    //Push only if it was a "GET" request and requested data was an HTML
    if (settings.type.toLowerCase() !== "post" && settings.dataType !== "json" && settings.dataType !== "jsonp") {
        window.history.pushState(null, "", settings.url);
        
        //Set page title from response
        var regex = /<title>(.*?)<\/title>/gi,
            input = xhr.responseText;
        if (regex.test(input)) {
            var matches = input.match(regex);
            for(var match in matches) {
                document.title = $(matches[match]).text();
            }
        }
    }
});


//Called when an error occurs on loading ajax
$(document).ajaxError(function (event, request, settings) {
    'use strict';
    console.log(event);
    
});

function octoPart_success(response) {
    'use strict';
    $('#description_select').modal('show');
    $('#description').val(response.results[0].snippet);
}

function octoPart() {
    'use strict';
    
    var url = 'http://octopart.com/api/v3/parts/search?',
        part = $('#name').val();
    
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

