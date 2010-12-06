<?php

class Hoa_Xyl_Interpreter_Html5_Document extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<html>' . "\n" .
            '<body>' . "\n\n"
        );

        foreach($this as $name => $child)
            $child->render($out);

        $out->writeAll(
            "\n" . '</body>' . "\n" . '</html>'
        );

        return;
    }
}
