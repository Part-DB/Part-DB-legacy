//import {addURLparam, openInNewTab, openLink, scrollUpForMsg} from "./functions";

let BASE="";


/****************************************************************************************
 * **************************************************************************************
 *                                      AjaxUI Class
 * **************************************************************************************
 ****************************************************************************************/


class AjaxUI {

    private static singleton : AjaxUI;

    private _this = this;

    private model_list;
    private img_list;

    private ajax_complete_listeners : Array<() => void> = [];
    private start_listeners : Array<() => void> = [];

    private trees_filled : boolean = false;

    private xhrPool : Array<JQueryXHR> = [];

    /**
     * Creates a new AjaxUI object.
     */
    private constructor()
    {
        //Make back in the browser go back in history
        window.onpopstate = this.onPopState;
        $(document).ajaxError(this.onAjaxError.bind(this));
        $(document).ajaxComplete(this.onAjaxComplete.bind(this));
    }

    /****************************************************************************
     * Public functions
     ***************************************************************************/

    /**
     * Gets a instance of AjaxUI. If no instance exits, then a new one is created.
     * @returns {AjaxUI} A instance of AjaxUI.
     */
    public static getInstance() : AjaxUI
    {
        if(AjaxUI.singleton == null || AjaxUI.singleton == undefined)
        {
            AjaxUI.singleton = new AjaxUI();
        }
        return AjaxUI.singleton;
    }

    /**
     * Starts the ajax ui und execute handlers registered in addStartAction().
     * Should be called in a document.ready, after handlers are set.
     */
    public start()
    {
        let page : string = window.location.pathname;

        //Set base path
        BASE = getBasePath();

        let _this = this;

        $.ajaxSetup(
            {beforeSend: function(jqXHR) { _this.xhrPool.push(jqXHR); }
            });

        this.checkRedirect();

        /* //Only load start page when on index.php (and no content is loaded already)!
         if (page.indexOf(".php") === -1 || page.indexOf("index.php") !== -1) {
             openLink("startup.php");
         }*/

        this.tree_fill();
        this.registerForm();
        this.registerLinks();

        this.getTypeaheadData();

        //Calls registered actions
        for (let entry of this.start_listeners)
        {
            entry();
        }
    }

    /**
     * Check if the Page should be redirected.
     */
    public checkRedirect()
    {
        if($("input#redirect_url").val() != null) {
            let redirect_url : string = $("input#redirect_url").val().toString();
            if(redirect_url != "") {
                openLink(redirect_url);
            }
        }
    }

    /**
     * Register a function, which will be executed every time, a ajax request was successful.
     * Should be used to register functions for elements in the #content div
     * @param {() => void} func The function which should be registered.
     */
    public addAjaxCompleteAction(func : ()=>void)
    {
        this.ajax_complete_listeners.push(func);
    }

    /**
     * Register a function, which will be called once, when start() is run.
     * Should be used to register functions for elements outside the #content div.
     * @param {() => void} func The function which should be registered.
     */
    public addStartAction(func: ()=>void)
    {
        this.start_listeners.push(func);
    }

    /*****************************************************************************
     * Form functions
     *****************************************************************************/

    /**
     * Registers all forms to use with jQuery.Form
     */
    private registerForm() {
        'use strict';

        let data : JQueryFormOptions = {
            success:  this.showFormResponse,
            beforeSubmit: this.showRequest,
            beforeSerialize: this.form_beforeSerialize
        };
        $('form').not(".no-ajax").ajaxForm(data);
    }



    /**
     * Called when Form submit was submited and we received a response.
     * We use it load the ajax content into the #content div and deactivate the loading bar.
     */
    private showFormResponse(responseText, statusText, xhr, $form) {
        'use strict';
        $("#content").html($(responseText).find("#content-data").html()).fadeIn('slow');
    }

    /**
     * Modify the form, so tristate checkbox values are submitted, even if the checkbox is not a succesfull control (value = checked)
     * @param $form
     * @param options
     */
    private form_beforeSerialize($form, options) {
        $form.find("input[type=checkbox].tristate").each(function(index)  {
            let name = $(this).attr("name");
            let value = $(this).val();
            $form.append('<input type="hidden" name="' + name + '" value="' + value + '">');
        });

        $form.find("input[type=checkbox].tristate").remove();

        return true;
    }

