 <link type="text/css" rel="stylesheet" href="../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../library/development-bundle/ui/ui.core.js"></script>
    <script src="../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_soa_date_start").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );
							$("#txt_soa_date_end").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );				  
                          }
						  );
    </script>
<?php
  $branch_id=$_SESSION['ses_id_branch'];
  $current_date=date('d-m-Y');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="425" border="0" class="table-list-home">
    <tr>
      <td colspan="3" align="center"><strong>STATMENT OF ACCOUNT REPORT</strong> </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Tipe Tampilan</td>
      <td>:</td>
      <td nowrap="nowrap"><input type='radio' id='rb_view_type' name='rb_view_type' value='0' checked='checked' onclick='call_disable_customer(0)'/>Tampilkan Semua
                          <input type='radio' id='rb_view_type' name='rb_view_type' value='1' onclick='call_disable_customer(1)'/>Tampilkan Sebagian	  </td>
    </tr>
    <tr>
      <td width="143" nowrap="nowrap" valign="top">Pilih Nama Customer </td>
      <td width="3" valign="top">:</td>
      <td width="265"><?php
	                     $q_get_customer="SELECT cust_id, cust_code, cust_name FROM customer WHERE cust_type='0' AND branch_id='$branch_id' ORDER BY cust_name ASC";
						 $exec_get_customer=mysqli_query($db_connection, $q_get_customer);
						 $total_customer=mysqli_num_rows($exec_get_customer);
	                  ?>
                      <select name="s_customer[]" <?php if ($total_customer==0) echo "size='1'"; else echo "size='10'";?> multiple="multiple" id="s_customer[]" 
					     style="width:800px" disabled="disabled">
                       <?php
						  
						  while ($field_customer=mysqli_fetch_array($exec_get_customer))
						        {
								  echo "<option value='".$field_customer['cust_id']."'>".$field_customer['cust_name']." [".$field_customer['cust_code']."]</option>";
								}
						?></select></td>
    </tr>
    <tr>
      <td>Mulai Tanggal</td>
      <td>:</td>
      <td><input type="text" id="txt_soa_date_start" name="txt_soa_date_start" value="<?php echo $current_date;?>"/></td>
    </tr>
    <tr>
      <td>Sampai Tanggal</td>
      <td>:</td>
      <td><input type="text" id="txt_soa_date_end" name="txt_soa_date_end" value="<?php echo $current_date;?>"/></td>
    </tr>
    
    <tr>
      <td>Tampilkan Secara</td>
      <td>&nbsp;</td>
      <td nowrap="nowrap"><input id="rb_type" name="rb_type" type="radio" value="0" / checked="checked">Tampilkan Langsung
                          <input id="rb_type" name="rb_type" type="radio" value="1" />Export ke Excel</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="button" id="btn_process" name="btn_proses" value="Proses" onClick="call_process()"/></td>
    </tr>
  </table>
</form>
</body>
</html>

<script language="javascript">
  function call_process()
           {
		     var x=document.getElementById('rb_type');
			 if (x.checked==true)
			    {
			      form1.action='../data/soa_report/view_data_soa_detail.php?v=0';
				}
			 else
			    {
				  form1.action='../data/soa_report/export_data.php';
				}
			 form1.submit();
			 
		    /*
			 var type=document.getElementById('rb_type').checked; 
			 
			 if (type2==true && type3==true)
			     form1.action='../data/soa_report/view_data_soa_sum.php';
			 else
			 if (type2==false && type3==true)
			     form1.action='../data/soa_report/view_data_soa_detail.php?v=0';
			 else
			 if (type2==true && type3==false)
			     form1.action='../data/soa_report/export_to_excel_sum.php';
			 else
			 if (type2==false && type3==false)
			     form1.action='../data/soa_report/export_data.php';
			 form1.submit();    */
		   }
 function call_disable_customer(x)
          {
		    if (x=='0')
			    document.getElementById('s_customer[]').disabled=true;
			else
			if (x=='1')
			    document.getElementById('s_customer[]').disabled=false;
		  }
</script>



