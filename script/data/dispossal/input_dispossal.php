<?php
  include "../../library/check_session.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";
  include "../../library/library_function.php";

  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction); 
  $created_time=get_create_time($db_connection);
  $is_continue='0';
  
  //$branch_id=htmlspecialchars($_GET['b']);
	$disph_id=htmlspecialchars($_POST['txt_id']);
	$m=substr(htmlspecialchars($_POST['txt_disph_date']),3,2);
	$y=substr(htmlspecialchars($_POST['txt_disph_date']),6,4); 
	$disph_date=get_date_2(htmlspecialchars($_POST['txt_disph_date']));
	$disph_sources=$_POST['rb_disph_sources'];
	$brokh_id=$_POST['s_broken'];
	$disph_reason=htmlspecialchars(trim($_POST['txt_disph_reason']));
	$cust_id=htmlspecialchars(trim($_POST['s_customer']));
	$emp_id=htmlspecialchars(trim($_POST['s_employee']));
	$disph_notes=htmlspecialchars($_POST['txt_disph_notes']);
	$date_is_greater="No";
	if ($disph_sources=='1')
	   { 
		 $q_check_broken_id="SELECT * FROM broken_header WHERE brokh_id='$brokh_id'";
		 $exec_check_broken_id=mysqli_query($db_connection,$q_check_broken_id);
		 $total_broken=mysqli_num_rows($exec_check_broken_id);
		 if ($total_broken>0)
			{
			  $field_broken_header=mysqli_fetch_array($exec_check_broken_id);
			  if ($disph_date<$field_broken_header['brokh_date'])
				  $date_is_greater="Yes";
			}
	   }	 
	$q_check_customer="SELECT cust_id FROM customer WHERE cust_id='$cust_id' AND cust_type='0' AND branch_id='$branch_id'";
	$q_check_employee="SELECT * FROM employee WHERE emp_id='$emp_id'";
	$exec_customer=mysqli_query($db_connection,$q_check_customer);
	$exec_check_employee=mysqli_query($db_connection,$q_check_employee);
	
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
	if (mysqli_num_rows($exec_customer)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Nama Customer tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if ($disph_sources=='1' && $total_broken==0)
	  {
		?>
			<script language="javascript">
			  alert('Kode Kerusakan tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	  }
   else
   if (mysqli_num_rows($exec_check_employee)==0)
	  {
		?>
		   <script language="javascript">
			 alert('Penjual tidak ditemukan!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php
	  }
   else
   if ($date_is_greater=='Yes')
	  {
		?>
		   <script language="javascript">
			 alert('Tanggal penjualan aset harus lebih besar atau sama dengan tanggal kerusakannya!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php		    
	  }
   else
   if ($disph_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$disph_code=get_no_transaction($db_connection, 'DSP',$branch_id);
		if ($disph_code!='')
		{
			$disph_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_dispossal_header="select * from dispossal_header where disph_code='$disph_code' and branch_id='$branch_id'";
			$exec_check_dispossal_header=mysqli_query($db_connection,$q_check_dispossal_header);
			if (mysqli_num_rows($exec_check_dispossal_header)>0)
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
			   {
				 $itemd_id="";
				 $input_data='';
				 $data=$_POST['cb_data'];
				 $detail_qty=0;
				 $total=count($data);
				 foreach ($data as $field_data)
						 {
						   if ($itemd_id=='')
							  {
								$itemd_id="'$field_data'";
								$id_broken='txt_brokd_id_'.$field_data;
								$broken_id="'".$_POST[$id_broken]."'";
							  }	
						   else
							  {
								$itemd_id=$itemd_id.",'$field_data'";
								$id_broken='txt_brokd_id_'.$field_data;
								$broken_id=$broken_id.", '".$_POST[$id_broken]."'";
							  }	
						   
						   $brokd_id='txt_brokd_id_'.$field_data;	    
						   $dispd_notes='txt_dispd_notes_'.$field_data;
							   
						   if ($input_data=='')
							   $input_data="VALUES ((SELECT disph_id FROM dispossal_header WHERE branch_id='$branch_id' AND disph_code='$disph_code'), '".$_POST[$brokd_id]."', '".$field_data."','1','0','".htmlspecialchars($_POST[$dispd_notes])."')";
						   else	  
							   $input_data=$input_data.", ((SELECT disph_id FROM dispossal_header WHERE branch_id='$branch_id' AND disph_code='$disph_code'), '".$_POST[$brokd_id]."', '".$field_data."','1','0','".htmlspecialchars($_POST[$dispd_notes])."')";
						 }
							 
			  //       echo $rir_storage."<br>";
					 if ($disph_sources=='0')
						 $q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									   (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, whsl_name 
										FROM item_detail
										INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
										INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
										INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
										WHERE itemd_is_broken='0' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
											  item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id)";
					 else
						 $q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									   (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, whsl_name 
										FROM item_detail
										INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
										INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
										INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
										INNER JOIN broken_detail ON broken_detail.itemd_id=item_detail.itemd_id
										WHERE itemd_is_broken='1' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
											  item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id) AND brokd_id IN ($broken_id)";
					 $exec_check_item=mysqli_query($db_connection,$q_check_item);
					 if (mysqli_num_rows($exec_check_item)==0)
						{
						  mysqli_rollback($db_connection);
						  ?>
							 <script language="javascript">
							   alert('Semua detail data tidak ditemukan!');
							   window.location.href='javascript:history.back(1)';
							 </script>
						  <?php
						}
					 else	
						{
						  if (mysqli_num_rows($exec_check_item)!=$total) 
							 {
							   mysqli_rollback($db_connection);
							   $no=1;
							   echo "<b>Beberapa detail data tidak ditemukan!</b><br>";
							   echo "<table>";  
							   echo "<tr><td>No</td><td>Kode Item</td><td>Nama Item</td><td>Serial No</td><td>Kapasitas</td></tr>";
							   while ($field_data=mysqli_fetch_array($exec_check_item))
									 {
									   echo "<tr><td>".$no++."</td><td>".$field_data['itemd_code']."</td><td>".$field_data['masti_name']."</td><td>".$field_data['itemd_serial_no']."</td><td>".$field_data['masti_capacity']."</td></tr>";
									 }
							   echo "</table>";
							 }
						  else
							 {
							   $action="NEW";
							   $disph_id="";
							   $disph_date_old="";
							   $disph_date_new=$disph_date;
							   $disph_created_time=$created_time;
							   $messages=is_there_any_new_trans($db_connection, $action, $disph_id, $itemd_id, $disph_date_old, $disph_date_new, $disph_created_time);
							   if ($messages!='1')
								  {
									mysqli_rollback($db_connection);
									?>
									   <script language="javascript">
											var message='<?php echo $messages; ?>';
											alert(message); 
											history.back(1);
									   </script>
									<?php							   
								  }		
							   else
								  {
									$is_continue='1';
									$q_input_dispossal_header="INSERT INTO dispossal_header (branch_id, disph_code, disph_date, disph_sources, brokh_id, disph_reason, cust_id, 
																							 emp_id_dispossed_by, disph_is_canceled, disph_notes, created_by, created_time)
																					 VALUES ('$branch_id','$disph_code', '$disph_date', '$disph_sources', '$brokh_id', 
																							 '$disph_reason', '$cust_id', '$emp_id', '0','$disph_notes','$maker','$created_time')";
									$q_input_dispossal_detail="INSERT INTO dispossal_detail (disph_id, brokd_id, itemd_id, dispd_qty, dispd_is_canceled, dispd_notes) ".$input_data;
									$q_update_item_detail="UPDATE item_detail, dispossal_detail, dispossal_header 
																  SET itemd_position='Customer', itemd_is_dispossed='1', itemd_status='1' 
														   WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND
																 item_detail.branch_id='$branch_id' AND dispossal_header.branch_id='$branch_id' AND disph_code='$disph_code' AND
																 item_detail.itemd_id IN ($itemd_id)";
									$exec_input_dispossal_header=mysqli_query($db_connection,$q_input_dispossal_header);
									$exec_input_dispossal_detail=mysqli_query($db_connection,$q_input_dispossal_detail);
									$exec_update_item_detail=mysqli_query($db_connection,$q_update_item_detail);
									if ($disph_sources=='1')
									   {  
										 $q_update_broken_detail_status="UPDATE broken_detail, dispossal_detail, dispossal_header 
																				SET brokd_is_dispossed='1'
																		 WHERE broken_detail.brokd_id=dispossal_detail.brokd_id AND 
																			   dispossal_detail.disph_id=dispossal_header.disph_id 
																			   AND dispossal_header.branch_id='$branch_id' AND dispd_is_canceled='0' AND 
																			   disph_code='$disph_code' AND
																			   broken_detail.brokd_id IN ($broken_id)";
										 $q_check_broken_1="SELECT COUNT(*) AS total 
															FROM broken_detail 
															INNER JOIN broken_header ON broken_detail.brokh_id=broken_header.brokh_id
															WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND broken_detail.brokh_id='$brokh_id'";
										 $q_check_broken_2="SELECT COUNT(*) AS total_wo
															FROM broken_detail 
															INNER JOIN broken_header ON broken_detail.brokh_id=broken_header.brokh_id
															WHERE brokd_is_canceled='0' AND brokd_is_wo='1' AND brokh_is_canceled='0' AND broken_detail.brokh_id='$brokh_id'";
										 $q_check_broken_3="SELECT COUNT(*) AS total_dispossed
															FROM broken_detail 
															INNER JOIN broken_header ON broken_detail.brokh_id=broken_header.brokh_id
															WHERE brokd_is_canceled='0' AND brokd_is_dispossed='1' AND brokh_is_canceled='0' AND 
																  broken_detail.brokh_id='$brokh_id'";
										 $exec_update_broken_detail_status=mysqli_query($db_connection,$q_update_broken_detail_status);
										 $exec_check_broken_1=mysqli_query($db_connection,$q_check_broken_1);
										 $exec_check_broken_2=mysqli_query($db_connection,$q_check_broken_2);
										 $exec_check_broken_3=mysqli_query($db_connection,$q_check_broken_3);
										 $field_broken_1=mysqli_fetch_array($exec_check_broken_1);
										 $field_broken_2=mysqli_fetch_array($exec_check_broken_2);
										 $field_broken_3=mysqli_fetch_array($exec_check_broken_3);
										 $total_broken=$field_broken_1['total'];
										 $total_wo=$field_broken_2['total_wo'];
										 $total_dispossed=$field_broken_3['total_dispossed'];
									
										 if ($total_broken>0 || $total_wo>0 || $total_dispossed>0)
											{
											  if ($total_wo==0 && $total_dispossed==0)
												  $status_broken='0';
											  else
											  if ($total_wo>0 && $total_wo<$total_broken && $total_dispossed==0)
												  $status_broken='1';
											  else
											  if ($total_wo==$total_broken && $total_dispossed==0)
												  $status_broken='2';
											  else
											  if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo==0)
												  $status_broken='3';
											  else
											  if ($total_dispossed==$total_broken && $total_wo==0)
												  $status_broken='4';
											  else
											  if ($total_dispossed+$total_wo==$total_broken)
												  $status_broken='6';
											  else
											  if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo>0 && $total_wo<$total_broken)
												  $status_broken='5';
										 
											  $q_update_broken_header_status="UPDATE broken_header
																					 SET brokh_status='$status_broken'
																			  WHERE brokh_id='$brokh_id'";
											 // echo $q_update_broken_header_status;
											  $exec_update_broken_header_status=mysqli_query($db_connection,$q_update_broken_header_status);
											  if (!$exec_update_broken_detail_status || !$exec_update_broken_header_status)
												 {
												   mysqli_rollback($db_connection);
												   ?>
													  <script language="javascript">
														alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
														history.back();
													  </script>
												   <?php 
												   exit;
												}
											}  
										 else
											{
											  mysqli_rollback($db_connection);
											  ?>
												 <script language="javascript">
												   alert('No Transaksi Kerusakan tidak ditemukan!');
												   window.location.href='javascript:history.back(1)';
												 </script>
											  <?php 
											  exit; 
											}
									   }
								  
									if ($is_continue=='1')
									   {	
										 if ($exec_input_dispossal_header && $exec_input_dispossal_detail && $exec_update_item_detail)
											{
											  update_runing_no($db_connection, 'DSP',$branch_id);
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
												   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
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
	  }
   else   // jika update data
	  {
		$disph_code=htmlspecialchars(trim($_POST['txt_code']));
		$disph_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_dispossal="select dispossal_header.*, MONTH(disph_date) AS month, YEAR(disph_date) AS year 
							from dispossal_header where disph_id='$disph_id' and branch_id='$branch_id'";
		$exec_check_dispossal=mysqli_query($db_connection,$q_check_dispossal);
		$field_check_dispossal=mysqli_fetch_array($exec_check_dispossal);
	
		$active_period=check_active_period($db_connection, $field_check_dispossal['month'], $field_check_dispossal['year']);	
	
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
		if ($field_check_dispossal['disph_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Penjualan tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_dispossal)>0)
		   {
			 $disph_original_date=$exec_check_dispossal['disph_date'];
			 $disph_created_time=$exec_check_dispossal['created_time'];
			 $itemd_id="";
			 $input_data='';
			 $data=$_POST['cb_data'];
			 $detail_qty=0;
			 $total=count($data);
			 foreach ($data as $field_data)
					 {
					   if ($itemd_id=='')
						  {
							$itemd_id="'$field_data'";
							$id_broken='txt_brokd_id_'.$field_data;
							$broken_id="'".$_POST[$id_broken]."'";
						  }	
					   else
						  {
							$itemd_id=$itemd_id.",'$field_data'";
							$id_broken='txt_brokd_id_'.$field_data;
							$broken_id=$broken_id.", '".$_POST[$id_broken]."'";
						  }	
						
					   $brokd_id='txt_brokd_id_'.$field_data;	 	   
					   $dispd_notes='txt_dispd_notes_'.$field_data;
					   $disp_is_canceled='cb_dispd_is_canceled_'.$field_data;
					   if (isset($_POST[$disp_is_canceled])=='')
						   $is_canceled='0';
					   else 
						   $is_canceled=$_POST[$disp_is_canceled];	
						   
					   if ($input_data=='')
						   $input_data="VALUES ('$disph_id', '".$_POST[$brokd_id]."', '".$field_data."','1','".$is_canceled."','".htmlspecialchars($_POST[$dispd_notes])."')";
					   else	  
						   $input_data=$input_data.", ('$disph_id', '".$_POST[$brokd_id]."', '".$field_data."','1','".$is_canceled."','".htmlspecialchars($_POST[$dispd_notes])."')";
					 }
						 
			 $tube_id_prev='';
			 $brokd_id_prev='';
			 $q_get_dispossal_detail="SELECT * FROM dispossal_detail WHERE disph_id='$disph_id' AND dispd_is_canceled='0'";
			 $exec_get_dispossal_detail=mysqli_query($db_connection,$q_get_dispossal_detail);
			 while ($field_dispossal_detail=mysqli_fetch_array($exec_get_dispossal_detail))
				   {
					 if ($tube_id_prev=='')
						 $tube_id_prev="'".$field_dispossal_detail['itemd_id']."'";
					 else
						 $tube_id_prev=$tube_id_prev.",'".$field_dispossal_detail['itemd_id']."'";
							  
					 if ($disph_sources=='1')
						{
						  if ($brokd_id_prev=='')
							  $brokd_id_prev="'".$field_dispossal_detail['brokd_id']."'";
						  else
							  $brokd_id_prev=$brokd_id_prev.",'".$field_dispossal_detail['brokd_id']."'";
						}
				   }
			 
			 $action="EDIT";
			 $disph_date_old=$field_check_dispossal['disph_date'];
			 $disph_date_new=$disph_date;
			 $disph_created_time=$field_check_dispossal['created_time'];
			 $messages=is_there_any_new_trans($db_connection, $action, $disph_id, $itemd_id, $disph_date_old, $disph_date_new, $disph_created_time);
			 if ($messages!='1')
				{
				  ?>
					 <script language="javascript">
						var message='<?php echo $messages; ?>';
						alert(message); 
						history.back(1);
					 </script>
				  <?php							   
				}		
			 else
				{			
				  mysqli_autocommit($db_connection, false);
				  $is_continue='1';
				  if ($disph_sources=='1')
					 {
					   $q_update_broken_detail_status="UPDATE broken_detail, dispossal_detail 
															  SET brokd_is_dispossed='0' 
													   WHERE broken_detail.brokd_id=dispossal_detail.brokd_id AND disph_id='$disph_id' AND brokd_is_canceled='0' AND
															 broken_detail.brokd_id IN ($brokd_id_prev)";
					   $exec_update_broken_detail_status=mysqli_query($db_connection,$q_update_broken_detail_status);
					   if (!$exec_update_broken_detail_status)
						  {
							mysqli_rollback($db_connection);
							?>
							   <script language="javascript">
								 alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 1!');
								 window.location.href='javascript:history.back(1)';
							   </script>
							<?php 
							exit;
						  }  
					 }
					
				  if ($is_continue=='1')
					 {
					   $q_update_item_detail="UPDATE item_detail, dispossal_detail, dispossal_header 
													 SET itemd_position='Internal', itemd_is_dispossed='0', itemd_status='0'  
											  WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND 
													dispossal_header.disph_id='$disph_id' AND item_detail.branch_id='$branch_id' AND dispossal_header.branch_id='$branch_id'";		
					   $q_delete_dispossal_detail="DELETE FROM dispossal_detail WHERE disph_id='$disph_id'";
					   $exec_upadte_item_detail=mysqli_query($db_connection,$q_update_item_detail);	
					   $exec_delete_dispossal_detail=mysqli_query($db_connection,$q_delete_dispossal_detail);
					   if (!$exec_upadte_item_detail || !$exec_delete_dispossal_detail)
						  {
							mysqli_rollback($db_connection);
							?>
							   <script language="javascript">
								 alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 2!');
								 window.location.href='javascript:history.back(1)';
							   </script>
							<?php 
							exit;
						  }
					 }
						 
				  if ($is_continue=='1')
					 {	
					   $q_update_item_detail="UPDATE item_detail, dispossal_detail, dispossal_header SET itemd_position='Internal', itemd_is_dispossed='0', itemd_status='0' 
											  WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND
													item_detail.branch_id='$branch_id' AND dispossal_header.branch_id='$branch_id' AND dispossal_detail.disph_id='$disph_id'
													AND dispd_is_canceled='0' AND item_detail.itemd_id IN ($tube_id_prev)";	
					   $q_delete_dispossal_detail="DELETE FROM dispossal_detail WHERE disph_id='$disph_id'";
					   $exec_upadte_item_detail=mysqli_query($db_connection,$q_update_item_detail);	
					   $exec_delete_dispossal_detail=mysqli_query($db_connection,$q_delete_dispossal_detail);

					   if (!$exec_upadte_item_detail || !$exec_delete_dispossal_detail)
						  {
							mysqli_rollback($db_connection);
							?>
							   <script language="javascript">
								  alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
								  window.location.href='javascript:history.back(1)';
							   </script>
							 <?php
							 exit; 
						  }
					   else
						  {
							if ($disph_sources=='0')
								$q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
											  (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
											   FROM item_detail
											   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
											   INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
											   WHERE itemd_is_broken='0' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
													 item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id)";
							else
								$q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
											  (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
											   FROM item_detail
											   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
											   INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
											   INNER JOIN broken_detail ON broken_detail.itemd_id=item_detail.itemd_id
											   WHERE itemd_is_broken='1' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
													 item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id) AND brokd_id IN ($broken_id)";				

							$exec_check_item=mysqli_query($db_connection,$q_check_item);
							if (mysqli_num_rows($exec_check_item)==0)
							   {
								 mysqli_rollback($db_connection);
								 ?>
									<script language="javascript">
									  alert('Semua detail data tidak ditemukan!');
									  window.location.href='javascript:history.back(1)';
									</script>
								 <?php
							   }
							else
							   {
								 if (mysqli_num_rows($exec_check_item)!=$total) 
									{
									  mysqli_rollback($db_connection);
									  $no=0;
									  echo "<b>Beberapa detail data tidak ditemukan!</b><br>";
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
									  if ($disph_code==$disph_code_1)
										  input_dispossal($branch_id,$disph_id,$disph_code,$disph_date,$disph_sources,$disph_reason,$cust_id,$emp_id,$disph_notes,$itemd_id,
														  $maker, $brokh_id, $input_data);
									  else
										 {
										   $q_check_new_dispossal="SELECT * FROM dispossal_header WHERE branch_id='$branch_id' AND disph_code='$disph_code'";
										   $exec_check_new_dispossal=mysqli_query($db_connection,$q_check_new_dispossal);
										   if (mysqli_num_rows($exec_check_new_dispossal)>0)
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
											  input_dispossal($branch_id,$disph_id,$disph_code,$disph_date,$disph_sources,$disph_reason,$cust_id,$emp_id,$disph_notes, 
															  $itemd_id, $maker, $brokh_id, $input_data);
										 }
									}
							   }
						  }	 
					 }
				}
		   }
		else
		   {
			 ?>
				<script language="javascript">
				  alert('Transaksi Pengembalian yang akan diupdate tidak ditemukan!');
				  window.close();
				</script>
			  <?php  
		   }     
	  }	
	  
	 
 function input_dispossal($branch_id, $disph_id, $disph_code, $disph_date, $disph_sources, $disph_reason, $cust_id, $emp_id, $disph_notes, $itemd_id,
                          $maker, $brokh_id, $input_data, $db_connection)
          {
		    $is_continue='1';
		    $current_date=date('Y-m-d');   
			if ($is_continue=='1')
			   {  
                 $q_input_dispossal_header="UPDATE dispossal_header SET disph_code='$disph_code', disph_date='$disph_date', disph_sources='$disph_sources', brokh_id='$brokh_id', 
			                                       disph_reason='$disph_reason', disph_notes='$disph_notes', updated_by='$maker', updated_time=NOW()
                                            WHERE branch_id='$branch_id' AND disph_id='$disph_id'";
			     $q_input_dispossal_detail="INSERT INTO dispossal_detail (disph_id, brokd_id, itemd_id, dispd_qty, dispd_is_canceled, dispd_notes) ".$input_data;
			     $q_update_item_detail="UPDATE item_detail, dispossal_detail 
                                               SET itemd_position='Customer', itemd_is_dispossed='1', itemd_status='1' and dispd_is_canceled='0'
                                        WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND disph_id='$disph_id' AND item_detail.itemd_id IN ($itemd_id) AND 
										      dispd_is_canceled='0'"; 
			     $exec_input_dispossal_header=mysqli_query($db_connection,$q_input_dispossal_header);
			     $exec_input_dispossal_detail=mysqli_query($db_connection,$q_input_dispossal_detail);
			     $exec_update_item_detail=mysqli_query($db_connection,$q_update_item_detail);
			     if (!$exec_input_dispossal_header || !$exec_input_dispossal_detail || !$exec_update_item_detail)
			        { 
					  $is_continue='0';
				      mysqli_rollback($db_connection);
				      ?>
                         <script language="javascript">
						    alert('Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					        window.location.href='javascript:history.back(1)';
					     </script>
				      <?php 
				    }
				 
				 if ($is_continue=='1')
				    {
			          if ($disph_sources=='1')
			             {  				   
			               $q_update_broken_detail_status="UPDATE broken_detail, dispossal_detail, dispossal_header 
                                                                  SET brokd_is_dispossed='1'
                                                           WHERE broken_detail.brokd_id=dispossal_detail.brokd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND
                                                                 dispd_is_canceled='0' AND dispossal_detail.disph_id='$disph_id'";
				           $q_check_broken_1="SELECT COUNT(*) AS total 
                                              FROM broken_detail 
                                              INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
                                              WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND broken_header.brokh_id='$brokh_id'";
				           $q_check_broken_2="SELECT COUNT(*) AS total_wo 
                                              FROM broken_detail 
                                              INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
                                              WHERE brokd_is_canceled='0' AND brokd_is_wo='1' AND brokh_is_canceled='0' AND broken_header.brokh_id='$brokh_id'";
				           $q_check_broken_3="SELECT COUNT(*) AS total_dispossed 
                                              FROM broken_detail 
                                              INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
                                              WHERE  brokd_is_canceled='0' AND brokd_is_dispossed='1' AND brokh_is_canceled='0' AND  broken_header.brokh_id='$brokh_id'"; 
				           $exec_update_broken_detail_status=mysqli_query($db_connection,$q_update_broken_detail_status);
				 
				           $exec_check_broken_1=mysqli_query($db_connection,$q_check_broken_1);
				           $exec_check_broken_2=mysqli_query($db_connection,$q_check_broken_2);
				           $exec_check_broken_3=mysqli_query($db_connection,$q_check_broken_3);
				 
				           $field_check_broken_1=mysqli_fetch_array($exec_check_broken_1);
				           $field_check_broken_2=mysqli_fetch_array($exec_check_broken_2);
				           $field_check_broken_3=mysqli_fetch_array($exec_check_broken_3);
				 
				           $total_broken=$field_check_broken_1['total'];
				           $total_wo=$field_check_broken_2['total_wo'];
				           $total_dispossed=$field_check_broken_3['total_dispossed'];	
				           if ($total_broken>0 or $total_wo>0 or $total_dispossed>0)	
				              {
					            if ($total_wo==0 && $total_dispossed==0)
					                $status_broken='0';
					            else
					            if ($total_wo>0 && $total_wo<$total_broken && $total_dispossed==0)
					                $status_broken='1';
					            else
					            if ($total_wo==$total_broken && $total_dispossed==0)
					                $status_broken='2';
					            else
					            if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo==0)
					                $status_broken='3';
					            else
					            if ($total_dispossed==$total_dispossed && $total_wo==0)
					                 $status_broken='4';
					            else
					            if ($total_dispossed+$total_wo==$total_broken)
					                $status_broken='6';
					            else
					            if ($total_dispossed>0 && $total_dispossed<$total_broken && $total_wo>0 && $total_wo<$total_broken)
					                 $status_broken='5';
											 
					            $q_update_broken_header_status="UPDATE broken_header
                                                                       SET brokh_status='$status_broken'
                                                                WHERE brokh_id='$brokh_id'";
					            $exec_update_broken_header_status=mysqli_query($db_connection,$q_update_broken_header_status);
					            if ($exec_update_broken_detail_status && $exec_update_broken_header_status)
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
						                   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
							               history.back();
					                    </script>
				                      <?php 
						            }
				              }  
				           else
					          {
					            mysqli_rollback($db_connection);
					            ?>
                                   <script language="javascript">
						              alert('No Transaksi Kerusakan tidak ditemukan!');
						              window.location.href='javascript:history.back(1)';
					               </script>
				                <?php 
					          }  
			             }
			          else
			             {
			               mysqli_commit($db_connection);
			               ?>
                               <script language="javascript">
					              opener.location.reload(true);
					              window.close();
					           </script>
				           <?php 
						 }
					}
			   }
		  }

?>		  