    /**
     * Called directly after a form was submited, and no content is requested yet.
     * We use it to show a progbar, if the form dont have a .no-progbar class.
     * @param formData Array<any>
     * @param jqForm
     * @param options
     */
    private showRequest(formData, jqForm: JQuery<HTMLElement>, options: JQueryFormOptions) : boolean {
        'use strict';
        if(!$(jqForm).hasClass("no-progbar")) {
            $('#content').hide(0);
            AjaxUI.getInstance().beforeAjaxSubmit();
            $('#progressbar').show(0);
        }
        return true;
    }

    /**
     * Unregister the form submit event on every button which has a "submit" class.
     * We need this, because when a form has multiple submit buttons, it is not specified, whose value is transmitted.
     * In that case, you has to call submitFormSubmitBtn() in onclick handler.
     */
    private registerSubmitBtn()
    {
        let _this = this;
        $("button.submit").unbind("click").click(function(){
            _this.submitFormSubmitBtn($(this).closest("form"), this);
        });
    }

    /**
     * Submit the given Form and shows a loading bar, if the form doesn't have a ".no-progbar" class.
     * @param form The Form which should be submited.
     */
    public submitForm(form) {
        'use strict';
        let data : JQueryFormOptions = {
            success: this.showFormResponse,
            beforeSubmit: this.showRequest
        };
        $(form).ajaxSubmit(data);
    }

    /**
     * Submit a form, via the given Button (it's value gets appended to request).
     * Needed when the submit buttons in the form has the "submit" class and we has to submit the form manually.
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

    /********************************************************************************
     * Link functions
     ********************************************************************************/

    /**
     * Registers every link (except the ones with .link-external or .link-anchor classes) for usage of Ajax.
     */
    private registerLinks() : void {
        'use strict';
        var _this = this;

        $("a").not(".link-anchor, .link-collapse, .link-external, .tree-btns, .back-to-top, .link-datasheet").unbind("click").click(function (event) {
            event.preventDefault();
            let a = $(this);
            if(a.attr("href") != null) {
                let href : string = addURLparam(a.attr("href"), "ajax"); //We dont need the full version of the page, soft request only the content
                _this.abortAllAjax();

                _this.beforeAjaxSubmit();
                $('#content').hide(0).load(href + " #content-data");
                $('#progressbar').show(0);
                return true;
            }
        });

        $("a.link-anchor").unbind("click").click(function (event) {
            event.preventDefault();
            scrollToAnchor($(this).prop("hash"));
        })
    }

    /***********************************************************************************
     * TreeView functions
     ***********************************************************************************/

    /**
     * Called whenever a node from the TreeView is clicked.
     * We use it to start a ajax request, to expand the node and to close the sidebar div on mobile view.
     * When the link contains "github.com" the link is opened in a new tab: We use this for the help node.
     * @param event
     * @param {BootstrapTreeViewNodeData} data
     */
    private onNodeSelected(event, data : BootstrapTreeViewNodeData) {
        'use strict';
        if(data.href.indexOf("github.com") !== -1 || data.href.indexOf("phpdoc") !== -1)  //If the href points to github, then open it in new tab. TODO: Find better solution to detect external links.
        {
            openInNewTab(data.href);
            $(this).treeview('toggleNodeSelected',data.nodeId);
        }
        else
        {
            AjaxUI.getInstance().abortAllAjax();
            $('#content').hide().load(addURLparam(data.href, "ajax") + " #content-data");
            $('#progressbar').show();
        }

        $(this).treeview('toggleNodeExpanded',data.nodeId);

        $("#sidebar-container").removeClass("show");
    }

    /**
     * Called whenever a node from the TreeView is clicked.
     * We use it to start a ajax request, to expand the node and to close the sidebar div on mobile view.
     * When the link contains "github.com" the link is opened in a new tab: We use this for the help node.
     * @param event
     * @param {BootstrapTreeViewNodeData} data
     */
    private onNodeContextmenu(event, data : BootstrapTreeViewNodeData) {
        'use strict';

        if(data.href !== "") {
            openInNewTab(data.href);
        }
    }

