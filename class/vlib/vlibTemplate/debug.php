<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002 Active Fish Group                                 |
// +----------------------------------------------------------------------+
// | Authors: Kelvin Jones <kelvin@kelvinjones.co.uk>                     |
// +----------------------------------------------------------------------+
//
// $Id: debug.php,v 1.5 2003/03/03 13:46:47 releasedj Exp $

/**
 * Class uses all of vlibTemplate's functionality but debugs the template files.
 * Outputs a debug console to an html page, only use this for html files.
 *
 * NB: This class is not as well documented as the main vlibTemplate class as all
 * functions are private. This will be cleaned up in the future but it's quite
 * far down the priority list. It works, and right now, that's what counts.
 *
 * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
 * @since 21/01/2002
 * @package vLIB
 * @access public
 */

class vlibTemplateDebug extends vlibTemplate {

/*-----------------------------------------------------------------------------\
|     DO NOT TOUCH ANYTHING IN THIS CLASS IT MAY NOT WORK OTHERWISE            |
\-----------------------------------------------------------------------------*/

    var $VLIBTEMPLATE_DEBUGMOD = './vlibTemplate/vlibTemplate_debugmod.html';
    var $VLIBTEMPLATE_DEBUGWRAPPER = './vlibTemplate/vlibTemplate_debugmodwrapper.html';

    /**
     * boolean var prints out vlibTemplate debug module
     * @see $_tmplfilep
     */
    var $_debug = 1;

    var $_debugIncludedfiles = array();
    var $_debugTemplatevars = array();

    var $_debugalltags = array();
    var $_debugtags = array();

    var $_debugwarnings = array();
    var $_debugwarningmsgscontrol = array();
    var $_debugwarningmsgs = array();

