<?php

class Hoa_Xyl_Interpreter_Html5_Page extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        $out = '<!DOCTYPE html>' . "\n\n" .
               '<html>' . "\n" .
               '<body>';

        foreach($this as $name => $child)
            $out .= "\n" .
                    $child->render();

        return $out .
               '</body>' . "\n" .
               '</html>';
    }
}
