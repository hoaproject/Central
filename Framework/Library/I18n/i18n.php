<?php

/**
 * Require the dynamic loader for phputf8 library.
 */

require_once 'framework.php';
import('I18n.utf8');

function utf8_loadFunction ( $fn ) {

	require_once UTF8.'/'.$fn.'.php';
}

function utf8_loadUtilFunction ( $fn ) {

	require_once UTF8.'/utils/'.$fn.'.php';
}
