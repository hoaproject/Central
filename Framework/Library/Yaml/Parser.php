<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2011, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category    Framework
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Parser
 *
 */

/**
 * Class Hoa_Yaml_Parser.
 *
 * Yaml parser.
 *
 * @author      Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright © 2007-2011 Ivan Enderlin.
 * @license     New BSD License
 * @since       PHP 5
 * @version     0.2
 * @package     Hoa_Yaml
 * @subpackage  Hoa_Yaml_Parser
 */

class Hoa_Yaml_Parser {

    /**
     * Node object.
     *
     * @var Hoa_Yaml_Node object
     */
    private $node = null;

    /**
     * Current document.
     *
     * @var Hoa_Yaml_Parser aray
     */
    protected $doc = array();

    /**
     * All documents.
     *
     * @var Hoa_Yaml_Parser array
     */
    public $docs = array();

    /**
     * Reference pointer.
     *
     * @var Hoa_Yaml_Parser array
     */
    protected $ref = array();

    /**
     * The result.
     *
     * @var Hoa_Yaml_Parser array
     */
    protected $out = array();



    /**
     * __construct
     * Parse a YAML file.
     *
     * @access  public
     * @param   source  string    YAML source.
     * @param   doc     int       Document number.
     * @return  array
     * @throw   Hoa_Yaml_Exception
     */
    public function __construct ( $source = '', $doc = '*' ) {

        $this->node = new Hoa_Yaml_Node;

        if(empty($source))
            throw new Hoa_Yaml_Exception('File could not be empty.', 1);

        if(empty($this->doc))
            $this->doc = $source;

        $max = count($this->doc);

        $this->rmComment();


        for($i = 0; $i < $max; $i++) {

            if($this->emptyLine($i))
                continue;

            if(substr($this->doc[$i], 0, 3) == '---') {
                $a = $this->node->finalize();

                if(!empty($a))
                    $this->docs[] = $this->node->finalize();

                unset($a);
                $this->node->clean();
                continue;
            }

            $do   = true;
            $line = rtrim($this->doc[$i]);

            if(preg_match('#^(\t)#', $line))
                throw new Hoa_Yaml_Exception('An error occured at line %d : '.
                    'line could not start by a tabulation.', 2, $i+1);

            // $id;
            $key   = '**auto**';
            $value = '';

            // Mapping (key: value).
            $this->mapping($line, $i, $id, $key, $value);


            // Sequence (- string).
            $this->sequence($line, $id, $key, $value, $do);


            // Reference (&Rf, *Rf).
            $this->reference($id, $key, $value);


            // Set type of key and value.
            $key = (string)$key;
            $this->toType($value);


            // Create final node.
            if($do)
                $this->node->create($id, $key, $value);
        }

        $a = $this->node->finalize();
        if(!empty($a))
            $this->docs[] = $this->node->finalize();
        unset($a);
        $this->node->clean();

        if($doc == '*')
            return $this->docs;
        else
            if(isset($this->docs[$doc]))
                return $this->docs[$doc];
            else
                throw new Hoa_Yaml_Exception('Document %d does not exist.', 3, $doc);
    }

    /**
     * mapping
     * Parse a mapping, folded and literal styles.
     *
     * @access  protected
     * @param   line   string    Current line.
     * @param   i      int       Line number.
     * @param   id     int       Id (2 spaces = 1 id).
     * @param   key    string    Key.
     * @param   value  string    Value.
     * @return  void
     */
    protected function mapping ( $line, &$i, &$id, &$key, &$value ) {

        // Mappings (key: value)
        if(preg_match('#^( *)"?([^"]+)"?(?::) ?+(.*)?$#', $line, $matches)) {

            list(, $id, $key, $value) = $matches;
            $id    = strlen($id)/2;
            $key   = trim  ($key);
            $value = trim  ($value);
            $this->unquote ($value);

            // Folded and Literal styles
            if(preg_match('#^(\>|\|)$#', $value, $matches)) {

                $value = array();
                if($matches[1] == '>') {
                    $folded  = true;
                    $literal = false;
                    $regex   = '#^( *)(?:\\\)?(.*?)$#s';
                }
                else {
                    $folded  = false;
                    $literal = true;
                    $regex   = '#^( *)(.*?)$#';
                }

                do {
                    $i++;
                    $line  = $this->doc[$i];
                    preg_match($regex, $line, $matches);
                    $space = strlen($matches[1])/2;
                    if($folded && strlen($matches[2]) > 1)
                        $matches[2] = rtrim($matches[2], "\n\r");

                    $value[] = $matches[2];

                } while(isset($this->doc[$i+1]) && $space > $id);

                if(isset($this->doc[$i+1])) {
                    array_pop($value);
                    $i--;
                }

                $line  = rtrim($this->doc[$i]);

                $value = implode('', $value);
            }
        }
    }