    /**
     * Request JSON files describing the TreeView nodes and fill them with that.
     */
    private tree_fill() {
        'use strict';

        let categories =  Cookies.get("tree_datasource_tree-categories");
        let devices =  Cookies.get("tree_datasource_tree-devices");
        let tools =  Cookies.get("tree_datasource_tree-tools");

        if(typeof categories == "undefined") {
            categories = "categories";
        }

        if(typeof devices == "undefined") {
            devices = "devices";
        }

        if(typeof tools == "undefined") {
            tools = "tools";
        }

        this.treeLoadDataSource("tree-categories", categories);
        this.treeLoadDataSource("tree-devices", devices);
        this.treeLoadDataSource("tree-tools", tools);

        this.trees_filled = true;
    }

    /**
     * Fill a treeview with data from the given url.
     * @param tree The Jquery selector for the tree (e.g. "#tree-tools")
     * @param url The url from where the data should be loaded
     */
    public initTree(tree, url) {
        let node_handler = this.onNodeSelected;
        let contextmenu_handler = this.onNodeContextmenu;

        $.getJSON(BASE + url, function (data : BootstrapTreeViewNodeData[]) {
            $(tree).treeview({data: data, enableLinks: false, showIcon: false
                ,showBorder: true, onNodeSelected: node_handler, onNodeContextmenu: contextmenu_handler,
                expandIcon: "fas fa-plus fa-fw fa-treeview", collapseIcon: "fas fa-minus fa-fw fa-treeview"}).treeview('collapseAll', { silent: true });
        });
    }

    public treeLoadDataSource(target_id, datasource) {
        let text : string = $(".tree-btns[data-mode='" + datasource + "']").html();
        text = text + " \n<span class='caret'></span>"; //Add caret or it will be removed, when written into title

        switch(datasource) {
            case "categories":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/categories');
                $( "#" + target_id + "-title").html(text);
                break;
            case "locations":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/locations');
                $( "#" + target_id + "-title").html(text);
                break;
            case "footprints":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/footprints');
                $("#" + target_id + "-title").html(text);
                break;
            case "manufacturers":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/manufacturers');
                $("#" + target_id + "-title").html(text);
                break;
            case "suppliers":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/suppliers');
                $("#" + target_id + "-title").html(text);
                break;
            case "tools":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/tools');
                $("#" + target_id + "-title").html(text);
                break;
            case "devices":
                ajaxui.initTree("#" + target_id, 'api.php/1.0.0/tree/devices');
                $("#" + target_id + "-title").html(text);
                break;
        }
    }


    /**
     * Update the treeviews.
     */
    public updateTrees()
    {
        this.tree_fill();
    }

    private getTypeaheadData()
    {
        var _this = this;
        $.getJSON("api.php/1.0.0/3d_models/files", function(data){
            _this.model_list = data;
            _this.fillTypeahead();
        });

        $.getJSON("api.php/1.0.0/img_files/files", function(data){
            _this.img_list = data;
            _this.fillTypeahead();
        });
    }

    private fillTypeahead() {
        if($("#models-search").length && !$("#models-search").hasClass("initialized")) {
            $("#models-search").addClass("initialized");
            $("#models-search").typeahead({ source: this.model_list });
        }

        if($("#img-search").length && !$("#img-search").hasClass("initialized")) {
            $("#img-search").addClass("initialized");
            $("#img-search").typeahead({ source: this.img_list });
        }
    }

    /**
     * Aborts all currently active XHR requests.
     */
    public abortAllAjax()
    {
        let _this = this;
        $(this.xhrPool).each(function(i, jqXHR : JQueryXHR) {   //  cycle through list of recorded connection
            jqXHR.abort();  //  aborts connection
            _this.xhrPool.splice(i, 1); //  removes from list by index
        });

    }

    /********************************************************************************************
     * Common ajax functions
     ********************************************************************************************/

    /**
     * Called before a new ajax submit is started. Use this to cleanup, the old page.
     */
    private beforeAjaxSubmit()
    {
        //$(".table-sortable").DataTable().fixedHeader.disable();
    }

    /**
     * Called when an error occurs on loading ajax. Outputs the message to the console.
     */
    private onAjaxError (event, request, settings) {
        'use strict';
        //Ignore aborted requests.
        if (request.statusText =='abort') {
            return;
        }

        console.log(event);
        //If it was a server error and response is not empty, show it to user.
        if(request.status == 500 && request.responseText !== "")
        {
            console.log("Response:" + request.responseText);
        }
    }

