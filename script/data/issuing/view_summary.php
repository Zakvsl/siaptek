<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
 <link type="text/css" rel="stylesheet" href="../../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../../library/development-bundle/ui/ui.core.js"></script>
    <script src="../../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_issuingh_date").datepicker(
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
  include "../../library/db_connection.php";	
  include "../../library/library_function.php";
  include "../../library/style.css";
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  if ($cru=='u')
     {
	   $q_get_issuing_header="SELECT issuingh_id, issuingh_code, issuingh_date, issuingh_do_no, issuingh_sent_by, issuingh_vehicle_no, emp_id, issuingh_type, cust_id, 
	                                 issuingh_receiver_name, issuingh_notes, 
									 CASE issuingh_is_canceled
									      WHEN '0' THEN 'Tidak'
										  WHEN '1' THEN 'Ya'
									 END issuingh_is_canceled, issuingh_canceled_date, issuingh_canceled_reason, created_by, created_time, updated_by, updated_time
                              FROM issuing_header
                              WHERE branch_id='$branch_id' AND issuingh_id='$id'";
	   $exec_get_issuing_header=mysqli_query($db_connection,$q_get_issuing_header);
	   $total_get_issuing_header=mysqli_num_rows($exec_get_issuing_header);
	   $field_issuing_header=mysqli_fetch_array($exec_get_issuing_header);
	   if ($total_get_issuing_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Pengeluaran Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_issuing_detail="SELECT issuing_detail.itemd_id AS itemd_id_1, master_item.masti_id AS masti_id_1, masti_code, masti_name, cati_name, masti_capacity, uom_name,
                                   issuingd_status AS issuingd_status_1,
                                   CASE issuingd_status
                                        WHEN '0' THEN 'Normal'
                                        WHEN '1' THEN 'Rental'
                                        WHEN '2' THEN 'UJM'
                                   END issuingd_status, issuingd_is_canceled,
                                  (SELECT COUNT(*) 
                                   FROM issuing_detail 
                                   INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
                                   WHERE masti_id=masti_id_1 AND issuingd_status=issuingd_status_1 AND issuingd_is_canceled='0' AND issuingh_id='$id') AS total_deposit,
                                  (SELECT COUNT(*) 
                                   FROM issuing_detail 
                                   INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
                                   WHERE masti_id=masti_id_1 AND issuingd_status=issuingd_status_1 AND issuingd_is_canceled='0' AND issuingh_id='$id' AND issuingd_is_return='1') AS
								   total_return,
                                  (SELECT COUNT(*) 
                                   FROM issuing_detail 
                                   INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
                                   WHERE masti_id=masti_id_1 AND issuingd_status=issuingd_status_1 AND issuingd_is_canceled='0' AND issuingh_id='$id' AND issuingd_is_return='0') AS
								   total_not_return
                            FROM issuing_detail
                            INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
                            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
							INNER JOIN uom ON uom.uom_id=master_item.uom_id_1
                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                            WHERE issuingh_id='$id' AND issuingd_status='2'
                            GROUP BY issuingd_status, masti_id_1
							ORDER BY masti_id_1 ASC";				
			$exec_get_issuing_detail=mysqli_query($db_connection,$q_get_issuing_detail);
			$issuingh_id=$field_issuing_header['issuingh_id'];
		    $trans_code=$field_issuing_header['issuingh_code'];
			$trans_code_1=$field_issuing_header['issuingh_code'];
			$issuingh_date=get_date_1($field_issuing_header['issuingh_date']);
			$issuingh_do_no=$field_issuing_header['issuingh_do_no'];
			$issuingh_sent_use=$field_issuing_header['issuingh_sent_by'];
			$issuingh_vehicle_no=$field_issuing_header['issuingh_vehicle_no'];
			$issuingh_sent_by=$field_issuing_header['emp_id'];
			$issuingh_type=$field_issuing_header['issuingh_type'];
			$issuingh_cust_=$field_issuing_header['cust_id'];
			$issuingh_receiver=$field_issuing_header['issuingh_receiver_name'];
			$issuingh_notes=$field_issuing_header['issuingh_notes'];
			$issuingh_is_canceled=$field_issuing_header['issuingh_is_canceled'];
			$issuingh_canceled_date=$field_issuing_header['issuingh_canceled_date'];
			$issuingh_canceled_reason=$field_issuing_header['issuingh_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_issuing_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection,$q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_issuing_header['created_time'];
			if ($field_issuing_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_issuing_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection,$q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_issuing_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
       $trans_code=get_format_number($db_connection, 'CYO',$branch_id);
	 }
 // echo $q_get_issuing_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_issuing" id="f_cru_issuing" onSubmit="">
  <table width="1280" border="0" cellspacing="1" cellpadding="1" id="tbl_issuing">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left">TRANSAKSI PENGELUARAN</th>
    </tr>
    <tr>
      <td>No Transaksi * </td>
      <td>:</td>
      <td><input name="txt_code" type="text" id="txt_code" size="30" maxlength="50"  readonly="readonly"  
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo $trans_code;
				}
		     else
			 if ($cru=='u')
			    {
				  echo $trans_code;
				}		
		 ?>">
        <input name="txt_id" type="hidden" id="txt_id" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_id;
				}		
		 ?>"/>
        <input name="txt_code_1" type="hidden" id="txt_code_1" <?php 
		     if ($cru=='i')
			    {
				  echo "value=''";
				}
		     else
			 if ($cru=='u')
			    {
				  echo "value='$trans_code_1'";
				}		
		 ?>/></td>
      <td>Tipe Pengeluaran</td>
      <td>:</td>
      <td>
    <?php
		    if (isset($_POST['rb_type'])) 
			    $issuingh_type=$_POST['rb_type']; 
		   
			if ($issuingh_type=='0')
			   { 
		         echo "<input id='rb_type' name='rb_type' type='radio' value='0' checked='checked' onClick='submit()'>Customer";
                 echo "<input id='rb_type' name='rb_type' type='radio' value='1' onClick='submit()'>Vendor</td>";
			   }
	        else
		    if ($issuingh_type=='1')
			   { 
		         echo "<input id='rb_type' name='rb_type' type='radio' value='0' onClick='submit()'>Customer";
                 echo "<input id='rb_type' name='rb_type' type='radio' value='1' checked='checked' onClick='submit()'>Vendor</td>";
			   }
			else
			   { 
		         echo "<input id='rb_type' name='rb_type' type='radio' value='0' checked='checked' onClick='submit()'>Customer";
                 echo "<input id='rb_type' name='rb_type' type='radio' value='1' onClick='submit()'>Vendor</td>";
			   }
		 ?>    </tr>
    <tr>
      <td width="16%" valign="top">Tanggal * </td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><input name="txt_issuingh_date" type="text" id="txt_issuingh_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$issuingh_date; 
			else 
		        echo "value='$current_date'";
		 ?>></td>
      <td width="18%" valign="top">Nama Customer/Vendor * </td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><select name="s_customer_vendor" id="s_customer_vendor" style="width:300px">
        <?php
		    if (isset($_POST['rb_type']))
			    $issuingh_type=$_POST['rb_type'];
		    else
			    $issuingh_type=0;
				
			if ($issuingh_type=='0' || $issuingh_type=='')	
		        $q_get_customer_vendor="SELECT cust_name as cust_name, cust_id as cust_id, cust_code as cust_code
                                        FROM customer
										WHERE cust_type='0'";
			else
			    $q_get_customer_vendor="SELECT cust_name as cust_name, cust_id as cust_id, cust_code as cust_code
                                        FROM customer
										WHERE cust_type='1'";
		 	$exec_get_customer_vendor=mysqli_query($db_connection,$q_get_customer_vendor);
			if ($issuingh_type=='' || $issuingh_type=='0')
			    echo "<option value='0'>-Pilih Customer-</option>";
			else
			    echo "<option value='0'>-Pilih Vendor-</option>";
				
			while ($field_customer_vendor=mysqli_fetch_array($exec_get_customer_vendor))
			      {    		 
		            if ($cru=='i')
			           {
			              echo "<option value='".$field_customer_vendor['cust_id']."'>".$field_customer_vendor['cust_name']." - [".$field_customer_vendor['cust_code']."]</option>";
			           }
			        else
			        if ($cru=='u')
					   {
					     if ($issuingh_cust_==$field_customer_vendor['cust_id'])
						     $selected="selected='selected'";
						 else
						     $selected="";
							 
					     echo "<option value='".$field_customer_vendor['cust_id']."' $selected>".$field_customer_vendor['cust_name']." - [".$field_customer_vendor['cust_id']."]</option>";
					   }
			      }
		  ?>
      </select></td>
    </tr>
    <tr>
      <td>DO No</td>
      <td>:</td>
      <td><input name="txt_issuingh_do_no" type="text" id="txt_issuingh_do_no" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_do_no;
				}	
		 ?>"/></td>
      <td>Nama Penerima </td>
      <td>:</td>
      <td><input name="txt_issuingh_receiver" type="text" id="txt_issuingh_receiver" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_receiver;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">Dikirim Menggunakan</td>
      <td valign="top">:</td>
      <td valign="top"><input name="txt_issuingh_sent_by" type="text" id="txt_issuingh_sent_by" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_sent_by;
				}	
		 ?>"/></td>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_issuingh_notes" id="txt_issuingh_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_notes;
				}
		 ?></textarea></td>
    </tr>
    <tr>
      <td>Nomor Kendaraan</td>
      <td>:</td>
      <td><input name="txt_issuingh_vehicle_no" type="text" id="txt_issuingh_vehicle_no" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_vehicle_no;
				}	
		 ?>"/></td>
      <td>Dibatalkan</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td>Dikirim Oleh * </td>
      <td>:</td>
      <td>
	  <select name="s_employee" id="s_employee" style="width:300px">
        <?php
		    $q_get_employee="SELECT emp_id, emp_code, emp_name
                             FROM employee
                             WHERE branch_id='$branch_id'";
		 	$exec_get_employee=mysqli_query($db_connection,$q_get_employee);
			echo "<option value='0'>-Pilih Pengirim-</option>";
			while ($field_employee=mysqli_fetch_array($exec_get_employee))
			      {    		 
		                 if ($cru=='i')
			                {
			                  echo "<option value='".$field_employee['emp_id']."'>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
			                }
			             else
			             if ($cru=='u' || isset($_POST['s_employee']))
					        {
					          if ($issuingh_sent_by==$field_employee['emp_id'])
						          $selected="selected='selected'";
						      else
						          $selected="";
							 
					          echo "<option value='".$field_employee['emp_id']."' $selected>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
					        }
			      }
		  ?> </select></td>
      <td>Alasan Pembatalan</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $issuingh_canceled_reason;
				}	
		 ?></td>
    </tr>
    
    <tr>
      <td><input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_issuing_detail);	  
				       }	  
			        else
			           {
				         echo "value='0'";
					   }	 
			   ?>>      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
    </tr>
    
    <tr>
      <td colspan="6">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="100%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">No</th>
          <th width="8%" scope="col">Kode</th>
          <th width="18%" scope="col">Nama Item</th>
          <th width="10%" scope="col">Kategori</th>
          <th width="6%" scope="col">Kapasitas</th>
          <th width="4%" scope="col">Status</th>
          <th width="8%" scope="col">Qty Total</th>
          <th width="8%" scope="col">Qty Kembali</th>
          <th width="8%" scope="col">Qty Belum Kembali</th>
          </tr>
        <?php  		   
			  if ($id!='')
			     {					
				   $no=0;
				   $qty_total_deposit=0;
				   $qty_total_return=0;
				   $qty_total_not_return=0;
				   while ($field_get_issuing_detail=mysqli_fetch_array($exec_get_issuing_detail)) 
				         {
						   $no++;
						   $issuingd_id=$field_get_issuing_detail['issuingd_id']; 
						   $masti_code=$field_get_issuing_detail['masti_code'];
						   $masti_name=$field_get_issuing_detail['masti_name'];
						   $cati_name=$field_get_issuing_detail['cati_name'];
						   $masti_capacity=$field_get_issuing_detail['itemd_capacity']." ".$field_get_issuing_detail['uom_name'];
						   $issuingd_status=$field_get_issuing_detail['issuingd_status'];
						   $total_deposit=$field_get_issuing_detail['total_deposit'];
					       $total_return=$field_get_issuing_detail['total_return'];
						   $total_not_return=$field_get_issuing_detail['total_not_return'];
						   $qty_total_deposit=$qty_total_deposit+$total_deposit;
						   $qty_total_return=$qty_total_return+$total_return;
						   $qty_total_not_return=$qty_total_not_return+$total_not_return;
			 ?>
		         <tr>
				     <td class="td_lb"><?php echo $no;?></td>
                     <td class="td_lb"><?php echo $masti_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $issuingd_status;?></td>
		             <td class="td_lb"><?php echo $total_deposit;?></td>
		             <td class="td_lb"><?php echo $total_return;?></td>
		             <td class="td_lb"><?php echo $total_not_return;?></td>
				 </tr>	     	
		    <?php		   
						 } ?>	
				 <tr bgcolor="#999999">
				     <td colspan="6" align="right"><font color="#0000FF"><b>Total</b></font></td>
					 <td><font color="#0000FF"><b><?php echo $qty_total_deposit;?></b></font></td>
					 <td><font color="#0000FF"><b><?php echo $qty_total_return;?></b></font></td>
					 <td><font color="#0000FF"><b><?php echo $qty_total_not_return;?></b></font></td>
				 </tr>            
						   <?php	  
			     }	
			?>	
      </table>
	  </div></td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right"><input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/></td>
    </tr>
  </table>
