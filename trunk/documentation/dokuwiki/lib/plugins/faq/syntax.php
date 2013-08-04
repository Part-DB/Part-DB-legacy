<?php
/**
 * FAQ plugin. FAQ styled headers.
 *
 * Syntax:
 *    ?????? FAQ level 1 ??????
 *    ????? FAQ level 2 ?????
 *    ???? FAQ level 3 ????
 *    ??? FAQ level 4 ???
 *    ?? FAQ level 5 ??
 *
 * FAQs are just headers with a different class in them.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Dion Nicolaas <dion@nicolaas.net>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_faq extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
                'author' => 'Dion Nicolaas',
                'email'  => 'dion@nicolaas.net',
                'date'   => '2008-10-01',
                'name'   => 'FAQ plugin',
                'desc'   => 'Easy markup for Frequently Asked Questions',
                'url'    => 'http://www.dokuwiki.org/plugin:faq',
                );
    }

    function getType() { return 'container'; }
    function getPType() { return 'block'; }
    function getSort() { return 49; }

    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'protected');
    }

    function preConnect() {
        $this->Lexer->addSpecialPattern(
                '(?m)^[ \t]*\?+[^\n]+\?+[ \t]*$',
                'base',
                'plugin_faq'
                );
    }

    function handle($match, $state, $pos, &$handler) {
        global $conf;

        // get level and title
        $title = trim($match);
        $level = 7 - strspn($title, '?');
        if ($level < 1) $level = 1;
        elseif ($level > 5) $level = 5;
        $title = trim($title, '?');
        $title = trim($title);

        // Repeat the normal handling, except for the default rendering
        if ($handler->status['section']) $handler->_addCall('section_close', array(), $pos);

        //$handler->_addCall('header', array($title, $level, $pos), $pos);

        $handler->_addCall('section_open', array($level), $pos);
        $handler->status['section'] = true;
        return array($level, $title);
    }

    function render($mode, &$renderer, $data) {
        // Repeat the rendering of normal headers, but with a span class=faq
        // in between
        list($level, $text) = $data;
        $hid = $renderer->_headerToLink($text,true);

        //only add items within configured levels
        $renderer->toc_additem($hid, $text, $level);

        // write the header
        $renderer->doc .= DOKU_LF.'<h'.$level.' class="faq"><a name="'.$hid.'" id="'.$hid.'"><span class="faq">';
        $renderer->doc .= $text;
        $renderer->doc .= "</span></a></h$level>".DOKU_LF; 
    }
}
// vim:ts=4:sw=4:et:enc=utf-8:
