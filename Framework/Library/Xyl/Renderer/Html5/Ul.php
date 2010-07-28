<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_Ul extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        $out = '  <ul>' . "\n";

        foreach($this->getElement() as $name => $child)
            $out .= $this->getRenderer()->render($child);

        return $out . '  </ul>' . "\n";
    }
}
