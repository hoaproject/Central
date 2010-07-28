<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_Yield extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        $out = null;

        foreach($this->getElement() as $name => $child)
            $out .= $this->getRenderer()->render($child);

        return $out;
    }
}
