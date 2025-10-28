<?php
  include "../../library/check_session.php";
  include "../../library/library_function.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction);
  $created_time=get_create_time($db_connection); 
  
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";	
  $rth_branch_id=$_POST['s_branch'];
  $rth_id=htmlspecialchars($_POST['txt_id']);
  $tth_id=$_POST['s_tth_id'];
  $m=substr(htmlspecialchars($_POST['txt_rth_date']),3,2);
  $y=substr(htmlspecialchars($_POST['txt_rth_date']),6,4); 
  $rth_date=get_date_2(htmlspecialchars($_POST['txt_rth_date']));
  $rth_receiver=htmlspecialchars(trim($_POST['s_employee']));
  $rth_notes=htmlspecialchars($_POST['txt_rth_notes']);
  $rth_ba_no=htmlspecialchars($_POST['txt_ba_no']);
  $rth_po_no=htmlspecialchars($_POST['txt_po_no']);
  if ($rth_id=='')
      $q_check_tth_id="SELECT * FROM transfer_header 
		               WHERE tth_status IN (0,1) AND branch_id='$rth_branch_id' AND tth_is_canceled='0' AND branch_id_to='$branch_id' AND tth_id='$tth_id'";
  else
	  $q_check_tth_id="SELECT * FROM transfer_header 
		               WHERE branch_id='$rth_branch_id' AND tth_is_canceled='0' AND branch_id_to='$branch_id' AND tth_id='$tth_id'";
  $q_check_employee="SELECT * FROM employee WHERE emp_id='$rth_receiver'";
  $exec_check_tth_id=mysqli_query($db_connection, $q_check_tth_id);
  $exec_check_employee=mysqli_query($db_connection, $q_check_employee);
  $field_transfer_header=mysqli_fetch_array($exec_check_tth_id);
  
  $active_period=check_active_period($db_connection, $m, $y);	
  $is_continue=0;
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
   if (mysqli_num_rows($exec_check_tth_id)==0)
	  {
		?>
		   <script language="javascript">
			 alert('No Referensi tidak ditemukan!');
			 window.location.href='javascript:history.back(1)';
		   </script>
		<?php
	  }
   else
   if ($rth_date<$field_transfer_header['tth_date'])
	  {
		?>
		   <script language="javascript">
			 alert('Tanggal Penerimaan Transfer Aset harus lebih besar atau sama dengan Tanggal Transfernya!');
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
   if ($rth_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$rth_code=get_no_transaction($db_connection, 'RTT',$branch_id);
		if ($rth_code!='')
		{
			$rth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_receipt_transfer_header="select * from receipt_transfer_header where rth_code='$rth_code' and branch_id='$branch_id'";
			$exec_check_receipt_transfer_header=mysqli_query($db_connection, $q_check_receipt_transfer_header);
			if (mysqli_num_rows($exec_check_receipt_transfer_header)>0)
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
				 $input_data='';
				 $ttd_id='';
				 $whsld_id='';
				 $data=$_POST['cb_data'];
				 $detail_qty=0;
				 $total=count($data);
				 foreach ($data as $field_data)
						 {
						   if ($ttd_id=='')
							   $ttd_id="'$field_data'";
						   else
							   $ttd_id=$ttd_id.",'$field_data'";
							   
						   $ttd_id_1='rtd_id_'.$field_data;
						   $whsld_id='s_whsld_'.$field_data;
						   $rtd_notes='txt_rtd_notes_'.$field_data;
						   if ($input_data=='')
							  {
								$input_data="VALUES ((SELECT rth_id FROM receipt_transfer_header WHERE branch_id='$branch_id' AND rth_code='$rth_code'), '".$field_data."',
													 (SELECT itemd_id FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),
													 (SELECT whsl_id_from FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),'".$_POST[$whsld_id]."','0','".htmlspecialchars($_POST[$rtd_notes])."')";
							  } 
						   else
							  {	  
								$input_data=$input_data.", ((SELECT rth_id FROM receipt_transfer_header WHERE branch_id='$branch_id' AND rth_code='$rth_code'), '".$field_data."',
															(SELECT itemd_id FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),
															(SELECT whsl_id_from FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),'".$_POST[$whsld_id]."','0','".htmlspecialchars($_POST[$rtd_notes])."')";
							  }
						 }
							 
				 $q_check_ttd="SELECT itemd_code, masti_name, itemd_serial_no 
							   FROM transfer_detail
							   INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
							   INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
							   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
							   WHERE ttd_is_canceled='0' AND ttd_status='0' AND transfer_detail.ttd_id IN($ttd_id) AND itemd_position='In Transit' AND  
									 item_detail.branch_id='$rth_branch_id' AND transfer_header.branch_id='$rth_branch_id' AND warehouse_location.branch_id='$rth_branch_id'
									 AND tth_is_canceled='0' AND tth_status!='2' AND transfer_detail.tth_id='$tth_id'";
				 // echo $q_check_ttd;
				 $exec_check_ttd=mysqli_query($db_connection, $q_check_ttd);
				 if (mysqli_num_rows($exec_check_ttd)==0)
					{
					  mysqli_rollback($db_connection);
					  ?>
						 <script language="javascript">
							alert('Semua Aset tidak ditemukan!');
							window.location.href='javascript:history.back(1)';
						 </script>
					  <?php
					}
				 else	
					{
					  if (mysqli_num_rows($exec_check_ttd)!=$total) 
						 {
						   mysqli_rollback($db_connection);
						   $no=0;
						   echo "<b>Beberapa Aset tidak ditemukan!</b><br>";
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
						   $q_input_receipt_transfer_header="INSERT INTO receipt_transfer_header (branch_id, tth_id, rth_code, rth_date, branch_id_from, emp_id_receiver, 
																		 rth_is_canceled, rth_notes, rth_ba_no, rth_po_no, created_by, created_time, updated_by, updated_time)
																 VALUES ('$branch_id','$tth_id','$rth_code', '$rth_date', '$rth_branch_id', '$rth_receiver', '0', 
																		 '$rth_notes','$rth_ba_no','$rth_po_no','$maker','$created_time','$maker','$created_time')";
						   $exec_input_receipt_transfer_header=mysqli_query($db_connection, $q_input_receipt_transfer_header);
						   if ($exec_input_receipt_transfer_header)
							  { 
								$q_input_receipt_transfer_detail="INSERT INTO receipt_transfer_detail (rth_id, ttd_id, itemd_id, whsl_id_old, whsl_id_new, rtd_is_canceled, 
																									   rtd_notes) ".$input_data;
								//echo $q_input_historical_trans."<br>";
								$exec_input_receipt_transfer_detail=mysqli_query($db_connection, $q_input_receipt_transfer_detail);
								if ($exec_input_receipt_transfer_detail)
								   {
									 $q_update_transfer_detail="UPDATE transfer_detail SET ttd_status='1'
																WHERE tth_id='$tth_id' AND ttd_id IN ($ttd_id)";
									 $exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
									 if ($exec_update_transfer_detail)
										{
										  $q_get_rth_code="SELECT rth_id FROM receipt_transfer_header WHERE rth_code='$rth_code' AND branch_id='$branch_id'";
										  $exec_get_rth_code=mysqli_query($db_connection, $q_get_rth_code);
										  $field_rth_code=mysqli_fetch_array($exec_get_rth_code);
										  $q_get_tube_id="SELECT TD.itemd_id, whsl_id_new  
														  FROM transfer_detail TD
														  INNER JOIN receipt_transfer_detail RTD ON RTD.ttd_id=TD.ttd_id
														  WHERE tth_id='$tth_id' AND 
															    rth_id='".$field_rth_code['rth_id']."' AND TD.ttd_id IN ($ttd_id)";
										  $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
										  while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
												{
												  $q_update_item_detail="UPDATE item_detail SET branch_id='$branch_id', whsl_id='".$field_tube_id['whsl_id_new']."',
																				itemd_position='Internal'
																		 WHERE itemd_id='".$field_tube_id['itemd_id']."'";
												  $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
												  if (!$exec_update_item_detail)
													 {
													   $is_continue=1;
													   mysqli_rollback($db_connection);
													   ?>
														  <script language="javascript">
															  alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
															  window.location.href='javascript:history.back(1)';
														  </script> 
													   <?php
													   break;
													   exit;
													 }
												 }
										   
										   if ($is_continue==0)
										   {  
											   $q_update_transfer_header_status="UPDATE transfer_header 
																						SET tth_status=(IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND 
																							ttd_is_canceled='0' AND tth_id='$tth_id')=0,'2',
																						IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND ttd_is_canceled='0' AND 
																							tth_id='$tth_id')!=(SELECT COUNT(*) FROM transfer_detail 
																												WHERE tth_id='$tth_id'),'1','0')))
																				 WHERE tth_id='$tth_id'";
											   $exec_update_transfer_header_status=mysqli_query($db_connection, $q_update_transfer_header_status);
											   if ($exec_update_transfer_header_status)
												  {
													update_runing_no($db_connection, 'RTT',$branch_id);
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
   else   // jika update data
	  {
		$reth_code=htmlspecialchars(trim($_POST['txt_code']));
		$reth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_receipt_transfer="select receipt_transfer_header.*, MONTH(rth_date) AS month, YEAR(rth_date) AS year  
								   from receipt_transfer_header where rth_id='$rth_id' and branch_id='$branch_id'";
		$exec_check_receipt_transfer=mysqli_query($db_connection, $q_check_receipt_transfer);
		$field_check_receipt_transfer=mysqli_fetch_array($exec_check_receipt_transfer);
		$rth_date_old=$field_check_receipt_transfer['rth_date'];
		$rth_created_time=$field_check_receipt_transfer['created_time'];
		
		$active_period=check_active_period($db_connection, $field_check_receipt_transfer['month'], $field_check_receipt_transfer['year']);
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
		if ($field_check_receipt_transfer['rth_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Penerimaan Transfer tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_receipt_transfer)>0)
		   {
			 $old_date=$field_check_receipt_transfer['rth_date'];
			 $input_data='';
			 
			 $ttd_id='';
			 $whsld_id='';
			 $data=$_POST['cb_data'];
			 $detail_qty=0;
			 $total=count($data);
			 //echo "total=".$total."<br>";
			 foreach ($data as $field_data)
					 {
					   if ($ttd_id=='')
						   $ttd_id="'$field_data'";
					   else
						   $ttd_id=$ttd_id.",'$field_data'";
					   $ttd_id_1='rtd_id_'.$field_data;
					   $whsld_id='s_whsld_'.$field_data;
					   $is_canceled='cb_rtd_is_canceled_'.$field_data;
					   if (isset($_POST[$is_canceled])=='1')
						   $rtd_is_canceled='1';
					   else
						   $rtd_is_canceled='0';
					   $rtd_notes='txt_rtd_notes_'.$field_data;
					   
					   if ($input_data=='')
						  {
							$input_data="VALUES ('$rth_id', '".$field_data."', (SELECT itemd_id FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),
																			  (SELECT whsl_id_from FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),'".$_POST[$whsld_id]."','".$rtd_is_canceled."','".htmlspecialchars($_POST[$rtd_notes])."')";
						  }
					   else	  
						  {
							$input_data=$input_data.", ('$rth_id', '".$field_data."', (SELECT itemd_id FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'),
																					 (SELECT whsl_id_from FROM transfer_detail WHERE ttd_id='$field_data' AND tth_id='$tth_id'), '".$_POST[$whsld_id]."','".$rtd_is_canceled."','".htmlspecialchars($_POST[$rtd_notes])."')";
						  }
					 }
					 
			 $q_check_tube="SELECT transfer_detail.ttd_id, transfer_detail.itemd_id,  receipt_transfer_detail.* 
							FROM receipt_transfer_detail
							INNER JOIN transfer_detail ON transfer_detail.ttd_id=receipt_transfer_detail.ttd_id
							INNER JOIN receipt_transfer_header ON receipt_transfer_header.rth_id=receipt_transfer_detail.rth_id
							WHERE rtd_is_canceled='0' AND receipt_transfer_detail.rth_id='$rth_id'";
			 $exec_check_tube=mysqli_query($db_connection, $q_check_tube);
			 $tube_id="";
			 $ttd_id_old="";
			 while ($field_tube_id=mysqli_fetch_array($exec_check_tube))
				   {
					 if ($tube_id=="")
						 $tube_id="'".$field_tube_id['itemd_id']."'";
					 else
						 $tube_id=$tube_id.", '".$field_tube_id['itemd_id']."'";
					 
					 if ($ttd_id_old=="")
						 $ttd_id_old="'".$field_tube_id['ttd_id']."'";
					 else
						 $ttd_id_old=$ttd_id_old.", '".$field_tube_id['ttd_id']."'";
				   }
			
			 $action="EDIT";
			 $rth_date_old=$rth_date_old;
			 $rth_date_new=$rth_date;
			 $rth_created_time=$rth_created_time;	   
			 $messages=is_there_any_new_trans($db_connection, $action, $rth_id, $tube_id, $rth_date_old, $rth_date_new, $rth_created_time);
			 
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
				  //echo $q_check_item;
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
							mysqli_autocommit($db_connection, false);
							$q_update_transfer_detail="UPDATE transfer_detail, receipt_transfer_detail 
															  SET ttd_status='0' 
													   WHERE transfer_detail.ttd_id=receipt_transfer_detail.ttd_id AND rtd_is_canceled='0' AND  
															 tth_id='$tth_id' AND rth_id='$rth_id'";
							$q_update_transfer_header="UPDATE transfer_header 
													   SET tth_status=(IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND ttd_is_canceled='0' AND 
																		   tth_id='$tth_id')=0,'2',
																	   IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND ttd_is_canceled='0' AND 
																		   tth_id='$tth_id')!=(SELECT COUNT(*) FROM transfer_detail 
																							   WHERE tth_id='$tth_id'),'1','0')))
													   WHERE tth_id='$tth_id'";
							$q_update_item_detail="UPDATE item_detail, transfer_detail, receipt_transfer_detail 
														  SET branch_id='$rth_branch_id', itemd_position='In Transit', whsl_id=whsl_id_old
												   WHERE item_detail.itemd_id=transfer_detail.itemd_id AND transfer_detail.ttd_id=receipt_transfer_detail.ttd_id AND
														 tth_id='$tth_id' AND ttd_is_canceled='0' AND ttd_status='0' AND rth_id='$rth_id' AND 
														 receipt_transfer_detail.ttd_id IN ($ttd_id_old)";
							$q_delete_receipt_transfer_detail="DELETE FROM receipt_transfer_detail WHERE rth_id='$rth_id'";
							$exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
							$exec_update_transfer_header=mysqli_query($db_connection, $q_update_transfer_header);
							$exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
							$exec_delete_receipt_transfer_detail=mysqli_query($db_connection, $q_delete_receipt_transfer_detail);
							if ($exec_update_transfer_detail && $exec_update_transfer_header && $exec_update_item_detail && $exec_delete_receipt_transfer_detail)
							   {
								 $q_check_ttd="SELECT itemd_code, masti_name, itemd_serial_no 
											   FROM transfer_detail
											   INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
											   INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
											   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
											   WHERE ttd_is_canceled='0' AND ttd_status='0' AND transfer_detail.ttd_id IN ($ttd_id) AND 
													 transfer_detail.tth_id='$tth_id' AND tth_is_canceled='0' AND 
													 transfer_header.branch_id='$rth_branch_id' AND tth_status!='2'";
								 $exec_check_ttd=mysqli_query($db_connection, $q_check_ttd);
								// echo $q_check_ttd;
								 if (mysqli_num_rows($exec_check_ttd)==0)
									{
									  mysqli_rollback($db_connection);
									  ?>
										 <script language="javascript">
										   alert('Semua Aset tidak ditemukan!');
										   window.location.href='javascript:history.back(1)';
										 </script>
									  <?php
									}
								 else	
									{
									  if (mysqli_num_rows($exec_check_ttd)!=$total) 
										 {
										   mysqli_rollback($db_connection);
										   $no=0;
										   echo "<b>Beberapa Aset tidak ditemukan!</b><br>";
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
										   $q_input_receipt_transfer_header="UPDATE receipt_transfer_header SET rth_date='$rth_date', emp_id_receiver='$rth_receiver', 
																					rth_notes='$rth_notes', updated_by='$maker', updated_time='$created_time',
																					rth_ba_no='$rth_ba_no', rth_po_no='$rth_po_no'
																			 WHERE rth_id='$rth_id'";
										   $q_input_receipt_transfer_detail="INSERT INTO receipt_transfer_detail (rth_id, ttd_id, itemd_id, whsl_id_old, whsl_id_new, 
																												  rtd_is_canceled, rtd_notes) ".$input_data;
										   $q_update_transfer_detail="UPDATE transfer_detail, receipt_transfer_detail 
																			 SET ttd_status='1' 
																	  WHERE transfer_detail.ttd_id=receipt_transfer_detail.ttd_id AND transfer_detail.tth_id='$tth_id' AND 
																			ttd_is_canceled='0' AND ttd_status='0' AND rtd_is_canceled='0' AND rth_id='$rth_id'";
										   $q_update_item_detail="UPDATE item_detail, transfer_detail, receipt_transfer_detail 
																		 SET branch_id='$branch_id', item_detail.whsl_id=receipt_transfer_detail.whsl_id_new, 
																		 itemd_position='Internal'
																  WHERE transfer_detail.ttd_id IN ($ttd_id) AND transfer_detail.itemd_id=item_detail.itemd_id AND 
																		transfer_detail.ttd_id=receipt_transfer_detail.ttd_id AND rtd_is_canceled='0' AND ttd_is_canceled='0'
																		AND ttd_status='1' AND  tth_id='$tth_id' AND rth_id='$rth_id' AND branch_id='$rth_branch_id'";
										   $q_update_transfer_header_status="UPDATE transfer_header 
																			 SET tth_status=(IF((SELECT COUNT(*) FROM transfer_detail 
																								 WHERE ttd_status='0' AND ttd_is_canceled='0' 
																								 AND tth_id='$tth_id')=0,'2',
																				 IF((SELECT COUNT(*) FROM transfer_detail WHERE ttd_status='0' AND ttd_is_canceled='0' AND 
																				 tth_id='$tth_id')!=(SELECT COUNT(*) FROM transfer_detail WHERE tth_id='$tth_id'),'1','0')))
																			 WHERE tth_id='$tth_id'";
									  
										   $exec_input_receipt_transfer_header=mysqli_query($db_connection, $q_input_receipt_transfer_header);
										   $exec_input_receipt_transfer_detail=mysqli_query($db_connection, $q_input_receipt_transfer_detail);
										   $exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
										   $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
										   $exec_update_transfer_header_status=mysqli_query($db_connection, $q_update_transfer_header_status);
									  
										   if ($exec_input_receipt_transfer_header && $exec_input_receipt_transfer_detail && $exec_update_transfer_detail && 
											   $exec_update_item_detail && $exec_update_transfer_header_status)
											  {
												update_runing_no($db_connection, 'RTT',$branch_id);
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

?>		  