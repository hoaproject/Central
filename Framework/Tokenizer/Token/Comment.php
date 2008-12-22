<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Comment
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Tokenizer_Token_Util_Exception
 */
import('Tokenizer.Token.Util.Exception');

/**
 * Hoa_Tokenizer_Token_Util_Interface
 */
import('Tokenizer.Token.Util.Interface');

/**
 * Hoa_Tokenizer
 */
import('Tokenizer.~');

/**
 * Class Hoa_Tokenizer_Token_Comment.
 *
 * Represents a comment.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Tokenizer
 * @subpackage  Hoa_Tokenizer_Token_Comment
 */

class Hoa_Tokenizer_Token_Comment implements Hoa_Tokenizer_Token_Util_Interface {

    /**
     * Documentation comment, i.e. starting by /**.
     *
     * @const int
     */
    const TYPE_DOCUMENTATION = 0;

    /**
     * Block comment, i.e. starting by /*.
     *
     * @const int
     */
    const TYPE_BLOCK         = 1;

    /**
     * Inline comment, i.e. starting by //.
     *
     * @const int
     */
    const TYPE_INLINE        = 2;

    /**
     * Shell comment, i.e. starting by #.
     *
     * @const int
     */
    const TYPE_SHELL         = 3;

    /**
     * Type of comment.
     *
     * @var Hoa_Tokenizer_Token_Comment string
     */
    protected $_type         = null;

    /**
     * Comment content.
     *
     * @var Hoa_Tokenizer_Token_Comment string
     */
    protected $_content      = null;



    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $comment    Comment.
     * @return  void
     */
    public function __construct ( $comment ) {

        $this->setComment($comment);
    }

    /**
     * Set comment, i.e. determine type and content.
     *
     * @access  public
     * @param   string  $comment    Comment.
     * @return  void
     */
    public function setComment ( $comment ) {

        $handle  = trim($comment);
        $type    = null;
        $content = $handle;

        if(empty($comment)) {

            $this->setType(self::TYPE_DOCUMENTATION);
            $this->setContent(null);

            return;
        }

        if($handle[0] == '#')
            $type = self::TYPE_SHELL;
        elseif($handle[0] == '/')
            if($handle[1] == '/')
                $type = self::TYPE_INLINE;
            elseif($handle[1] == '*')
                if($handle[2] == '*')
                    $type = self::TYPE_DOCUMENTATION;
                else
                    $type = self::TYPE_BLOCK;
            else
                throw new Hoa_Tokenizer_Token_Util_Exception(
                    'Comment %s is not well-formed.', 0, $comment);
        else
            throw new Hoa_Tokenizer_Token_Util_Exception(
                'Comment %s is not well-formed.', 1, $comment);

        switch($type) {

            case self::TYPE_SHELL:
                $content = preg_replace('#^(\t*| *)?\#\s?#m', '', $content);
                $content = trim($content);
              break;

            case self::TYPE_INLINE:
                $content = preg_replace('#^(\t*| *)?//\s?#m', '', $content);
                $content = trim($content);
              break;

            case self::TYPE_BLOCK:
                $content = trim(substr($content, 2));
                $content = trim(substr($content, 0, -2));
                $content = preg_replace('#^(\t*| *)?\*\s?#m', '', $content);
                $content = trim($content);
              break;

            case self::TYPE_DOCUMENTATION:
                $content = trim(substr($content, 3));
                $content = trim(substr($content, 0, -2));
                $content = preg_replace('#^(\t*| *)?\*\s?#m', '', $content);
                $content = trim($content);
              break;
        }

        $this->setType($type);
        $this->setContent($content);
    }

    /**
     * Set comment type.
     *
     * @access  public
     * @param   int     $type    Type, given by constant self::TYPE_*.
     * @return  int
     */
    public function setType ( $type ) {

        $old         = $this->_type;
        $this->_type = $type;

        return $old;
    }

    /**
     * Set comment content.
     *
     * @access  public
     * @param   string  $content    Comment content.
     * @return  string
     */
    public function setContent ( $content ) {

        $old            = $this->_content;
        $this->_content = $content;

        return $old;
    }

    /**
     * Get comment type.
     *
     * @access  public
     * @return  int
     */
    public function getType ( ) {

        return $this->_type;
    }

    /**
     * Get comment content.
     *
     * @access  public
     * @return  string
     */
    public function getContent ( ) {

        return $this->_content;
    }

    /**
     * Transform token to “tokenizer array”.
     *
     * @access  public
     * @return  array
     */
    public function toArray ( ) {

        return array(
            self::TYPE_DOCUMENTATION === $this->getType()
                ? Hoa_Tokenizer::_DOC_COMMENT
                : Hoa_Tokenizer::_COMMENT,
            $this->getContent(),
            -1
        );
    }

    /**
     * Good idea ?
     *
     * public function toHTML ( );
     */
}