    /**
     * sequence
     * Parse a sequence, folded and literal styles.
     *
     * @access  protected
     * @param   line   string    Current line.
     * @param   i      int       Line number.
     * @param   id     int       Id (2 spaces = 1 id).
     * @param   key    string    Key.
     * @param   value  string    Value.
     * @param   do     bool      Create the final node or not.
     * @return  void
     */
    protected function sequence ( $line, &$id, &$key, &$value, &$do ) {

        if(preg_match('#^( *)- *(.*?)$#', $line, $matches)) {

            list(, $id, $value) = $matches;
            $id                 = strlen($id)/2;
            $key                = '**auto**';
            $value              = trim($value);

            // Nested mapping inline a sequence.
            if(preg_match('#^"?([^{"]+)"?(?::) ?+(.*)?$#', $value, $matches)) {
                $this->node->create($id++, '**auto**');
                list(, $key, $value) = $matches;
            }

            if(!$this->unserializeMappingSequence($id, $value, $do))
                $this->unquote($value);
        }
    }

    /**
     * reference
     * Make a reference pointer.
     *
     * @access  protected
     * @param   id     int       Id (2 spaces = 1 id).
     * @param   key    string    Key.
     * @param   value  string    Value.
     * @return  void
     */
    protected function reference ( &$id, &$key, &$value ) {

        if(preg_match('#^&([\w]+) *(.*)?+$#', $value, $matches)) {

            $value = $matches[2];
            $this->node->create($id, $key, $value);
            $this->ref[$matches[1]] = array('id' => $id, 'key' => $key);
        }
        elseif(preg_match('#^\*([\w]+)$#', $value, $matches)) {

            if(isset($this->ref[$matches[1]]['id'])
               && isset($this->ref[$matches[1]]['key']))
                $value = $this->node->reach($this->ref[$matches[1]]['id'],
                                            $this->ref[$matches[1]]['key']);
            else
                $value = '**reference "'.$matches[1].'" is not found**';
        }
    }

