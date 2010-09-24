<?php

class Hoa_Xyl_Interpreter_Html5_P extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        $out = '<p' . $this->getAbstractElement()->readAttributesAsString() . '>';

        foreach($this as $name => $child)
            $out .= "\n" .
                    $child->render();

        return $out . '</p>' . "\n";
    }
}
