<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_Li extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        return '    <li>' . $this->getElement()->getCurrentData() . '</li>';
    }
}