    /**
     * This function gets called every time, the "back" button in the browser is pressed.
     * We use it to load the content from history stack via ajax and to rewrite url, so we only have
     * to load #content-data
     * @param event
     */
    private onPopState(event)
    {
        let page : string = location.href;
        //Go back only when the the target isnt the empty index.
        if (page.indexOf(".php") !== -1 && page.indexOf("index.php") === -1) {
            $('#content').hide(0).load(addURLparam(location.href, "ajax") + " #content-data");
            $('#progressbar').show(0);
        }
    }


    /**
     * Called whenever a Ajax Request was successful completed.
     * We use it to hide the progbar and show the requested content, register some elements on the page for ajax usage
     * and change the title of the tab. Also the functions registered via addAjaxCompleteAction() are executed here.
     * @param event
     * @param xhr
     * @param settings
     */
    private onAjaxComplete (event, xhr, settings)
    {
        //Remove the current XHR request from XHR pool.
        let i = this.xhrPool.indexOf(xhr);   //  get index for current connection completed
        if (i > -1) this.xhrPool.splice(i, 1); //  removes from list by index

        let url = settings.url;
        //Ignore all API Ajax requests.
        if (url.indexOf("api.php") != -1) {
            return;
        }

        //Hide progressbar and show Result
        $('#progressbar').hide(0);
        $('#content').fadeIn("fast");

        this.registerForm();
        this.registerLinks();
        this.registerSubmitBtn();

        this.fillTypeahead();



        if(url.indexOf("#") != -1)
        {
            let hash = url.substring(url.indexOf("#"));
            scrollToAnchor(hash);
        }

        if(url.indexOf("api.php/1.0.0/3d_models") != -1)
        {
            return;
        }

        this.checkRedirect();

        //Execute the registered handlers.
        for(let entry of this.ajax_complete_listeners)
        {
            entry();
        }

        //Push only if it was a "GET" request and requested data was an HTML
        if (settings.type.toLowerCase() !== "post" && settings.dataType !== "json" && settings.dataType !== "jsonp") {

            //Push the cleaned (no ajax request) to history
            window.history.pushState(null, "", removeURLparam(settings.url, "ajax"));

            //Update redirect param in login link:
            $("#login-link").attr("href", "login.php?redirect=" + encodeURIComponent(url));

            //Set page title from response
            let input : string = xhr.responseText;
            let title : string = extractTitle(input);

            if(title !== "")
            {
                document.title = title;
            }

            if(this.trees_filled) {
                //Maybe deselect the treeview nodes if, we are not on the site, that it has requested.
                let selected = $("#tree-categories").treeview("getSelected")[0];
                //If the current page, does not contain the url of the selected tree node...
                if (typeof selected !== 'undefined' && settings.url.indexOf(selected.href) == -1) {
                    $('#tree-categories').treeview('unselectNode', [selected.nodeId, {silent: true}]);
                }

                //The same for devices tree
                //Maybe deselect the treeview nodes if, we are not on the site, that it has requested.
                selected = $("#tree-devices").treeview("getSelected")[0];
                //If the current page, does not contain the url of the selected tree node...
                if (typeof selected !== 'undefined' && settings.url.indexOf(selected.href) == -1) {
                    $('#tree-devices').treeview('unselectNode', [selected.nodeId, {silent: true}]);
                }

                //The same for tools tree
                //Maybe deselect the treeview nodes if, we are not on the site, that it has requested.
                selected = $("#tree-tools").treeview("getSelected")[0];
                //If the current page, does not contain the url of the selected tree node...
                if (typeof selected !== 'undefined' && settings.url.indexOf(selected.href) == -1) {
                    $('#tree-tools').treeview('unselectNode', [selected.nodeId, {silent: true}]);
                }
            }
        }
    }
}

/*********************************************************************************
 * AjaxUI additions
 ********************************************************************************/

let ajaxui : AjaxUI = AjaxUI.getInstance();

/**
 * Register the events which has to be run in AjaxUI and start the execution.
 */
