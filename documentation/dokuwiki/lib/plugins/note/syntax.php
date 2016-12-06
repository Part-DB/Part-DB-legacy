<?php
/**
 * Add Note capability to dokuwiki
 *
 * <note>This is note</note>
 * <note classic>This is note</note>
 * <note important>This is an important note</note>
 * <note warning>This is a big warning</note>
 * <note tip>This is a tip</note>
 *
 * by Olivier Cortès <olive@deep-ocean.net>
 * under the terms of the GNU GPL v2.
 *
 * Originaly derived from the work of :
 * Stephane Chamberland <stephane.chamberland@ec.gc.ca> (Side Notes PlugIn)
 * Carl-Christian Salvesen <calle@ioslo.net> (Graphviz plugin)
 *
 * Contributions by Eric Hameleers <alien [at] slackware [dot] com> :
 *   use <div> instead of <table>,
 *   contain the images and stylesheet inside the plugin,
 *   permit nesting of notes,
 *
 * Contributed by Christopher Smith <chris [at] jalakai [dot] co [dot] uk>
 *   fix some parsing problems and a security hole.
 *   make note types case independent
 *   simplify code reading
 *   modernise the plugin for changes/fixes/improvements to the underlying Dokuwiki plugin class,
 *   improve efficiency.
 *
 * Contributed by Aurélien Bompard <aurelien [at] bompard [dot] org>
 *   support for the ODT output format.
 *
 * @license    GNU_GPL_v2
 * @author     Olivier Cortes <olive@deep-ocean.net>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');


class syntax_plugin_note extends DokuWiki_Syntax_Plugin {
 
    var $notes = array(
        'noteimportant' => array('important', 'importante'),
        'notewarning'   => array('warning','bloquante','critique'),
        'notetip'       => array('tip','tuyau','idée'),
        'noteclassic'   => array('','classic','classique')
      );
      
    var $default = 'noteclassic';

    function getType(){ return 'container'; }
    function getPType(){ return 'block'; }
    function getAllowedTypes() { 
        return array('container','substition','protected','disabled','formatting','paragraphs');
    }

    function getSort(){ return 195; }

    // override default accepts() method to allow nesting 
    // - ie, to get the plugin accepts its own entry syntax
    function accepts($mode) {
      if ($mode == substr(get_class($this), 7)) return true;
        return parent::accepts($mode);
      }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern('<note.*?>(?=.*?</note>)',$mode,'plugin_note');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('</note>','plugin_note');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {

        switch ($state) {

          case DOKU_LEXER_ENTER : 
            $note = strtolower(trim(substr($match,5,-1)));
 
            foreach( $this->notes as $class => $names ) {
              if (in_array($note, $names))
                return array($state, $class);
            }            
            
            return array($state, $this->default);          
 
          case DOKU_LEXER_UNMATCHED :
            return array($state, $match);
        
          default:
            return array($state);
        }
    }

    function render($mode, Doku_Renderer $renderer, $indata) {

        if($mode == 'xhtml'){

          list($state, $data) = $indata;

          switch ($state) {
            case DOKU_LEXER_ENTER :
              $renderer->doc .= '<div class="'.$data.'">';
              break;
  
            case DOKU_LEXER_UNMATCHED :
              $renderer->doc .= $renderer->_xmlEntities($data);
              break;
  
            case DOKU_LEXER_EXIT :
              $renderer->doc .= "\n</div>";
              break;
          }
          return true;

        } elseif ($mode == 'odt'){

          list($state, $data) = $indata;

          $this->render_odt ($renderer, $state, $data);
          return true;
        }
        
        // unsupported $mode
        return false;
    } 

    protected function render_odt ($renderer, $state, $data) {
        static $first = true;
        static $new;
        
        if ($first == true) {
            $new = method_exists ($renderer, 'getODTPropertiesFromElement');
            $first = false;
        }
        
        if (!$new) {
            // Render with older ODT plugin version.
            $this->render_odt_old ($renderer, $state, $data);
        } else {
            // Render with newer ODT plugin version.
            $this->render_odt_new ($renderer, $state, $data);
        }
    }

    protected function render_odt_old ($renderer, $state, $data) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $type = substr($data, 4);
                if ($type == "classic") {
                    $type = "note"; // the icon for classic notes is named note.png
                }
                $colors = array("note"=>"#eeeeff", "warning"=>"#ffdddd", "important"=>"#ffffcc", "tip"=>"#ddffdd");

                // Content
                $properties = array();
                $properties ['width'] = '100%';
                $properties ['align'] = 'center';
                $properties ['shadow'] = '#808080 0.18cm 0.18cm';
                $renderer->_odtTableOpenUseProperties($properties);

                $properties = array();
                $properties ['width'] = '1.5cm';
                $renderer->_odtTableAddColumnUseProperties($properties);

                $properties = array();
                $properties ['width'] = '13.5cm';
                $renderer->_odtTableAddColumnUseProperties($properties);

                $renderer->tablerow_open();

                $properties = array();
                $properties ['vertical-align'] = 'middle';
                $properties ['text-align'] = 'center';
                $properties ['padding'] = '0.1cm';
                $properties ['border'] = '0.002cm solid #000000';
                $properties ['background-color'] = $colors[$type];
                $renderer->_odtTableCellOpenUseProperties($properties);

                $src = DOKU_PLUGIN."note/images/".$type.".png";
                $renderer->_odtAddImage($src);

                $renderer->tablecell_close();

                $properties = array();
                $properties ['vertical-align'] = 'middle';
                $properties ['padding'] = '0.3cm';
                $properties ['border'] = '0.002cm solid #000000';
                $properties ['background-color'] = $colors[$type];
                $renderer->_odtTableCellOpenUseProperties($properties);
                break;

            case DOKU_LEXER_UNMATCHED :
                $renderer->cdata($data);
                break;

            case DOKU_LEXER_EXIT :
                $renderer->tablecell_close();
                $renderer->tablerow_close();
                $renderer->_odtTableClose();
                $renderer->p_open();
                break;
        }
    }

    /**
     * ODT rendering for new versions of the ODT plugin.
     *
     * @param $renderer the renderer to use
     * @param $state    the current state
     * @param $data     data from handle()
     * @author LarsDW223
     */
    protected function render_odt_new ($renderer, $state, $data) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $css_properties = array ();

                // Get CSS properties for ODT export.
                $renderer->getODTPropertiesNew ($css_properties, 'div', 'class="'.$data.'"', NULL, true);

                // Create Content
                // (We only use the CSS parameters that are meaningful for creating the ODT table)
                $properties = array();
                $properties ['width'] = '100%';
                $properties ['align'] = 'center';
                $properties ['shadow'] = '#808080 0.18cm 0.18cm';
                $renderer->_odtTableOpenUseProperties($properties);

                $properties = array();
                $properties ['width'] = '1.5cm';
                $renderer->_odtTableAddColumnUseProperties($properties);

                $properties = array();
                $properties ['width'] = '13.5cm';
                $renderer->_odtTableAddColumnUseProperties($properties);

                $renderer->tablerow_open();

                $properties = array();
                $properties ['vertical-align'] = $css_properties ['vertical-align'];
                $properties ['text-align'] = 'center';
                $properties ['padding'] = '0.1cm';
                $properties ['border'] = '0.002cm solid #000000';
                $properties ['background-color'] = $css_properties ['background-color'];
                $renderer->_odtTableCellOpenUseProperties($properties);

                if ($css_properties ['background-image']) {
                    $renderer->_odtAddImage($css_properties ['background-image']);
                }

                $renderer->tablecell_close();

                $properties = array();
                $properties ['vertical-align'] = $css_properties ['vertical-align'];
                $properties ['text-align'] = $css_properties ['text-align'];
                $properties ['padding'] = '0.3cm';
                $properties ['border'] = '0.002cm solid #000000';
                $properties ['background-color'] = $css_properties ['background-color'];
                $renderer->_odtTableCellOpenUseProperties($properties);
                break;

            case DOKU_LEXER_UNMATCHED :
                $renderer->cdata($data);
                break;

            case DOKU_LEXER_EXIT :
                $renderer->tablecell_close();
                $renderer->tablerow_close();
                $renderer->_odtTableClose();
                $renderer->p_open();
                break;
        }
    }
}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
