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
                            $("#txt_reth_date").datepicker(
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
  $reth_cust_vend="";
  $reth_type=0;
  $reth_receiver=0;
  $issuingh_id=0;
  $reth_is_canceled='Tidak';
  $whsl_id=0;
  if ($cru=='u' || $cru=='d')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_return_header="SELECT reth_id, issuingh_type, cust_id, reth_code, return_header.issuingh_id, reth_date, reth_ref_no, reth_by, reth_vehicle_no, reth_returned_by, 
	                                emp_id_receiver, cust_id, return_header.whsl_id, reth_notes, 
									CASE reth_is_canceled
									     WHEN '0' THEN 'Tidak'
										 WHEN '1' THEN 'Ya'
									END reth_is_canceled, reth_canceled_date, reth_canceled_reason, reth_ba_no, reth_po_no, return_header.created_by, return_header.created_time, 
									return_header.updated_by, return_header.updated_time
                              FROM return_header
							  INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
							  INNER JOIN warehouse_location ON warehouse_location.whsl_id=return_header.whsl_id
                              WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND reth_id='$id'";
	   $exec_get_return_header=mysqli_query($db_connection, $q_get_return_header);
	   $total_get_return_header=mysqli_num_rows($exec_get_return_header);
	   $field_return_header=mysqli_fetch_array($exec_get_return_header);
	   if ($total_get_return_header==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Transaksi Pengembalian Aset yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $q_get_return_detail="SELECT retd_id, return_detail.issuingd_id, issuing_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name,                                  cati_name, retd_qty,return_detail.whsl_id,
                                  retd_is_canceled, retd_notes
                                  FROM return_detail
                                  INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
                                  INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
                                  INNER JOIN issuing_detail ON issuing_detail.issuingh_id=issuing_detail.issuingh_id
                                  INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
								  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                  INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                  INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                  WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND issuing_detail.issuingd_id=return_detail.issuingd_id 
								        AND return_detail.reth_id='$id'";				
		//	echo $q_get_return_detail;
			$exec_get_return_detail=mysqli_query($db_connection, $q_get_return_detail);
			$reth_type=$field_return_header['issuingh_type'];
			$reth_cust_vend=$field_return_header['cust_id'];
			$issuingh_id=$field_return_header['issuingh_id'];
			$reth_id=$field_return_header['reth_id'];
		    $trans_code=$field_return_header['reth_code'];
			$trans_code_1=$field_return_header['reth_code'];
			$reth_date=get_date_1($field_return_header['reth_date']);
			$reth_ref_no=$field_return_header['reth_ref_no'];
			$reth_by=$field_return_header['reth_by'];
			$reth_vehicle_no=$field_return_header['reth_vehicle_no'];
			$reth_returned_by=$field_return_header['reth_returned_by'];
			$reth_receiver=$field_return_header['emp_id_receiver'];
			$whsl_id=$field_return_header['whsl_id'];
			$reth_notes=$field_return_header['reth_notes'];
			$reth_ba_no=$field_return_header['reth_ba_no'];
			$reth_po_no=$field_return_header['reth_po_no'];
			$reth_is_canceled=$field_return_header['reth_is_canceled'];
			$reth_canceled_date=$field_return_header['reth_canceled_date'];
			$reth_canceled_reason=$field_return_header['reth_canceled_reason'];
			$q_get_maker="SELECT users_names FROM users WHERE users_id='".$field_return_header['created_by']."'";
			$exec_get_maker=mysqli_query($db_connection, $q_get_maker);
			$field_maker=mysqli_fetch_array($exec_get_maker);
			$created_by=$field_maker['users_names']." ".$field_return_header['created_time'];
			if ($field_return_header['created_by']!='')
			   {
			     $q_get_updater="SELECT users_names FROM users WHERE users_id='".$field_return_header['updated_by']."'";
			     $exec_get_updater=mysqli_query($db_connection, $q_get_updater);
			     $field_maker=mysqli_fetch_array($exec_get_updater);
			     $updated_by=$field_maker['users_names']." ".$field_return_header['updated_time'];
			   }
			else
			   $updated_by='-';
		  }	  
	 }
  else
     {
	   $id="";
       $trans_code=get_format_number($db_connection, 'CYI',$branch_id);
	 }
  //echo $q_get_return_detail;
  $current_date=date('d-m-Y');
