<?php

class Hoa_Xyl_Interpreter_Html5_Li extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $e = $this->getAbstractElement();

        if($e->attributeExists('bind')) {

            $out->writeAll( '    <li>' . $this->getCurrentData() .
                            '</li>' . "\n");

            return;
        }

        $out->writeAll('    <li' . $e->readAttributesAsString() . '>' .
                       $e . '</li>' . "\n");

        return;
    }
}
