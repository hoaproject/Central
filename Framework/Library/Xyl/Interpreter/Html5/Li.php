<?php

class Hoa_Xyl_Interpreter_Html5_Li extends Hoa_Xyl_Element_Concrete {

    public function paint ( ) {

        $e = $this->getAbstractElement();

        if($e->attributeExists('bind'))
            return '    <li>' . $this->getCurrentData() . '</li>' . "\n";

        return '    <li' . $e->readAttributesAsString() . '>' . $e . '</li>' . "\n";
    }
}