$(function(event){

    ajaxui.addStartAction(addCollapsedClass);
    ajaxui.addStartAction(fixSelectPaginationHeight);
    ajaxui.addStartAction(treeviewBtnInit);
    ajaxui.addStartAction(registerJumpToTop);
    ajaxui.addStartAction(makeTooltips);
    ajaxui.addStartAction(fixCurrencyEdits);
    ajaxui.addStartAction(registerAutoRefresh);
    ajaxui.addStartAction(scrollUpForMsg);
    ajaxui.addStartAction(makeSortTable);
    ajaxui.addStartAction(rightClickSubmit);
    ajaxui.addStartAction(makeTriStateCheckbox);
    ajaxui.addStartAction(makeHighlight);
    ajaxui.addStartAction(viewer3d_models);
    ajaxui.addStartAction(makeGreekInput);
    ajaxui.addStartAction(makeCharts);
    ajaxui.addStartAction(setBootstrapSelectStyle);
    ajaxui.addStartAction(makeDateTimePickers);

    ajaxui.addAjaxCompleteAction(addCollapsedClass);
    ajaxui.addAjaxCompleteAction(fixSelectPaginationHeight);
    ajaxui.addAjaxCompleteAction(registerHoverImages);
    ajaxui.addAjaxCompleteAction(makeSortTable);
    ajaxui.addAjaxCompleteAction(makeFileInput);
    ajaxui.addAjaxCompleteAction(makeTooltips);
    ajaxui.addAjaxCompleteAction(registerX3DOM);
    ajaxui.addAjaxCompleteAction(registerBootstrapSelect);
    ajaxui.addAjaxCompleteAction(fixCurrencyEdits);
    ajaxui.addAjaxCompleteAction(registerAutoRefresh);
    ajaxui.addAjaxCompleteAction(scrollUpForMsg);
    ajaxui.addAjaxCompleteAction(rightClickSubmit);
    ajaxui.addAjaxCompleteAction(makeTriStateCheckbox);
    ajaxui.addAjaxCompleteAction(makeHighlight);
    ajaxui.addAjaxCompleteAction(viewer3d_models);
    ajaxui.addAjaxCompleteAction(makeGreekInput);
    ajaxui.addAjaxCompleteAction(makeCharts);
    ajaxui.addAjaxCompleteAction(makeDateTimePickers);

    //ajaxui.addAjaxCompleteAction(makeTypeAhead);

    ajaxui.start();
});

function makeDateTimePickers() {
    $('.datetime').each(function() {
        $(this).datetimepicker({
            useCurrent: false,
            icons: {
                time: "fas fa-clock fa-lg",
                clear: 'fas fa-trash-alt fa-lg',
                today: "fa fa-calendar-check fa-lg"
            },
            format: "YYYY-MM-DD hh:mm:ss",
            buttons: {
                showToday: true
            },
            defaultDate: $(this).data("default-date")
        });
    });


    $("#datetimepicker_from").on("change.datetimepicker", function (e) {
        $('#datetimepicker_to').datetimepicker('minDate', e.date);
    });
    $("#datetimepicker_to").on("change.datetimepicker", function (e) {
        $('#datetimepicker_from').datetimepicker('maxDate', e.date);
    });
}

function setBootstrapSelectStyle() {
    //Set a style for the Bootstrap-select which looks more like the BS3 ones, and the form-controls
    $.fn.selectpicker.Constructor.DEFAULTS.style = "bg-white border";
}

