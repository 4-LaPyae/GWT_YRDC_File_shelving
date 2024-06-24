<?php
	require_once("autoload.php");
	require_once("library/reference.php");
	require_once("adminauth.php");
	require_once ('library/PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php');
	error_reporting(E_ALL);
	ob_start();
	ini_set('memory_limit', '512M');
	
	$download_reportname = "file_summary_report_export";
	$reporttitle = 'File Summary Report/စာဖိုင် အနှစ်ချုပ်စာရင်း';
	
	$report_bol = new report_bol();
	
	// Searching
	$cri_str = ' WHERE 1=1 ';
	
	// permission by usertype_department 
	if ( $usertypeid != 0 && $department_enables !='')
		$cri_str .= ' AND td.department_id IN ('.$department_enables.')';
	
	// permission by user_type_security_type 
	if ( $usertypeid != 0 && $security_type_enables !='')
		$cri_str .= ' AND f.security_type_id IN ('.$security_type_enables.')';
	
	// permission by user_type_application_type 
	if ( $usertypeid != 0 && $application_type_enables !='')
		$cri_str .= ' AND f.application_type_id IN ('.$application_type_enables.')';
	
	$param = array();
	if( isset($_POST['filter']) )
	{
		$criobj = json_safedecode($_POST['filter']);
		
		if(isset($criobj->cri_txt_fromdate) && $criobj->cri_txt_fromdate != '')
		{
			$cri_str .= " AND DATE(letter_date) >= :from_date  ";
			$param['from_date'] =   to_ymd($criobj->cri_txt_fromdate);				
		}
			
		if(isset($criobj->cri_txt_todate) && $criobj->cri_txt_todate != '')
		{
			$cri_str .= " AND DATE(letter_date) <= :to_date  ";
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
	if( isset($_SESSION['SESS_SUMMARY_FILE_SORTINGCOLS_MLR']) )
		$SortingCols = $_SESSION['SESS_SUMMARY_FILE_SORTINGCOLS_MLR'];
	
	$cri_text = '';
	if(isset($_POST['cri_text']) && $_POST['cri_text'] != '')
		$cri_text = ",<b>ရှာဖွေသောအချက်အလက်များ</b>,".clean($_POST['cri_text']);
		
	$header_colunm = array();
	$type = "list";
	$datasource = $report_bol->select_file_summary_report_list(0, 0, $SortingCols, $cri_arr);

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
	$sheet->getColumnDimension('D')->setWidth(25);
	$sheet->getColumnDimension('E')->setWidth(25);
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
	$response[] =array('အမှတ်စဉ်', 'ဌာန', 'စုစုပေါင်းရှိသောစာဖိုင်တွဲ', 'ငှားထားသောစာဖိုင်တွဲ', 'စင်မှာရှိသောစာဖိုင်တွဲ', 'ဖျက်သိမ်းမှု အရေအတွက်');

	$sum_file_count = $sum_borrow_count = $sum_destroy_count = 0;
	if( $datasource->rowCount() > 0 )
	{
		while($aRow = $datasource->getNext())
		{
			$department_name = htmlspecialchars($aRow['department_name']);
			$file_count = htmlspecialchars($aRow['file_count']);
			$borrow_count = htmlspecialchars($aRow['borrow_count']);
			$destroy_count = htmlspecialchars($aRow['destroy_count']);
			
			$shelf_have_count = 0;	
			if	($file_count > 0)
				$shelf_have_count = $file_count - $borrow_count; 
			
			$line++;
			$c++;
			$tmpentry = array();
			$tmpentry[] = $c;
			$tmpentry[] = $department_name;
			$tmpentry[] = $file_count;
			$tmpentry[] = $borrow_count;
			$tmpentry[] = $shelf_have_count;
			$tmpentry[] = $destroy_count;
			$response[] = $tmpentry;

			$sheet->getStyle("B$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("C$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("D$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("E$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle("F$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		}
		// စုစုပေါင်း
		$line++;
		$response[] = total_file_summary_report();
		$sheet->getStyle("B$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("C$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("D$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("E$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle("F$line")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	$sheet->fromArray_ValueExplicit($response, NULL, "A2", true);
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
	$table = 'file';
	$type = 'Download Excel';
	$eventloginfo = new eventloginfo();
	$eventloginfo->setuser_id($user_id);
	$eventloginfo->setaction_type($type);
	$eventloginfo->settable_name($table);
	$eventloginfo->setdescription("$reporttitle ကို Excel Export ထုတ်ပါသည်။".$cri_text);
	$result = $eventlogbol->save_eventlog($eventloginfo);
	
	// စုစုပေါင်း
	function total_file_summary_report()
	{
		global $report_bol, $cri_arr;
		$total_arr = $report_bol->get_total_file_summary_report($cri_arr);	
		$sum_file_count = $total_arr['sum_file_count'];
		$sum_borrow_count = $total_arr['sum_borrow_count'];
		$sum_destroy_count = $total_arr['sum_destroy_count'];
		
		$sum_shelf_have_count = 0;	
		if	($sum_file_count > 0)
			$sum_shelf_have_count = $sum_file_count - $sum_borrow_count; 		
			
		$tmpentryout = array();
		$tmpentryout[]  = '';
		$tmpentryout[]  = 'စုစုပေါင်း';
		$tmpentryout[]  = $sum_file_count;
		$tmpentryout[]  = $sum_borrow_count;
		$tmpentryout[]  = $sum_shelf_have_count;
		$tmpentryout[]  = $sum_destroy_count;
		return $tmpentryout;
	}
?>