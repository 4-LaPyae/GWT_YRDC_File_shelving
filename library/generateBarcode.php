<?php
	$class_dir = "library/PHPBarcode";
	require_once($class_dir . '/BCGColor.php');
	require_once($class_dir . '/BCGBarcode.php');
	require_once($class_dir . '/BCGDrawing.php');
	require_once($class_dir . '/BCGFontFile.php');
	require_once($class_dir . '/BCGcode128.barcode.php');
	require_once($class_dir . '/BCGgs1128.barcode.php');
	require_once($class_dir . '/BCGcode39.barcode.php');

	// Configure barcode options
	$bar_output = 1; // const PNG = 1; JPEG = 2; GIF = 3; WBMP = 4;
	$dpi = 100; // default - 72;
	$bar_thick = 70; // bar length
	$bar_scale = 2; // bar_resolution
	$rotate = 0;
	$bar_font = "Arial.ttf";
	$bar_fontsize = 20;
	$bar_checksum = "";
	
	function generate_Barcode($type, $prefix, $code, $bar_start="NULL", $showlabel=true, $bar_label="")
	{
		global $class_dir, $bar_output, $dpi, $bar_thick, $bar_scale, $rotate, $bar_font, $bar_fontsize, $bar_checksum;
		$filename = str_ireplace("/","", $code);				
		$filename = "library/barcode/".$prefix."_".$filename.".png";
		
		if( ! file_exists($filename) )
		{
			$font = new BCGFontFile($class_dir . '/font/' . $bar_font , intval($bar_fontsize));
			$color_black = new BCGColor(0, 0, 0);
			$color_white = new BCGColor(255, 255, 255);
			$codebar = $type; //'BCGcode128';

			$drawException = null;
			try {
				$code_generated = new $codebar();
				if(isset($bar_checksum) && intval($bar_checksum) === 1) {
					$code_generated->setChecksum(true);
				}
				if(isset($bar_start) && !empty($bar_start)) {
					$code_generated->setStart($bar_start === 'NULL' ? null : $bar_start);
				}
				if(isset($bar_label) && !empty($bar_label)) {
					$code_generated->setLabel($bar_label === 'NULL' ? null : $bar_label);
				}
				$code_generated->setThickness($bar_thick);
				$code_generated->setScale($bar_scale);
				$code_generated->setBackgroundColor($color_white);
				$code_generated->setForegroundColor($color_black);
				$code_generated->setFont($font);
				$code_generated->setShowLabel($showlabel);
				$code_generated->parse($code);
				
			} catch(Exception $exception) {
				$drawException = $exception;
			}

			$drawing = new BCGDrawing('', $color_white);
			if($drawException) {
				$drawing->drawException($drawException);
			} else {
				$drawing->setBarcode($code_generated);
				$drawing->setRotationAngle($rotate);
				$drawing->setDPI($dpi == 'null' ? null : (int)$dpi);
				$drawing->draw();
			}

			$drawing->setFilename($filename);
			$drawing->finish(intval($bar_output));
		}
		return $filename;
	}
?>