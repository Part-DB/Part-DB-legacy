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
// $Id$

// check to avoid multiple including of class
if (!defined('vlibSearchToSQLClassLoaded')) {
    define('vlibSearchToSQLClassLoaded', 1);

    include_once (dirname(__FILE__).'/vlibIni.php');

    /**
     * vlibSearchToSQL is a class used to turn a boolean text
     * string, i.e., "+cats -dogs" or "cats NOT dogs".. into a
     * fully qualified SQL where clause, i.e.,
     * 	"field1 LIKE '%cats%' AND field1 NOT LIKE '%dogs%'".
     *
     * @since 13/02/2003
     * @author Kelvin Jones <kelvin@kelvinjones.co.uk>
     * @package vLIB
     * @access public
     * @see vlibSearchToSQL.html
     */

    class vlibSearchToSQL {

    /*-------------------------------------------------------------------------------\
    |                                 ATTENTION                                      |
    | Do not touch the following variables. vlibSearchToSQL will not work otherwise. |
    \-------------------------------------------------------------------------------*/

        var $OPTIONS = array(
                        'MIN_WORD_LENGTH'       => '',
                        'ALLOW_WILDCARDS'       => '',
                        'ENCLOSE_FIELDS_WITH'   => '',
                        'DEFAULT_SEPERATOR'     => '',
                        'STOP_WORDS'			=> ''
                             );

		var $stop_words = '';
		var $escapechars = "\0\r\n\t\\";
		var $words_stopped = array();
		var $words_too_short = array();


        /**
         * FUNCTION: vlibSearchToSQL
         *
         * vlibSearchToSQL constructor.
         *
         * @param array $options see above
         * @return boolean true/false
         * @access private
         */
		function vlibSearchToSQL ($options=null) {

            if (is_array(vlibIni::vlibSearchToSQL())) {
                foreach (vlibIni::vlibSearchToSQL() as $name => $val) {
                    $this->OPTIONS[$name] = $val;
                }
            }

            if (is_array($options)) {
                foreach($options as $key => $val) {
                	if (strtoupper($key) == 'STOP_WORDS') {
                		$this->OPTIONS['STOP_WORDS'] .= ' '.$val;
                	}
                	else {
                    	$this->OPTIONS[strtoupper($key)] = $val;
                    }
                }
            }

			$this->stop_words = str_replace(' ', '|', $this->OPTIONS['STOP_WORDS']);
			if (!$this->OPTIONS['allow_wildcards']) $this->escapechars .= '%_';

			return true;
		}


        /**
         * FUNCTION: convert
         *
         * converts the search string to it's SQL equivilent.
         *
         * @param array $fields, all the fields in the database that you want to search
         * @return boolean true/false
         * @access private
         */
		function convert ($fields, $str) {
			$this->reset(); // reset vars before we start, in case a search was done previous to this.

			// run regex against search string
			$rgx = "/
					\s*
					(AND|OR|NOT|\+|\-|\|\||\!)? # any inclusion exclusion identifier
					\s*
					([\"\']?) # first quote
					\s*
					((?<=[\"\']) # if last one was a quote
						[^\"\']+ # search for any non quote
					| # else
						[^\s]+ # search for a word
					) # endif
					\s*
					([\"\']?) # second quote
				/ix";

			preg_match_all($rgx, $str, $matches, PREG_SET_ORDER);

			$words_used = array();

			// convert matches to readable SQL
			$sql = '';
			foreach ($matches as $m) {

				if (strlen($m[3]) < $this->OPTIONS['MIN_WORD_LENGTH']) { // word to short
					array_push($this->words_too_short, $m[3]);
					continue;
				}
				if (in_array(strtolower($m[3]), $words_used)) { // word duplicated
					continue;
				}
				if (preg_match('/^('.$this->stop_words.')$/i', $m[3]) && $m[1] != '+') { // stop word
					array_push($this->words_stopped, $m[3]);
					continue;
				}

				$preclause = null;
				$clause = null;
				switch (strtoupper($m[1])) {
					case 'AND':
					case '+':
						if ($sql) $preclause = ' AND ';
						$clause = ' LIKE ';
						break;

					case 'OR':
					case '||':
						if ($sql) $preclause = ' OR ';
						$clause = ' LIKE ';
						break;

					case 'NOT':
					case '-':
						if ($sql) $preclause = ' AND ';
						$clause = ' IS NOT LIKE ';
						break;

					default:
						if ($sql) $preclause = ' '.$this->OPTIONS['DEFAULT_SEPERATOR'].' ';
						$clause = ' LIKE ';
						break;
				}

				$sql .= $preclause.'$fieldname$'.$clause.'\'%'.addcslashes($m[3], $this->escapechars).'%\'';

				$words_used[] = strtolower($m[3]);
			}

			// loop through Db fields, creating identical SQL for each
			$where_clauses = array();
			foreach ($fields as $f) {
				if ($this->OPTIONS['ENCLOSE_FIELDS_WITH']) {
					$f = $this->OPTIONS['ENCLOSE_FIELDS_WITH'] . $f . $this->OPTIONS['ENCLOSE_FIELDS_WITH'];
				}
				$where_clauses[] = '('.str_replace('$fieldname$', $f, $sql).')';
			}


			return '('.implode(' OR ', $where_clauses).')';
		}


        /**
         * FUNCTION: getStoppedWords
         *
         * get an array of the words that were omitted from the search because
         * they were in the stop words list.
         *
         * @return array
         * @access public
         */
		function getStoppedWords () {
			return $this->words_stopped;
		}

        /**
         * FUNCTION: getShortWords
         *
         * get an array of the words that were omitted from the search because
         * they were too short.
         *
         * @return array
         * @access public
         */
		function getShortWords () {
			return $this->words_too_short;
		}

        /**
         * FUNCTION: reset
         *
         * reset any vars before a convert.
         *
         * @return true
         * @access public
         */
		function reset () {
			$this->words_stopped = array();
			$this->words_too_short = array();
		}

	} // end of class
}// end of if (defined)
?>