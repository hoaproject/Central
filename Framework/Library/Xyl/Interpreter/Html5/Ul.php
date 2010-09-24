<?php

class Hoa_Xyl_Interpreter_Html5_Ul extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        $out = '  <ul' . $this->getAbstractElement()->readAttributesAsString() . '>' . "\n";

        foreach($this as $name => $child)
            $out .= $child->render();

        return $out . '  </ul>' . "\n";
    }
}