function makeCharts() {
    $(".chart").each(function(index, element) {
        let data : any = $(element).data("data");
        let type : string = $(element).data("type");

        let ctx = (<HTMLCanvasElement> element).getContext("2d");
        let myChart = new Chart(ctx, {
            type: type,
            data: data,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
}

function makeGreekInput() {

    $("input[type=text], textarea, input[type=search]").unbind("keydown").keydown(function (event : KeyboardEvent) {
        let greek = event.altKey;

        let greek_char : string = "";
        if (greek){
            switch(event.key) {
                case "w": //Omega
                    greek_char = '\u2126';
                    break;
                case "u":
                case "m": //Micro
                    greek_char = "\u00B5";
                    break;
                case "p": //Phi
                    greek_char = "\u03C6";
                    break;
                case "a": //Alpha
                    greek_char = "\u03B1";
                    break;
                case "b": //Beta
                    greek_char = "\u03B2";
                    break;
                case "c": //Gamma
                    greek_char = "\u03B3";
                    break;
                case "d": //Delta
                    greek_char = "\u03B4";
                    break;
                case "l": //Pound
                    greek_char = "\u00A3";
                    break;
                case "y": //Yen
                    greek_char = "\u00A5";
                    break;
                case "o": //Yen
                    greek_char = "\u00A4";
                    break;
                case "1": //Sum symbol
                    greek_char = "\u2211";
                    break;
                case "2": //Integral
                    greek_char = "\u222B";
                    break;
                case "3": //Less-than or equal
                    greek_char = "\u2264";
                    break;
                case "4": //Greater than or equal
                    greek_char = "\u2265";
                    break;
                case "5": //PI
                    greek_char = "\u03c0";
                    break;
                case "q": //Copyright
                    greek_char = "\u00A9";
                    break;
                case "e": //Euro
                    greek_char = "\u20AC";
                    break;
            }

            if(greek_char=="") return;

            let $txt = $(this);
            let caretPos = $txt[0].selectionStart;
            let textAreaTxt = $txt.val();
            $txt.val(textAreaTxt.substring(0, caretPos) + greek_char + textAreaTxt.substring(caretPos) );

        }
    });
    this.greek_once = true;
}

// noinspection JSUnusedGlobalSymbols
function makeTypeAhead() {
    if($("#models-search").length && !$("#models-search").hasClass("initialized")) {
        $("#models-search").addClass("initialized");
        $.getJSON("api.php/1.0.0/3d_models/files", function(data){
            //alert("Filled");
            $("#models-search").typeahead({ source:data });
        });
    }
}

function makeTriStateCheckbox() {
    $(".tristate").tristate( {
        checked:            "true",
        unchecked:          "false",
        indeterminate:      "indeterminate",
    });
}

/**
 * Registers the popups for the hover images in the table-
 */
function registerHoverImages() {
    'use strict';
    $('img[rel=popover]').popover({
        html: true,
        trigger: 'hover',
        placement: 'auto',
        container: 'body',
        content: function () {
            return '<img class="img-fluid" src="' + this.src + '" />';
        }
    });
}

/**
 * Activate the features of Datatables for the .table-sortable tables on the page.
 */
function makeSortTable() {
    'use strict';

    //Remove old datatables
    let table = $($.fn.dataTable.tables()).DataTable();
    table.fixedHeader.adjust();



    //Register export helpers
    $(".export-helper").each(function(index : int) {
        let input = $(this).siblings("input");
        let that = this;
        $(this).text(input.val().toString());
    });

    //Override datatables button style, so buttons are XS.
    $.extend( true, $.fn.DataTable.Buttons.defaults, {
        dom: {
            container: {
                className: 'dt-buttons btn-group float-right'
            },
            button: {
                className: 'btn btn-light btn-xs border'
            },
            collection: {
                tag: 'ul',
                className: 'dt-button-collection dropdown-menu',
                button: {
                    tag: 'li',
                    className: 'dt-button',
                    active: 'active',
                    disabled: 'disabled'
                },
                buttonLiner: {
                    tag: 'a',
                    className: ''
                }
            }
        }
    } );

    //The string that should appear in the footer.
    let exportFooter = function() {
        return "Generated by Part-DB on " + new Date().toLocaleString();
    };

    let exportTitle = function () {
        if($("#export-title").length) {
            return $("#export-title").text();
        }
        return "*"; //Show default title
    };

    let exportMessageTop = function () {
        if($("#export-messageTop").length) {
            return $("#export-messageTop").text();
        }
        return "*"; //Show default title
    };

    if (!$.fn.DataTable.isDataTable('.table-sortable')) {
        let table = $('.table-sortable').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false,
            "fixedHeader": { header: $(window).width() >= 768, //Only enable fixedHeaders on devices with big screen. Fixes scrolling issues on smartphones.
                headerOffset: $("#navbar").height()},
            "searching":   false,
            "select":   $(".table-sortable").hasClass("table-selectable") ? {style: "os", selector: "td:not(.no-select)"} : false,
            "order": [],
            "buttons": $(".table-sortable").hasClass("table-export") ? [
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fas fa-copy fa-fw"></i>',
                    titleAttr: 'Copy',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fas fa-file-excel fa-fw"></i>',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    },
                    messageBottom: exportFooter,
                    messageTop: exportMessageTop,
                    title: exportTitle

                },
                {
                    extend:    'csvHtml5',
                    text:      '<i class="fas fa-file-alt fa-fw"></i>',
                    titleAttr: 'CSV',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fas fa-file-pdf fa-fw"></i>',
                    titleAttr: 'PDF',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    },
                    messageBottom: exportFooter,
                    messageTop: exportMessageTop,
                    title: exportTitle
                },
                {
                    extend:    'print',
                    text:      '<i class="fas fa-print fa-fw"></i>',
                    titleAttr: 'Print',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    },
                    messageBottom: exportFooter,
                    messageTop: exportMessageTop,
                    title: exportTitle
                },
            ] : null,
            "columnDefs": [
                {
                    "targets": [1], type: "natural-nohtml"
                }, {
                    targets: 'no-sort', orderable: false
                }]
        });

        if($("#auto_sort").val() == true) {
            table.columns(".order-default").order('asc').draw();
        }

        table
            .on( 'select deselect', function ( e, dt, type, indexes ) {
                let data = table.rows( { selected: true } );
                let count = data.count();
                let tmp = [];
                //Show The select action bar only, if a element is selected.
                if(count > 0) {
                    $(".select_actions").show();
                    $(".selected_n").text(count);
                    //Build a string containing all parts, that should be modified
                    for (let n of data[0]) {
                        tmp.push($(data.row(n).node()).find("input").val());
                    }
                } else {
                    $(".select_actions").hide();
                }

                //Combine all selected IDs into a string.
                let str = tmp.join();
                $("input[name='selected_ids']").val(str);
            } );

        for(let n = 0; n < table.context.length; n++) {
            let my_panel_header = $(table.table(n).container()).closest(".card").find(".card-header");

            table.table(n).buttons().container().appendTo(my_panel_header);
            //table.buttons(n, null).containers().appendTo(my_panel_header);
        }
    }
}

