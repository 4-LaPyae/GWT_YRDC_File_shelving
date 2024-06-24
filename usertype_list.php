<?php
	$pgTitle = 'အသုံးပြုသူအမျိုးအစား စာရင်း';
	$currentPg = 'User Type List';
	
	require_once ("autoload.php");
	require_once('library/reference.php');
	require_once ("adminauth.php");
	
	/* 1. Root Admin, 2. Department, 3. User */
	$root_admin = 0;		
	if(isset($_SESSION ['YRDCFSH_ROOT_ADMIN']))
		$root_admin = $_SESSION ['YRDCFSH_ROOT_ADMIN'];
	//echo $root_admin;exit;
	
	$session_err ='';
	if (isset ($_SESSION ['ur_type_error']) && count($_SESSION ['ur_type_error']) > 0 )
	{
		$session_err = $_SESSION ['ur_type_error'];
	}
	include 'admin_header.php';
?>
<script language="javascript">
	var delete_id = 0;

	jQuery(document).ready(function()
	{
		if(jQuery.cookie('usertype_setup[iDisplayStart]')==null)
		jQuery.cookie('usertype_setup[iDisplayStart]', 0);

		if(jQuery.cookie('usertype_setup[iDisplayLength]')==null)
			jQuery.cookie('usertype_setup[iDisplayLength]', 10);

		ilength = parseInt(jQuery.cookie('usertype_setup[iDisplayLength]'));
		istart = parseInt(jQuery.cookie('usertype_setup[iDisplayStart]'));
		jQuery.fn.dataTableExt.sErrMode = 'throw';
		oTable=jQuery('#dtList').dataTable({
			responsive: true,
			pageLength: ilength,
			displayStart: istart,
			aaSorting: [],
			processing: true,
			serverSide: true,
			lengthChange: true,
			asSorting: [ 'asc', 'desc' ],
			pagingType: 'simple_numbers',
			searching: false,
			autoWidth: false,
			search: {"sSearch":'1=1'},
			bRegex: false,
			/*language: {
				oPaginate: {
					sNext: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-angle-right" /></svg>',
					sPrevious: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-angle-left" /></svg>',
					sFirst: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-angle-double-left" /></svg>',
					sLast: '<svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-angle-double-right" /></svg>' 
				}
			},*/
			drawCallback: function() {	//added to show correct records count base on two columns display
				var oSettings = oTable.fnSettings();
				//store paging state into cookie
				jQuery.cookie('usertype_setup[iDisplayLength]', oSettings._iDisplayLength);
				jQuery.cookie('usertype_setup[iDisplayStart]', oSettings._iDisplayStart);
			},
			"sAjaxSource": "usertype_getlist.php?root_admin=<?php echo $root_admin; ?>",
			columns: [
				{"bSortable": false,"sWidth": "80px"},
				{"sWidth": "auto"},
				{"bSortable": false,"sWidth": "100px"}]
		});
		jQuery('.dataTables_filter').hide();
	});

	function delete_user_type(user_type_id, user_type_name)
	{
		delete_id = user_type_id;
		confirm_delete_popup('အသုံးပြုသူအမျိုးအစား '+decodeURIComponent(user_type_name) + ' ကို ဖျက်ရန်သေချာပါသလား?', 'modal-delete');
	}

	function continue_delete()
	{
		$.post('usertype_exec.php?authaction=delete', {'delete_user_type_id':delete_id}, delete_exec_callback, 'json');
	}

	$(document.body).on('hidden.bs.modal', '.modeldiv', function () {
		$(this).remove();
	});
</script>
<?php
	echo $session_err;
	unset ($_SESSION ['ur_type_error']);

	if (isset ($_SESSION ['ERRMSG_ARR']) && count($_SESSION ['ERRMSG_ARR']) > 0 )
	{
		$errors=array();
		$errors = $_SESSION ['ERRMSG_ARR'];
		echo "<div>";
		echo $errors;
		echo "</div>";
		unset ($_SESSION ['ERRMSG_ARR']);
		unset($errors);
	}
?>

<?php
	echo create_actionmessage_container();

	if ( isset($pageenablearr["Add"])  || $usertypeid == 0 )
		echo '<div class="d-block float-none float-sm-right text-center d-sm-inline-block"><button type="button" onClick="window.location=\'usertype_addnew.php\'" class="btn btn-outline-success btn-sm mb-2 mb-sm-0" id="btnadd"><svg class="icon i-xs align-middle"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="js/symbol-defs.svg#icon-plus" /></svg> အသစ်ထည့်မည် </button></div>';
	
	echo create_dataTable_table( array('အမှတ်စဉ်', 'အသုံးပြုသူအမျိုးအစားအမည်', 'လုပ်ဆောင်ချက်') );
?>

<?php include 'admin_footer.php'; ?>