//import {addURLparam, openInNewTab, openLink, scrollUpForMsg} from "./functions";
var BASE = "";
var AjaxUI = (function () {
    function AjaxUI() {
        this._this = this;
        //Make back in the browser go back in history
        window.onpopstate = this.onPopState;
        $(document).ajaxError(this.onAjaxError.bind(this));
        $(document).ajaxComplete(this.onAjaxComplete.bind(this));
    }
    AjaxUI.prototype.start = function () {
        var page = window.location.pathname;
        //Only load start page when on index.php (and no content is loaded already)!
        if (page.indexOf(".php") === -1 || page.indexOf("index.php") !== -1) {
            openLink("startup.php");
        }
        this.tree_fill();
        treeview_btn_init();
        this.registerForm();
        this.registerLinks();
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            }
            else {
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
    };
    AjaxUI.prototype.registerForm = function () {
        'use strict';
        var data = {
            success: this.showFormResponse,
            beforeSubmit: this.showRequest
        };
        $('form').ajaxForm(data);
    };
    AjaxUI.prototype.onPopState = function (event) {
        var page = location.href;
        //Go back only when the the target isnt the empty index.
        if (page.indexOf(".php") !== -1 && page.indexOf("index.php") === -1) {
            $('#content').hide(0).load(addURLparam(location.href, "ajax") + " #content-data");
            $('#progressbar').show(0);
        }
    };
    //Called when Form submit was submited
    AjaxUI.prototype.showFormResponse = function (responseText, statusText, xhr, $form) {
        'use strict';
        $("#content").html($(responseText).find("#content-data").html()).fadeIn('slow');
    };
    AjaxUI.prototype.showRequest = function (formData, jqForm, options) {
        'use strict';
        if (!$(jqForm).hasClass("no-progbar")) {
            $('#content').hide(0);
            $('#progressbar').show(0);
        }
    };
    AjaxUI.prototype.registerLinks = function () {
        'use strict';
        $("a").not(".link-anchor").not(".link-external").not(".tree-btns").unbind("click").click(function (event) {
            event.preventDefault();
            var a = $(this);
            var href = addURLparam(a.attr("href"), "ajax"); //We dont need the full version of the page, so request only the content
            $('#content').hide(0).load(href + " #content-data");
            $('#progressbar').show(0);
            return true;
        });
    };
    //Called when an error occurs on loading ajax
    AjaxUI.prototype.onAjaxError = function (event, request, settings) {
        'use strict';
        console.log(event);
    };
    AjaxUI.prototype.onNodeSelected = function (event, data) {
        'use strict';
        if (data.href.indexOf("github.com") !== -1) {
            openInNewTab(data.href);
            $(this).treeview('toggleNodeSelected', data.nodeId);
        }
        else {
            $('#content').hide().load(addURLparam(data.href, "ajax") + " #content-data");
            $('#progressbar').show();
        }
        //$('#content').fadeOut("fast");
        //$('#progressbar').show();
        $(this).treeview('toggleNodeExpanded', data.nodeId);
        $("#sidebar").removeClass("in");
    };
    AjaxUI.prototype.tree_fill = function () {
        'use strict';
        var node_handler = this.onNodeSelected;
        $.getJSON(BASE + 'api_json.php?mode="tree_category"', function (tree) {
            $("#tree-categories").treeview({ data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler }).treeview('collapseAll', { silent: true });
        });
        $.getJSON(BASE + 'api_json.php?mode="tree_devices"', function (tree) {
            $('#tree-devices').treeview({ data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler }).treeview('collapseAll', { silent: true });
        });
        $.getJSON(BASE + 'api_json.php?mode="tree_tools"', function (tree) {
            $('#tree-tools').treeview({ data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler }).treeview('collapseAll', { silent: true });
        });
    };
    AjaxUI.prototype.registerSubmitBtn = function () {
        var _this = this;
        $("button.submit").unbind("click").click(function () {
            _this.submitFormSubmitBtn($(this).closest("form"), this);
        });
    };
    AjaxUI.prototype.submitForm = function (form) {
        'use strict';
        var data = {
            success: this.showFormResponse,
            beforeSubmit: this.showRequest
        };
        $(form).ajaxSubmit(data);
    };
    /**
     * Submit a form, via the given Button (it's value gets appended to request)
     * @param form The form which should be submited.
     * @param btn The button, which was pressed to submit the form.
     */
    AjaxUI.prototype.submitFormSubmitBtn = function (form, btn) {
        var name = $(btn).attr('name');
        var value = $(btn).attr('value');
        if (value === undefined)
            value = "";
        $(form).append('<input type="hidden" name="' + name + '" value="' + value + '">');
        this.submitForm(form);
    };
    AjaxUI.prototype.onAjaxComplete = function (event, xhr, settings) {
        'use strict';
        //Hide progressbar and show Result
        $('#progressbar').hide(0);
        //$('#content').show(0);
        $('#content').fadeIn("fast");
        makeSortTable();
        this.registerForm();
        this.registerLinks();
        makeFileInput();
        registerHoverImages();
        scrollUpForMsg();
        this.registerSubmitBtn();
        if ($("x3d").length) {
            x3dom.reload();
        }
        $(".selectpicker").selectpicker();
        //Push only if it was a "GET" request and requested data was an HTML
        if (settings.type.toLowerCase() !== "post" && settings.dataType !== "json" && settings.dataType !== "jsonp") {
            //Push the cleaned (no ajax request) to history
            window.history.pushState(null, "", settings.url.replace("&ajax", "").replace("?ajax", ""));
            //Set page title from response
            var regex = /<title>(.*?)<\/title>/gi, input = xhr.responseText;
            if (regex.test(input)) {
                var matches = input.match(regex);
                for (var match in matches) {
                    document.title = $(matches[match]).text();
                }
            }
        }
    };
    return AjaxUI;
}());
var ajaxui = new AjaxUI();
$(document).ready(function (event) {
    ajaxui.start();
});
function registerHoverImages() {
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
function makeSortTable() {
    'use strict';
    if (!$.fn.DataTable.isDataTable('.table-sortable')) {
        $('.table-sortable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": false,
            "order": [],
            "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false
                }]
        });
        //$(".table-sortable").DataTable().fnDraw();
    }
}
function makeFileInput() {
    'use strict';
    $(".file").fileinput();
}
$("#search-submit").click(function (event) {
    $("#searchbar").removeClass("in");
});
function treeview_btn_init() {
    $(".tree-btns").click(function (event) {
        event.preventDefault();
        $(this).parents("div.dropdown").removeClass('open');
        var mode = $(this).data("mode");
        var target = $(this).data("target");
        if (mode === "collapse") {
            $('#' + target).treeview('collapseAll', { silent: true });
        }
        else if (mode === "expand") {
            $('#' + target).treeview('expandAll', { silent: true });
        }
        return false;
    });
}
