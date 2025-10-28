<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  
  $branch_id=$_SESSION['ses_id_branch'];
  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction);  
  $created_time=get_create_time($db_connection); 
  
	$continue=0;
	$maker=$_SESSION['ses_user_id'];
	//$branch_id=htmlspecialchars($_GET['b']);
	$reth_type=htmlspecialchars(trim($_POST['s_type']));
	$reth_cust_vendor=$_POST['s_customer_vendor'];
	$reth_id=htmlspecialchars($_POST['txt_id']);
	$issuingh_id=$_POST['s_issuing_id'];
	$m=substr(htmlspecialchars($_POST['txt_reth_date']),3,2);
	$y=substr(htmlspecialchars($_POST['txt_reth_date']),6,4); 
	$reth_date=get_date_2(htmlspecialchars($_POST['txt_reth_date']));
	$reth_ref_no=htmlspecialchars(trim($_POST['txt_reth_ref_no']));
	$reth_ba_no=htmlspecialchars(trim($_POST['txt_ba_no']));
	$reth_po_no=htmlspecialchars(trim($_POST['txt_po_no']));
	$reth_by=htmlspecialchars(trim($_POST['txt_reth_by']));
	$reth_vehicle_no=htmlspecialchars(trim($_POST['txt_reth_vehicle_no']));
	$reth_returned_by=htmlspecialchars(trim($_POST['txt_reth_returned_by']));
	$reth_receiver=htmlspecialchars(trim($_POST['s_employee']));
	$whsl_id=$_POST['s_whs'];
	$reth_notes=htmlspecialchars($_POST['txt_reth_notes']);
	if ($reth_type=='0')
		$q_check_customer_vendor="SELECT cust_id FROM customer WHERE cust_id='$reth_cust_vendor' AND cust_type='0' AND branch_id='$branch_id'";
	else
		$q_check_customer_vendor="SELECT cust_id FROM customer WHERE cust_id='$reth_cust_vendor' AND cust_type='1' AND branch_id='$branch_id'";
		
	$q_check_issuingh_id="SELECT * FROM issuing_header WHERE branch_id='$branch_id' AND issuingh_id='$issuingh_id'";
	$q_check_employee="SELECT * FROM employee WHERE emp_id='$reth_receiver'";
	$q_check_whs="SELECT * FROM warehouse_location WHERE branch_id='$branch_id' AND whsl_id='$whsl_id'";
	
	$exec_customer_vendor=mysqli_query($db_connection, $q_check_customer_vendor);
	$exec_check_issuingh_id=mysqli_query($db_connection, $q_check_issuingh_id);
	$exec_check_employee=mysqli_query($db_connection, $q_check_employee);
	$exec_check_whs=mysqli_query($db_connection, $q_check_whs);
	$field_issuing_header=mysqli_fetch_array($exec_check_issuingh_id);

	$active_period=check_active_period($db_connection, $m, $y);	
	
	if ($active_period!='OK')
	   {
		 ?>
			<script language="javascript">
			  var x='<?php echo $active_period;?>';
			  alert(x);
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php		     
	   }
	else
	if (mysqli_num_rows($exec_customer_vendor)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Nama Customer/Vendor tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if (mysqli_num_rows($exec_check_issuingh_id)==0)
	  {
		?>
		   <script language="javascript">
			 alert('Kode Pengeluaran tidak ditemukan!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php
	  }
   else
   if ($reth_date<$field_issuing_header['issuingh_date'])
	  {
		?>
		   <script language="javascript">
			 alert('Tanggal Pengembalian harus lebih besar atau sama dengan Tanggal Pengeluaran!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php
	  }
   else
   if ($field_issuing_header['issuingh_status']=='2' && $reth_id=='')
	  {
		?>
		   <script language="javascript">
			 alert('Status aset sudah dikembalikan semuanya!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php
	  }
   else
   if (mysqli_num_rows($exec_check_employee)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Penerima tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if (mysqli_num_rows($exec_check_whs)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Lokasi Gudang tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if ($reth_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$reth_code=get_no_transaction($db_connection, 'CYI',$branch_id);
		if ($reth_code!='')
		{
			$reth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_return_header="select * from return_header where reth_code='$reth_code' and branch_id='$branch_id'";
			$exec_check_return_header=mysqli_query($db_connection, $q_check_return_header);
			if (mysqli_num_rows($exec_check_return_header)>0)
			   {
				 mysqli_rollback($db_connection);
				 ?>
				   <script language="javascript">
					 var abc="<?php echo $reth_code; ?>";
					 alert('Duplikasi No Transaksi! abc='+abc);
					 window.location.href='javascript:history.back(1)';
				   </script>
				 <?php 
			   }
			else
			   {
				 $input_data='';
				 $issuingd_id='';
				 $whsld_id='';
				 $data=$_POST['cb_data'];
				 $detail_qty=0;
				 $total=count($data);
				 foreach ($data as $field_data)
						 {
						   if ($issuingd_id=='')
							   $issuingd_id="'$field_data'";
						   else
							   $issuingd_id=$issuingd_id.",'$field_data'";
							   
						   $issuingd_id_1='returnd_id_'.$field_data;
						   $whsld_id='s_whsld_'.$field_data;
						   $returnd_notes='txt_returnd_notes_'.$field_data;
						
						   $q_get_whsl_id="SELECT whsl_id, itemd_position, itemd_status, itemd_code 
										   FROM item_detail 
										   INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
										   WHERE issuingh_id='$issuingh_id' AND issuingd_id='$field_data'";
						   $exec_get_whsl_id=mysqli_query($db_connection, $q_get_whsl_id);	
						   if (mysqli_num_rows($exec_get_whsl_id)==0)
							  {
								$continue=1;
								mysqli_rollback($db_connection);
								?>
								  <script language="javascript">
									 alert('Aset tidak ditemukan!');
									 window.location.href='javascript:history.back(1)';
								  </script>								  
								<?php
								break;
							  }
						   else
							  {	    
								$field_get_whsl=mysqli_fetch_array($exec_get_whsl_id);
								if (($field_get_whsl['itemd_position']!='Customer') && ($field_get_whsl['itemd_position']!='Vendor') )
								   {
									 $continue=1;
									 $code_tube=$field_get_whsl['itemd_code'];
									 mysqli_rollback($db_connection);
									 ?>
									   <script language="javascript">
										  var tube_code='<?php echo $code_tube;?>';
										  alert('Mohon dapat dicek posisi aset berikut ini : '+tube_code);
										  window.location.href='javascript:history.back(1)';
									   </script>								  
									 <?php
									 break;
								   }								    
								else
								if ($field_get_whsl['itemd_status']=='1')
								   {
									 $continue=1;
									 $code_tube=$field_get_whsl['itemd_code'];
									 mysqli_rollback($db_connection);
									 ?>
									   <script language="javascript">
										  var tube_code='<?php echo $code_tube;?>';
										  alert('Status aset berikut ini InActive : '+tube_code);
										  window.location.href='javascript:history.back(1)';
									   </script>								  
									 <?php
									 break;
								   }
								else
								   {
									 if ($input_data=='')
										 $input_data="VALUES ((SELECT reth_id FROM return_header WHERE branch_id='$branch_id' AND reth_code='$reth_code'), '".$field_data."','1','".$field_get_whsl['whsl_id']."','".$_POST[$whsld_id]."','0','".htmlspecialchars($_POST[$returnd_notes])."')";
									 else	  
										 $input_data=$input_data.", ((SELECT reth_id FROM return_header WHERE branch_id='$branch_id' AND reth_code='$reth_code'), '".$field_data."','1','".$field_get_whsl['whsl_id']."','".$_POST[$whsld_id]."','0','".htmlspecialchars($_POST[$returnd_notes])."')";
								   }		 
							  }
						 }
							 
				 if ($continue==0)
					{  
					  $q_check_issuingd="SELECT itemd_code, masti_name, itemd_serial_no, issuingd_id, issuing_detail.itemd_id 
										 FROM issuing_detail
										 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
										 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
										 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
										 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuing_detail.issuingd_id IN ($issuingd_id) AND 
											   issuing_detail.issuingh_id='$issuingh_id' AND item_detail.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND 
											   issuingh_status!='2' AND issuingh_is_canceled='0'";
				 //	  echo $q_check_issuingd;
					  $exec_check_issuingd=mysqli_query($db_connection, $q_check_issuingd);
					  if (mysqli_num_rows($exec_check_issuingd)==0)
						 {
						   mysqli_rollback($db_connection);
						   ?>
							  <script language="javascript">
								alert('Semua aset tidak ditemukan!');
								window.location.href='javascript:history.back(1)';
							  </script>
						   <?php
						 }
					  else	
						 {
						   if (mysqli_num_rows($exec_check_issuingd)!=$total) 
							  {
								mysqli_rollback($db_connection);
								$no=0;
								echo "<b>Beberapa aset tidak ditemukan!</b><br>";
								echo "<table>";  
								echo "<tr><td>No</td><td>Kode Item</td><td>Nama Item</td><td>Serial No</td><td>Kapasitas</td></tr>";
								while ($field_data=mysqli_fetch_array($exec_check_itemd))
									  {
										echo "<tr><td>$no++</td><td>".$field_data['itemd_code']."</td><td>".$field_data['masti_name']."</td><td>".$field_data['itemd_serial_no']."</td><td>".$field_data['masti_capacity']."</td></tr>";
									  }
								echo "</table>";
							  }
						   else
							  {
								$q_input_return_header="INSERT INTO return_header (branch_id, reth_code, issuingh_id, reth_date, reth_ref_no, reth_by, reth_vehicle_no, 
																				   reth_returned_by, emp_id_receiver, whsl_id, reth_is_canceled, reth_notes, created_by, 
																				   created_time, reth_ba_no, reth_po_no)
														VALUES ('$branch_id','$reth_code', '$issuingh_id', '$reth_date', '$reth_ref_no', '$reth_by', '$reth_vehicle_no', 
																'$reth_returned_by', '$reth_receiver', '$whsl_id', '0', '$reth_notes','$maker','$created_time', 
																'$reth_ba_no','$reth_po_no')";
								//echo "ABC=".$q_input_return_header."<BR>";
								$exec_input_return_header=mysqli_query($db_connection, $q_input_return_header);
								if ($exec_input_return_header)
								   {						    
									 $q_get_returnh_id="SELECT reth_id FROM return_header WHERE branch_id='$branch_id' AND reth_code='$reth_code'";
									 $exec_get_returnh_id=mysqli_query($db_connection, $q_get_returnh_id);
									 $field_data_returnh_id=mysqli_fetch_array($exec_get_returnh_id);
									 $reth_id=$field_data_returnh_id['reth_id'];
									
									 $q_input_return_detail="INSERT INTO return_detail (reth_id, issuingd_id, retd_qty, whsl_id_first, whsl_id, 
																						retd_is_canceled, retd_notes) ".$input_data;
									 $exec_input_return_detail=mysqli_query($db_connection, $q_input_return_detail);
									 if ($exec_input_return_detail) 
										{
										  $issuingd_id="";
										  $tube_id="";
										  while ($field_issuingd_id=mysqli_fetch_array($exec_check_issuingd))
												{
												  if ($issuingd_id=="")
													  $issuingd_id="'".$field_issuingd_id['issuingd_id']."'";
												  else
													  $issuingd_id=$issuingd_id.",'".$field_issuingd_id['issuingd_id']."'";

												  if ($tube_id=="")
													  $tube_id="'".$field_issuingd_id['itemd_id']."'";
												  else
													  $tube_id=$tube_id.",'".$field_issuingd_id['itemd_id']."'";
												}
											   
										  $q_update_issuing_detail="UPDATE issuing_detail
										  								   SET issuingd_is_return='1' 
																	WHERE issuingh_id='$issuingh_id' AND issuingd_is_canceled='0' and issuingd_is_return='0' AND
																		  issuingd_id IN ($issuingd_id)";
										  $exec_update_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
										  if ($exec_update_issuing_detail)
											 {			 
											   $q_update_item_detail="UPDATE item_detail, issuing_detail, return_detail SET itemd_position='Internal', 
																			 item_detail.whsl_id=return_detail.whsl_id
																	  WHERE item_detail.itemd_id=issuing_detail.itemd_id AND 
																			issuing_detail.issuingd_id=return_detail.issuingd_id AND
																			item_detail.branch_id='$branch_id' AND retd_is_canceled='0' AND issuingh_id='$issuingh_id' AND
																			reth_id='$reth_id' AND issuingd_is_canceled='0'";
											   //echo $q_update_item_detail;
											   $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);								
											   if ($exec_update_item_detail)
												  {
													$q_update_issuing_header_status="UPDATE issuing_header 
																					 SET issuingh_status=(IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_return='0' AND 
																						 issuingh_id='$issuingh_id' AND issuingd_is_canceled='0')=0,'2',
																						 IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND 
																							 issuingd_is_canceled='0' AND issuingd_is_return='0')!=
																							(SELECT COUNT(*) FROM issuing_detail 
																							 WHERE issuingh_id='$issuingh_id' AND issuingd_is_canceled='0'),'1','0')))
																					 WHERE issuingh_id='$issuingh_id'";
													$exec_update_issuing_header_status=mysqli_query($db_connection, $q_update_issuing_header_status);
													if ($exec_update_issuing_header_status)
													   {
														 update_runing_no($db_connection, 'CYI',$branch_id);
														 mysqli_commit($db_connection);
														 ?>
															<script language="javascript">
															  opener.location.reload(true);
															  window.close();
															</script>
														 <?php
													   }
													else
													   {
														 mysqli_rollback($db_connection);
														 ?>
															<script language="javascript">
															  alert('1. Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
															  window.location.href='javascript:history.back(1)';
															</script>
														 <?php 
													   }
												  }
											   else
												  {
													mysqli_rollback($db_connection);
													?>
													   <script language="javascript">
														 alert('2. Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
														 window.location.href='javascript:history.back(1)';
													   </script>
													<?php 
												  }
											 }
										  else
											 {
											   mysqli_rollback($db_connection);
											   ?>
												  <script language="javascript">
													alert('3. Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
													window.location.href='javascript:history.back(1)';
												  </script>
											   <?php  
											 }
										}
									 else
										{
										  mysqli_rollback($db_connection);
										  ?>
											 <script language="javascript">
											   alert('4. Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
											   window.location.href='javascript:history.back(1)';
											 </script>
										  <?php  
										}
									}
								 else
									{
									  mysqli_rollback($db_connection);
									  ?>
										 <script language="javascript">
										   alert('5. Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
										   window.location.href='javascript:history.back(1)';
										 </script>
									  <?php  
									}
							  }	 
						 }	
					}	
			   }   
		}
	  }
   else   // jika update data
	  {
		$reth_code=htmlspecialchars(trim($_POST['txt_code']));
		$reth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_return="select return_header.*, MONTH(reth_date) AS month, YEAR(reth_date) AS year 
						 from return_header where reth_id='$reth_id' and branch_id='$branch_id'";
		$exec_check_return=mysqli_query($db_connection, $q_check_return);
		$field_check_return=mysqli_fetch_array($exec_check_return);

		$active_period=check_active_period($db_connection, $field_check_return['month'], $field_check_return['year']);	
	
		if ($active_period!='OK')
		   {
			 ?>
				<script language="javascript">
				   var x='<?php echo $active_period;?>';
				   alert(x);
				   window.location.href='javascript:history.back(1)';
				</script>
			  <?php		     
		   }
		else
		if ($field_check_return['reth_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Pengembalian tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_return)>0)
		   {
			 $reth_id=$field_check_return['reth_id'];
			 $input_data='';
			 $issuingd_id='';
			 $whsld_id='';
			 $data=$_POST['cb_data'];
			 $detail_qty=0;
			 $total=count($data);
			 foreach ($data as $field_data)
					 {
					   if ($issuingd_id=='')
						   $issuingd_id="'$field_data'";
					   else
						   $issuingd_id=$issuingd_id.",'$field_data'";
						   
					   $issuingd_id_1='returnd_id_'.$field_data;
					   $whsld_id='s_whsld_'.$field_data;
					   $is_canceled='cb_returnd_is_canceled_'.$field_data;
					   if (isset($_POST[$is_canceled])=='1')
						   $is_canceled='1';
					   else
						   $is_canceled='0';
					   $returnd_notes='txt_returnd_notes_'.$field_data;
				
					   $q_get_whsl_id="SELECT whsl_id, itemd_position, itemd_status 
									   FROM item_detail 
									   INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
									   WHERE issuingh_id='$issuingh_id' AND issuingd_id='$field_data'";
					   $exec_get_whsl_id=mysqli_query($db_connection, $q_get_whsl_id);	
					   if (mysqli_num_rows($exec_get_whsl_id)==0)
						  {
							$continue=1;
							?>
							  <script language="javascript">
								 alert('Aset tidak ditemukan!');
								 window.location.href='javascript:history.back(1)';
							  </script>								  
							<?php
							break;
						  }
					   else
						  {	 	
							$field_get_whsl=mysqli_fetch_array($exec_get_whsl_id);						   
							if ($input_data=='') 
								$input_data="VALUES ('$reth_id', '".$field_data."','1','".$field_get_whsl['whsl_id']."','".$_POST[$whsld_id]."','$is_canceled','".htmlspecialchars($_POST[$returnd_notes])."')";
							else	  
								$input_data=$input_data.", ('$reth_id', '".$field_data."','1','".$field_get_whsl['whsl_id']."','".$_POST[$whsld_id]."','$is_canceled','".htmlspecialchars($_POST[$returnd_notes])."')";
						  } 
					 }
			 
			 if ($continue==0)
				{
				  $q_check_tube="SELECT issuing_detail.itemd_id, issuing_detail.issuingd_id, retd_id
								 FROM return_detail
								 INNER JOIN issuing_detail ON issuing_detail.issuingd_id=return_detail.issuingd_id
								 INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
								 WHERE retd_is_canceled='0' AND return_detail.reth_id='$reth_id' AND return_header.reth_id='$reth_id'";
				  $exec_check_tube=mysqli_query($db_connection, $q_check_tube);
				  $tube_id="";
				  $issuingd_id_old="";
				  while ($field_tube_id=mysqli_fetch_array($exec_check_tube))
						{
						  if ($tube_id=="")
							  $tube_id="'".$field_tube_id['itemd_id']."'";
						  else
							  $tube_id=$tube_id.", '".$field_tube_id['itemd_id']."'";
					 
						  if ($issuingd_id_old=="")
							  $issuingd_id_old="'".$field_tube_id['issuingd_id']."'";
						  else
							  $issuingd_id_old=$issuingd_id_old.", '".$field_tube_id['issuingd_id']."'";
						}
				  $action="EDIT";
				  $reth_date_old=$field_check_return['reth_date']; 
				  $reth_date_new=$reth_date;
				  $reth_created_time=$field_check_return['created_time']; 
				  $messages=is_there_any_new_trans($db_connection, $action, $reth_id, $tube_id, $reth_date_old, $reth_date_new, $reth_created_time);
			 
				  if ($messages!='1')
					 {
					   ?>
						  <script language="javascript">
							 var message='<?php echo $messages;?>';
							 alert(message);
							 history.back(1);
						  </script>
					   <?php						  
					 }
				  else
					 {	
					   $q_check_item="SELECT * FROM item_detail 
									  WHERE itemd_id IN ($tube_id) AND (itemd_position!='Internal' OR itemd_status='1' OR 
											itemd_is_broken='1' OR itemd_is_wo='1' OR itemd_is_dispossed='1' OR branch_id!='$branch_id')";
					   $exec_check_item=mysqli_query($db_connection, $q_check_item);
					   if (mysqli_num_rows($exec_check_item)>0)
						  {
							$tube_codes="";
							while ($tube_id_error=mysqli_fetch_array($exec_check_item))
								  {
									if ($tube_codes=="")
										$tube_codes=$tube_id_error['itemd_code'];
									else
										$tube_codes=$tube_codes.",".$tube_id_error['itemd_code'];
								  }
							?>
							   <script language="javascript">
								  var x='<?php echo $tube_codes;?>';
								  alert('Mohon cek status Aset berikut ini :\n'+x);
								  history.back(1);
							   </script>
							<?php
						  } 
					   else
						  {	
							mysqli_autocommit($db_connection,false);
							$q_update_issuing_detail="UPDATE issuing_detail, return_detail 
															 SET issuingd_is_return='0' 
													  WHERE issuing_detail.issuingd_id=return_detail.issuingd_id AND retd_is_canceled='0' AND  
															reth_id='$reth_id'";
							if ($reth_type=='0')
								$q_update_status_item="UPDATE item_detail, issuing_detail, return_detail
															  SET itemd_position='Customer', item_detail.whsl_id=whsl_id_first 
													   WHERE item_detail.itemd_id=issuing_detail.itemd_id AND issuing_detail.issuingd_id=return_detail.issuingd_id AND
															 retd_is_canceled='0' AND item_detail.branch_id='$branch_id' AND 
															 issuingh_id='$issuingh_id' AND reth_id='$reth_id'";
							else 
								$q_update_status_item="UPDATE item_detail, issuing_detail,return_detail 
															  SET itemd_position='Vendor', item_detail.whsl_id=whsl_id_first 
													   WHERE item_detail.itemd_id=issuing_detail.itemd_id AND issuing_detail.issuingd_id=return_detail.issuingd_id AND
															 retd_is_canceled='0' AND item_detail.branch_id='$branch_id' AND 
															 issuingh_id='$issuingh_id' AND reth_id='$reth_id'";
															  
							$q_delete_return_detail="DELETE FROM return_detail WHERE reth_id='$reth_id'";		 
							$exec_update_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
							$exec_update_status_item=mysqli_query($db_connection, $q_update_status_item);
							$exec_delete_return_detail=mysqli_query($db_connection, $q_delete_return_detail);
							if ($exec_update_issuing_detail && $exec_update_status_item && $exec_delete_return_detail )
							   {
								 if ($reth_code==$reth_code_1)
												   
									 input_return($branch_id, $reth_id, $reth_code, $issuingh_id,$reth_date, $reth_ref_no, $reth_ba_no, $reth_po_no, $reth_by, 
												  $reth_vehicle_no, $reth_returned_by, $reth_receiver, $whsl_id, $reth_notes, $maker, $input_data);
									  
								 else
									{
									  $q_check_new_return="SELECT * FROM return_header WHERE branch_id='$branch_id' AND reth_code='$reth_code'";
									  $exec_check_new_return=mysqli_query($db_connection, $q_check_new_return);
									  if (mysqli_num_rows($exec_check_new_return)>0)
										 {
										   mysqli_rollback($db_connection);
										   ?>
											  <script language="javascript">
												  alert('Duplikasi No Transaksi!');
												  window.location.href='javascript:history.back(1)';
											  </script>
										   <?php 
										 }
									  else
										 input_return($branch_id, $reth_id, $reth_code, $issuingh_id,$reth_date, $reth_ref_no, $reth_ba_no, $reth_po_no, $reth_by, 
													  $reth_vehicle_no, $reth_returned_by, $reth_receiver, $whsl_id, $reth_notes, $maker, $input_data);
									 }	
								}	
							 else
								{
								  mysqli_rollback($db_connection);
								  ?>
									 <script language="javascript">
										 alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
										 window.location.href='javascript:history.back(1)';
									 </script>
								  <?php 
								} 						   
						  }
				   
					 }	
				}
			 else
				{
				  ?>
					 <script language="javascript">
					   alert('Transaksi Pengembalian tidak ditemukan!');
					   window.close();
					 </script>
				   <?php  
				}
		   }     
	  }	
	 
 function input_return($branch_id, $reth_id, $reth_code, $issuingh_id,$reth_date, $reth_ref_no, $reth_ba_no, $reth_po_no, $reth_by, $reth_vehicle_no, $reth_returned_by, 
                       $reth_receiver, $whsl_id, $reth_notes, $maker, $input_data, $db_connection)
          {
		    $current_date=date('Y-m-d');
            $q_input_return_header="UPDATE return_header SET reth_code='$reth_code', reth_date='$reth_date', reth_ref_no='$reth_ref_no', reth_by='$reth_by', 
			                               reth_vehicle_no='$reth_vehicle_no', reth_returned_by='$reth_returned_by', emp_id_receiver='$reth_receiver', whsl_id='$whsl_id', 
										   reth_notes='$reth_notes', updated_by='$maker', updated_time=NOW(), reth_ba_no='$reth_ba_no', reth_po_no='$reth_po_no'
                                    WHERE branch_id='$branch_id' AND reth_id='$reth_id'";
			$exec_input_return_header=mysqli_query($db_connection, $q_input_return_header);
			if ($exec_input_return_header)
			   {
			     $q_input_return_detail="INSERT INTO return_detail (reth_id, issuingd_id, retd_qty, whsl_id_first, whsl_id, retd_is_canceled, retd_notes) ".$input_data;
			     $exec_input_return_detail=mysqli_query($db_connection, $q_input_return_detail);
			     if ($exec_input_return_detail)
			        {
			          $q_update_issuing_detail="UPDATE issuing_detail, return_detail SET issuingd_is_return='1' 
                                                WHERE issuingh_id='$issuingh_id' AND issuing_detail.issuingd_id=return_detail.issuingd_id AND retd_is_canceled='0' AND 
												      reth_id='$reth_id'";
			          $exec_update_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
			          if ($exec_update_issuing_detail)
						 { 			   
			               $q_update_item_detail="UPDATE item_detail, issuing_detail, return_detail SET itemd_position='Internal' 
                                                  WHERE item_detail.itemd_id=issuing_detail.itemd_id AND issuing_detail.issuingd_id=return_detail.issuingd_id AND 
												        issuingh_id='$issuingh_id' AND reth_id='$reth_id' AND retd_is_canceled='0'";
			               $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
			               if ($exec_update_item_detail)
							  {						   
		                        $q_update_issuing_header_status="UPDATE issuing_header 
                                                                 SET issuingh_status=(IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_return='0' AND 
												                     issuingh_id='$issuingh_id')=0,'2',
                                                                     IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_return='0' AND 
												                         issuingh_id='$issuingh_id')!=(SELECT COUNT(*) FROM issuing_detail 
																		 WHERE issuingh_id='$issuingh_id'),'1','0')))
                                                                 WHERE issuingh_id='$issuingh_id'";
			                    $exec_update_issuing_header_status=mysqli_query($db_connection, $q_update_issuing_header_status);
								if ($exec_update_issuing_header_status)
								   {
				                     mysqli_commit($db_connection);
			                         ?>
                                        <script language="javascript">
					                      opener.location.reload(true);
			    	                      window.close();
					                    </script>
				                     <?php
								   }
								else
								   {
								     mysqli_rollback($db_connection);
				                     ?>
                                        <script language="javascript">
						                  alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					                      window.location.href='javascript:history.back(1)';
					                    </script>
				                     <?php  
								   }
							  }
						   else
						      {
							    mysqli_rollback($db_connection);
				                ?>
                                  <script language="javascript">
						            alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					                window.location.href='javascript:history.back(1)';
					              </script>
				                <?php 
							  }
						 }
					  else
					     {
						   mysqli_rollback($db_connection);
				           ?>
                             <script language="javascript">
						       alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					           window.location.href='javascript:history.back(1)';
					         </script>
				           <?php 
						 } 
					}
				 else
				    {
					  mysqli_rollback($db_connection);
				      ?>
                         <script language="javascript">
						   alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					       window.location.href='javascript:history.back(1)';
					     </script>
				      <?php 
					}
               }
			else
			   {
			      mysqli_rollback($db_connection);
				  ?>
                     <script language="javascript">
						alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					    window.location.href='javascript:history.back(1)';
					 </script>
				   <?php  
			   }
		  }

?>		  