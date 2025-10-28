<?php
  include "../library/check_session.php";
  include "../library/db_connection.php";
  $branch_id=$_SESSION['ses_id_branch'];
?> 
 <link type="text/css" rel="stylesheet" href="../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../library/development-bundle/ui/ui.core.js"></script>
    <script src="../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_date").datepicker(
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
      <td colspan="3" align="center"><strong>LAPORAN HISTORI TRANSAKSI </strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="127" nowrap="nowrap">Tipe Transaksi </td>
      <td width="5">:</td>
      <td width="271"><label>
        <select name="s_transaction_type" id="s_transaction_type">
		   <option value="0">--Pilih Tipe Transaksi--</option>
		   <option value="1">Transfer Posisi Aset</option>
		   <option value="2">Penerimaan Transfer Posisi Aset</option>
		   <option value="3">Pengeluaran Aset</option>
		   <option value="4">Pengembalian Aset</option>
		   <option value="5">Aset Rusak</option>
		   <option value="6">Penghapusan Aset</option>
		   <option value="7">Penjualan Aset</option>
        </select>
      </label>
    </tr>
    <tr>
      <td>No Transaksi</td>
      <td>:</td>
      <td><input type="text" name="txt_transaction_no" id="txt_transaction_no" size="35"/></td>
    <tr>
      <td>Tampilkan Secara</td>
      <td>:</td>
    <td nowrap="nowrap"><?php
	                           if (isset($_POST['rb_type']))
				                   $type_as=$_POST['rb_type'];
			                   else
				                   $type_as='0';
			                   if ($type_as=='0')
			                      {
				                    echo "<input type='radio' id='rb_type' name='rb_type' value='0' checked='checked'/>Langsung";
                                    echo "<input type='radio' id='rb_type' name='rb_type' value='1'/>Export ke Excel</td>";
			                      }
			                   else
			                      {
				                    echo "<input type='radio' id='rb_type' name='rb_type' value='0'/>Langsung";
                                    echo "<input type='radio' id='rb_type' name='rb_type' value='1' checked='checked'/>Export ke Excel</td>";
			                      }
	      ?>    </tr>
    
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
		     var tipe_trans=document.getElementById('s_transaction_type').value;
			 var no_trans=document.getElementById('txt_transaction_no').value.trim();
		     var type=document.getElementById('rb_type').checked; 
			 if (tipe_trans=='0')
			    {
				  alert('Silahkan pilih tipe Transaksi!');
				}
			 else
			 if (no_trans=='')
			    {
				  alert('Nomor transaksi harus diisi!');
				}
			 else
			    {
			      if (type==true)
			          form1.action='../data/document_flow_report/view_direct.php';
			      else
			          form1.action='../data/document_flow_report/view_excell.php';
			      form1.submit();  
				}
		   }
</script>



