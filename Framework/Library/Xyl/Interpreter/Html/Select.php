<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
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
 */

namespace {

from('Hoa')

/**
 * \Hoa\Xyl\Element\Concrete
 */
-> import('Xyl.Element.Concrete');

}

namespace Hoa\Xyl\Interpreter\Html {

/**
 * Class \Hoa\Xyl\Interpreter\Html\Select.
 *
 * The <select /> component.
 *
 * @author     Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright (c) 2007, 2011 Ivan ENDERLIN.
 * @license    http://gnu.org/licenses/gpl.txt GNU GPL
 */

class Select extends \Hoa\Xyl\Element\Concrete {

    /**
     * Whether the select is valid or not.
     *
     * @var \Hoa\Xyl\Interpreter\Html\Input bool
     */
    protected $_validity = false;



    /**
     * Paint the element.
     *
     * @access  protected
     * @param   \Hoa\Stream\IStream\Out  $out    Out stream.
     * @return  void
     */
    protected function paint ( \Hoa\Stream\IStream\Out $out ) {

        $out->writeAll('<select' . $this->readAttributesAsString() . '>' . "\n");

        foreach($this as $child)
            $child->render($out);

        $out->writeAll('</select>');

        return;
    }

    /**
     * Set (or restore) the select value.
     *
     * @access  public
     * @param   string  $value    Value.
     * @return  string
     */
    public function setValue ( $value ) {

        foreach($this->getOptions() as $option) {

            $option = $this->getConcreteElement($option);

            if($value == $option->getValue())
                $option->setValue($value);
            else
                $option->unsetValue($value);
        }

        return;
    }

    /**
     * Get the select value.
     *
     * @access  public
     * @return  string
     */
    public function getValue ( ) {

        if(null === $option = $this->getSelectedOption())
            return null;

        return $option->getValue();
    }

    /**
     * Unset the select value.
     *
     * @access  public
     * @return  void
     */
    public function unsetValue ( ) {

        if(null === $option = $this->getSelectedOption())
            return;

        $option->unsetValue();

        return;
    }

    /**
     * Get all <option /> components.
     *
     * @access  public
     * @return  array
     */
    public function getOptions ( ) {

        return array_merge(
            $this->xpath('./__current_ns:option'),
            $this->xpath('./__current_ns:optgroup/__current_ns:option')
        );
    }

    /**
     * Get the selected <option /> components.
     *
     * @access  public
     * @return  \Hoa\Xyl\Interpreter\Html\Option
     */
    public function getSelectedOption ( ) {

        $options = array_merge(
            $this->xpath('./__current_ns:option[@selected]'),
            $this->xpath('./__current_ns:optgroup/__current_ns:option[@selected]')
        );

        if(empty($options))
            return null;

        return $this->getConcreteElement($options[0]);
    }

    /**
     * Check the select validity.
     *
     * @access  public
     * @param   mixed  $value    Value (if null, will find the value).
     * @return  bool
     */
    public function checkValidity ( $value = null ) {

        $options = $this->getOptions();
        $values  = array();

        if(null === $value)
            foreach($options as $option) {

                $option = $this->getConcreteElement($option);

                if(true === $option->isSelected())
                    $values[] = $value = $option->getValue();
                else
                    $values[] = $option->getValue();
            }
        else
            foreach($options as $option ) {

                $option = $this->getConcreteElement($option);
                $values[] = $option->getValue();
            }

        return $this->_validity = in_array($value, $values);
    }

    /**
     * Whether the select is valid or not.
     *
     * @access  public
     * @return  bool
     */
    public function isValid ( ) {

        return $this->_validity;
    }
}

}
