<?php

class Hoa_Xyl_Interpreter_Html5_Section1 extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        return '<h1>' . $this->getAbstractElement() . '</h1>' . "\n";
    }
}
