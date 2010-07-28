<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_P extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        $out = '<p>';

        foreach($this->getElement() as $name => $child)
            $out .= "\n" .
                    $this->getRenderer()->render($child);

        return $out . '</p>' . "\n";
    }
}
