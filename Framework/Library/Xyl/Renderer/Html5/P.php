<?php

import('Xyl.Element.Render');

class Hoa_Xyl_Renderer_Html5_P extends Hoa_Xyl_Element_Render {

    public function paint ( ) {

        return '<p>' . $this->getElement()->getData() . '</p>';
    }
}
