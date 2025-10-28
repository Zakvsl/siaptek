<?php
  include "../../library/check_session.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  
  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction);    
  $created_time=get_create_time($db_connection); 
  
	$maker=$_SESSION['ses_user_id'];
	$woh_id=htmlspecialchars($_POST['txt_id']);
	$m=substr(htmlspecialchars($_POST['txt_woh_date']),3,2);
	$y=substr(htmlspecialchars($_POST['txt_woh_date']),6,4); 
	$woh_date=get_date_2(htmlspecialchars($_POST['txt_woh_date']));
	$woh_sources=$_POST['rb_woh_sources'];
	$brokh_id=$_POST['s_broken'];
	$woh_reason=htmlspecialchars(trim($_POST['txt_woh_reason']));
	$woh_notes=htmlspecialchars($_POST['txt_woh_notes']);
	$date_is_greater="No";
	if ($woh_sources=='1')
	   { 
		 $q_check_broken_id="SELECT * FROM broken_header WHERE brokh_id='$brokh_id'";
		 $exec_check_broken_id=mysqli_query($db_connection, $q_check_broken_id);
		 $total_broken=mysqli_num_rows($exec_check_broken_id);
		 if ($total_broken>0)
			{
			  $field_broken_header=mysqli_fetch_array($exec_check_broken_id);
			  if ($woh_date<$field_broken_header['brokh_date'])
				  $date_is_greater="Yes";
			}
	   }	
		
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
   if ($woh_sources=='1' && $total_broken==0)
	  {
		?>
			<script language="javascript">
			  alert('No Transaksi Kerusakan tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	  }
   else
   if ($date_is_greater=='Yes')
	  {
		?>
		   <script language="javascript">
			 alert('Tanggal penghapusan aset harus lebih besar atau sama dengan tanggal kerusakannya!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php		    
	  }
   else
   if ($woh_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$woh_code=get_no_transaction($db_connection, 'WO',$branch_id);
		if ($woh_code!='')
		{
			$woh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_write_off_header="select * from write_off_header where woh_code='$woh_code' and branch_id='$branch_id'";
			$exec_check_write_off_header=mysqli_query($db_connection, $q_check_write_off_header);
			if (mysqli_num_rows($exec_check_write_off_header)>0)
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
						   $wod_notes='txt_wod_notes_'.$field_data;
							   
						   if ($input_data=='')
							   $input_data="VALUES ((SELECT woh_id FROM write_off_header WHERE branch_id='$branch_id' AND woh_code='$woh_code'), '".$_POST[$brokd_id]."', '".$field_data."','1','0','".htmlspecialchars($_POST[$wod_notes])."')";
						   else	  
							   $input_data=$input_data.", ((SELECT woh_id FROM write_off_header WHERE branch_id='$branch_id' AND woh_code='$woh_code'), '".$_POST[$brokd_id]."', '".$field_data."','1','0','".htmlspecialchars($_POST[$wod_notes])."')";
						 }
							 
			  //       echo $rir_storage."<br>";
					 if ($woh_sources=='0')
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
											  item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id) AND brokd_id IN ($broken_id) AND brokd_is_canceled='0' AND
											  brokh_id='$brokh_id'";
													
				  //   echo $q_check_item."<br>";
					 $exec_check_item=mysqli_query($db_connection, $q_check_item);
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
						  $woh_id="";
						  $woh_date_old="";
						  $woh_date_new=$woh_date;
						  $woh_created_time=$created_time;
						  $messages=is_there_any_new_trans($db_connection, $action, $woh_id, $itemd_id, $woh_date_old, $woh_date_new, $woh_created_time);
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
							   $q_input_write_off_header="INSERT INTO write_off_header (branch_id, woh_code, woh_date, woh_sources, brokh_id, woh_reason, woh_is_canceled, 
																						woh_notes, created_by, created_time)
																				VALUES ('$branch_id','$woh_code', '$woh_date', '$woh_sources', '$brokh_id', '$woh_reason', 
																						'0','$woh_notes','$maker','$created_time')";
							   $q_input_write_off_detail="INSERT INTO write_off_detail (woh_id, brokd_id, itemd_id, wod_qty, wod_is_canceled, wod_notes) ".$input_data;
							   $q_update_item_detail="UPDATE item_detail, write_off_detail, write_off_header 
															 SET itemd_position='Dihapus', itemd_is_wo='1', itemd_status='1' 
													  WHERE item_detail.itemd_id=write_off_detail.itemd_id AND write_off_detail.woh_id=write_off_header.woh_id AND
															item_detail.branch_id='$branch_id' AND write_off_header.branch_id='$branch_id' AND woh_code='$woh_code' AND
															item_detail.itemd_id IN ($itemd_id)";
							   $exec_input_write_off_header=mysqli_query($db_connection, $q_input_write_off_header);
							   $exec_input_write_off_detail=mysqli_query($db_connection, $q_input_write_off_detail);
							   $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
							   
							   if ($woh_sources=='1')
								  {  
									$q_update_broken_detail_status="UPDATE broken_detail, write_off_detail, write_off_header 
																		   SET brokd_is_wo='1'
																	WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND  brokd_is_canceled='0' AND 
																		  write_off_detail.woh_id=write_off_header.woh_id AND
																		  wod_is_canceled='0' AND woh_code='$woh_code' AND '$branch_id' AND 
																		  broken_detail.brokd_id IN ($broken_id)";
									$q_check_broken_1="SELECT COUNT(*) AS total 
													   FROM broken_detail 
													   INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
													   WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND broken_detail.brokh_id='$brokh_id'";
									$q_check_broken_2="SELECT COUNT(*) AS total_wo 
													   FROM broken_detail 
													   INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
													   WHERE brokd_is_canceled='0' AND brokd_is_wo='1' AND brokh_is_canceled='0' AND broken_detail.brokh_id='$brokh_id'";
									$q_check_broken_3="SELECT COUNT(*) AS total_dispossed 
													   FROM broken_detail 
													   INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
													   WHERE brokd_is_canceled='0' AND brokd_is_dispossed='1' AND brokh_is_canceled='0' AND 
															 broken_detail.brokh_id='$brokh_id'";
									$exec_update_broken_detail_status=mysqli_query($db_connection, $q_update_broken_detail_status);
									$exec_check_broken_1=mysqli_query($db_connection, $q_check_broken_1);
									$exec_check_broken_2=mysqli_query($db_connection, $q_check_broken_2);
									$exec_check_broken_3=mysqli_query($db_connection, $q_check_broken_3);
									
									$field_check_broken_1=mysqli_fetch_array($exec_check_broken_1);
									$field_check_broken_2=mysqli_fetch_array($exec_check_broken_2);
									$field_check_broken_3=mysqli_fetch_array($exec_check_broken_3);
									$total_broken=$field_check_broken_1['total'];
									$total_wo=$field_check_broken_2['total_wo'];
									$total_dispossed=$field_check_broken_3['total_dispossed'];
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
										 $exec_update_broken_header_status=mysqli_query($db_connection, $q_update_broken_header_status);
										 if (!$exec_update_broken_detail_status || !$exec_update_broken_header_status)
											{
											  $is_continue='0';
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
										 $is_continue='0';
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
									if ($exec_input_write_off_header && $exec_input_write_off_detail && $exec_update_item_detail)
									   {
										 update_runing_no($db_connection, 'WO',$branch_id);
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
   else   // jika update data
	  {
		$woh_code=htmlspecialchars(trim($_POST['txt_code']));
		$woh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_write_off="select write_off_header.*, MONTH(woh_date) AS month, YEAR(woh_date) AS year  
							from write_off_header where woh_id='$woh_id' and branch_id='$branch_id'";
		$exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
		$field_check_write_off=mysqli_fetch_array($exec_check_write_off);

		$active_period=check_active_period($db_connection, $field_check_write_off['month'], $field_check_write_off['year']);	
	
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
		if ($field_check_write_off['woh_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Penghapusan Aset tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_write_off)>0)
		   {
			 $woh_original_date=$field_check_write_off['woh_date'];
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
					   $wod_notes='txt_wod_notes_'.$field_data;
					   $wod_is_canceled='cb_wod_is_canceled_'.$field_data;
					   if (isset($_POST[$wod_is_canceled])=='')
						   $is_canceled='0';
					   else 
						   $is_canceled=$_POST[$wod_is_canceled];	
						   
					   if ($input_data=='')
						   $input_data="VALUES ('$woh_id', '".$_POST[$brokd_id]."', '".$field_data."','1','".$is_canceled."','".htmlspecialchars($_POST[$wod_notes])."')";
					   else	  
						   $input_data=$input_data.", ('$woh_id', '".$_POST[$brokd_id]."', '".$field_data."','1','".$is_canceled."','".htmlspecialchars($_POST[$wod_notes])."')";
					 }
				 
			 $tube_id_prev='';
			 $brokd_id_prev='';
			 $q_get_wo_detail="SELECT * FROM write_off_detail WHERE woh_id='$woh_id' AND wod_is_canceled='0'";
			 $exec_get_wo_detail=mysqli_query($db_connection, $q_get_wo_detail);
			 while ($field_wo_detail=mysqli_fetch_array($exec_get_wo_detail))
				   {
					 if ($tube_id_prev=='')
						 $tube_id_prev="'".$field_wo_detail['itemd_id']."'";
					 else
						 $tube_id_prev=$tube_id_prev.",'".$field_wo_detail['itemd_id']."'";
							  
					 if ($woh_sources=='1')
						{
						  if ($brokd_id_prev=='')
							  $brokd_id_prev="'".$field_wo_detail['brokd_id']."'";
						  else
							  $brokd_id_prev=$brokd_id_prev.",'".$field_wo_detail['brokd_id']."'";
						}
				   }
			 
			 $action="EDIT";
			 $woh_date_old=$field_check_write_off['woh_date'];
			 $woh_date_new=$woh_date;
			 $woh_created_time=$field_check_write_off['created_time'];
			 $messages=is_there_any_new_trans($db_connection, $action, $woh_id, $itemd_id, $woh_date_old, $woh_date_new, $woh_created_time);
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
					  if ($woh_sources=='1')
						 {
						   $q_update_broken_detail_status="UPDATE broken_detail, write_off_detail 
																  SET brokd_is_wo='0' 
														   WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND woh_id='$woh_id' AND brokd_is_canceled='0' AND
																 broken_detail.brokd_id IN ($brokd_id_prev)";
						   $exec_update_broken_detail_status=mysqli_query($db_connection, $q_update_broken_detail_status);
						   if (!$exec_update_broken_detail_status)
							  {
								 $is_continue='0';
								 mysqli_rollback($db_connection);
								 ?>
									<script language="javascript">
									  alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
									  window.location.href='javascript:history.back(1)';
									</script>
								 <?php 
								 exit; 
							  }
						 }
						 
					  if ($is_continue=='1')
						 {
						   $q_update_item_detail="UPDATE item_detail, write_off_detail, write_off_header SET itemd_position='Internal', itemd_is_wo='0', itemd_status='0' 
												  WHERE item_detail.itemd_id=write_off_detail.itemd_id AND write_off_detail.woh_id=write_off_header.woh_id AND
														item_detail.branch_id='$branch_id' AND write_off_header.branch_id='$branch_id' AND write_off_detail.woh_id='$woh_id'
														AND wod_is_canceled='0' AND item_detail.itemd_id IN ($tube_id_prev)";	
						   $q_delete_write_off_detail="DELETE FROM write_off_detail WHERE woh_id='$woh_id'";
						   $exec_upadte_item_detail=mysqli_query($db_connection, $q_update_item_detail);	
						   $exec_delete_write_off_detail=mysqli_query($db_connection, $q_delete_write_off_detail);
						   if (!$exec_upadte_item_detail || !$exec_delete_write_off_detail)
							  {
								$is_continue='0';
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
								if ($woh_sources=='0')
									$q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, 
														  itemd_qty, uom_id_2,
														 (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
														 (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
												   FROM item_detail
												   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
												   INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
												   WHERE itemd_is_broken='0' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND 
														 itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN ($itemd_id)";
								else
									$q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, 
														  itemd_qty, uom_id_2,
												  (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
												  (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
												   FROM item_detail
												   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
												   INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
												   INNER JOIN broken_detail ON broken_detail.itemd_id=item_detail.itemd_id
												   WHERE itemd_is_broken='1' AND itemd_is_wo='0' AND  itemd_is_dispossed='0' AND itemd_status='0' AND 
														 itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id) AND 
														 brokd_id IN ($broken_id)";				

								$exec_check_item=mysqli_query($db_connection, $q_check_item);
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
										  if ($woh_code==$woh_code_1)
											  input_write_off($branch_id,$woh_id,$brokh_id,$woh_code,$woh_date,$woh_sources,$woh_reason,$woh_notes,$itemd_id,
															  $maker,$input_data);
										  else
											 {
											   $q_check_new_write_off="SELECT * FROM write_off_header WHERE branch_id='$branch_id' AND woh_code='$woh_code'";
											   $exec_check_new_write_off=mysqli_query($db_connection, $q_check_new_write_off);
											   if (mysqli_num_rows($exec_check_new_write_off)>0)
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
												  input_write_off($branch_id,$woh_id,$brokh_id,$woh_code,$woh_date,$woh_sources,$woh_reason,$woh_notes,$itemd_id,
																  $maker,$input_data);
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
					 alert('Transaksi Penghapusan Aset yang akan diupdate tidak ditemukan!');
					 window.close();
				 </script>
			 <?php  
		   }     
	  }	
	  
	 
 function input_write_off($branch_id,$woh_id,$brokh_id,$woh_code,$woh_date,$woh_sources,$woh_reason,$woh_notes,$itemd_id,$maker,$input_data,$db_connection)
          {
		    $is_continue='1';
		    $current_date=date('Y-m-d');
			if ($is_continue=='1')
			   {  
                 $q_input_write_off_header="UPDATE write_off_header SET woh_code='$woh_code', woh_date='$woh_date', woh_sources='$woh_sources', brokh_id='$brokh_id', 
			                                       woh_reason='$woh_reason', woh_notes='$woh_notes', updated_by='$maker', updated_time=NOW()
                                            WHERE branch_id='$branch_id' AND woh_id='$woh_id'";
			     $q_input_write_off_detail="INSERT INTO write_off_detail (woh_id, brokd_id, itemd_id, wod_qty, wod_is_canceled, wod_notes) ".$input_data;
			      $q_update_item_detail="UPDATE item_detail, write_off_detail 
                                                SET itemd_position='Dihapus', itemd_is_wo='1', itemd_status='1' and wod_is_canceled='0'
                                         WHERE item_detail.itemd_id=write_off_detail.itemd_id AND woh_id='$woh_id' AND item_detail.itemd_id IN ($itemd_id) AND
										       wod_is_canceled='0'"; 
			     $exec_input_write_off_header=mysqli_query($db_connection, $q_input_write_off_header);
			     $exec_input_write_off_detail=mysqli_query($db_connection, $q_input_write_off_detail);
			     $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
			     if (!$exec_input_write_off_header || !$exec_input_write_off_detail || !$exec_update_item_detail)
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
					  if ($woh_sources=='1')
			             {  
			               $q_update_broken_detail_status="UPDATE broken_detail, write_off_detail, write_off_header 
                                                                  SET brokd_is_wo='1'
                                                           WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND write_off_detail.woh_id=write_off_header.woh_id AND
                                                                 wod_is_canceled='0' AND write_off_detail.woh_id='$woh_id'";
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
				 
				           $exec_update_broken_detail_status=mysqli_query($db_connection, $q_update_broken_detail_status);
				           $exec_check_broken_1=mysqli_query($db_connection, $q_check_broken_1);
				           $exec_check_broken_2=mysqli_query($db_connection, $q_check_broken_2);
				           $exec_check_broken_3=mysqli_query($db_connection, $q_check_broken_3);
				 
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
					            $exec_update_broken_header_status=mysqli_query($db_connection, $q_update_broken_header_status);
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