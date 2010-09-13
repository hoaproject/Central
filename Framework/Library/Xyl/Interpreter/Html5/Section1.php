<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_Section1 extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        return '<h1>' . $this->getElement()->readAll() . '</h1>' . "\n";
    }
}
