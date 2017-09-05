//import ajaxui from "./ajax_ui";
/**
 * Opens the given Link in the #content div
 * @param {string} page The URL that should be opened. (Must be on Part-DB)
 */
function openLink(page) {
    'use strict';
    $('#content').load(page + " #content-data");
}
/**
 * Add the given param to a existing URL.
 * @param {string} url The URL which should be modified.
 * @param {string} param The param (in Form "key=value", or simply key) which should be appended to the URL
 * @returns {string} The url with the appended parameter.
 */
function addURLparam(url, param) {
    'use strict';
    var hash = "";
    if (url.indexOf("#") != -1) {
        hash = url.substring(url.indexOf("#"));
        url = url.replace(hash, "");
    }
    //If url already contains a ? than use a & for param addition
    if (url.indexOf('?') >= 0) {
        url = url + "&" + param;
    }
    else {
        url = url + "?" + param;
    }
    return url + hash;
}
/**
 * Removes the given param from the url.
 * @param {string} url The URL which should be modified.
 * @param {string} param The param (in Form "key=value", or simply key) which should be removed from the URL
 * @returns {string} The url without the specified parameter.
 */
function removeURLparam(url, param) {
    'use strict';
    return url.replace("&" + param, "").replace("?" + param, "");
}
/**
 * Submit the given Form and shows a loading bar, if the form doesn't have a ".no-progbar" class.
 * @param form The Form which should be submited.
 */
function submitForm(form) {
    'use strict';
    ajaxui.submitForm(form);
}
/**
 * Submit a form, via the given Button (it's value gets appended to request)
 * Needed when the submit buttons in the form has the "submit" class and we has to submit the form manually.
 * @param form The form which should be submited.
 * @param btn The button, which was pressed to submit the form.
 */
function submitFormSubmitBtn(form, btn) {
    ajaxui.submitFormSubmitBtn(form, btn);
}
/**
 * Extract the title (The name between the <title> tags) of a HTML snippet.
 * @param {string} html The HTML code which should be searched.
 * @returns {string} The title extracted from the html.
 */
function extractTitle(html) {
    var title = "";
    var regex = /<title>(.*?)<\/title>/gi;
    if (regex.test(html)) {
        var matches = html.match(regex);
        for (var match in matches) {
            title = $(matches[match]).text();
        }
    }
    return title;
}
/**
 * Opens the given URL in a new tab.
 * @param {string} url The URL which should be opened in a new Tab.
 */
function openInNewTab(url) {
    $("<a>").attr("href", url).attr("target", "_blank")[0].click();
}
/**
 * Scrolls Up, if a message is shown.
 * @returns {boolean}
 */
function scrollUpForMsg() {
    if ($("#messages").length) {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    }
}
/**
 * Scroll to the given anchor.
 * @param {string} anchor The anchor, to which should be scrolled (with the #)
 */
function scrollToAnchor(anchor) {
    if (anchor.indexOf("#") == -1)
        throw new Error("The anchor string must contain a #.");
    $(document).scrollTop($(anchor).offset().top - 100);
}
/**
 * Returns the base path of Part-DB
 * @returns {string} The base path.
 */
function getBasePath() {
    return String($("#basepath").val());
}
/**
 * Reloads the current Page.
 */
function reloadPage() {
    openLink(document.location.href);
}
