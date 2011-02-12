<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of HOA Open Accessibility.
 * Copyright (c) 2007, 2011 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Tag
 *
 */

/**
 * Hoa_Filter_Abstract
 */
import('Filter.Abstract');

/**
 * Class Hoa_Filter_Tag.
 *
 * Apply a tag filter on XML.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Filter
 * @subpackage  Hoa_Filter_Tag
 */

class Hoa_Filter_Tag extends Hoa_Filter_Abstract {

    /**
     * Tag to keep.
     *
     * @var Hoa_Filter_Tag array
     */
    private $keepTag       = array();

    /**
     * Attribute to keep.
     *
     * @var Hoa_Filter_Tag array
     */
    private $keepAttribute = array();

    /**
     * Value of attribute to keep.
     *
     * @var Hoa_Filter_Tag array
     */
    private $keepValue     = array();

    /**
     * Needed arguments.
     *
     * @var Hoa_Filter_Abstract array
     */
    protected $arguments   = array(
        'tag'       => 'specify a list of tag to keep.',
        'attribute' => 'specify a list of attribute to keep.',
        'value'     => 'specify a list of attribute value to keep.'
    );



    /**
     * Set a list of tag to keep.
     *
     * @access  private
     * @param   array    $tag    Tag to keep.
     * @return  array
     */
    private function setKeepTag ( $tag = array() ) {

        if(!is_array($tag))
            $tag = array($tag);

        $old           = $this->keepTag;
        $this->keepTag = array_map('strtolower', $tag);

        return $old;
    }

    /**
     * Set a list of attribute to keep.
     *
     * @access  private
     * @param   array    $attribute    Attribute to keep.
     * @return  array
     */
    private function setKeepAttribute ( $attribute = array() ) {

        if(!is_array($attribute))
            $attribute = array($attribute);

        $old                 = $this->keepAttribute;
        $this->keepAttribute = array_map('strtolower', $attribute);

        return $old;
    }

    /**
     * Set a list of attribute value to keep.
     *
     * @access  private
     * @param   array    $value     Value of attribute to keep.
     * @return  array
     */
    private function setKeepValue ( $value = array() ) {

        if(!is_array($value))
            $value = array($value);

        $old             = $this->keepValue;
        $this->keepValue = array_map('strtolower', $value);

        return $old;
    }

    /**
     * Get tag to keep.
     *
     * @access  public
     * @return  array
     */
    public function getKeepTag ( ) {

        return $this->keepTag;
    }

    /**
     * Get attribute to keep.
     *
     * @access  public
     * @return  array
     */
    public function getKeepAttribute ( ) {

        return $this->keepAttribute;
    }

    /**
     * Get attribute value to keep.
     *
     * @access  public
     * @return  array
     */
    public function getKeepValue ( ) {

        return $this->keepValue;
    }

    /**
     * Initialize parameters.
     *
     * @access  protected
     * @param   string  string    String to filter.
     * @return  void
     */
    protected function stripTag ( $string = '' ) {

        // 0. captured string ;
        // 1. previous tag string ;
        // 2. tag ;
        // 3. attributes and values ;
        // 4. tag content ;
        // 5. next tag string.
        //               1        2        3                        4                   5
        $pattern  = '#([^>]+)?<([\w]+)(\s?[^>]*)(?(?<!(?:[/\s?]))>(.*?)(?:</\2>)+|>?)([^<]+)?#Ss';
        $out      = '';

        if(preg_match_all($pattern, $string, $substring, PREG_SET_ORDER)) {

            foreach($substring as $tags => $tag) {

                // filter tags
                $keepA      = in_array(strtolower($tag[2]), $this->getKeepTag());

                // recursive tags filter
                if(isset($tag[4]) && preg_match($pattern, $tag[4]))
                    $tag[4] = $this->stripTag($tag[4]);
                $tag[5]     = isset($tag[5]) ? $tag[5] : '';

                // filter attributes and values
                // pairs   keys match with attributes
                // unpairs keys match with values
				$attrval    = preg_split('#\s*([^=]*)="([^"]*)"\s*/?\s*#Ss',
                                  $tag[3], -1,
                                  PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                $keepB = true;
                $keepC = true;

                if(!empty($attrval))
                    for($i = 0, $max = count($attrval); $i < $max; $i += 2) {

                        $keepB &= in_array(strtolower($attrval[$i]), $this->getKeepAttribute());
                        $keepC &= in_array(strtolower($attrval[$i+1]), $this->getKeepValue());
                    }

                $out .= $tag[1];

                if(true == $keepA) {

                    $out .= '<' . $tag[2];

                    if(true == $keepB || true == $keepC)
                        $out .= $tag[3];

                    $out .= '>' .
                            (isset($tag[4])
                                 ? $tag[4]
                                 : '') .
                            '</' . $tag[2] . '>';
                }

                $out .= $tag[5];
            }
        }

        return $out;
    }

    /**
     * Apply a tag filter.
     *
     * @access  public
     * @param   string  $string    The string to filter.
     * @return  string
     */
    public function filter ( $string = null ) {

        $this->setKeepTag($this->getFilterArgument('tag'));
        $this->setKeepAttribute($this->getFilterArgument('attribute'));
        $this->setKeepValue($this->getFilterArgument('value'));

        return $this->stripTag($string);
    }
}
