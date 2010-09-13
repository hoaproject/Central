<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_Yield extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        $out = null;

        foreach($this->getElement() as $name => $child)
            $out .= $this->render($child);

        return $out;
    }
}
