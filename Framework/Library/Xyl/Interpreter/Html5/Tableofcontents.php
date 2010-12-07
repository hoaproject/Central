<?php

class       Hoa_Xyl_Interpreter_Html5_Tableofcontents
    extends Hoa_Xyl_Element_Concrete {

    protected $_entry = array();

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll('<ul class="toc"' .
                       $this->readAttributesAsString() . '>' . "\n");

        foreach($this->_entry as $entry)
            $out->writeAll(
                '  <li>' . $entry . '</li>' .
                "\n"
            );

        $out->writeAll('</ul>' . "\n");

        return;
    }

    public function addEntry ( Hoa_Xyl_Interpreter_Html5_Section $section ) {

        $this->_entry[] = $section->getValue();

        return;
    }
}
