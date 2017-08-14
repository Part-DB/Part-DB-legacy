//import {addURLparam, openInNewTab, openLink, scrollUpForMsg} from "./functions";

"use_strict";

let BASE="";

class AjaxUI {

    private static singleton : AjaxUI;

    private _this = this;

    private ajax_complete_listeners : Array<() => void> = [];
    private start_listeners : Array<() => void> = [];

    public static getInstance() : AjaxUI
    {
        if(AjaxUI.singleton == null || AjaxUI.singleton == undefined)
        {
            AjaxUI.singleton = new AjaxUI();
        }
        return AjaxUI.singleton;
    }

    private constructor()
    {
        //Make back in the browser go back in history
        window.onpopstate = this.onPopState;
        $(document).ajaxError(this.onAjaxError.bind(this));
        $(document).ajaxComplete(this.onAjaxComplete.bind(this));
    }

    public start()
    {
        let page : string = window.location.pathname;

        //Only load start page when on index.php (and no content is loaded already)!
        if (page.indexOf(".php") === -1 || page.indexOf("index.php") !== -1) {
            openLink("startup.php");
        }

        this.tree_fill();
        this.registerForm();
        this.registerLinks();

        //Calls registered actions
        for (let entry of this.start_listeners)
        {
            entry();
        }


    }

    public addAjaxCompleteAction(func : ()=>void)
    {
        this.ajax_complete_listeners.push(func);
    }

    public addStartAction(func: ()=>void)
    {
        this.start_listeners.push(func);
    }

    private registerForm() {
        'use strict';

        let data : JQueryFormOptions = {
            success:  this.showFormResponse,
            beforeSubmit: this.showRequest
        };
        $('form').ajaxForm(data);
    }

    private onPopState(event)
    {
        let page : string = location.href;
        //Go back only when the the target isnt the empty index.
        if (page.indexOf(".php") !== -1 && page.indexOf("index.php") === -1) {
            $('#content').hide(0).load(addURLparam(location.href, "ajax") + " #content-data");
            $('#progressbar').show(0);
        }
    }

    //Called when Form submit was submited
    private showFormResponse(responseText, statusText, xhr, $form) {
        'use strict';
        $("#content").html($(responseText).find("#content-data").html()).fadeIn('slow');
    }

    private showRequest(formData, jqForm, options) : void {
        'use strict';
        if(!$(jqForm).hasClass("no-progbar")) {
            $('#content').hide(0);
            $('#progressbar').show(0);
        }
    }

    private registerLinks() : void {
        'use strict';
        $("a").not(".link-anchor").not(".link-external").not(".tree-btns").unbind("click").click(function (event) {
            event.preventDefault();
            let a = $(this);
            let href : string = addURLparam(a.attr("href"), "ajax"); //We dont need the full version of the page, so request only the content

            $('#content').hide(0).load(href + " #content-data");
            $('#progressbar').show(0);
            return true;
        });
    }

    //Called when an error occurs on loading ajax
    private onAjaxError (event, request, settings) {
        'use strict';
        console.log(event);
    }

    private onNodeSelected(event, data : BootstrapTreeViewNodeData) {
        'use strict';
        if(data.href.indexOf("github.com") !== -1)  //If the href points to github, then open it in new tab. TODO: Find better solution to detect external links.
        {
            openInNewTab(data.href);
            $(this).treeview('toggleNodeSelected',data.nodeId);
        }
        else
        {
            $('#content').hide().load(addURLparam(data.href, "ajax") + " #content-data");
            $('#progressbar').show();
        }

        //$('#content').fadeOut("fast");
        //$('#progressbar').show();

        $(this).treeview('toggleNodeExpanded',data.nodeId);

        $("#sidebar").removeClass("in");
    }

