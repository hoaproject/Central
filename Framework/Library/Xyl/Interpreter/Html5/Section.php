<?php

abstract class Hoa_Xyl_Interpreter_Html5_Section
    extends    Hoa_Xyl_Element_Concrete
    implements Hoa_Xyl_Element_Executable {

    protected $_n = 0;

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll('<h' . $this->_n .
                       $this->readAttributesAsString() . '>');
        $out->writeAll($this->getValue($out));
        $out->writeAll('</h' . $this->_n . '>' . "\n");

        return;
    }

    public function execute ( ) {

        $ae = $this->getAbstractElement();

        if(false === $ae->attributeExists('for'))
            return;

        $for = $ae->readAttribute('for');
        $toc = $ae->xpath(
            '//__current_ns:tableofcontents[@id = "' . $for . '"]'
        );

        if(!isset($toc[0]))
            return;

        $this->getConcreteElement($toc[0])->addEntry($this);

        return;
    }
}
