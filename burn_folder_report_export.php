<?php
	require_once("autoload.php");
	require_once("library/reference.php");
	require_once("adminauth.php");
	require_once ('library/PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php');
	error_reporting(E_ALL);
	ob_start();
	ini_set('memory_limit', '512M');
	
	$download_reportname = "burn_folder_report_export";
	$reporttitle = 'Folder Burn Report/စာဖိုင်တွဲ ဖျက်သိမ်းမှုစာရင်း';
	
	$report_bol = new report_bol();
	
	// Searching
	$cri_str = ' WHERE 1=1 AND status = 3 ';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND sf.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND fd.security_type_id IN ('.$security_type_enables.')';
	
	$param = array();
	if( isset($_POST['filter']) )
	{
		$criobj = json_safedecode($_POST['filter']);
		
		if( isset($criobj->cri_rfid_no) && $criobj->cri_rfid_no != '' )	
		{
			$cri_str .= " AND rfid_no LIKE :cri_rfid_no";	
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
		
		if( isset($criobj->cri_file_type_id) && $criobj->cri_file_type_id != '' )
		{
			$cri_str .= " AND fd.file_type_id = :file_type_id";
			$param[':file_type_id'] = $criobj->cri_file_type_id;
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
	if( isset($_SESSION['SESS_BURN_FOLDER_SORTINGCOLS_MLR']) )
		$SortingCols = $_SESSION['SESS_BURN_FOLDER_SORTINGCOLS_MLR'];
	
	$cri_text = '';
	if(isset($_POST['cri_text']) && $_POST['cri_text'] != '')
		$cri_text = ",<b>ရှာဖွေသောအချက်အလက်များ</b>,".clean($_POST['cri_text']);
		
	$header_colunm = array();
	$type = "list";
	$datasource = $report_bol->select_folder_burn_report_list(0, 0, $SortingCols, $cri_arr);

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
	$sheet->getColumnDimension('B')->setWidth(15);
	$sheet->getColumnDimension('C')->setWidth(20);
	$sheet->getColumnDimension('D')->setWidth(30);
	$sheet->getColumnDimension('E')->setWidth(20);
	$sheet->getColumnDimension('F')->setWidth(20);
	$sheet->getColumnDimension('G')->setWidth(20);
	$sheet->getColumnDimension('H')->setWidth(25);
	$sheet->getColumnDimension('I')->setWidth(25);
	$sheet->getColumnDimension('J')->setWidth(25);
	$sheet->getColumnDimension('K')->setWidth(25);
	$sheet->getColumnDimension('L')->setWidth(25);
	$sheet->getColumnDimension('M')->setWidth(25);
	$endcol = "M";
	$sheet->mergeCells("A1:M1");
	$sheet->getStyle("A1:M1")->getFont()->setSize(12);	
	
	$line++;
	$curcol="A";
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("A2:A3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'အမှတ်စဉ်');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("B2:B3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'အမိန့်အမှတ်');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("C2:C3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'ဖျက်သိမ်းရက်စွဲ');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	PHPExcelMergeCenter($sheet,"$curcol$line:F$line");
	$sheet->setCellValue($curcol.$line, 'ခွင့်ပြုသူ');
	
	$sheet->setCellValue("D3", 'ကိုယ်ပိုင်အမှတ်');	
	$sheet->getStyle("D3")->getFont()->setBold(true);	
	$sheet->getStyle("D3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->setCellValue("E3", 'အမည်');	
	$sheet->getStyle("E3")->getFont()->setBold(true);	
	$sheet->getStyle("E3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->setCellValue("F3", 'ဌာန');	
	$sheet->getStyle("F3")->getFont()->setBold(true);	
	$sheet->getStyle("F3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	PHPExcelMergeCenter($sheet,"$curcol$line:I$line");
	$sheet->setCellValue($curcol.$line, 'တာ၀န်ခံရသူ');
	
	$sheet->setCellValue("G3", 'ကိုယ်ပိုင်အမှတ်');	
	$sheet->getStyle("G3")->getFont()->setBold(true);	
	$sheet->getStyle("G3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->setCellValue("H3", 'အမည်');	
	$sheet->getStyle("H3")->getFont()->setBold(true);	
	$sheet->getStyle("H3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->setCellValue("I3", 'ဌာန');	
	$sheet->getStyle("I3")->getFont()->setBold(true);	
	$sheet->getStyle("I3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("J2:J3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'ID No.');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("K2:K3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'စာဖိုင်တွဲ အမှတ်');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("L2:L3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'အကြောင်းအရာ');
	$curcol = Next_Excel_Col($curcol);
	
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);	
	$sheet->mergeCells("M2:M3");
	PHPExcelMergeCenter($sheet,"$curcol$line:$curcol".($line));
	$sheet->setCellValue($curcol.$line, 'ဖိုင်တွဲအမျိုးအစား');
	
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
	$sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$workbook->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
	$workbook->getActiveSheet()->setCellValue("A1", $reporttitle);	
	$line++;
	$c = 0;
	$sheet->getStyle($curcol.$line)->getFont()->setBold(true);
	$sheet->getStyle($curcol.$line)->getFont()->setSize(11);
	
	$response= array();	
	while($aRow = $datasource->getNext())
	{
		$destroy_order_no = htmlspecialchars($aRow['destroy_order_no']);
		$destroy_order_employeeid = htmlspecialchars($aRow['destroy_order_employeeid']);
		$destroy_order_employee_name = htmlspecialchars($aRow['destroy_order_employee_name']);
		$destroy_order_department = htmlspecialchars($aRow['destroy_order_department']);
		$destroy_date = htmlspecialchars($aRow['now_destroy_date']);
		$destroy_duty_employeeid = htmlspecialchars($aRow['destroy_duty_employeeid']);
		$destroy_duty_employee_name = htmlspecialchars($aRow['destroy_duty_employee_name']);
		$destroy_duty_department = htmlspecialchars($aRow['destroy_duty_department']);
		$rfid_no = htmlspecialchars($aRow['rfid_no']);
		$folder_no = htmlspecialchars($aRow['folder_no']);
		$description = htmlspecialchars($aRow['description']);
		$file_type_name = htmlspecialchars($aRow['file_type_name']);
		
		$line++;
		$c++;
		$tmpentry = array();
		$tmpentry[] = $c;
		$tmpentry[] = $destroy_order_no;
		$tmpentry[] = $destroy_date;
		$tmpentry[] = $destroy_order_employeeid;
		$tmpentry[] = $destroy_order_employee_name;
		$tmpentry[] = $destroy_order_department;
		$tmpentry[] = $destroy_duty_employeeid;
		$tmpentry[] = $destroy_duty_employee_name;
		$tmpentry[] = $destroy_duty_department;		
		$tmpentry[] = $rfid_no;
		$tmpentry[] = $folder_no;
		$tmpentry[] = $description;
		$tmpentry[] = $file_type_name;
		$response[] = $tmpentry;
				
		$sheet->getStyle("B$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("C$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("D$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("E$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("F$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("G$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("H$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("I$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("J$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("K$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("L$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("M$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	$sheet->fromArray($response, NULL, "A4", true);
	$sheet->getStyle("B2")->getFont()->setBold(true);
	$sheet->getStyle("C2")->getFont()->setBold(true);
	$sheet->getStyle("D2")->getFont()->setBold(true);
	$sheet->getStyle("E2")->getFont()->setBold(true);
	$sheet->getStyle("F2")->getFont()->setBold(true);
	$sheet->getStyle("G2")->getFont()->setBold(true);
	$sheet->getStyle("H2")->getFont()->setBold(true);
	$sheet->getStyle("I2")->getFont()->setBold(true);
	$sheet->getStyle("J2")->getFont()->setBold(true);
	$sheet->getStyle("K2")->getFont()->setBold(true);
	$sheet->getStyle("L2")->getFont()->setBold(true);
	$sheet->getStyle("M2")->getFont()->setBold(true);
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
	$table = 'folder';
	$type = 'Download Excel';
	$eventloginfo = new eventloginfo();
	$eventloginfo->setuser_id($user_id);
	$eventloginfo->setaction_type($type);
	$eventloginfo->settable_name($table);
	$eventloginfo->setdescription("$reporttitle ကို Excel Export ထုတ်ပါသည်။".$cri_text);
	$result = $eventlogbol->save_eventlog($eventloginfo);
?>