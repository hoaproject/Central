<?php

class          Hoa_Xyl_Interpreter_Html5_Document
    extends    Hoa_Xyl_Element_Concrete
    implements Hoa_Xyl_Element_Executable {

    protected $_resources = array();

    public function paint ( Hoa_Stream_Interface_Out $out ) {

        $root = $this->getAbstractElementSuperRoot();

        $out->writeAll(
            '<!DOCTYPE html>' . "\n\n" .
            '<html>' . "\n" .
            '<head>' . "\n"
        );

        if(isset($this->_resources['css']))
            foreach($this->_resources['css'] as $href)
                $out->writeAll(
                    '  <link type="text/css" href="' .
                    $root->resolve($href) .
                    '" rel="stylesheet" />' . "\n"
                );

        $out->writeAll(
            '</head>' . "\n" .
            '<body>' . "\n\n"
        );

        foreach($this as $name => $child)
            $child->render($out);

        $out->writeAll(
            "\n" . '</body>' . "\n" . '</html>'
        );

        return;
    }

    public function execute ( ) {

        $root                    = $this->getAbstractElementSuperRoot();
        $styles                  = $root->getStylesheets();
        $this->_resources['css'] = array();

        foreach($styles as $style) {

            $resolved = $root->resolve($style);

            if(false === file_exists($resolved))
                continue;

            if('hoa://Library/Xyl/' == substr($style, 0, 18)) {

                $redirect = 'hoa://Application/Public/' . substr($style, 18);

                if(false === file_exists($redirect))
                    if(false === copy($resolved, $redirect))
                        throw new Hoa_Xyl_Interpreter_Html5_Exception(
                            'Failed to copy %s in %s.',
                            0, array($style, $redirect));

                $style    = $redirect;
            }

            $this->_resources['css'][] = $style;
        }

        return;
    }
}
