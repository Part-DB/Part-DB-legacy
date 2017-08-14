/**
 * Opens the given Link in the #content div
 * @param {string} page The URL that should be opened. (Must be on Part-DB)
 */
function openLink(page : string) {
    'use strict';
    $('#content').load(page + " #content-data");
}

function addURLparam(url, param)
{
    'use strict';

    //If url already contains a ? than use a & for param addition
    if(url.indexOf('?') >= 0)
    {
        return url + "&" + param;
    }
    else  //Else use a ?
    {
        return url + "?" + param;
    }

}

function submitForm(form) {
    'use strict';
    var data = {
        success:  showFormResponse,
        beforeSubmit: showRequest
    };
    $(form).ajaxSubmit(data);
}

function submitFormSubmitBtn(form, btn) {
    var name = $(btn).attr('name');
    var value = $(btn).attr('value');
    if(value === undefined)
        value = "";

    $(form).append('<input type="hidden" name="' + name + '" value="' + value + '">');
    submitForm(form);
}

function openInNewTab(url) {
    $("<a>").attr("href", url).attr("target", "_blank")[0].click();
}

function scrollUpForMsg()
{
    if($("#messages").length)
    {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    }
}
