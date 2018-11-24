<?php

    $config['system']['current_config_version']              = 2;
    $config['db']['type']                                    = 'mysql';
    $config['db']['host']                                    = 'localhost';
    $config['db']['name']                                    = 'part-db';
    $config['db']['user']                                    = 'part-db';
    $config['db']['password']                                = '19BUFFYfloh99';
    $config['db']['charset']                                 = 'utf8';
    $config['db']['auto_update']                             = false;
    $config['db']['backup']['name']                          = '';
    $config['db']['backup']['url']                           = '';
    $config['db']['update_error']['version']                 = -1;
    $config['db']['update_error']['next_step']               = 0;
    $config['db']['limit']['search_parts']                   = 200;
    $config['db']['space_fix']                               = false;
    $config['html']['http_charset']                          = 'utf-8';
    $config['html']['theme']                                 = 'nextgen';
    $config['html']['custom_css']                            = '';
    $config['update']['type']                                = 'stable';
    $config['startup']['custom_banner']                      = '[url=privacy.html]Privacy Policy[/url]

[br][br]

[size=5]Login:[/size] [br]
[b]Username:[/b] "user" [br]
[b]Password:[/b] "user"';
    $config['startup']['disable_update_list']                = true;
    $config['devices']['disable']                            = false;
    $config['footprints']['disable']                         = false;
    $config['manufacturers']['disable']                      = false;
    $config['auto_datasheets']['disable']                    = false;
    $config['suppliers']['disable']                          = false;
    $config['tools']['footprints']['autoload']               = false;
    $config['menu']['disable_help']                          = false;
    $config['menu']['disable_config']                        = false;
    $config['menu']['enable_debug']                          = false;
    $config['menu']['disable_labels']                        = false;
    $config['menu']['disable_calculator']                    = false;
    $config['menu']['disable_iclogos']                       = false;
    $config['menu']['disable_footprints']                    = false;
    $config['popup']['modal']                                = false;
    $config['popup']['width']                                = 1000;
    $config['popup']['height']                               = 800;
    $config['debug']['enable']                               = false;
    $config['debug']['debugbar']                             = false;
    $config['debug']['debugbar_db']                          = false;
    $config['debug']['template_debugging_enable']            = false;
    $config['debug']['request_debugging_enable']             = false;
    $config['admin']['password']                             = '$2y$10$pzgblXDlUlSyGabpIoVWIOEV0kc2riHN6Aot5eFA0eTZkjNckog16';
    $config['installation_complete']['locales']              = true;
    $config['installation_complete']['admin_password']       = true;
    $config['installation_complete']['database']             = true;
    $config['installation_complete']['db_backup_path']       = true;
    $config['timezone']                                      = 'Europe/Berlin';
    $config['language']                                      = 'en_US';
    $config['is_online_demo']                                = false;
    $config['developer_mode']                                = false;
    $config['page_title']                                    = 'Part-DB Elektronische Bauteile-Datenbank';
    $config['partdb_title']                                  = 'Part-DB (Demo EN)';
    $config['tracking_code']                                 = '<script> 
    var gaProperty = "UA-77699801-2"; 
    var disableStr = "ga-disable-" + gaProperty; 
    if (document.cookie.indexOf(disableStr + "=true") > -1) { 
        window[disableStr] = true;
    } 
    function gaOptout() { 
        document.cookie = disableStr + "=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/"; 
        window[disableStr] = true; 
        alert("The tracking is now deactivated!"); 
    } 
    (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){ 
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), 
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) 
    })(window,document,"script","https://www.google-analytics.com/analytics.js","ga"); 

    ga("create", "UA-77699801-2", "auto"); 
    ga("set", "anonymizeIp", true); 
    ga("send", "pageview"); 
</script>';
    $config['allow_server_downloads']                        = false;
    $config['design']['use_smarty']                          = true;
    $config['foot3d']['active']                              = true;
    $config['foot3d']['show_info']                           = false;
    $config['appearance']['use_old_datasheet_icons']         = false;
    $config['appearance']['short_description_length']        = 200;
    $config['appearance']['short_description']               = true;
    $config['other_panel']['collapsed']                      = false;
    $config['other_panel']['position']                       = 'top';
    $config['part_info']['hide_actions']                     = false;
    $config['part_info']['hide_empty_attachements']          = false;
    $config['part_info']['hide_empty_orderdetails']          = false;
    $config['properties']['active']                          = true;
    $config['edit_parts']['created_go_to_info']              = false;
    $config['edit_parts']['saved_go_to_info']                = false;
    $config['table']['autosort']                             = false;
    $config['table']['default_show_subcategories']           = true;
    $config['table']['default_limit']                        = 50;
    $config['search']['livesearch']                          = true;
    $config['search']['highlighting']                        = true;
    $config['attachements']['folder_structure']              = false;
    $config['attachements']['download_default']              = false;
    $config['attachements']['show_name']                     = false;
    $config['user']['avatars']['use_gravatar']               = true;
    $config['user']['redirect_to_login']                     = true;
    $config['user']['gc_maxlifetime']                        = 5400;
	$config['cookie_consent']['enable']                      = true;
	$config['cookie_consent']['link_href']                   = 'http://part-db.bplaced.net/part-en/privacy.html';
	$config['cookie_consent']['link_text']                   = 'Privacy Policy';
	
    //How to declare manual configs:
    //$manual_config['money_format']['POSIX']                = '%!n €';
    //$manual_config['DOCUMENT_ROOT']                        = '/var/www';