    private tree_fill() {
        'use strict';

        let node_handler = this.onNodeSelected;

        $.getJSON(BASE + 'api_json.php?mode="tree_category"', function (tree : BootstrapTreeViewNodeData) {
            $("#tree-categories").treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler}).treeview('collapseAll', { silent: true });
        });

        $.getJSON(BASE + 'api_json.php?mode="tree_devices"', function (tree :BootstrapTreeViewNodeData) {
            $('#tree-devices').treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler}).treeview('collapseAll', { silent: true });
        });

        $.getJSON(BASE + 'api_json.php?mode="tree_tools"', function (tree :BootstrapTreeViewNodeData) {
            $('#tree-tools').treeview({data: tree, enableLinks: false, showBorder: true, onNodeSelected: node_handler}).treeview('collapseAll', { silent: true });
        });
    }



    private registerSubmitBtn()
    {
        let _this = this;
        $("button.submit").unbind("click").click(function(){
            _this.submitFormSubmitBtn($(this).closest("form"), this);
        });
    }

    public submitForm(form) {
        'use strict';
        let data : JQueryFormOptions = {
            success: this.showFormResponse,
            beforeSubmit: this.showRequest
        };
        $(form).ajaxSubmit(data);
    }

    /**
     * Submit a form, via the given Button (it's value gets appended to request)
     * @param form The form which should be submited.
     * @param btn The button, which was pressed to submit the form.
     */
    public submitFormSubmitBtn(form, btn) {
        let name : string = $(btn).attr('name');
        let value : string = $(btn).attr('value');
        if(value === undefined)
            value = "";

        $(form).append('<input type="hidden" name="' + name + '" value="' + value + '">');
        this.submitForm(form);
    }

    private onAjaxComplete (event, xhr, settings) {

        //Hide progressbar and show Result
        $('#progressbar').hide(0);
        //$('#content').show(0);
        $('#content').fadeIn("fast");

        this.registerForm();
        this.registerLinks();
        //makeFileInput();
        //registerHoverImages();
        //scrollUpForMsg();
        this.registerSubmitBtn();

        //Execute the registered handlers.
        for(let entry of this.ajax_complete_listeners)
        {
            entry();
        }

        if ($("x3d").length) {
            x3dom.reload();
        }

        $(".selectpicker").selectpicker();

        //Push only if it was a "GET" request and requested data was an HTML
        if (settings.type.toLowerCase() !== "post" && settings.dataType !== "json" && settings.dataType !== "jsonp") {

            //Push the cleaned (no ajax request) to history
            window.history.pushState(null, "", settings.url.replace("&ajax", "").replace("?ajax", "")  );

            //Set page title from response
            let regex = /<title>(.*?)<\/title>/gi,
                input : string = xhr.responseText;
            if (regex.test(input)) {
                let matches = input.match(regex);
                for(let match in matches) {
                    document.title = $(matches[match]).text();
                }
            }
        }
    }


}

let ajaxui : AjaxUI = AjaxUI.getInstance();

$(document).ready(function(event){

    ajaxui.addStartAction(treeviewBtnInit);
    ajaxui.addStartAction(registerJumpToTop);

    ajaxui.addAjaxCompleteAction(registerHoverImages);
    ajaxui.addAjaxCompleteAction(makeSortTable);
    ajaxui.addAjaxCompleteAction(makeFileInput);

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
        //$(".table-sortable").DataTable().fnDraw();
    }
}
function makeFileInput() {
    'use strict';
    $(".file").fileinput();
}

function registerJumpToTop() {
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
};

function treeviewBtnInit() {
    $(".tree-btns").click(function (event) {
        event.preventDefault();
        $(this).parents("div.dropdown").removeClass('open');
        let mode = $(this).data("mode");
        let target = $(this).data("target");

        if(mode==="collapse") {
            $('#' + target).treeview('collapseAll', { silent: true });
        }
        else if(mode==="expand") {
            $('#' + target).treeview('expandAll', { silent: true });
        }
        return false;
    });
}

$("#search-submit").click(function (event) {
    $("#searchbar").removeClass("in");
});
