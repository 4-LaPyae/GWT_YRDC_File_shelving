<?php
function gwtautoloadlib($class_name)
{
	global $classdirectory;
	foreach($classdirectory as $prefix)
	{ 
	  $path = '../' . $prefix . '/' . $class_name . '.php'; 
	  if(file_exists($path))
	  {
	  	require_once $path;
	    return;	  
	  }
	}
}
spl_autoload_register('gwtautoloadlib');  //allow multiple autoload functions
?>