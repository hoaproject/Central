<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_Ul extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        $out = '  <ul' . $this->getElement()->readAttributesAsString() . '>' . "\n";

        foreach($this->getElement() as $name => $child)
            $out .= $this->render($child);

        return $out . '  </ul>' . "\n";
    }
}