    /**
     * Builds entire debug module window
     * prints it by default.
     *
     * @access private
    */
    function doDebug() {
        $vLIBtmpl = new vlibTemplate ($this->VLIBTEMPLATE_DEBUGMOD);

        // set vars needed for JavaScript in popup window
        $filename_enc = md5($this->_tmplfilename);
        $vLIBtmpl->setvar('vLIB_template_name', $this->_tmplfilename);
        $vLIBtmpl->setvar('filename_enc', $filename_enc);

        // grab the vars and loops array
        ob_start();
            print_r($this->_arrvars);
            $vLIB_allarrvars = ob_get_contents();
        ob_end_clean();
        ob_start();
            print_r($this->_vars);
            $vLIB_allvars = ob_get_contents();
        ob_end_clean();

        $vLIBtmpl->setvar('vLIB_allarrvars', wordwrap($vLIB_allarrvars, 70, "\n", 1));
        $vLIBtmpl->setvar('vLIB_allvars', wordwrap($vLIB_allvars, 70, "\n", 1));


        $vLIBtmpl->setvar('vLIB_num_global_vars', (count($this->_vars)) ? count($this->_vars) : '0');
        $vLIBtmpl->setvar('vLIB_num_top_level_loops', (count($this->_arrvars)) ? count($this->_arrvars) : '0');

        // get all files included using <tmpl_include>
        $incdpaths = "";
        foreach($this->_debugIncludedfiles as $v) {
            $incdpaths .= $v.'<br>';
        }

        // get the Options and some other stuff
        $params = array('vlibTemplate Root' => $this->VLIBTEMPLATE_ROOT);
        foreach ($this->OPTIONS as $option => $value) {
            (empty($value)) and $value = '<i>null</i>';
            (is_array($value)) and $value = join('<br>', $value);
            $params[$option] = $value;
        }

        $paramsarr = array();
        foreach($params as $k => $v) {
            array_push($paramsarr, array(
                                        'param_name' => $k,
                                        'param_value' => $v
                                        ));
        }

        $vLIBtmpl->setloop('vLIB_list_params', $paramsarr);

        // template vars and stuff
        $filesarr = array();
        if (!is_array($this->_debugTemplatevars['include'])) $this->_debugTemplatevars['include'] = array();
        foreach($this->_debugTemplatevars['include'] as $v) {
            array_push($filesarr, array(
                                        'filename' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_include_files', $filesarr);

        $varsarr = array();
        if (!is_array($this->_debugTemplatevars['var'])) $this->_debugTemplatevars['var'] = array();
        foreach($this->_debugTemplatevars['var'] as $v) {
            array_push($varsarr, array(
                                        'varname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_vars', $varsarr);

        $ifsarr = array();
        if (!is_array($this->_debugTemplatevars['if'])) $this->_debugTemplatevars['if'] = array();
        foreach($this->_debugTemplatevars['if'] as $v) {
            array_push($ifsarr, array(
                                        'ifname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_ifs', $ifsarr);

        $elseifsarr = array();
        if (!is_array($this->_debugTemplatevars['elseif'])) $this->_debugTemplatevars['elseif'] = array();
        foreach($this->_debugTemplatevars['elseif'] as $v) {
            array_push($elseifsarr, array(
                                        'elseifname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_elseifs', $elseifsarr);

        $unlessarr = array();
        if (!is_array($this->_debugTemplatevars['unless'])) $this->_debugTemplatevars['unless'] = array();
        foreach($this->_debugTemplatevars['unless'] as $v) {
            array_push($unlessarr, array(
                                        'unlessname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_unless', $unlessarr);

        $loopsarr = array();
        if (!is_array($this->_debugTemplatevars['loop'])) $this->_debugTemplatevars['loop'] = array();
        foreach($this->_debugTemplatevars['loop'] as $v) {
            array_push($loopsarr, array(
                                        'loopname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_loops', $loopsarr);

        $varsarr = array();
        foreach($this->_vars as $k => $v) {
            array_push($varsarr, array(
                                        'varname' => $k
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_tmplvars', $varsarr);

        $loopsarr = array();
        $loopst = array_keys($this->_arrvars);
        foreach ($loopst as $v) {
            array_push($loopsarr, array(
                                        'loopname' => $v
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_tmplloops', $loopsarr);

        $this->doDebugParse(); // does real debugging

        $warningsarr = array();
        if (count($this->_debugwarningmsgs) > 0) $vLIBtmpl->setvar('warnings', 1);
        foreach($this->_debugwarningmsgs as $v) {
            $vals = array_values($v);
            list($problem, $detail, $location) = $vals;
            array_push($warningsarr, array(
                                        'problem' => $problem,
                                        'detail'  => $detail,
                                        'location'=> $location
                                        ));
        }
        $vLIBtmpl->setloop('vLIB_warnings', $warningsarr);

        // finally grab and echo in JS formatted way
        $output = $vLIBtmpl->grab();

        $wrapper_tmpl = new vlibTemplate($this->VLIBTEMPLATE_DEBUGWRAPPER);

        $filename = $this->_tmplfilename;
        $filename = str_replace('\\', "\\\\", $filename);
        $filename = str_replace('\'', "\\'", $filename);
        $filename = str_replace("\r", '', $filename);
        $filename = str_replace("\n", "\\n", $filename);
        $filename = str_replace("\t", "\\t", $filename);

        $wrapper_tmpl->setVar('filename', $filename);
        $wrapper_tmpl->setvar('filename_enc', $filename_enc);
        $wrapper_tmpl->setvar('securecode', md5(time()));
        $wrapper_tmpl->setVar('debug_content', $this->enjavanate($output, 120));
        $wrapper_tmpl->pparse();
    }

    /**
     * rearranges all vars from below regex
     *
     *
     */
    function arrangeTags ($arr) {
        $temparr = array();
        $temparr['tag'] = $arr['tag'];
        $temparr['openclose'] = $arr['openclose'];
        $temparr['file'] = $arr['file'];
        $temparr['line'] = $arr['line'];
        $temparr['entire_tag'] = $arr['entire_tag'];

        for($i=1; $i < 4; $i++) {
            if (empty($arr['paramname'.$i]) && empty($arr['paramval'.$i])) break;
            (empty($arr['paramname'.$i])) ? $key = 'name' : $key = strtolower($arr['paramname'.$i]);
            $temparr[$key] = trim($arr['paramval'.$i]);
        }

        array_push($this->_debugalltags, $temparr);
    }

    /**
     * parses the template files
     *
     * @param array $data data of files line by line
     * @param string $filename name of current template file
     * @access private
     */
    function doDebugWarnings ($data, $filename) {

        $regex = '/(<|<\/|{|{\/|<!--|<!--\/){1}\s*';
        $regex.= '(?:tmpl_)?';
        $regex.= '(var|if|elseif|else|endif|unless|endunless|loop|endloop|include|phpinclude|comment|endcomment)\s*';
        $regex.= '(?:';
        $regex.=    '(?:';
        $regex.=        '(name|format|escape|op|value)';
        $regex.=        '\s*=\s*';
        $regex.=    ')?';
        $regex.=    '(?:[\"\'])?';
        $regex.=    '((?<=[\"\'])';
        $regex.=    '[^\"\']*|[a-z0-9_\.]*)';
        $regex.=    '[\"\']?';
        $regex.= ')?\s*';
        $regex.= '(?:';
        $regex.=    '(?:';
        $regex.=        '(name|format|escape|op|value)';
        $regex.=        '\s*=\s*';
        $regex.=    ')';
        $regex.=    '(?:[\"\'])?';
        $regex.=    '((?<=[\"\'])';
        $regex.=    '[^\"\']*|[a-z0-9_\.]*)';
        $regex.=    '[\"\']?';
        $regex.= ')?\s*';
        $regex.= '(?:';
        $regex.=    '(?:';
        $regex.=        '(name|format|escape|op|value)';
        $regex.=        '\s*=\s*';
        $regex.=    ')';
        $regex.=    '(?:[\"\'])?';
        $regex.=    '((?<=[\"\'])';
        $regex.=    '[^\"\']*|[a-z0-9_\.]*)';
        $regex.=    '[\"\']?';
        $regex.= ')?\s*';
        $regex.= '(?:>|\/>|}|-->){1}';
        $regex.= '/ie';

        for ($i = 0; $i < count($data); $i++) {
            $file = preg_replace(
                                    $regex,
                                  "\$this->arrangeTags(array(
                                                    'tag' => strtolower('\\2'),
                                                    'paramname1' => strtolower('\\3'),
                                                    'paramval1' => strtolower('\\4'),
                                                    'paramname2' => strtolower('\\5'),
                                                    'paramval2' => strtolower('\\6'),
                                                    'paramname3' => strtolower('\\7'),
                                                    'paramval3' => strtolower('\\8'),
                                                    'openclose' => '\\1',
                                                    'file' => realpath('$filename'),
                                                    'line' => ".($i+1).",
                                                    'entire_tag' => '\\0')
                                                    );"
                                ,

                               $data[$i]
                               );
        }
    }

    /**
     * takes values from preg_replace in _intparse and determines the replace string
     *
     * @access private
     * @return string replace values
     */
    function doDebugParse() {
        $valid_tags = array('var','loop','if','elseif','else','unless','endloop',
                            'endif','endunless', 'include', 'phpinclude', 'comment', 'endcomment');

        foreach($this->_debugalltags as $v) {

            $lasttag = $this->_debugtags[(count($this->_debugtags)-1)];
            $tag = $v['tag'];
            $escape = $v['escape'];
            $format = $v['format'];
            $op = $v['op'];
            $value = $v['value'];
            $var = trim($v['name']);
            $openclose = $v['openclose'];
            $file = $v['file'];
            $line = $v['line'];
            $entire_tag = stripslashes($v['entire_tag']);

            // continue if it's a one=line comment
            if ($tag == 'comment' && !empty($var)) continue;

            // bad tag
            if (!in_array($tag, $valid_tags)) {
                array_push($this->_debugwarningmsgs, array(
                                    'problem' => 'Warning: Invalid tag',
                                    'detail'  => "The following tag is not valid:\n".$entire_tag,
                                    'location' => 'Line: '.$line.', in file: '.$file
                            ));
                continue;
            }

            // bad escape
            if (!empty($escape)){
                if (!isset($this->ESCAPE_TAGS[$escape])) {
                    array_push($this->_debugwarningmsgs, array(
                                        'problem' => 'Warning: Invalid escape type',
                                        'detail'  => "The escape attribute of the following tag is not valid:\n".$entire_tag,
                                        'location' => 'Line: '.$line.', in file: '.$file
                                ));
                }
            }

            // bad format
            if (!empty($format)){
                if (!isset($this->FORMAT_TAGS[$format])) {
                    array_push($this->_debugwarningmsgs, array(
                                        'problem' => 'Warning: Invalid format type',
                                        'detail'  => "The format attribute of the following tag is not valid:\n".$entire_tag,
                                        'location' => 'Line: '.$line.', in file: '.$file
                                ));
                }
            }

            // bad operator
            if (!empty($op)){
                if (!in_array($op, $this->allowed_if_ops)) {
                    array_push($this->_debugwarningmsgs, array(
                                        'problem' => 'Warning: Invalid operator type',
                                        'detail'  => "The op attribute of the following tag is not supported:\n".$entire_tag,
                                        'location' => 'Line: '.$line.', in file: '.$file
                                ));
                }
            }

            if (eregi("^([a-z0-9_]+\.)*([a-z0-9_]+[_a-z0-9]*)$", $var, $matches) && !ereg('include|comment', $tag)) $var = $matches[2];

            // out of sequences
            if ($tag == 'else') {
                if (ereg('if|elseif|unless', $lasttag[0])) {
                    continue;
                } else {
                    array_push($this->_debugwarningmsgs, array(
                                        'problem' => 'Error: tmpl_else out of sequence',
                                        'detail'  => "The following tag is out of sequence:\n".$entire_tag,
                                        'location' => 'Line: '.$line.', in file: '.$file
                                ));
                }
            }

            if ($tag == 'elseif') {
                if (ereg('if|elseif', $lasttag[0])) {
                    array_pop($this->_debugtags);
                } else {
                    array_push($this->_debugwarningmsgs, array(
                                        'problem' => 'Error: tmpl_elseif out of sequence',
                                        'detail'  => "The following tag is out of sequence:\n".$entire_tag,
                                        'location' => 'Line: '.$line.', in file: '.$file
                                ));
                }
            }


            // bad syntax
            if (!eregi("^([a-z_]+[_a-z0-9\.]*)$", $var) && !ereg('include|comment', $tag) && !empty($var)) {
                array_push($this->_debugwarningmsgs, array(
                                    'problem' => 'Warning: Invalid variable name',
                                    'detail'  => "The variable name in the following tag does not comply with the correct php syntax:\n".$entire_tag,
                                    'location' => 'Line: '.$line.', in file: '.$file
                            ));
            }

            // if it's a closing tag return
            if (preg_match("/^<\/|{\/|<!--\/$/s", $openclose) || ereg("endloop|endif|endunless|endcomment", $tag)) {
                $closetag = 1;
                if ($tag == 'loop' || $tag == 'endloop') $tag = 'loop';
                if ($tag == 'if' || $tag == 'endif') $tag = 'if';
                if ($tag == 'unless' || $tag == 'endunless') $tag = 'unless';
                if ($tag == 'comment' || $tag == 'endcomment') $tag = 'comment';

                if ($tag == 'loop') {
                    if ($lasttag[0] == 'unless') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Notice: tmpl_endloop',
                                            'detail'  => "The following end loop tag was found as a closing tag to a tmpl_unless:".$entire_tag
                                                            .", whilst this 'may' not cause an error, it is incorrect vlibTemplate syntax (this maybe because of previous errors).",
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                        array_pop($this->_debugtags);
                    }
                    elseif ($lasttag[0] == 'if') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Error: tmpl_endloop',
                                            'detail'  => "The following end loop tag was found as a closing tag to a tmpl_if:".$entire_tag
                                                            .", whilst this 'may' not cause an error, it is incorrect vlibTemplate syntax (this maybe because of previous errors).",
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                    }
                    elseif($lasttag[0] != 'comment') {
                        array_pop($this->_debugtags);
                    }
                }
                elseif ($tag == 'if') {
                    if ($lasttag[0] == 'unless') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Notice: tmpl_endif',
                                            'detail'  => "The following end if tag was found as a closing tag to a tmpl_unless:".$entire_tag
                                                            .", whilst this may not cause an error, it is incorrect vlibTemplate syntax (this maybe because of previous errors).",
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                        array_pop($this->_debugtags);
                    }
                    elseif ($lasttag[0] == 'loop') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Error: tmpl_endif',
                                            'detail'  => "The following end if tag was found without it's opening tag (this maybe because of previous errors):\n".$entire_tag,
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                    }
                    elseif($lasttag[0] != 'comment') {
                        array_pop($this->_debugtags);
                    }
                }
                elseif ($tag == 'unless') {
                    if ($lasttag[0] == 'if') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Notice: tmpl_endunless',
                                            'detail'  => "The following end unless tag was found as a closing tag to a tmpl_if:".$entire_tag
                                                            .", whilst this may not cause an error, it is incorrect vlibTemplate syntax (this maybe because of previous errors).",
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                        array_pop($this->_debugtags);
                    }
                    elseif ($lasttag[0] == 'loop') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Error: tmpl_endunless',
                                            'detail'  => "The following end unless tag was found without it's opening tag (this maybe because of previous errors):\n".$entire_tag,
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                    }
                    elseif($lasttag[0] != 'comment') {
                        array_pop($this->_debugtags);
                    }
                }
                elseif ($tag == 'comment') {
                    if ($lasttag[0] != 'comment') {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Error: tmpl_'.$tag,
                                            'detail'  => "The following end comment tag was found without it's opening tag (this maybe because of previous errors):\n".$entire_tag,
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                    }
                    else {
                        array_pop($this->_debugtags);
                    }
                }

                if (empty($lasttag[0])) {
                        array_push($this->_debugwarningmsgs, array(
                                            'problem' => 'Error: tmpl_'.$tag,
                                            'detail'  => "The following end ".$tag." tag was found without it's opening tag (this maybe because of previous errors):\n".$entire_tag,
                                            'location' => 'Line: '.$line.', in file: '.$file
                                    ));
                }

            }

            if (ereg('if|unless|loop|elseif|comment', $tag) && !$closetag) {
                array_push($this->_debugtags, array(
                                        $tag,
                                        $file,
                                        $line,
                                        $entire_tag
                            ));
            }
            unset($closetag);
        } // foreach

        // check for any unclosed tags
        foreach($this->_debugtags as $v) {
            array_push($this->_debugwarningmsgs, array(
                                'problem' => 'Error: tmpl_'.$v[0],
                                'detail'  => "The following tmpl_".$v[0]." tag was found without it's closing tag (this maybe because of previous errors):\n".$v[3],
                                'location' => 'Line: '.$v[2].', in file: '.$v[1]
                        ));
        }

    }

    /**
     * Puts a text string into document.write functions calls in javascript
     */
    function enjavanate ($str, $limit=60) {
        $str   = str_replace("\r", '', $str);
        $retstr; // string to return

        $retstr .= "var vlib_debug_str2add = '';\n\n";

        while (strlen($str) > 0)
        {
            $line = substr ($str, 0, $limit);
            $str  = substr ($str, $limit);

            $line = str_replace('\\', "\\\\", $line);
            $line = str_replace('\'', "\\'", $line);
            $line = str_replace("\r", '', $line);
            $line = str_replace("\n", "\\n", $line);
            $line = str_replace("\t", "\\t", $line);
            $line = str_replace('<', "<'+'", $line);

            $retstr .=  "vlib_debug_str2add += '$line';\n";
        }

        $retstr .=  "\nvlib_window_obj.document.write(vlib_debug_str2add);\n";
        return $retstr;
    }

    /**
     * Resets the debug module ready for more information
     */
    function _debugReset () {
        $this->_debugIncludedfiles = array();
        $this->_debugTemplatevars = array();
        $this->_debugtags = array();
        $this->_debugwarnings = array();
        $this->_debugwarningmsgscontrol = array();
        $this->_debugwarningmsgs = array();
    }
/*- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
The following functions have no use and are included just so that if the user
is making use of vlibTemplateCache functions, this doesn't crash when changed to
vlibTemplateDebug.
The reason it would crash is that when you call vlibTemplateDebug, it bypasses
the cache class.
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
    function clearCache() {}
    function recache() {}
    function setCacheLifeTime() {}
    function setCacheExtension() {}
}
?>