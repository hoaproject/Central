<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_Li extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        $e = $this->getElement();

        if($e->attributeExists('bind'))
            return '    <li>' . $e->getCurrentData() . '</li>' . "\n";

        return '    <li' . $e->readAttributesAsString() . '>' .
                           $this->getElement() . '</li>' . "\n";
    }
}
