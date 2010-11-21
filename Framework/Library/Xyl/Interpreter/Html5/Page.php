<?php

class Hoa_Xyl_Interpreter_Html5_Page extends Hoa_Xyl_Element_Concrete {

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $out->writeAll('<!DOCTYPE html>' . "\n\n" .
                       '<html>' . "\n" .
                       '<body>');

        foreach($this as $name => $child) {

            $out->writeAll("\n");
            $child->render($out);
        }

        $out->writeAll('</body>' . "\n" . '</html>');

        return;
    }
}
