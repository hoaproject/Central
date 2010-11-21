<?php

class Hoa_Xyl_Interpreter_Html5_Section1 extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $e = $this->getAbstractElement();

        if($e->attributeExists('bind')) {

            $out->writeAll('<h1>' . $this->getCurrentData() . '</h1>'.  "\n");

            return;
        }

        $out->writeAll('<h1>' . $e . '</h1>' . "\n");

        return;
    }
}