/**
 * Use jQuery.fileinput for fileinputs.
 */
function makeFileInput() {
    'use strict';
    $(".file").fileinput();
}

/**
 * Register the button, to jump to the top of the page.
 */
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
    }).tooltip();
}

/**
 * This function add a hidden input element, if a button with the class ".rightclick" is rightclicked.
 */
function rightClickSubmit()
{
    let _ajaxui = AjaxUI.getInstance();

    $("button.rightclick").off("contextmenu").contextmenu(function (event) {
        event.preventDefault();

        let form = $(this).closest("form");
        form.append('<input type="hidden" name="rightclicked" value="true">');
        _ajaxui.submitFormSubmitBtn(form, this);

        return false;
    });
}

/**
 * Registers the collapse/expand all buttons of the TreeViews
 */
function treeviewBtnInit() {
    $(".tree-btns").click(function (event) {
        event.preventDefault();
        $(this).parents("div.dropdown").removeClass('show');
        //$(this).closest(".dropdown-menu").removeClass('show');
        $(".dropdown-menu.show").removeClass("show");
        let mode = $(this).data("mode");
        let target = $(this).data("target");
        let text = $(this).text() + " \n<span class='caret'></span>"; //Add caret or it will be removed, when written into title

        if (mode==="collapse") {
            $('#' + target).treeview('collapseAll', { silent: true });
        }
        else if(mode==="expand") {
            $('#' + target).treeview('expandAll', { silent: true });
        } else {
            Cookies.set("tree_datasource_" + target, mode);
            ajaxui.treeLoadDataSource(target, mode);
        }

        return false;
    });
}

/**
 * Activates the X3Dom library on all x3d elements.
 */
async function registerX3DOM() {
    if ($("x3d").length) {
        try {
            x3dom.reload();
        } catch(e) {
            //Ignore everything
        }
    }
}

/**
 * Activates the Bootstrap-selectpicker.
 */
async function registerBootstrapSelect() {
    $(".selectpicker").selectpicker();
}

/**
 * Add collapsed class to a before a collapse panel body, so the icon is correct.
 */
function addCollapsedClass() {
    $('div.collapse.card-collapse').siblings("div.card-header")
        .children('a[data-toggle="collapse"]').addClass("collapsed");
}

/**
 * Fix price edit fields. HTML wants prices with a decimal dot, Part-DB gives sometime commas.
 */
