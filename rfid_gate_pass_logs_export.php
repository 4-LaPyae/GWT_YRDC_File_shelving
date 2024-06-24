<?php
	require_once("autoload.php");
	require_once("library/reference.php");
	require_once("adminauth.php");
	require_once ('library/PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php');
	error_reporting(E_ALL);
	ob_start();
	ini_set('memory_limit', '512M');
	
	$download_reportname = "rfid_gate_pass_logs_export";
	$reporttitle = 'ID Gate Pass Logs Report';
	
	$gate_bol = new gate_bol();
	
	// Searching
	$cri_str = ' WHERE 1=1 ';	
	$param = array();
	if( isset($_POST['filter']) )
	{
		$criobj = json_safedecode($_POST['filter']);
		
		if( isset($criobj->cri_rfid_no) && $criobj->cri_rfid_no != '' )	
		{
			$cri_str .= " AND gl.rfid_no LIKE :cri_rfid_no";	
			$param[':cri_rfid_no'] = '%'. clean($criobj->cri_rfid_no) .'%';
		}
		
		if( isset($criobj->cri_folder_no) && $criobj->cri_folder_no != '' )	
		{
			$cri_str .= " AND folder_no LIKE :cri_folder_no";	
			$param[':cri_folder_no'] = '%'. clean($criobj->cri_folder_no) .'%';
		}
		
		if( isset($criobj->cri_folder_description) && $criobj->cri_folder_description != '' )	
		{
			$cri_str .= " AND description LIKE :cri_folder_description";	
			$param[':cri_folder_description'] = '%'. clean($criobj->cri_folder_description) .'%';
		}
		
		if( isset($criobj->cri_gate_name) && $criobj->cri_gate_name != '' )	
		{
			$cri_str .= " AND gate_name LIKE :cri_gate_name";	
			$param[':cri_gate_name'] = '%'. clean($criobj->cri_gate_name) .'%';
		}
		
		if(isset($criobj->cri_txt_fromdate) && $criobj->cri_txt_fromdate != '')
		{
			$cri_str .= " AND DATE(log_time) >= :from_date  ";
			$param['from_date'] =   to_ymd($criobj->cri_txt_fromdate);				
		}
			
		if(isset($criobj->cri_txt_todate) && $criobj->cri_txt_todate != '')
		{
			$cri_str .= " AND DATE(log_time) <= :to_date  ";
			$param['to_date'] =   to_ymd($criobj->cri_txt_todate);				
		}
	}
	$cri_arr = array($cri_str, $param);
	// print_r($cri_arr);exit;
	
	$user_id = $usertypeid = 0;
	if(isset($_SESSION ['YRDCFSH_LOGIN_ID']))
		$user_id = $_SESSION['YRDCFSH_LOGIN_ID'];
		
	if(isset($_SESSION ['YRDCFSH_LOGIN_TYPE_ID']))
		$usertypeid = $_SESSION['YRDCFSH_LOGIN_TYPE_ID'];
	
	$user = '';
	if( isset($_SESSION['YRDCFSH_LOGIN_NAME']) )
		$user = clean($_SESSION['YRDCFSH_LOGIN_NAME']);
	
	$SortingCols = '';
	if( isset($_SESSION['SESS_RFID_GATE_PASS_LOGS_SORTINGCOLS_MLR']) )
		$SortingCols = $_SESSION['SESS_RFID_GATE_PASS_LOGS_SORTINGCOLS_MLR'];
	
	$cri_text = '';
	if(isset($_POST['cri_text']) && $_POST['cri_text'] != '')
		$cri_text = ",<b>ရှာဖွေသောအချက်အလက်များ</b>,".clean($_POST['cri_text']);
		
	$header_colunm = array();
	$type = "list";
	$datasource = $gate_bol->select_rfid_gate_pass_log_list(0, 0, $SortingCols, $cri_arr);

	/** PHPExcel */
	require_once ('library/PHPExcel/Classes/PHPExcel.php');

	/** PHPExcel_IOFactory */
	require_once ('library/PHPExcel/Classes/PHPExcel/IOFactory.php');

	$outputPath = tempnam("tmp/", "xlsfile");
	$myFile = $outputPath . ".xls";
	$retbol = rename($outputPath,$myFile);
	$fileType = "EXCEL";

	$workbook = new PHPExcel();
	$line = 1;
	$strline = $line;
	$sheet = $workbook->getActiveSheet();
	$sheet->getDefaultStyle()->getFont()->setName('Myanmar3');
	$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
	$sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$sheet->getColumnDimension('A')->setWidth(10);
	$sheet->getColumnDimension('B')->setWidth(30);
	$sheet->getColumnDimension('C')->setWidth(30);
	$sheet->getColumnDimension('D')->setWidth(30);
	$sheet->getColumnDimension('E')->setWidth(30);
	$sheet->getColumnDimension('F')->setWidth(30);
	$endcol = "F";
	$curcol = "";
	$strcol = "A";
	$curcol = $strcol;
	$styleArray = array(
			'borders' => array(
			'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,		
			'color' => array( 'rgb' => '000000' ),
			),
			'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
			'inside' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array( 'rgb' => '000000' ),
			),));

	$sheet->getStyle("A1")->getFont()->setBold(true);
	$sheet->getStyle("A1")->getFont()->setSize(12);

	$sheet->mergeCells("A1:F1");
	$sheet->getStyle("A1:F1")->getFont()->setSize(12);	

	$sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$workbook->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
	$workbook->getActiveSheet()->setCellValue("A1", $reporttitle);	
	$line++;
	$c = 0;
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);
	$sheet->getStyle($curcol.$line)->getFont()->setSize(11);
	
	$response= array();	
	$response[] =array('အမှတ်စဉ်', 'ID No', 'စာဖိုင်တွဲ အမှတ်', 'အကြောင်းအရာ', 'ရက်စွဲ', 'ဂိတ် အမည်');

	while($aRow = $datasource->getNext())
	{
		$rfid_card_no = htmlspecialchars($aRow['rfid_card_no']);
		$folder_no = htmlspecialchars($aRow['folder_no']);
		$description = htmlspecialchars($aRow['description']);
		$now_date = htmlspecialchars($aRow['now_date']);
		$gate_name = htmlspecialchars($aRow['gate_name']);
		
		$line++;
		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = $rfid_card_no;
		$tmpentry[] = $folder_no;
		$tmpentry[] = $description;
		$tmpentry[] = $now_date;
		$tmpentry[] = $gate_name;
		$response[] = $tmpentry;
				
		$sheet->getStyle("B$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("C$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("D$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("E$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("F$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	$sheet->fromArray($response, NULL, "A2", true);
	$sheet->getStyle("B2")->getFont()->setBold(true);
	$sheet->getStyle("C2")->getFont()->setBold(true);
	$sheet->getStyle("D2")->getFont()->setBold(true);
	$sheet->getStyle("E2")->getFont()->setBold(true);
	$sheet->getStyle("F2")->getFont()->setBold(true);
	$sheet->duplicateStyleArray($styleArray, "$strcol".'2'.":$endcol$line");
	$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');

	$xlsmyFileCut =tempfile_unique("tmp", "xlsdata", ".xls"); // to solve linux error
	$objWriter->save($xlsmyFileCut);
	$xlsmyFileCutName = $download_reportname;
	header('Content-type: application/xls');
	header('Pragma: public' ); // required for IE
	header('Content-Disposition: attachment; filename='.$download_reportname.'.xls');

	ob_end_clean();
	readfile($xlsmyFileCut);
	unlink($xlsmyFileCut);
	
	//save to eventlog//
	$eventlogbol = new eventlogbol();
	$table = 'gate_log';
	$type = 'Download Excel';
	$eventloginfo = new eventloginfo();
	$eventloginfo->setuser_id($user_id);
	$eventloginfo->setaction_type($type);
	$eventloginfo->settable_name($table);
	$eventloginfo->setdescription("$reporttitle ကို Excel Export ထုတ်ပါသည်။".$cri_text);
	$result = $eventlogbol->save_eventlog($eventloginfo);
?>