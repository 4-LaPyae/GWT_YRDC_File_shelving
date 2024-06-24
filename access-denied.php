<?php
	$movepath = '';
	require_once('autoload.php');
	require_once($movepath .'library/reference.php');	
	$title = "Access denied";
	require_once('admin_header.php');
?>
<script>
	function back()
	{
		//window.location = document.referrer
		history.go(-1);
	}
</script>
	<br /><br />
	<table align="center">
		<tr>
			<td>
				<h1>ဝင်ခွင့်ပြုထားခြင်းမရှိပါ။</h1><br />
				<p>
					ဝင်ရောက်ကြည့်ရှုရန် ခွင့်ပြုထားခြင်း မရှိပါ။ <br/>
					ကျေးဇူးပြု၍ သက်ဆိုင်ရာတာဝန်ရှိအကြီးအကဲအား ဆက်သွယ်စုံစမ်းပါ။ <br/><br/>
					<input type="button" class="btn btn-primary" value="ရှေ့သို့" onclick="back()" >
				</p>
			</td>
		</tr>
	</table>
	<br /><br />
<?php
	require_once('admin_footer.php');
?>