    /**
     * unserializeMappingSequence
     * Unserialize a mapping or a sequence.
     *
     * @access  protected
     * @param   id     int       Id (2 spaces = 1 id).
     * @param   value  string    Value.
     * @param   do     bool      Create the final node or not.
     * @return  void
     */
    protected function unserializeMappingSequence ( $id, &$value, &$do) {

        // Serialized mapping and sequence.
        if(   preg_match('#^(\[.*\])$#', $value, $matches)
           || preg_match('#^({.*})$#'  , $value, $matches)) {

            if($matches[1]{0} == '{') {
                $mapping  = true;
                $to       = '{';                 // Tag Open.
                $tc       = '}';                 // Tag Close.
                $sequence = false;
            }
            else {
                $mapping  = false;
                $to       = '[';                 // Tag Open.
                $tc       = ']';                 // Tag Close.
                $sequence = true;
            }

            $this->node->create($id++, '**auto**');

            $str    = $matches[1];               // Start string.
            $end    = array($to => 1, $tc => 1); // Stop condition.
            $substr = 0;                         // Pointer string.

            $toq    = preg_quote($to);           // Preg quoted tag open.
            $tcq    = preg_quote($tc);           // Preg quoted tag close.

            // #({+)?([^{|}]+)(?:({+|}+)?(?:, )?)#
            //                  or
            // #(\[+)?([^\[|\]]+)(?:(\[+|\]+)?(?:, )?)#
            $regex = "#($toq+)?([^$toq|$tcq]+)(?:($toq+|$tcq+)?(?:, )?)#";
            preg_match_all($regex, $str, $matches);

            for($e = 0, $stop = count($matches[0]); $e < $stop; $e++) {

                if($mapping && false !== strpos($matches[2][$e], '[')) {
                    $handles = explode(', ', $matches[2][$e]);
                    $count   = 0;
                    $e       = 0;
                    $values  = array();
                    foreach($handles as $i => $handle) {
                        $count += substr_count($handle, '[');
                        $count -= substr_count($handle, ']');

                        if(isset($values[$e]))
                            $values[$e] .= ', '.$handle;
                        else
                            $values[$e] = $handle;

                        if($count == 0)
                            $e++;
                    }
                }
                else
                    $values = preg_split('#, #', $matches[2][$e], -1, PREG_SPLIT_NO_EMPTY);

                // Quote
                $qOpen = '';
                $qPos  =  0;
                for($f = 0, $max = count($values); $f < $max; $f++) {

                    $add = 0;
                    if(preg_match('#^"?(?:[^"]+)"?(: +)(?:.*)?$#', $values[$f], $add))
                        $add = strpos($values[$f], $add[1])+strlen($add[1]);
                    $a = substr($values[$f], $add,  1);
                    $y = substr($values[$f],   -2, -1);
                    $z = substr($values[$f],   -1    );

                    if(empty($qOpen)) {

                        if($a == '"' || $a == "'")
                            $qOpen = $a;
                        if($y != '\\' && $z == $qOpen)
                            $qOpen = '';
                        $qPos = $f;
                    }
                    else {

                        $values[$qPos] .= ', '.$values[$f];
                        unset($values[$f]);

                        if($y != '\\' && $z == $qOpen)
                            $qOpen = '';
                    }
                }

                $count = 0;
                foreach($values as $subKey => $subValue) {

                    // Mapping value.
                    if(preg_match('#^"?([^"]+)"?(?::) ?+(.*)?#', $subValue, $subMatches)) {

                        $subKey   = trim($subMatches[1]);
                        $subValue = trim($subMatches[2]);
                        unset($subMatches);

                        if(!empty($subValue) && $subValue{0} == '[') {

                            $this->unserializeMappingSequence($id, $subValue, $do);
                            $subValue = $this->node->reach($id, $count);
                            $this->node->remove($id, $count++);
                            $this->node->create($id, $subKey);
                        }

                    }
                    // Sequence value.
                    else
                        $subKey = '**auto**';

                    if(is_string($subValue))
                        $this->unquote($subValue);
                    $this->node->create($id, $subKey, $subValue);
                }

                if(isset($matches[3][$e])) {

                    $loop = strlen($matches[3][$e]);
                    if($matches[3][$e]{0} == $to) {
                        while($loop-- > 0)
                            $this->node->create($id++, $subKey);
                    }
                    elseif($matches[3][$e]{0} == $tc) {
                        while($loop-- > 0)
                            $id--;
                    }
                }
            }

            // Donnot create final node,
            // because it is already done above.
            $do = false;

            return $subKey;
        }
        else
            return false;
    }

    /**
     * toType
     * Transform value in the good type.
     *
     * @access  public
     * @param   value  mixed    Value.
     * @return  mixed
     */
    public function toType ( &$value ) {

        $null  = array('null' , ''   , '~'            );
        $true  = array('true' , 'on' , '+', 'yes', 'y');
        $false = array('false', 'off', '-', 'no' , 'n');

        if(in_array($value, $null))
            $value = null;
        elseif(ctype_digit($value))
            $value = (int)$value;
        elseif(in_array($value, $true))
            $value = true;
        elseif(in_array($value, $false))
            $value = false;
        elseif(is_numeric($value))
            $value = (float)$value;

        return $value;
    }

    /**
     * unquote
     * Unquote a string.
     *
     * @access  public
     * @param   value   string    Value.
     * @return  bool
     */
    public function unquote ( &$value ) {

        if(preg_match('#(?:(?<!\\\)("|\'))(.*)?(?:(?(1)(?<!\\\)\1|))#U',
                      $value, $matches)) {
            $value = str_replace('\\'.$matches[1], $matches[1], $matches[2]);
            return true;
        }

        return false;
    }

    /**
     * rmComment
     * Remove comments.
     *
     * @access  protected
     * @return  array
     * @todo    Remake, bad expreg, does not match all cases.
     */
    protected function rmComment ( ) {

        if(!is_array($this->doc))
            throw new Hoa_Yaml_Exception('Document is not well-parsed.', 7);

        foreach($this->doc as $i => $line) {

            if(0 !== preg_match('#((?:(?:(?:(?<!\\\)("|\')).*?(?:(?(1)(?<!\\\)\1|)))|(?:^\s*)))\#.*?#U',
                                $line, $matches))
                $this->doc[$i] = substr($line, 0, strlen($matches[1]));
        }

        return $this->doc;
    }

    /**
     * emptyLine
     * Remove empties lines.
     *
     * @access  protected
     * @param   i          int    Line number.
     * @return  bool
     */
    protected function emptyLine ( $i ) {

        $line = $this->doc[$i];
        preg_match('#^\s*(.*)?$#', $line, $matche);

        return empty($matche[1]);
    }
}
