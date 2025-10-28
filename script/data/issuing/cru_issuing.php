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
  $issuingh_type=0;
  $issuingh_is_canceled='Tidak';
  $issuingh_sent_by=0;
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_issuing_header="SELECT issuingh_id, issuingh_code, issuingh_date, issuingh_do_no, issuingh_sent_by, issuingh_vehicle_no, emp_id, issuingh_type, cust_id, 
	                                 issuingh_receiver_name, issuingh_notes, issuingh_ba_no, issuingh_po_no,
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
		    $q_get_issuing_detail="SELECT issuingd_id, issuing_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty,                                          issuingd_notes, issuingd_status, issuingd_is_canceled
                                   FROM issuing_detail
                                   INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                   INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
								   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                   WHERE item_detail.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND issuing_detail.issuingh_id='$id'";				
			$exec_get_issuing_detail=mysqli_query($db_connection,$q_get_issuing_detail);
			$issuingh_id=$field_issuing_header['issuingh_id'];
		    $trans_code=$field_issuing_header['issuingh_code'];
			$trans_code_1=$field_issuing_header['issuingh_code'];
			$issuingh_date=get_date_1($field_issuing_header['issuingh_date']);
			$issuingh_ba_no=$field_issuing_header['issuingh_ba_no'];
			$issuingh_po_no=$field_issuing_header['issuingh_po_no'];
			$issuingh_do_no=$field_issuing_header['issuingh_do_no'];
			$issuingh_sent_use=$field_issuing_header['issuingh_sent_by'];
			$issuingh_vehicle_no=$field_issuing_header['issuingh_vehicle_no'];
			$issuingh_sent_by=$field_issuing_header['issuingh_sent_by'];
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
	   $id="";
       $trans_code=get_format_number($db_connection, 'CYO',$branch_id);
	 }
  //echo $q_get_issuing_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_issuing" id="f_cru_issuing" onSubmit="">
  <table width="1280" border="0" cellspacing="1" cellpadding="1" id="tbl_issuing">
    <tr>
      <th colspan="6" scope="col" align="right">Kantor Cabang    :
        <select id="s_branch_trans" name="s_branch_trans" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></th>
    </tr>
    <tr>
      <th colspan="6" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH TRANSAKSI PENGELUARAN BARU";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI PENGELUARAN";
															 }
													?>	  </th>
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
			 if ($cru=='u' || $cru=='d')
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
			 if ($cru=='u' || $cru=='d')
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
			 if ($cru=='u' || $cru=='d')
			    {
				  echo "value='$trans_code_1'";
				}		
		 ?>/></td>
      <td>Dikirim Oleh * </td>
      <td>:</td>
      <td><select name="s_employee" id="s_employee" style="width:300px">
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
			             if ($cru=='u' || $cru=='d' || isset($_POST['s_employee']))
					        {
					          if ($issuingh_sent_by==$field_employee['emp_id'])
						          $selected="selected='selected'";
						      else
						          $selected="";
							 
					          echo "<option value='".$field_employee['emp_id']."' $selected>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
					        }
			      }
		  ?>
      </select>    </tr>
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
      <td width="18%" valign="top">Tipe Pengeluaran</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><?php
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
		 ?></td>
    </tr>
    <tr>
      <td>Nomor Berita Acara </td>
      <td>:</td>
      <td><input type="text" id="txt_ba_no" name="txt_ba_no" size="30" 
	       value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_ba_no;
				}	
		 ?>"></td>
      <td>Nama Customer/Vendor * </td>
      <td>:</td>
      <td><select name="s_customer_vendor" id="s_customer_vendor" style="width:300px">
        <?php
		    if (isset($_POST['rb_type']))
			    $issuingh_type=$_POST['rb_type'];
				
			if ($issuingh_type=='0' || $issuingh_type=='')	
		        $q_get_customer_vendor="SELECT cust_name as cust_name, cust_id as cust_id, cust_code as cust_code
                                        FROM customer
										WHERE cust_type='0' AND branch_id='$branch_id' ORDER BY cust_name ASC";
			else
			    $q_get_customer_vendor="SELECT cust_name as cust_name, cust_id as cust_id, cust_code as cust_code
                                        FROM customer
										WHERE cust_type='1' AND branch_id='$branch_id' ORDER BY cust_name ASC";
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
			        if ($cru=='u' || $cru=='d')
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
      <td>Purchase No </td>
      <td>:</td>
      <td><input type="text" id="txt_po_no" name="txt_po_no" size="30"
	       		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_po_no;
				}	
		 ?>"></td>
      <td>Nama Penerima </td>
      <td>:</td>
      <td><input name="txt_issuingh_receiver" type="text" id="txt_issuingh_receiver" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_receiver;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td valign="top">DO No *</td>
      <td valign="top">:</td>
      <td valign="top"><input name="txt_issuingh_do_no" type="text" id="txt_issuingh_do_no" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_do_no;
				}	
		 ?>"/></td>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td valign="top"><textarea name="txt_issuingh_notes" id="txt_issuingh_notes" cols="35"><?php if ($cru=='i') echo ""; else if ($cru=='u' || $cru=='d') echo $issuingh_notes; ?></textarea></td>
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
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_sent_by;
				}	
		 ?>"/></td>
      <td valign="top">Dibatalkan</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_is_canceled;
				}	
		 ?></td>
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
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_vehicle_no;
				}	
		 ?>"/></td>
      <td>Alasan Pembatalan</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $issuingh_canceled_reason;
				}	
		 ?></td>
    </tr>
    
    
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"></td>
    </tr>
    <tr>
      <td><input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()">
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_issuing_detail);	  
				       }	  
			        else
			           {
				         echo "value='0'";
					   }	 
			   ?>>      </td>
      <td>&nbsp;</td>
      <td><input type="button" id="btn_view" name="btn_view" value="Tampilkan Summary UJM" onClick="call_show_summary('<?php echo $id;?>')"
	       <?php
		      if ($cru=='i')
			      echo "disabled='disabled'"; 
		   ?>></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
    </tr>
    
    <tr>
      <td colspan="6">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="120%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="13%" scope="col">Kode Aset </th>
          <th width="29%" scope="col">Deskripsi Isi Aset </th>
          <th width="12%" scope="col">Serial No</th>
          <th width="7%" scope="col">Kapasitas</th>
          <th width="12%" scope="col">Kategori</th>
          <th width="6%" scope="col">Qty</th>
          <th width="6%" scope="col">Status</th>
          <th width="6%" scope="col">Dibatalkan</th>
          <th width="19%" scope="col">Notes</th>
        </tr>
        <?php  		   
			  if ($id!='')
			     {					
				   $no=0;
				   while ($field_get_issuing_detail=mysqli_fetch_array($exec_get_issuing_detail)) 
				         {
						   $no++;
						   $issuingd_id=$field_get_issuing_detail['issuingd_id']; 
						   $itemd_id= $field_get_issuing_detail['itemd_id']; 
						   $itemd_code=$field_get_issuing_detail['itemd_code'];
						   $masti_name=$field_get_issuing_detail['masti_name'];
						   $itemd_serial_no=$field_get_issuing_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_issuing_detail['itemd_capacity']." ".$field_get_issuing_detail['uom_name'];
						   $cati_name=$field_get_issuing_detail['cati_name'];
						   $itemd_qty=$field_get_issuing_detail['itemd_qty']." Cylinder";
						   $issuingd_status=$field_get_issuing_detail['issuingd_status'];
						   $issuingd_is_canceled=$field_get_issuing_detail['issuingd_is_canceled'];
					       $issuingd_notes=$field_get_issuing_detail['issuingd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $itemd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><?php if ($issuingd_status=='0')
					                            {
												  $check_normal="selected='selected'";
												  $check_rental="";
												  $check_ujm="";
												}
										     else
											 if ($issuingd_status=='1')
					                            {
												  $check_normal="";
												  $check_rental="selected='selected'";
												  $check_ujm="";
												}
										     else
											 if ($issuingd_status=='2')
					                            {
												  $check_normal="";
												  $check_rental="";
												  $check_ujm="selected='selected'";
												}
											 if ($issuingd_is_canceled=='1') 
											     $disabled="disabled='disabled'";	
											 else
											     $disabled="";
					                   ?>
					                   <select id="s_issuingd_status_<?php echo $itemd_id;?>" name"s_issuingd_status_<?php echo $itemd_id;?>" <?php echo $disabled;?> 
									     onChange="call_change_status(<?php echo $itemd_id;?>)">
					                      <option value="0" <?php echo $check_normal;?>>Normal</option>
										  <option value="1" <?php echo $check_rental;?>>Rental</option>
										  <option value="2" <?php echo $check_ujm;?>>UJM</option>
									   </select><input type="hidden" id="txt_issuingd_status_<?php echo $itemd_id;?>" name="txt_issuingd_status_<?php echo $itemd_id;?>" 
									             value="<?php echo $issuingd_status;?>"></td>
		             <td class="td_lb"><input type="checkbox" id="cb_issuingd_is_canceled_<?php echo $itemd_id;?>" name="cb_issuingd_is_canceled_<?php echo $itemd_id;?>" 
					                    value="1" <?php if ($issuingd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $itemd_id;?>','<?php echo $issuingd_id;?>')"><?php if ($issuingd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><input type="text" id="txt_issuingd_notes_<?php echo $itemd_id;?>" name="txt_issuingd_notes_<?php echo $itemd_id;?>" 
					                    value="<?php echo $issuingd_notes;?>"  <?php if ($issuingd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
				 </tr>	     	
		    <?php		   
						 }	   
			     }	
			?>	
      </table>
	  </div></td>
    </tr>
    
    <tr>
      <td>Dibuat Oleh </td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $created_by;
				}		
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($issuingh_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
        <input name="btn_new" type="reset" id="btn_new" value="Baru" <?php if ($cru=='u' || $cru=='d') echo "disabled='disabled'"; ?>/>
        <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/></td>
    </tr>
    <tr>
      <td>Diupdate Oleh</td>
      <td>:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $updated_by;
				}		
		 ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right">&nbsp;</td>
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
			 var do_no=document.getElementById('txt_issuingh_do_no').value.trim(); 
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
			 if (do_no=='')
			    {
				  alert('DO No harus diisi!');
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
				       for (i=0; i<document.getElementsByName('cb_data[]').length; i++)
					       {
					         chk_data[i].checked=true;	
							 y='cb_issuingd_is_canceled_'+chk_data[i].value;
							 z='s_issuingd_status_'+chk_data[i].value;
							 if (x=='u')
							    {
							      document.getElementById(y).disabled=false;
								  document.getElementById(z).disabled=false;
								}
						   }	 
				     }
			      f_cru_issuing.action='../../data/issuing/input_issuing.php?b='+branch_id;
				  f_cru_issuing.submit(); 
				} 
		   }
		   
  function call_add_data()
           {
		     var branch_id='<?php echo $branch_id;?>';
		     var w=1500;
			 var h=600;
			 var l=(screen.width/2)-(w/2);
			 var t=(screen.height/2)-(h/2);
			 open_child=window.open("../../data/issuing/pick_tube.php?b="+branch_id, "f_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0, scrollbars=Yes");
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
		     var id_branch=document.getElementById('s_branch_trans').value;
		     var z=document.getElementById('cb_issuingd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_issuing.action='../../data/issuing/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_issuing.submit();
					 }
				  else
				     z.checked=false;
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






