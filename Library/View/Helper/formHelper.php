<?php

class formHelper extends Hoa_View_Helper_Abstract {

    /**
     * .
     *
     * @var Hoa_ type
     */
    protected $var = null;

    /**
     * 
     * .
     *
     * @access  public
     * @param   
     * @return  
     * @throw   
     */
    public function __construct ( ) {

        $this->var = 'form helper';
    }

    /**
     * 
     * .
     *
     * @access  public
     * @param   
     * @return  
     * @throw   
     */
    public function __toString ( ) {

        return $this->var;
    }
}
