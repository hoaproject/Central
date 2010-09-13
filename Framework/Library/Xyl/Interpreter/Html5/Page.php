<?php

import('Xyl.Interpreter.Html5');

class Hoa_Xyl_Interpreter_Html5_Page extends Hoa_Xyl_Interpreter_Html5 {

    public function paint ( ) {

        $out = '<!DOCTYPE html>' . "\n\n" .
               '<html>' . "\n" .
               '<body>';

        foreach($this->getElement() as $name => $child)
            $out .= "\n" .
                    $this->render($child);

        return $out .
               '</body>' . "\n" .
               '</html>';
    }
}
