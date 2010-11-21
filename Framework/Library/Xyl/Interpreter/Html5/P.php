<?php

class Hoa_Xyl_Interpreter_Html5_P extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $e = $this->getAbstractElement();

        $out->writeAll('<p' .
                       $e->readAttributesAsString() .
                       '>');

        $out->writeAll($e . '');

        foreach($this as $name => $child) {

            $out->writeAll("\n");
            $child->render($out);
        }

        $out->writeAll('</p>' . "\n");

        return;
    }
}