?>

<body background='../../images/bg/bg.png'>
<form action="" method="post" enctype="multipart/form-data" name="f_cru_return" id="f_cru_return" onSubmit="">
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
															   echo "TAMBAH TRANSAKSI PENGEMBALIAN BARU";
															 }
														  else
														  if ($cru=='u' || $cru=='d')
														     {
															   echo "UPDATE TRANSAKSI PENGEMBALIAN";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td>Nama Customer/Vendor * </td>
      <td>:</td>
      <td>
        <select name="s_type" id="s_type" onChange="submit()">
		  <?php 
		     if (isset($_POST['s_type']))
			     $reth_type=$_POST['s_type'];
				 
			 if ($reth_type=='0' && $cru=='i')
			    {
		          echo "<option value='0' selected='selected'>Customer</option>";
		          echo "<option value='1'>Vendor</option>";
				} 
			 else
			 if ($reth_type=='1'  && $cru=='i')
			    {
		          echo "<option value='0'>Customer</option>";
		          echo "<option value='1' selected='selected'>Vendor</option>";
				} 
		     else
			 if ($reth_type=='0' && ($cru=='u' || $cru=='d'))
			    {
		          echo "<option value='0' selected='selected'>Customer</option>";
				} 
			 else
			 if ($reth_type=='1'  && ($cru=='u' || $cru=='d'))
			    {
		          echo "<option value='1' selected='selected'>Vendor</option>";
				} 
		  ?>
        </select>
        <select name="s_customer_vendor" id="s_customer_vendor" onChange="submit()" style="width:300px">
        <?php
		    if (isset($_POST['s_customer_vendor']))
			    $reth_cust_vend=$_POST['s_customer_vendor'];
				
			if ($reth_type=='0' && $cru=='i')	
		        $q_get_customer_vendor="SELECT cust_name as cust_vend_name, cust_id, cust_code as cust_vend_code
                                        FROM customer
										WHERE cust_type='0' AND branch_id='$branch_id' ORDER BY cust_name ASC";
			else
			if ($reth_type=='1' && $cru=='i')
			    $q_get_customer_vendor="SELECT cust_name as cust_vend_name, cust_id, cust_code as cust_vend_code
                                        FROM customer
										WHERE cust_type='1'AND branch_id='$branch_id' ORDER BY cust_name ASC";
			else
			if ($reth_type=='0' && ($cru=='u' || $cru=='d'))	
		        $q_get_customer_vendor="SELECT cust_name as cust_vend_name, cust_id, cust_code as cust_vend_code
                                        FROM customer
										WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id='$reth_cust_vend' ORDER BY cust_name ASC";
			else
			if ($reth_type=='1' && ($cru=='u' || $cru=='d'))
			    $q_get_customer_vendor="SELECT cust_name as cust_vend_name, cust_id, cust_code as cust_vend_code
                                        FROM customer
										WHERE cust_type='1' AND branch_id='$branch_id' AND cust_id='$reth_cust_vend' ORDER BY cust_name ASC";			
			
		 	$exec_get_customer_vendor=mysqli_query($db_connection, $q_get_customer_vendor);
			
			if ($reth_type=='0' && $cru=='i')
			    echo "<option value='0'>-Pilih Customer-</option>";
			else
			if ($reth_type=='1' && $cru=='i')
			    echo "<option value='0'>-Pilih Vendor-</option>";
				
			while ($field_customer_vendor=mysqli_fetch_array($exec_get_customer_vendor))
			      {    	
				    if ($reth_cust_vend==$field_customer_vendor['cust_id'])
						$selected="selected='selected'";
			        else
						$selected="";
				  	 
		            if ($cru=='i')
			            echo "<option value='".$field_customer_vendor['cust_id']."' $selected>".$field_customer_vendor['cust_vend_name']." - [".$field_customer_vendor['cust_vend_code']."]</option>";
			        else
			        if ($cru=='u' || $cru=='d')
					    echo "<option value='".$field_customer_vendor['cust_id']."' $selected>".$field_customer_vendor['cust_vend_name']." - [".$field_customer_vendor['cust_id']."]</option>";
			      }
		  ?>
      </select></td>
      <td>Dikembalikan Oleh</td>
      <td>:</td>
      <td><input name="txt_reth_returned_by" type="text" id="txt_reth_returned_by" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $reth_returned_by;
				}	
		 ?>"/>    </tr>
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
				  echo $reth_id;
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
      <td>Diterima Oleh * </td>
      <td>:</td>
      <td><select name="s_employee" id="s_employee" style="width:300px">
        <?php
		    if (isset($_POST['s_employee']))
			    $reth_receiver=$_POST['s_employee'];
				
		    $q_get_employee="SELECT emp_id, emp_code, emp_name
                             FROM employee
                             WHERE branch_id='$branch_id'";
		 	$exec_get_employee=mysqli_query($db_connection, $q_get_employee);
			echo "<option value='0'>-Pilih Penerima-</option>";
			while ($field_employee=mysqli_fetch_array($exec_get_employee))
			      {    		 
				    if ($reth_receiver!='')
					   {
					     if ($reth_receiver==$field_employee['emp_id'])
						     $selected="selected='selected'"; 
						 else
						     $selected='';
					   }
					else
					    $selected='';  
			        echo "<option value='".$field_employee['emp_id']."' $selected>".$field_employee['emp_name']." - [".$field_employee['emp_code']."]</option>";
			      }
		  ?>
      </select>    </tr>
    <tr>
      <td width="16%" valign="top">No Ref Pengeluaran * </td>
      <td width="0%" valign="top">:</td>
      <td width="39%" valign="top"><select id="s_issuing_id" name="s_issuing_id" onChange="submit()" style="width:300px">
									 <?php
									   if (isset($_POST['s_issuing_id']))
									       $issuingh_id=$_POST['s_issuing_id'];
										   	   
									   if ($reth_type=='0')
									       $q_check_customer="SELECT * FROM customer
														      WHERE cust_type='0' AND branch_id='$branch_id' AND cust_id='$reth_cust_vend'";
									   else
										   $q_check_customer="SELECT * FROM customer
														      WHERE cust_type='1' AND branch_id='$branch_id' AND cust_id='$reth_cust_vend'";
									   $exec_check_customer=mysqli_query($db_connection, $q_check_customer);
									   $total_check_customer=mysqli_num_rows($exec_check_customer); 
									   
									   if ($total_check_customer==0 && $cru=='i')
										   echo "<option value='0'>-Pilih Pengeluaran-</option>";
									   else
									      {
										    if ($total_check_customer>0 && $cru=='i')
										        echo "<option value='0'>-Pilih Pengeluaran-</option>";
										    if ($reth_type=='0' && $cru=='i')
									            $q_check_issuing="SELECT * FROM issuing_header 
														          INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
														          WHERE customer.cust_id='$reth_cust_vend' AND issuingh_type='0' AND issuing_header.branch_id='$branch_id' AND 
																        customer.branch_id='$branch_id' AND issuingh_status!='2' AND issuingh_is_canceled='0'";
									        else
											if ($reth_type=='1' && $cru=='i')
										        $q_check_issuing="SELECT * FROM issuing_header 
														          INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
															      WHERE customer.cust_id='$reth_cust_vend' AND issuingh_type='1' AND issuing_header.branch_id='$branch_id' AND 
																        customer.branch_id='$branch_id' AND issuingh_status!='2'  AND issuingh_is_canceled='0'";  
											else
											if ($reth_type=='0' && ($cru=='u' || $cru=='d'))
										        $q_check_issuing="SELECT * FROM issuing_header 
														          INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
															      WHERE customer.cust_id='$reth_cust_vend' AND issuingh_type='0' AND issuing_header.branch_id='$branch_id' AND 
																        customer.branch_id='$branch_id' AND issuingh_id='$issuingh_id'  AND issuingh_is_canceled='0'"; 
											else
											if ($reth_type=='1' && ($cru=='u' || $cru=='d'))
										        $q_check_issuing="SELECT * FROM issuing_header 
														          INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
															      WHERE customer.cust_id='$reth_cust_vend' AND issuingh_type='1' AND issuing_header.branch_id='$branch_id' AND 
																        customer.branch_id='$branch_id' AND issuingh_id='$issuingh_id'  AND issuingh_is_canceled='0'"; 
																		
										    $exec_get_issuing=mysqli_query($db_connection, $q_check_issuing);
									        while ($field_get_issuing=mysqli_fetch_array($exec_get_issuing))
									              {
											         if ($issuingh_id!='0')
											            {
												          if ($issuingh_id==$field_get_issuing['issuingh_id'])
													          $selected="selected='selected'";
												          else
													          $selected='';
												        }
											         echo "<option value='".$field_get_issuing['issuingh_id']."' $selected>".$field_get_issuing['issuingh_code']." - [".get_date_1($field_get_issuing['issuingh_date'])."]</option>";
											      } 
										  }
									 ?>
                                   </select></td>
      <td width="18%" valign="top">Lokasi Gudang *</td>
      <td width="0%" valign="top">:</td>
      <td width="26%" valign="top"><select id="s_whs" name="s_whs" style="width:300px">
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
    </tr>
    <tr>
      <td valign="top">Tanggal Pengembalian * <br>
        Referensi No * </td>
      <td valign="top">:<br>:</td>
      <td valign="top"><input name="txt_reth_date" type="text" id="txt_reth_date"  readonly="readonly" size="10" 
		 <?php 
			if ($id!='')
				echo "value=".$reth_date; 
			else 
		        echo "value='$current_date'";
		 ?>><br>
      <input name="txt_reth_ref_no" type="text" id="txt_reth_ref_no" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $reth_ref_no;
				}	
		 ?>"/></td>
      <td valign="top">Keterangan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_reth_notes" id="txt_reth_notes" cols="35"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $reth_notes;
				}
		 ?></textarea></td>
    </tr>
    <tr>
      <td valign="top">Dikirim Menggunakan</td>
      <td valign="top">:</td>
      <td valign="top"><input name="txt_reth_by" type="text" id="txt_reth_by" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $reth_by;
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
				  echo $reth_is_canceled;
				}	
		 ?></td>
    </tr>
    <tr>
      <td>Nomor Kendaraan</td>
      <td>:</td>
      <td><input name="txt_reth_vehicle_no" type="text" id="txt_reth_vehicle_no" size="30" 
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u' || $cru=='d')
			    {
				  echo $reth_vehicle_no;
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
				  echo $reth_canceled_reason;
				}	
		 ?></td>
    </tr>
    <tr>
      <td>Nomor Berita Acara</td>
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
				                  echo $reth_ba_no;
				                }	
		 ?>"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Purchase No</td>
      <td>:</td>
      <td><label>
        <input type="text" id="txt_po_no" name="txt_po_no" size="30"
		       value="<?php 
		                     if ($cru=='i')
			                    {
				                  echo "";
				                }
		                     else
			                 if ($cru=='u' || $cru=='d')
			                    {
				                  echo $reth_po_no;
				                }	
		 ?>">
      </label></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
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
					       echo "value=".mysqli_num_rows($exec_get_return_detail);	  
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
          <th width="10%" scope="col">Kode Aset</th>
          <th width="25%" scope="col">Deskripsi Isi Aset</th>
          <th width="9%" scope="col">Serial No</th>
          <th width="6%" scope="col">Kapasitas</th>
          <th width="9%" scope="col">Kategori</th>
          <th width="3%" scope="col">Qty</th>
          <th width="6%" scope="col">Dibatalkan</th>
          <th width="13%" scope="col">Lokasi Gudang </th>
          <th width="17%" scope="col">Notes</th>
        </tr>
        <?php  
		      if ($id=='')
			     {
				   $q_check_issuing_doc="SELECT * FROM issuing_header 
				                         INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
				                         WHERE issuingh_id='$issuingh_id' AND issuing_header.cust_id='$reth_cust_vend' AND cust_type='$reth_type'";
				   $exec_check_issuing_doc=mysqli_query($db_connection, $q_check_issuing_doc);
				   $total=mysqli_num_rows($exec_check_issuing_doc);
				   if ($total>0)
				       $q_get_issuing_detail="SELECT issuingd_id, issuing_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty,
                                              issuingd_notes, issuingd_is_canceled 
                                              FROM issuing_detail 
                                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                              INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
											  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                              INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                              INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                              WHERE issuing_header.branch_id='$branch_id' AND issuing_detail.issuingh_id='$issuingh_id' AND issuingd_is_canceled='0' AND 
										            issuingd_is_return='0' AND cust_id='$reth_cust_vend'";
				   else
				      $q_get_issuing_detail="SELECT issuingd_id, issuing_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, 
                                              uom_name, issuingd_notes, issuingd_is_canceled 
                                              FROM issuing_detail 
                                              INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                              INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
											  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                              INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                              INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                              WHERE issuing_header.branch_id='$branch_id' AND issuing_detail.issuingh_id='0' AND issuingd_is_canceled='0' AND 
										            issuingd_is_return='0' AND cust_id='$reth_cust_vend'";
				   $exec_get_issuing_detail=mysqli_query($db_connection, $q_get_issuing_detail);
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
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $issuingd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_returnd_is_canceled_<?php echo $issuingd_id;?>" name="cb_returnd_is_canceled_<?php echo $issuingd_id;?>" 
					                    value="1"  disabled='disabled'>Tidak</td>
					 <td class="td_lb"><select id="s_whsld_<?php echo $issuingd_id;?>" name="s_whsld_<?php echo $issuingd_id;?>" style="width:300px">
                       <?php 
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
					 <td class="td_lb"><input type="text" id="txt_returnd_notes_<?php echo $issuingd_id;?>" name="txt_returnd_notes_<?php echo $issuingd_id;?>" 
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
				   while ($field_get_return_detail=mysqli_fetch_array($exec_get_return_detail)) 
				         {
						   $no++;
						   $issuingd_id=$field_get_return_detail['issuingd_id']; 
						   $retd_id=$field_get_return_detail['retd_id']; 
						   $itemd_id= $field_get_return_detail['itemd_id']; 
						   $itemd_code=$field_get_return_detail['itemd_code'];
						   $masti_name=$field_get_return_detail['masti_name'];
						   $itemd_serial_no=$field_get_return_detail['itemd_serial_no'];
						   $masti_capacity=$field_get_return_detail['itemd_capacity']." ".$field_get_return_detail['uom_name'];
						   $cati_name=$field_get_return_detail['cati_name'];
						   $itemd_qty=$field_get_return_detail['retd_qty']." Cylinder";
						   $whsld_id=$field_get_return_detail['whsl_id'];
						   $retd_is_canceled=$field_get_return_detail['retd_is_canceled'];
					       $retd_notes=$field_get_return_detail['retd_notes'];
			 ?>
		         <tr>
				     <td class="td_lb"><input type="checkbox" id="cb_data[]" name="cb_data[]" value="<?php echo $issuingd_id;?>"/></td>
                     <td class="td_lb"><?php echo $itemd_code;?></td>
                     <td class="td_lb"><?php echo $masti_name;?></td>
		             <td class="td_lb"><?php echo $itemd_serial_no;?></td>
		             <td class="td_lb"><?php echo $masti_capacity;?></td>
		             <td class="td_lb"><?php echo $cati_name;?></td>
		             <td class="td_lb"><?php echo $itemd_qty;?></td>
		             <td class="td_lb"><input type="checkbox" id="cb_returnd_is_canceled_<?php echo $issuingd_id;?>" name="cb_returnd_is_canceled_<?php echo $issuingd_id;?>" 
					                    value="1" <?php if ($retd_is_canceled=='1') 
										                   {
														     echo "checked='checked' disabled='disabled'";
														   }  ?> onClick="call_canceled_item('<?php echo $issuingd_id;?>','<?php echo $retd_id;?>')"><?php if ($retd_is_canceled=='1') 
														                   echo "Ya"; 
																	   else 
																	       echo "Tidak";?></td>
					 <td class="td_lb"><select id="s_whsld_<?php echo $issuingd_id;?>" name="s_whsld_<?php echo $issuingd_id;?>" style="width:300px">
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
					 $whsldid='s_whsld_'.$issuingd_id;
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
					 <td class="td_lb"><input type="text" id="txt_returnd_notes_<?php echo $issuingd_id;?>" name="txt_returnd_notes_<?php echo $issuingd_id;?>" 
					                    value="<?php echo $retd_notes;?>"  <?php if ($retd_is_canceled=='1') echo "readonly='readonly'"; ?> size="35"></td>
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
      <td align="right"><input name="btn_save" type="button" id="btn_save" value="Simpan" onClick="input_data()" <?php if ($reth_is_canceled=='Ya' || $cru=='d') echo "disabled='disabled'";?>/>
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
			 var cust_vend=document.getElementById('s_customer_vendor').value; 
			 var return_code=document.getElementById('txt_code').value.trim(); 
			 var issuing_id=document.getElementById('s_issuing_id').value.trim(); 
			 var date_return=document.getElementById('txt_reth_date').value.trim();
			 var receiver=document.getElementById('s_employee').value; 
			 var whs=document.getElementById('s_whs').value;
			 var d=date_return.substring(0,2);
			 var m=date_return.substring(3,5);
			 var y=date_return.substring(6,10);
			 var return_date=Date.parse(y+'-'+m+'-'+d);
			 var ref_no=document.getElementById('txt_reth_ref_no').value.trim(); 
			 var total_detail=document.getElementById('txt_rows').value;

			 if (cust_vend=='0')
			    {
			      alert('Nama Customer/Vendor harus diisi!');
			    }
			 else
			 if (return_code=='')
			    {
				  alert('No Transaksi harus diisi!');
				}
		     else
			 if (issuing_id=='0')
			    {
				  alert('No Ref Pengeluaran harus diisi!');
				}
		     else
			 if (date_return=='')		
			    {
				  alert('Tanggal Pengembalian harus diisi!');
				}
			 else
			 if (ref_no=='')
			    {
				  alert('Referensi No harus diisi!');
				}
			 else
			 if (receiver=='0')
			    {
			      alert('Nama Penerima harus diisi!');
			    }
			 else
			 if (whs=='0')
			    {
			      alert('Lokasi Gudang harus diisi!');
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
							 y='cb_returnd_is_canceled_'+chk_data[i].value;
							 z='s_whsld_'+chk_data[i].value;
							 if (x=='u')
							     document.getElementById(y).disabled=false;
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
			           f_cru_return.action='../../data/return/input_return.php?b='+branch_id;
				       f_cru_return.submit();
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
		     var z=document.getElementById('cb_returnd_is_canceled_'+x);
			 if (z.checked==true)
			    {
		          var konfirmasi=confirm('Apakah yakin akan dibatalkan?');
				  if (konfirmasi)
				     {
					   f_cru_return.action='../../data/return/canceled_item.php?id='+y+'&b='+id_branch;
				       f_cru_return.submit();
					 }
				  else
				     z.checked=false;
				}  
		   }	
</script>






