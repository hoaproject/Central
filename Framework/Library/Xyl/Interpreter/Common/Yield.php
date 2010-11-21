<?php

class Hoa_Xyl_Interpreter_Common_Yield extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        foreach($this as $name => $child)
            $child->render($out);

        return;
    }
}