</form>

<script language="javascript">
  function input_data()
           {
		     var branch_id='<?php echo $branch_id;?>';
			 var issuing_code=document.getElementById('txt_code').value.trim(); 
			 var date_issuing=document.getElementById('txt_issuingh_date').value.trim();
			 var d=date_issuing.substring(0,2);
			 var m=date_issuing.substring(3,5);
			 var y=date_issuing.substring(6,10);
			 var issuing_date=Date.parse(y+'-'+m+'-'+d);
			 var sender=document.getElementById('s_employee').value;
			 var vend_id=document.getElementById('s_customer_vendor').value; 
			 var total_detail=document.getElementById('txt_rows').value;
			 
	/*		 if (total_detail>0)
			    {
				  chk_data=document.getElementsByName('cb_data[]');
				  for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					   chk_data[i].checked=true;	
				}
			    */
			 	
			 if (issuing_code=='')
			    {
				  alert('No Transaksi harus diisi!');
				  return (false)
				}
		     else
			 if (date_issuing=='')		
			    {
				  alert('Tanggal Pengeluaran harus diisi!');
				  return (false)
				}
			 else
			 if (sender=='0')
			    {
				  alert('Nama Pengirim harus diisi!');
				  return (false)
				} 
			 if (vend_id=='0')
			    {
			      alert('Nama Customer/Vendor harus diisi!');
				  return (false)
			    }
			 else
			 if (total_detail<=0) 
			    {
				  alert('Detail Data harus diisi!');
				  return (false);
				}
			 else	
			    { 
				  if (total_detail>0)
			         { 
					   var x='<?php echo $cru;?>';
				       chk_data=document.getElementsByName('cb_data[]');
				       for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					       {
					         chk_data[i].checked=true;	
							 y='cb_issuingd_is_canceled_'+chk_data[i].value;
							 if (x=='u')
							     document.getElementById(y).disabled=false;
						   }	 
				     }
					 
			      f_cru_issuing.action='../../data/issuing/input_issuing.php?b='+branch_id;
				  f_cru_issuing.submit();
				} 
		   }
		   
  function call_add_data()
           {
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/issuing/pick_tube.php", "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
		   }
		   
  function call_delete_data()
           {
		     var objTable = document.getElementById('tbl_detail');
			 var objTR, objTD, intIndex;
			 var chks = document.getElementsByName('cb_data[]');
			 if (typeof(chks) == 'object') 
			    {
				  for (i = chks.length - 1; i >= 0; i--) 
				      {
					    if (chks[i].checked) 
					       {
						     x=chks[i].value;
						     objTable.deleteRow(i + 1);
						     document.getElementById("txt_rows").value = parseInt(document.getElementById("txt_rows").value) - 1;
					       }
				      }
			    } 
		   }		  
			
  function select_unselect_all()
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('cb_data[]');
			 
             for (i = 0; i<select_unselect_all_data.length; i++)
			     {
				   if (check_select[0].checked==true)
				      {
                        select_unselect_all_data[i].checked = true ;
					  }
				   else
				      {
					    select_unselect_all_data[i].checked = false ;
					  }	  	
				 }   
		   }
		   
  function call_empty_strg(x)
           {
	 	     var id='strg_detail_'+x;
	 		 document.getElementById(id).value='';
		   }		    
		   
  function call_validation_number(number)
           {
			  if (!/^[0-9.]+$/.test(number.value))
	             {
	               number.value = number.value.substring(0,number.value.length-1000);
	             }
		   }		
  function call_calculate_lead_time()
           {
		     var date_po=document.getElementById('txt_poh_date').value.trim();
			 var d_1=date_po.substring(0,2);
			 var m_1=date_po.substring(3,5);
			 var y_1=date_po.substring(6,10);
			 var poh_date=Date.parse(y_1+'-'+m_1+'-'+d_1);
			 var date_eta=document.getElementById('txt_poh_eta').value.trim();
			 var d_2=date_eta.substring(0,2);
			 var m_2=date_eta.substring(3,5);
			 var y_2=date_eta.substring(6,10);
			 var eta_date=Date.parse(y_2+'-'+m_2+'-'+d_2);
          //   selisih=Math.round(((eta_date-poh_date)/86400000)/7);
		     selisih=((eta_date-poh_date)/86400000)/7; 
			    document.getElementById('txt_poh_lead_time_delivery').value=rounding(selisih);
		   }
	
  function call_canceled_item(x,y)
           {
		     var z=document.getElementById('cb_issuingd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_issuing.action='../../data/issuing/canceled_item.php?id='+y;
				       f_cru_issuing.submit();
					 }
				  else
				     z.checked==false;
				}  
		   }	
  	
  function call_change_status(item_id)
           {
		     var x='txt_issuingd_status_'+item_id;
			 var y='s_issuingd_status_'+item_id;
			 var status_id=document.getElementById(y).value;
			 document.getElementById(x).value=status_id;
		   }	
   	
  function call_show_summary(id)
           {
		     var w=1300;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open('../../data/issuing/view_summary.php?c=u&id='+id, 'f_view_summary', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
		   }	
		   
  function rounding(num) 
           {    
              return +(Math.round(num + "e+2")  + "e-2");
           }
</script>






