<?php
 include "../../library/check_session.php";
 include "../../library/library_function.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 
 compare_branch($branch_id, $branch_id_transaction);
?>
 <link type="text/css" rel="stylesheet" href="../../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../../library/development-bundle/ui/ui.core.js"></script>
    <script src="../../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_rth_date").datepicker(
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
  include "../../library/style.css";
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  $tth_id=0;
  $rth_type=0;
  $rth_branch_id=0;
  $rth_receiver=0;
  $rth_is_canceled='Tidak';
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_receipt_transfer_header="SELECT rth_id, rth_code, receipt_transfer_header.tth_id, tth_code, rth_date, branch_id_from, receipt_transfer_header.emp_id_receiver,  
	                                          rth_notes, rth_ba_no, rth_po_no,
                                              CASE rth_is_canceled
                                                   WHEN '0' THEN 'Tidak'
	                                               WHEN '1' THEN 'Ya'
                                              END rth_is_canceled, rth_canceled_date, rth_canceled_reason, receipt_transfer_header.created_by, 
                                              receipt_transfer_header.created_time, receipt_transfer_header.updated_by, receipt_transfer_header.updated_time
                                       FROM receipt_transfer_header
                                       INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id
                                       WHERE receipt_transfer_header.branch_id='$branch_id' AND rth_id='$id'";
	   $exec_get_receipt_transfer_header=mysqli_query($db_connection, $q_get_receipt_transfer_header);
	   $total_get_receipt_transfer_header=mysqli_num_rows($exec_get_receipt_transfer_header);
	   $field_receipt_header=mysqli_fetch_array($exec_get_receipt_transfer_header);
	   if ($total_get_receipt_transfer_header==0)
	      {
		    ?>
			   <script language="javascript">
		           alert('Transaksi Penerimaan Transfer Aset yang akan diupdate tidak ditemukan!');
				   window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_receipt_transfer_detail="SELECT rtd_id, transfer_detail.ttd_id, transfer_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
			                                	   cati_name, rtd_qty, receipt_transfer_detail.whsl_id_new, rtd_is_canceled, rtd_notes  
                                            FROM receipt_transfer_detail 
                                            INNER JOIN receipt_transfer_header ON receipt_transfer_header.rth_id=receipt_transfer_detail.rth_id 
                                            INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id 
                                            INNER JOIN transfer_detail ON transfer_detail.tth_id=transfer_header.tth_id 
                                            INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id 
											INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                            INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                            WHERE receipt_transfer_detail.ttd_id=transfer_detail.ttd_id AND receipt_transfer_header.branch_id='$branch_id' AND 
											      receipt_transfer_detail.rth_id='$id'";			
			//echo $q_get_receipt_transfer_detail;
			$exec_get_receipt_transfer_detail=mysqli_query($db_connection, $q_get_receipt_transfer_detail);
			$rth_branch_id=$field_receipt_header['branch_id_from'];
			$rth_id=$field_receipt_header['rth_id'];
			$tth_id=$field_receipt_header['tth_id'];
		    $trans_code=$field_receipt_header['rth_code'];
			$trans_code_1=$field_receipt_header['rth_code'];
			$rth_date=get_date_1($field_receipt_header['rth_date']);
			$rth_receiver=$field_receipt_header['emp_id_receiver'];
			$rth_notes=$field_receipt_header['rth_notes'];
			$rth_ba_no=$field_receipt_header['rth_ba_no'];
			$rth_po_no=$field_receipt_header['rth_po_no'];
			$rth_is_canceled=$field_receipt_header['rth_is_canceled'];
			$rth_canceled_date=$field_receipt_header['rth_canceled_date'];
			$rth_canceled_reason=$field_receipt_header['rth_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_receipt_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_receipt_header['created_time'];
			if ($field_receipt_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_receipt_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_receipt_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
	   $id="";
       $trans_code=get_format_number($db_connection, 'RTT',$branch_id);
	 }
  //echo $q_get_receipt_transfer_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_receipt_transfer" id="f_cru_receipt_transfer" onSubmit="">
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
															   echo "TAMBAH TRANSAKSI PENERIMAAN TRANSFER ASET";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI PENERIMAAN TRANSFER ASET";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td valign="top">Nama Kantor Cabang Asal *</td>
      <td valign="top">:</td>
      <td valign="top"><select name="s_branch" id="s_branch" onChange="submit()" style="width:300px">
        <?php
		    if (isset($_POST['s_branch']))
			    $rth_branch_id=$_POST['s_branch'];
				
			if ($cru=='i')	
		        $q_get_branch="SELECT branch_name, branch_id, branch_code
                               FROM branch
							   WHERE branch_id!='$branch_id'";
			else
			if ($cru=='u' || $cru=='d')	
		        $q_get_branch="SELECT branch_name, branch_id, branch_code
                               FROM branch 
							   WHERE branch_id!='$branch_id' AND branch_id='$rth_branch_id'";
			
		 	$exec_get_branch=mysqli_query($db_connection, $q_get_branch);
			echo "<option value='0'>-Pilih Kantor Cabang Asal-</option>";	
			while ($field_branch=mysqli_fetch_array($exec_get_branch))
			      {    	
				    if ($rth_branch_id==$field_branch['branch_id'])
						$selected="selected='selected'";
			        else
						$selected="";
				  	 
		            if ($cru=='i')
			            echo "<option value='".$field_branch['branch_id']."' $selected>".$field_branch['branch_name']." - [".$field_branch['branch_code']."]</option>";
			        else
			        if ($cru=='u' || $cru=='d')
					    echo "<option value='".$field_branch['branch_id']."' $selected>".$field_branch['branch_name']." - [".$field_branch['branch_id']."]</option>";
			      }
		  ?>
      </select></td>
      <td valign="top">Nomor Berita Acara </td>
      <td valign="top">:</td>
      <td><input type="text" id="txt_ba_no" name="txt_ba_no" size="30"
	             <?php 
			           if ($id!='')
				           echo "value=".$rth_ba_no; 
			           else 
		                   echo "value=''";
		         ?>></td>
    </tr>
    <tr>
      <td>No Transaksi  *</td>
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
				  echo $rth_id;
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
      <td>Purchase No </td>
      <td>:</td>
      <td><input type="text" id="txt_po_no" name="txt_po_no" size="30"
	             <?php 
			           if ($id!='')
				           echo "value=".$rth_po_no; 
			           else 
		                   echo "value=''";
		         ?>></td>
	</tr>
    <tr>
      <td width="16%" valign="top">No Referensi *</td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><select id="s_tth_id" name="s_tth_id" onChange="submit()" style="width:300px">
									 <?php
									   if (isset($_POST['s_tth_id']))
									       $tth_id=$_POST['s_tth_id'];
										   
									   echo "<option value='0'>-Pilih Nomor Transfer-</option>";   
									   if ($cru=='i')
									       $q_check_transfer="SELECT * FROM transfer_header 
														      WHERE tth_status IN (0,1) AND branch_id='$rth_branch_id' AND tth_is_canceled='0' AND
															        branch_id_to='$branch_id'";
									   if ($cru=='u' || $cru=='d')
										   $q_check_transfer="SELECT * FROM transfer_header 
														      WHERE tth_id='$tth_id' AND branch_id='$rth_branch_id' AND branch_id_to='$branch_id'"; 
									   $exec_get_transfer=mysqli_query($db_connection, $q_check_transfer);
									   while ($field_get_transfer=mysqli_fetch_array($exec_get_transfer))
										     {
											   $selected='';
											   if ($tth_id!='0')
												  {
												    if ($tth_id==$field_get_transfer['tth_id'])
													    $selected="selected='selected'";
												  }
												
											   echo "<option value='".$field_get_transfer['tth_id']."' $selected>".$field_get_transfer['tth_code']." - [".get_date_1($field_get_transfer['tth_date'])."]</option>";
										     } //THONI 28072020
									 ?>
                                   </select></td>
      <td width="18%" valign="top">Keterangan</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><textarea name="txt_rth_notes" id="txt_rth_notes" cols="35"><?php 
	         if (isset($_POST['txt_rth_notes']))
			     echo $_POST['txt_rth_notes'];
			 else
			    {
		          if ($cru=='i')
			         {
				       echo "";
				     }
		          else
			      if ($cru=='u' || $cru=='d')
			         {
				       echo $rth_notes;
					 }  
				}
		 ?></textarea></td>
    </tr>
    <tr>
      <td valign="top">Tanggal *<br></td>
      <td valign="top">:</td>
      <td valign="top"><input name="txt_rth_date" type="text" id="txt_rth_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$rth_date; 
			else 
		        echo "value='$current_date'";
		 ?>>
      <br></td><td valign="top">Dibatalkan</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "Tidak";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $rth_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td valign="top">Diterima Oleh * </td>
      <td valign="top">:</td>
      <td valign="top"><select name="s_employee" id="s_employee" style="width:300px">
        <?php
		    if (isset($_POST['s_employee']))
			    $rth_receiver=$_POST['s_employee'];
				
		    $q_get_employee="SELECT emp_id, emp_code, emp_name
                             FROM employee
                             WHERE branch_id='$branch_id'";
		 	$exec_get_employee=mysqli_query($db_connection, $q_get_employee);
			echo "<option value='0'>-Pilih Penerima-</option>";
			while ($field_employee=mysqli_fetch_array($exec_get_employee))
			      {    		 
				    if ($rth_receiver!='')
					   {
					     if ($rth_receiver==$field_employee['emp_id'])
						     $selected="selected='selected'"; 
						 else
						     $selected='';
					   }
					else
					    $selected='';  
			        echo "<option value='".$field_employee['emp_id']."' $selected>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
			      }
		  ?>
      </select></td>
      <td valign="top">Alasan Pembatalan</td>
      <td valign="top">:</td>
      <td><?php 
		     if ($cru=='i')
			    {
				  echo "-";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $rth_canceled_reason;
				}	
		 ?></td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td><!---<input type="button" id="btn_add" name="btn_add" value="Tambah" onClick="call_add_data()"> --->
	      <input type="button" id="btn_delete" name="btn_delete" value="Hapus" onClick="call_delete_data()">
	      <input type="hidden" name="txt_rows" id="txt_rows" <?php if ($id!='')
			           {
					       echo "value=".mysqli_num_rows($exec_get_receipt_transfer_detail);	  
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
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td colspan="6">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;">
	  <table width="120%" border="0" cellspacing="1" cellpadding="1" id="tbl_detail" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col">
            <input type="checkbox" id="check_all_data" name="check_all_data" value="1" onClick="select_unselect_all()"/></th>
          <th width="10%" scope="col">Kode Aset </th>
          <th width="25%" scope="col">Deskripsi Isi Aset </th>
          <th width="9%" scope="col">Serial No</th>
          <th width="6%" scope="col">Kapasitas</th>
          <th width="9%" scope="col">Kategori</th>
          <th width="3%" scope="col">Qty</th>
          <th width="6%" scope="col">Dibatalkan</th>
          <th width="13%" scope="col">Lokasi Gudang</th>
          <th width="17%" scope="col">Notes</th>
        </tr>
        <?php  
		      if ($id=='')
			     {
				   $q_get_transfer_detail="SELECT ttd_id, transfer_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, 
                                           uom_name, ttd_notes, ttd_is_canceled 
                                           FROM transfer_detail 
                                           INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id 
                                           INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id 
										   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                           INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                           INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                           WHERE transfer_header.branch_id='$rth_branch_id' AND branch_id_to='$branch_id' AND transfer_detail.tth_id='$tth_id' AND 
											     ttd_is_canceled='0' AND ttd_status='0'";
				   //echo $q_get_transfer_detail;
				   $exec_get_transfer_detail=mysqli_query($db_connection, $q_get_transfer_detail);
				   $no=0;
				   while ($field_get_transfer_detail=mysqli_fetch_array($exec_get_transfer_detail)) 
				         {
						   $no++;
						   $ttd_id=$field_get_transfer_detail['ttd_id']; 
						   $itemd_id= $field_get_transfer_detail['itemd_id']; 
						   $itemd_code=$field_get_transfer_detail['itemd_code'];
						   $masti_name=$field_get_transfer_detail['masti_name'];
						   $itemd_serial_no=$field_get_transfer_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_transfer_detail['itemd_capacity']." ".$field_get_transfer_detail['uom_name'];
						   $cati_name=$field_get_transfer_detail['cati_name'];
						   $itemd_qty=$field_get_transfer_detail['itemd_qty']." Cylinder";
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $ttd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_rtd_is_canceled_<?php echo $ttd_id;?>" name="cb_rtd_is_canceled_<?php echo $ttd_id;?>" 
					                    value="1"  disabled='disabled'>Tidak</td>
					 <td class="td_lb"><select id="s_whsld_<?php echo $ttd_id;?>" name="s_whsld_<?php echo $ttd_id;?>" style="width:300px">
                       <?php 
			 $level="";
		     $level_2="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_3="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_4="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_5="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
		     $q_get_whs="SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, CAST(whsl_id AS CHAR) AS whsl_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location
                         WHERE whsl_level='1' AND branch_id='$branch_id'
                         UNION 
                         SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, whsl_parent_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location 
                         WHERE whsl_level!='1' AND branch_id='$branch_id'
                         ORDER BY whsl_path, whsl_name";
			 $exec_get_whs=mysqli_query($db_connection, $q_get_whs);
			 echo "<option value='0'>-Pilih Lokasi Gudang-</option>";
		     while ($field=mysqli_fetch_array($exec_get_whs))
			       {
				     if ($field['whsl_level']=='2')
					    {
					      $level=$level_2;
						}   
					 else
					 if ($field['whsl_level']=='3')
					    {
					      $level=$level_3;
						} 
					 else
					 if ($field['whsl_level']=='4')
					    {
					      $level=$level_4;
						} 
					 else
					 if ($field['whsl_level']=='5')
					    {
					      $level=$level_5;
					 	} 
					 
					 if ($field['whsl_type']=='0')
					     $disabled="disabled='disabled'";
					 else
					     $disabled="";	 
					 
					 if (isset($_POST['s_whs']))
					    {
						  if ($whsl_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  	  
						}
					 else
					 if ($cru=='i')
					     echo "<option value='".$field['whsl_id']."' $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";
					 else
					 if ($cru=='u' || $cru=='d')	
					    {
						  if ($whsl_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  
						}	 
					 $level=''; 
					 $disabled="";
				   }		
		  ?>
                     </select></td>
					 <td class="td_lb"><input type="text" id="txt_rtd_notes_<?php echo $ttd_id;?>" name="txt_rtd_notes_<?php echo $ttd_id;?>" 
					                    value="" size="35"></td>
				 </tr><script language="javascript">
				        var x='<?php echo $no; ?>';
						document.getElementById('txt_rows').value=x;
				      </script>     	
		    <?php		   
						 }	
				 }
			  else		  
			     {					
				   $no=0;
				   while ($field_get_receipt_transfer_detail=mysqli_fetch_array($exec_get_receipt_transfer_detail)) 
				         {
						   $no++;
						   $ttd_id=$field_get_receipt_transfer_detail['ttd_id']; 
						   $rtd_id=$field_get_receipt_transfer_detail['rtd_id']; 
						   $itemd_id= $field_get_receipt_transfer_detail['itemd_id']; 
						   $itemd_code=$field_get_receipt_transfer_detail['itemd_code'];
						   $masti_name=$field_get_receipt_transfer_detail['masti_name'];
						   $itemd_serial_no=$field_get_receipt_transfer_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_receipt_transfer_detail['itemd_capacity']." ".$field_get_receipt_transfer_detail['uom_name'];
						   $cati_name=$field_get_receipt_transfer_detail['cati_name'];
						   $itemd_qty=$field_get_receipt_transfer_detail['rtd_qty']." Cylinder";
						   $whsld_id=$field_get_receipt_transfer_detail['whsl_id_new'];
						   $rtd_is_canceled=$field_get_receipt_transfer_detail['rtd_is_canceled'];
					       $rtd_notes=$field_get_receipt_transfer_detail['rtd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $ttd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_rtd_is_canceled_<?php echo $ttd_id;?>" name="cb_rtd_is_canceled_<?php echo $ttd_id;?>" 
					                    value="1" <?php if ($rtd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $ttd_id;?>','<?php echo $rtd_id;?>')"><?php if ($rtd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><select id="s_whsld_<?php echo $ttd_id;?>" name="s_whsld_<?php echo $ttd_id;?>" style="width:300px" 
					                                       <?php if ($rtd_is_canceled=='1') 
														     echo "disabled='disabled'";
														   ?>>
                       <?php 
		     $level="";
			 $level_2="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_3="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_4="&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
			 $level_5="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;";
		     $q_get_whs="SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, CAST(whsl_id AS CHAR) AS whsl_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location
                         WHERE whsl_level='1'  AND branch_id='$branch_id'
                         UNION 
                         SELECT CAST(whsl_id AS CHAR) AS whsl_id, whsl_name, whsl_parent_path, whsl_code, whsl_level, whsl_type 
                         FROM warehouse_location 
                         WHERE whsl_level!='1' AND branch_id='$branch_id'
                         ORDER BY whsl_path, whsl_name";
			 $exec_get_whs=mysqli_query($db_connection, $q_get_whs);
			 echo "<option value='0'>-Pilih Lokasi Gudang-</option>";
		     while ($field=mysqli_fetch_array($exec_get_whs))
			       {
				     if ($field['whsl_level']=='2')
					    {
					      $level=$level_2;
						}   
					 else
					 if ($field['whsl_level']=='3')
					    {
					      $level=$level_3;
						} 
					 else
					 if ($field['whsl_level']=='4')
					    {
					      $level=$level_4;
						} 
					 else
					 if ($field['whsl_level']=='5')
					    {
					      $level=$level_5;
					 	} 
					 
					 if ($field['whsl_type']=='0')
					     $disabled="disabled='disabled'";
					 else
					     $disabled="";	 
					 $whsldid='s_whsld_'.$ttd_id;
					 if (isset($_POST[$whsldid]))
					    {
						  if ($whsld_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  	  
						}
					 else
					 if ($cru=='i')
					     echo "<option value='".$field['whsl_id']."' $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";
					 else
					 if ($cru=='u' || $cru=='d')	
					    {
						  if ($whsld_id==$field['whsl_id'])
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  echo "<option value='".$field['whsl_id']."' $selected_field $disabled>".$level.$field['whsl_name']." - [".$field['whsl_code']."]</option>";	  
						}	 
					 $level=''; 
					 $disabled="";
				   }		
		  ?>
                     </select></td>
					 <td class="td_lb"><input type="text" id="txt_rtd_notes_<?php echo $ttd_id;?>" name="txt_rtd_notes_<?php echo $ttd_id;?>" 
					                    value="<?php echo $rtd_notes;?>"  <?php if ($rtd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
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
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($rth_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
        <input name="btn_new" type="reset" id="btn_new" value="Baru" <?php if ($cru=='u' || $cru=='d') echo "disabled='disabled'"; ?>/>
        <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/></td>
    </tr>
    <tr>
      <td>Diupdate Oleh </td>
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
			 var branch_id_from=document.getElementById('s_branch').value.trim(); 
			 var rth_code=document.getElementById('txt_code').value.trim(); 
			 var tth_id=document.getElementById('s_tth_id').value.trim(); 
			 var date_transfer=document.getElementById('txt_rth_date').value.trim();
			 var receiver=document.getElementById('s_employee').value; 
			 var d=date_transfer.substring(0,2);
			 var m=date_transfer.substring(3,5);
			 var y=date_transfer.substring(6,10);
			 var transfer_date=Date.parse(y+'-'+m+'-'+d);
			 var total_detail=document.getElementById('txt_rows').value;
             
			 if (branch_id=='')
			    {
				  alert('Kantor Cabang Asal harus diisi!');
				}
		     else
			 if (rth_code=='')
			    {
				  alert('No Transaksi harus diisi!');
				}
		     else
			 if (tth_id=='0')
			    {
				  alert('No Referensi harus diisi!');
				}
		     else
			 if (date_transfer=='')		
			    {
				  alert('Tanggal harus diisi!');
				}
			 else
			 if (receiver=='0')
			    {
			      alert('Nama Penerima harus diisi!');
			    }
			 else
			 if (total_detail<=0) 
			    {
				  alert('Detail Data harus diisi!');
				}
			 else	
			    { 
				  no_whs=0;
				  if (total_detail>0)
			         { 
					   var x='<?php echo $cru;?>';
				       chk_data=document.getElementsByName('cb_data[]');
				       for (i=0; i<document.getElementsByName('cb_data[]').length;i++)
					       {
					         chk_data[i].checked=true;	
							 y='cb_rtd_is_canceled_'+chk_data[i].value;
							 z='s_whsld_'+chk_data[i].value;
							 if (x=='u')
							    {
							      document.getElementById(y).disabled=false;
								  document.getElementById(z).disabled=false;
								}
							 whs=document.getElementById(z).value;
							 if (whs=='0')
							     no_whs=1;
						   }	 
				     }
				  if (no_whs==1)
				     {
					   alert('Lokasi Gudang pada detail data harus diisi!');
					 } 
				  else
				     { 
			           f_cru_receipt_transfer.action='../../data/receipt_transfer/input_receipt_transfer.php?b='+branch_id;
				       f_cru_receipt_transfer.submit();
					 }  
				} 
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
	
  function call_canceled_item(x,y)
           {
		     var id_branch=document.getElementById('s_branch_trans').value;
		     var z=document.getElementById('cb_rtd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_receipt_transfer.action='../../data/receipt_transfer/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_receipt_transfer.submit();
					 }
				  else
				     z.checked=false;
				}  
		   }	
</script>






