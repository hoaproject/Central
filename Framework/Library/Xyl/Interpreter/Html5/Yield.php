<?php

class Hoa_Xyl_Interpreter_Html5_Yield extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        $out = null;

        foreach($this as $name => $child)
            $out .= $child->render();

        return $out;
    }
}