function fixCurrencyEdits() {
    let inputs = $('input[type=number]').each(function(index, element){
        let e = $(element);
        if(e.val() == "" && e.prop("defaultValue").indexOf(",") !== -1)
        {
            let newval: string = e.prop("defaultValue").replace(",", ".");
            e.val(newval);
        }
    });
}

/**
 * Register the autorefresh
 */
function registerAutoRefresh() {
    let val : number =  $("#autorefresh").val() as number;
    if(val > 0)
    {
        window.setTimeout(reloadPage, val);
    }
}

function fixSelectPaginationHeight() {
    if(parseInt($('.page-item').css("height")) != 0) {
        $('.pagination>li>select').css('height', parseInt($('.page-item').css("height")));
    }
}

/**
 * Close the #searchbar div, when a search was submitted on mobile view.
 */
$("#search-submit").click(function (event) {
    $("#searchbar").removeClass("show");
});

/**
 * Implements the livesearch for the searchbar.
 * @param object
 * @param {int} threshold
 */
function livesearch(event, object : any, threshold : int) {
    //Ignore enter key.
    if(event.key == "Enter") {
        return;
    }

    let $obj = $(object);
    let q = <string> $obj.val();
    let form = $obj.closest("form");
    //Dont show progbar on live search.
    form.addClass("no-progbar");
    let xhr = form.data('jqxhr');
    //If an ajax operation is already ongoing, then stop it.
    if(typeof xhr !== "undefined") {
        xhr.abort();
    }
    if(q.length >= threshold) {
        submitForm(form);
    }
    else {
        //Only show link, if the text is shorter than before.
        if(event.key == "Backspace") {
            openLink(BASE + "show_search_parts.php?hint");
        }
    }
    //Show progbar, when user presses submit button.
    form.removeClass("no-progbar");
}


function makeHighlight() {
    let highlight = $("#highlight").val();
    if(typeof highlight !== "undefined" && highlight != "") {
        $("table").highlight(highlight, {
            element: "span"
        });
    }
}

/**
 * Use Bootstrap for tooltips.
 * Function need to be not async, otherwise not every tooltip gets removed, when page is loaded.
 */
function makeTooltips() {
    //$('[data-toggle="tooltip"]').tooltip();
    //$('a[title]').tooltip("hide").tooltip({container: "body"});
    //$('body').tooltip('dispose');
    //$(".tooltip").remove();
    //$("body").tooltip({ selector: 'a[title]', container: "body" });
    //$('button[title]').tooltip("hide").tooltip({container: "body"});
    $('a[title]').tooltip("hide").tooltip({container: "body"});
    $('button[title]').tooltip("hide").tooltip({container: "body"});
}

function viewer3d_models() {
    if(!$("#models-picker").length) return;

    var dir = "";

    function update() {
        var name = $("#models-picker").val();
        //dir = $("#tree-footprint").treeview("getSelected").data.href;
        if(dir == "") return;
        var path = "models/" + dir + "/"  + name;
        $("#foot3d-model").attr("url", path);
        $("#foot3d-model2").attr("url", path);

        $("#path").text(path);
    }

    $("#models-picker").change(update);

    function node_handler(event, data) {
        dir = data.href;
        $.getJSON('api.php/1.0.0/3d_models/files/' + dir, function (list) {
            $("#models-picker").empty();
            list.forEach( function (element) {
                $("<option><option/>").val(element).text(element).appendTo("#models-picker");
                $('#models-picker').selectpicker('refresh');

                update();
            });
        });
    }

    $.getJSON('api.php/1.0.0/3d_models/dir_tree', function (tree) {
        $("#tree-footprint").treeview({ data: tree, enableLinks: false, showIcon: false
            ,showBorder: true, onNodeSelected: node_handler }).treeview('collapseAll', { silent: true });
    });

    $("#models-search-go").click(function () {
        var name = $("#models-search").val();
        var path = "models/" + name;
        $("#foot3d-model").attr("url", path);
        $("#foot3d-model2").attr("url", path);

        $("#path").text(path);
    });
}

//Need for proper body padding, with every navbar height
$(window).resize(function () {
    let height : number = $('#navbar').height() + 10;
    $('body').css('padding-top', height);
    $('#fixed-sidebar').css('top', height);
});

$(window).on('load', function () {
    let height : number = $('#navbar').height() + 10;
    $('body').css('padding-top', height);
    $('#fixed-sidebar').css('top', height);
});
