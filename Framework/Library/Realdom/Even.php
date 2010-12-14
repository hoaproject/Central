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
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
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
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Even
 *
 */

/**
 * Hoa_Realdom_Integer
 */
import('Realdom.Integer') and load();

/**
 * Class Hoa_Realdom_Even.
 *
 * Realistic domain: even.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Realdom
 * @subpackage  Hoa_Realdom_Even
 */

class Hoa_Realdom_Even extends Hoa_Realdom_Integer {

    /**
     * Realistic domain name.
     *
     * @var Hoa_Realdom string
     */
    protected $_name    = 'even';

    /**
     * Subject.
     *
     * @var Hoa_Realdom_Integer object
     */
    protected $_subject = null;



    /**
     * Construct a realistic domain.
     *
     * @access  public
     * @param   Hoa_Realdom_Integer  $subject    Subject.
     * @return  void
     */
    public function construct ( Hoa_Realdom_Integer $subject = null ) {

        if(null === $subject)
            $subject = new Hoa_Realdom_Integer();

        $this->_subject = $subject;

        return;
    }

    /**
     * Predicate whether the sampled value belongs to the realistic domains.
     *
     * @access  public
     * @param   mixed   $q    Sampled value.
     * @return  boolean
     */
    public function predicate ( $q ) {

        return    $this->getSubject()->predicate($q)
               && ($q & 1) == 0;
    }

    /**
     * Sample one new value.
     *
     * @access  protected
     * @return  mixed
     */
    protected function _sample ( Hoa_Test_Sampler $sampler ) {

        $q = $this->getSubject()->sample($sampler);

        if(($q & 1) == 1)
            ++$q;

        return $q;
    }

    /**
     * Get subject.
     *
     * @access  public
     * @return  Hoa_Realdom_Integer
     */
    public function getSubject ( ) {

        return $this->_subject;
    }
}
