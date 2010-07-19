<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_Page extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        $out = '<!DOCTYPE html>' . "\n\n" .
               '<html>' . "\n" .
               '<body>' . "\n";

        foreach($this->getElement() as $name => $child)
            $out .= $this->getRenderer()->render($child) . "\n";

        return $out .
               '</body>' . "\n" .
               '</html>';
    }
}
