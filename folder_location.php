<?php
	$movepath = '';
	$pgTitle = 'စာဖိုင်တွဲ တည်နေရာ';
	$currentPg = 'Folder Location';
	require_once($movepath . "library/reference.php");
	require_once("autoload.php");
	require_once("adminauth.php");	
	require_once($movepath.'library/generateBarcode.php');

	$folder_bol = new folder_bol();
	$shelf_bol = new shelf_bol();
	$folder_id = 0;
	$redirect_flag = true;
	
	if(isset($_GET['shelf_id']) && isset($_GET['folder_id']))
	{
		$shelf_id = clean($_GET['shelf_id']);
		$folder_id = clean($_GET['folder_id']);
		$rfid_no = clean($_GET['rfid_no']);
		$shelf_result = $shelf_bol->get_shelf_data_by_id($shelf_id);
		if($shelf_result->rowCount() > 0)
		{
			$srow = $shelf_result->getNext();
			$shelf_row = htmlspecialchars($srow['shelf_row']);
			$shelf_column = htmlspecialchars($srow['shelf_column']);
			$filename = generate_Barcode("BCGcode128", $folder_id, $rfid_no, "B");
			$redirect_flag = false;
		}
	}
		
	if($redirect_flag == true)
	{
		echo '<script>window.location="folder_list.php";</script>';
		exit();
	}
	
	require_once('admin_header.php');	
?>
<script>
	var movepath = '<?php echo $movepath; ?>';
	var filename = '<?php echo $filename; ?>';
	
	function show_barcode()
	{
		create_loadingimage_dialog('modal-barcode', 'Book Tag ID', movepath);
		$.post('folder_exec.php', {'filename':filename}, function(result)
		{
			select_data_exec_call_back(result);
		}, 'json');
	}
</script>

<div id="diverror">
	<?php
		if( isset($_SESSION ['folder_msg']) && $_SESSION ['folder_msg'] != "" )
		{
			echo "<div id='div_sess_mes' class='alert alert-success'>". $_SESSION ['folder_msg'] .'</div>';
			unset($_SESSION ['folder_msg']);
		}
	?>
</div>

<?php
	echo '<h4 class="my-4 h6 text-center">စင်နံပါတ်- '. htmlspecialchars($srow['shelf_code']) .' [ '. htmlspecialchars($srow['shelf_name']).' ]</h4>';
?>

<div class="table-responsive" id="grid_container">
	<table class="table table-borderless">
		<tbody id="grid_table">
		<?php
			$gcount = 0;
			$gcount_arr = array();
			
			for($i=$shelf_row; $i>=0; $i--)
			{
				echo "<tr>";
				for($j=0; $j<=$shelf_column; $j++)
				{
					if($i == 0)
					{
						if($j != 0)
							echo "<td><b>".$j."</b></td>";
						else
							echo "<td></td>";							
					}
					 else if($j == 0)
						echo "<td><b>".$i."</b></td>";
					else
					{
						$app_flag = false;
						$shelf_column_str ="";
						$result = $folder_bol->get_folder_location_by_shelf_id($shelf_id, $folder_id, $i, $j);
						$col_count = $result->rowCount();
						if($col_count > 0)
						{
							while($row = $result->getNext())
							{
								$folder_no = htmlspecialchars($row['folder_no']);
								$file_type_name = htmlspecialchars($row['file_type_name']);
								$gcount++;
								$gcount_arr[] = $gcount;								
								$shelf_column_str .= '<div id="grid_'. $gcount .'" class="application-block bg-lightlime p-3 text-center">
																အမှုတွဲအမှတ် - '. $folder_no .' <br> အမှုတွဲအမျိုးအစား - '. $file_type_name .'
																<br/><br/> <a data-toggle="modal" data-target="#modal-barcode" onclick="show_barcode()">Barcode ကြည့်ရန်</a>
														  </div>';
							}
						}
						echo "<td class='shelf-col p-0'>". $shelf_column_str ."</td>";
					}
				}
				echo "</tr>";
			}
		?>
		</tbody>			
	</table>
</div>
<input type="button" value="ရှေ့သို့" onclick="window.location='folder_list.php'" class="btn btn-info pull-left m-t" />

<div id="divloading" style="display:none;" class="d-loading" data-text="Processing ..."><!-- Can change 'data-text' for custom text to showing loading time. Default is 'Loading ...' --></div>

<?php
	require_once("admin_footer.php");
?>