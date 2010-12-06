<?php

abstract class Hoa_Xyl_Interpreter_Html5_Generic
    extends    Hoa_Xyl_Element_Concrete {

    protected $_map = null;

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll('<' . $this->_map .
                       $this->readAttributesAsString() . '>');
        $out->writeAll($this->getValue($out));
        $out->writeAll('</' . $this->_map . '>' . "\n");

        return;
    }
}
