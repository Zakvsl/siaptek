<?php
  include "../../library/check_session.php";
  include "../../library/library_function.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction);
  $created_time=get_create_time($db_connection);
  $is_continue=0;
  
	$maker=$_SESSION['ses_user_id'];
	include "../../library/db_connection.php";
	/*$branch_id=htmlspecialchars($_GET['b']); */
	$tth_id=htmlspecialchars($_POST['txt_id']);
	$tth_date=htmlspecialchars($_POST['txt_tth_date']);
	$d=substr($tth_date,0,2);
	$m=substr($tth_date,3,2);
	$y=substr($tth_date,6,4); 
	$tth_date=$y."-".$m."-".$d;
	$branch_id_to=htmlspecialchars(trim($_POST['s_branch_to']));
	$emp_id_sender=htmlspecialchars(trim($_POST['s_employee_sender']));
	$tth_ba_no=htmlspecialchars($_POST['txt_ba_no']);
	$tth_po_no=htmlspecialchars($_POST['txt_po_no']);
	$tth_notes=htmlspecialchars($_POST['txt_tth_notes']);
	$q_check_branch="SELECT * FROM branch WHERE branch_id='$branch_id_to'";
	$q_check_sender="SELECT * FROM employee WHERE emp_id='$emp_id_sender' AND branch_id='$branch_id'";
	$exec_check_branch=mysqli_query($db_connection, $q_check_branch);
	$exec_check_sender=mysqli_query($db_connection, $q_check_sender);	
	
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
	if (mysqli_num_rows($exec_check_branch)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Kantor Cabang Tujuan tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if (mysqli_num_rows($exec_check_sender)==0)
	   {
		 ?>
			<script language="javascript">
			  alert('Nama Pengirim tidak ditemukan!');
			  window.location.href='javascript:history.back(1)';
			</script>
		 <?php
	   }
   else
   if ($tth_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$tth_code=get_no_transaction($db_connection, 'TRANS',$branch_id);
		if ($tth_code!='')
		{
			$tth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_transfer_header="select * from transfer_header where tth_code='$tth_code' and branch_id='$branch_id'";
			$exec_check_transfer_header=mysqli_query($db_connection, $q_check_transfer_header);
			if (mysqli_num_rows($exec_check_transfer_header)>0)
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
				 $itemd_id='';
				 $data=$_POST['cb_data'];
				 $detail_qty=0;
				 $total=count($data);
				 $id_whsl=array();
				 foreach ($data as $field_data)
						 {
						   if ($itemd_id=='')
							   $itemd_id="'$field_data'";
						   else
							   $itemd_id=$itemd_id.",'$field_data'";
							   
						   $itemd_id_1='ttd_id_'.$field_data;
						   $ttd_notes='ttd_notes_'.$field_data;
						   
						   if ($input_data=='')
							  {
								$input_data="VALUES ((SELECT tth_id FROM transfer_header WHERE branch_id='$branch_id' AND tth_code='$tth_code'), '".$field_data."', 
													 (SELECT whsl_id FROM item_detail WHERE itemd_id='$field_data' AND branch_id='$branch_id'),'0',
													  '".htmlspecialchars($_POST[$ttd_notes])."')";
							  }
						   else	   
							  {
								$input_data=$input_data.", ((SELECT tth_id FROM transfer_header WHERE branch_id='$branch_id' AND tth_code='$tth_code'), '".$field_data."', 
															(SELECT whsl_id FROM item_detail WHERE itemd_id='$field_data' AND branch_id='$branch_id'),'0',
															 '".htmlspecialchars($_POST[$ttd_notes])."')";
							  }
						 }
						 
				$q_check_item="SELECT * 
							   FROM item_detail
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
							   WHERE itemd_id IN ($itemd_id) AND item_detail.branch_id='$branch_id' AND itemd_position='Internal' AND itemd_status='0' AND 
									 itemd_is_broken='0' AND itemd_is_wo='0' AND itemd_is_dispossed='0' AND warehouse_location.branch_id='$branch_id'";
				$exec_check_item=mysqli_query($db_connection, $q_check_item);
				if (mysqli_num_rows($exec_check_item)==0)
				   {
					 mysqli_rollback($db_connection);
					 ?>
						<script language="javascript">
						  alert('Semua aset tidak ditemukan!');
						  history.back(1);
						</script>
					 <?php
				   }
				else
				if (mysqli_num_rows($exec_check_item)!=$total)
				   {
					 mysqli_rollback($db_connection);
					 $itemd_code_not_found='';
					 while ($field_item_not_found=mysqli_fetch_array($exec_check_item))
						   {
							 if ($itemd_code_not_found=='')
								 $itemd_code_not_found=$field_item_not_found['itemd_code'];
							 else
								 $itemd_code_not_found=$itemd_code_not_found.", ".$field_item_not_found['itemd_code'];
						   }
					 ?>
						<script language="javascript">
						  var x='<?php echo $itemd_code_not_found;?>';
						  alert('Ada beberapa Aset yang tidak ditemukan! Berikut adalah detail Aset yang ditemukan :\n'+x);
						  history.back(1);
						</script>
					 <?php
				   }
				else
				   { 
					 $itemd_code_not_internal="";
					 while ($data_item_detail=mysqli_fetch_array($exec_check_item))
						   {
							 if (strtoupper($data_item_detail['itemd_position'])!=strtoupper('Internal')) //THONI 06032020 add upper in the rights side
								{
								  if ($itemd_code_not_internal=='')
									  $itemd_code_not_internal=$data_item_detail['itemd_code'];
								  else
									  $itemd_code_not_internal=$itemd_code_not_internal.", ".$data_item_detail['itemd_code'];
								}
						   }
						   
					 if ($itemd_code_not_internal!="")
						{
						  mysqli_rollback($db_connection);
						  ?>
							 <script language="javascript">
								var tube_code='<?php echo $itemd_code_not_internal;?>';
								alert('Ada Aset yang posisinya tidak di Internal!\n'+tube_code);
								history.back(1);
							 </script>
						  <?php
						}
					 else
						{	
						  $action="NEW";
						  $tth_id="";
						  $tth_date_old="";
						  $tth_date_new=$tth_date;
						  $tth_created_time=$created_time;
						  $messages=is_there_any_new_trans($db_connection, $action, $tth_id, $itemd_id, $tth_date_old, $tth_date_new, $tth_created_time);
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
							   $q_input_transfer_header="INSERT INTO transfer_header (branch_id, tth_code, tth_date, branch_id_to, emp_id_sender, 
																					  tth_ba_no, tth_po_no, tth_is_canceled, tth_notes, created_by, created_time)
																			  VALUES ('$branch_id','$tth_code','$tth_date','$branch_id_to','$emp_id_sender', 
																					  '$tth_ba_no', '$tth_po_no', '0','$tth_notes',$maker, '$created_time')";
							   $exec_input_transfer_header=mysqli_query($db_connection, $q_input_transfer_header);
							   if ($exec_input_transfer_header)
								  {
									$q_input_transfer_detail="INSERT INTO transfer_detail (tth_id, itemd_id, whsl_id_from, ttd_is_canceled, ttd_notes)".$input_data;
									$exec_input_transfer_detail=mysqli_query($db_connection, $q_input_transfer_detail);
									if ($exec_input_transfer_detail)
									   {
										 $q_update_item_detail="UPDATE item_detail SET itemd_position='In Transit'
																WHERE item_detail.itemd_id IN ($itemd_id)";  
										 $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
										 if ($exec_update_item_detail)
											{
											  update_runing_no($db_connection, 'TRANS',$branch_id);
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
	  }
   else   // jika update data
	  {
		$tth_code=htmlspecialchars(trim($_POST['txt_code']));
		$tth_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_transfer="select transfer_header.*, MONTH(tth_date) AS month, YEAR(tth_date) AS year 
						   from transfer_header where tth_id='$tth_id' and branch_id='$branch_id'";
		$exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
		$field_check_transfer=mysqli_fetch_array($exec_check_transfer);
		
		$active_period=check_active_period($db_connection, $field_check_transfer['month'], $field_check_transfer['year']);	
	
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
		if ($field_check_transfer['tth_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Perpindahan Aset tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if ($field_check_transfer['tth_status']!='0')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Perpindahan Aset tidak dapat dilakukan!\nTransaksi sudah diteruskan ke Penerimaan Transfer Aset!');
				  window.close();
				</script>
			 <?php
		   }
		else
		if (mysqli_num_rows($exec_check_transfer)>0)
		   {	
			 $input_data='';
			 $itemd_id='';
			 $data=$_POST['cb_data'];
			 $detail_qty=0;
			 $total=count($data);
			 foreach ($data as $field_data)
					 {
					   if ($itemd_id=='')
						   $itemd_id="'$field_data'";
					   else
						   $itemd_id=$itemd_id.",'$field_data'";
						   
					   $itemd_id_1='ttd_id_'.$field_data;
					   $whsl_id='s_whsl_detail_'.$field_data;
					   $is_canceled='cb_ttd_is_canceled_'.$field_data;
					   if (isset($_POST[$is_canceled])=='1')
						   $ttd_is_canceled='1';
					   else
						   $ttd_is_canceled='0';
					   $ttd_notes='ttd_notes_'.$field_data;
					   
					   if ($input_data=='')
						  {
							$input_data="VALUES ('$tth_id', '".$field_data."', (SELECT whsl_id FROM item_detail WHERE itemd_id='$field_data' AND branch_id='$branch_id'),'$ttd_is_canceled','".htmlspecialchars($_POST[$ttd_notes])."')";
						  }
					   else	  
						  {
							$input_data=$input_data.", ('$tth_id', '".$field_data."', (SELECT whsl_id FROM item_detail WHERE itemd_id='$field_data' AND branch_id='$branch_id'),'$ttd_is_canceled','".htmlspecialchars($_POST[$ttd_notes])."')";
						  }
					 }
					 
			 $q_get_new_tube="SELECT DISTINCT(item_detail.itemd_id) 
							  FROM item_detail, transfer_detail, transfer_header
							  WHERE item_detail.itemd_id!=transfer_detail.itemd_id AND transfer_detail.tth_id=transfer_header.tth_id AND 
									item_detail.itemd_id IN ($itemd_id) AND transfer_detail.tth_id='$tth_id' AND ttd_is_canceled='0'";
			// echo $q_get_new_tube;
			 $exec_get_new_tube=mysqli_query($db_connection, $q_get_new_tube);
			 if (mysqli_num_rows($exec_get_new_tube)>0)
				{
				  $new_tube_id="";
				  while ($new_tube=mysqli_fetch_array($exec_get_new_tube))
						{
						  if ($new_tube_id=="")
							  $new_tube_id="'".$new_tube['itemd_id']."'";
						  else
							  $new_tube_id=$new_tube_id.",'".$new_tube['itemd_id']."'";
						}
					
				  $action="EDIT";
				  $tth_id=$tth_id;
				  $tth_date_old=$field_check_transfer['tth_date'];	
				  $tth_date_new=$tth_date;
				  $tth_created_time=$field_check_transfer['created_time'];	
					
				  $messages=is_there_any_new_trans($db_connection, $action, $tth_id, $itemd_id, $tth_date_old, $tth_date_new, $tth_created_time);
				}
			 else
				 $messages='1';
			 
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
				  $old_tube_id="";
				  $q_get_old_tube="SELECT * FROM transfer_detail 
								   WHERE transfer_detail.tth_id='$tth_id' AND ttd_is_canceled='0'";
				  $exec_get_old_tube=mysqli_query($db_connection, $q_get_old_tube);
				  while ($field_old_tube=mysqli_fetch_array($exec_get_old_tube))
						{
						  if ($old_tube_id=="")
							  $old_tube_id="'".$field_old_tube['itemd_id']."'";
						  else
							  $old_tube_id=$old_tube_id.",'".$field_old_tube['itemd_id']."'";
						}
				  
				  mysqli_autocommit($db_connection, false);
				  $q_update_item_detail="UPDATE item_detail SET itemd_position='Internal'
										 WHERE itemd_id IN ($old_tube_id)"; 
				  $q_delete_transfer_detail="DELETE FROM transfer_detail WHERE tth_id='$tth_id'";
				  //echo $q_update_item_detail."<br>";
				  //echo $q_delete_transfer_detail."<br>";
				  $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
				  $exec_delete_transfer_detail=mysqli_query($db_connection, $q_delete_transfer_detail);
				  if ($exec_update_item_detail && $exec_delete_transfer_detail)
					 {
					   $q_check_item="SELECT * FROM item_detail 
									  INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
									  WHERE itemd_id IN ($itemd_id) AND item_detail.branch_id='$branch_id' AND warehouse_location.branch_id=item_detail.whsl_id AND
											itemd_position='Internal' AND itemd_status='0' AND itemd_is_broken='0' AND itemd_is_wo='0' AND itemd_is_dispossed='0'";
					   //echo $q_check_item."<br>";
					   $exec_check_item=mysqli_query($db_connection, $q_check_item);
					   if (mysqli_num_rows($exec_check_item)==0)
						  {
							mysqli_rollback($db_connection);
							?>
							  <script language="javascript">
								alert('Semua Aset tidak ditemukan!');
								window.close();
							  </script>
						   <?php
						 }
					  else
					  if (mysqli_num_rows($exec_check_item)!=$total)
						 {
						   mysqli_rollback($db_connection);	
						   $itemd_code_not_found='';
						   while ($field_item_not_found=mysqli_fetch_array($exec_check_item))
								 {
								   if ($itemd_code_not_found=='')
									   $itemd_code_not_found=$field_item_not_found['itemd_code'];
								   else
									   $itemd_code_not_found=$itemd_code_not_found.", ".$field_item_not_found['itemd_code'];
								 }
						   ?>
							  <script language="javascript">
								var x='<?php echo $itemd_code_not_found;?>';
								alert('Ada beberapa Aset yang tidak ditemukan! Berikut adalah Aset yang ditemukan :\n'+x);
								history.back(1);
							  </script>
						   <?php
						 }
					  else
						 {
						   $itemd_code_not_internal="";
						   while ($data_item_detail=mysqli_fetch_array($exec_check_item))
								 {
								   if (strtoupper($data_item_detail['itemd_position'])!=strtoupper('Internal'))//THONI 06032020 add upper in the rights side
									  {
										if ($itemd_code_not_internal=='')
											$itemd_code_not_internal=$data_item_detail['itemd_code'];
										else
											$itemd_code_not_internal=$itemd_code_not_internal.", ".$data_item_detail['itemd_code'];
									  }
								 }
					   
						   if ($itemd_code_not_internal!="")
							  {
								mysqli_rollback($db_connection);
								?>
								   <script language="javascript">
									  var tube_code='<?php echo $itemd_code_not_internal;?>';
									  alert('Ada Aset yang posisinya tidak di Internal!\n'+tube_code);
									  history.back(1);
								   </script>
								<?php
							  }
						   else
							  {
								$q_input_transfer_header="UPDATE transfer_header SET tth_code='$tth_code', tth_date='$tth_date', branch_id_to='$branch_id_to', 
																 tth_ba_no='$tth_ba_no', tth_po_no='$tth_po_no', emp_id_sender='$emp_id_sender', tth_notes='$tth_notes', 
																 updated_by='$maker', updated_time='$created_time'
														  WHERE branch_id='$branch_id' AND tth_id='$tth_id'";
								$q_input_transfer_detail="INSERT INTO transfer_detail (tth_id, itemd_id, whsl_id_from, ttd_is_canceled, ttd_notes)".$input_data;
								$exec_input_transfer_header=mysqli_query($db_connection, $q_input_transfer_header);
								$exec_input_transfer_detail=mysqli_query($db_connection, $q_input_transfer_detail);
								
								if ($exec_input_transfer_header && $exec_input_transfer_header)
								   {
									 $tube_id="";
									 $q_get_tube_id="SELECT * FROM transfer_detail WHERE tth_id='$tth_id' AND ttd_is_canceled='0'";
									 $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
									 while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
										   {
											 if ($tube_id=='')
												 $tube_id="'".$field_tube_id['itemd_id']."'";
											 else
												 $tube_id=$tube_id.",'".$field_tube_id['itemd_id']."'";
										   }
									 
									 $q_update_item_detail="UPDATE item_detail SET itemd_position='In Transit'
															WHERE itemd_id IN ($tube_id)";
									 $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
									 if ($exec_update_item_detail)
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
						  }
					} 
				}	
		   }
		else
		   {
			 ?>
				<script language="javascript">
				  alert('Transaksi Perpindahan Aset yang akan diupdate tidak ditemukan!');
				  window.close();
				</script>
			  <?php  
		   }     
	  }	
	//}
?>		  