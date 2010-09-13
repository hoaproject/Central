<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_P extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        $out = '<p' . $this->getElement()->readAttributesAsString() . '>';

        foreach($this->getElement() as $name => $child)
            $out .= "\n" .
                    $this->render($child);

        return $out . '</p>' . "\n";
    }
}
