<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

include_once(BASE.'/lib/lib.php'); // for save_config()

/*
 * CONFIG UPDATE STEPS:
 *
 * This file contains all steps to update the user's config.php step by step to the latest version.
 *
 * To add a new step, you have to:
 *      - increment the variable "$config['system']['latest_config_version']" by one (in config_defaults.php)
 *      - add a new "case" element at the end of the function below.
 *          -> this new "case" must have the number "$config['system']['latest_config_version'] - 1"!
 */

/**
 * @brief Update the user's config.php to the latest version
 *
 * @retval array    If there are messages to display, this function will return an array of string.
 *                  If there are no messages, this function will return an empty array.
 *
 * @throws Exception if there was an error
 */
function update_users_config_php()
{
    global $config;
    $messages = array();

    $current = $config['system']['current_config_version'];
    $latest = $config['system']['latest_config_version'];

    while ($current < $latest) {
        switch ($current) {
            case 0: // this is used for all Part-DB versions before v0.3.0.RC1
                // set the most important settings

                global $mysql_server;
                $config['db']['host']                       = $mysql_server;
                global $db_user;
                $config['db']['user']                       = $db_user;
                global $db_password;
                $config['db']['password']                   = $db_password;
                global $database;
                $config['db']['name']                       = $database;
                global $http_charset;
                $config['html']['http_charset']             = $http_charset;
                global $disable_update_list;
                $config['startup']['disable_update_list']   = $disable_update_list;
                global $disable_devices;
                $config['devices']['disable']               = $disable_devices;
                global $disable_footprints;
                $config['footprints']['disable']            = $disable_footprints;
                global $disable_help;
                $config['menu']['disable_help']             = $disable_help;
                global $disable_config;
                $config['menu']['disable_config']           = $disable_config;
                global $use_modal_dialog;
                $config['popup']['modal']                   = $use_modal_dialog;
                global $dialog_width;
                $config['popup']['width']                   = $dialog_width;
                global $dialog_height;
                $config['popup']['height']                  = $dialog_height;
                global $banner;
                $config['startup']['custom_banner']         = $banner;

                // check if the config.php is really from Part-DB 0.2.2
                if ( ! isset($mysql_server)) {
                    throw new Exception('Fehler beim Updaten der config.php: Unbekannte Version!');
                }

                /*
                 * Please Note:     We have migrated the old database connection and some other settings into the new config.php.
                 *                  But we cannot set the locales, database backup path and the administrator password.
                 *                  So these three Installers (install.php) will be shown on next startup.
                 */
                $config['installation_complete']['database'] = true;

                $messages[] =   'ACHTUNG: Für hochgeladene Bilder und andere Dateien gibt es jetzt das Verzeichnis "media" im Unterverzeichnis "data". '.
                    'Falls Sie bereits eigene Dateien im Verzeichnis "img" haben (was für hochgeladene Bilder bisher der Zielordner war), '.
                    'müssen Sie diese nun von Hand in der Ordner "data/media/" verschieben! Achten Sie darauf, dass die versteckte Datei '.
                    '"data/media/.htaccess" nicht gelöscht wird, diese ist wichtig für den sicheren Betrieb von Part-DB. '.
                    'Die Dateipfade werden beim folgenden Datenbankupdate automatisch angepasst.';

                break;

            case 1:
                // remove for example ".utf8" in $config['language'] because of a change in start_session.php
                if (strpos($config['language'], '.') > 0) {
                    $config['language'] = substr($config['language'], 0, strpos($config['language'], '.'));
                }
                break;

            default:
                throw new Exception('Unbekannte config.php-Version: "'.$current.'"');
                break;
        }

        $current++;
    }

    saveConfig(); // now save all changes

    return $messages;
}
