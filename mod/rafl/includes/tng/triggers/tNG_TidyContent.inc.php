<?php
// tidy configuration values
  $GLOBALS['TidyContent_TidyLocations'] = array(
	'C:\utils\tidy.exe',
	'tidy',
	'/usr/bin/tidy',
	'/usr/local/bin/tidy',
	'/usr/local/tidy/bin/tidy',
	'/usr/bin/tidy/bin/tidy',
	'tidy.exe', 
	'C:/Progra~1/tidy/tidy.exe',
	'C:/Windows/tidy.exe', 
	'C:/utils/dlls/tidy.exe'
	);
  $GLOBALS['TidyContent_TidyConfiguration'] = KT_RealPath(dirname(realpath(__FILE__)), true) . '.tidyconf';
  $GLOBALS['TidyContent_TidyTempPath'] = dirname(__FILE__).'/../../common/_temp/.tidy/';
?>
