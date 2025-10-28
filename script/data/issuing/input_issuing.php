<?php
  include "../../library/check_session.php";
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  
  $branch_id=$_SESSION['ses_id_branch'];
  $branch_id_transaction=$_POST["s_branch_trans"];
  compare_branch($branch_id, $branch_id_transaction);
  $created_time=get_create_time($db_connection);
  $is_continue=0;
	/*$branch_id=htmlspecialchars($_GET['b']); */
	$issuingh_id=htmlspecialchars($_POST['txt_id']);
	$issuingh_date=htmlspecialchars($_POST['txt_issuingh_date']);
	$d=substr($issuingh_date,0,2);
	$m=substr($issuingh_date,3,2);
	$y=substr($issuingh_date,6,4); 
	$issuingh_date=$y."-".$m."-".$d;
	$issuingh_ba_no=htmlspecialchars(trim($_POST['txt_ba_no']));
	$issuingh_po_no=htmlspecialchars(trim($_POST['txt_po_no']));
	$issuingh_do_no=htmlspecialchars(trim($_POST['txt_issuingh_do_no']));
	$issuingh_sent_use=htmlspecialchars(trim($_POST['txt_issuingh_sent_by']));
	$issuingh_vehicle_no=htmlspecialchars(trim($_POST['txt_issuingh_vehicle_no']));
	$issuingh_sent_by=htmlspecialchars(trim($_POST['s_employee']));
	$issuingh_type=htmlspecialchars(trim($_POST['rb_type']));
	$issuingh_cust_vendor=htmlspecialchars(trim($_POST['s_customer_vendor']));
	$issuingh_receiver=htmlspecialchars(trim($_POST['txt_issuingh_receiver']));
	$issuingh_notes=htmlspecialchars($_POST['txt_issuingh_notes']);
	if ($issuingh_type=='0')
		$q_check_customer_vendor="SELECT cust_id FROM customer WHERE cust_id='$issuingh_cust_vendor' AND cust_type='0' AND branch_id='$branch_id'";
	else
		$q_check_customer_vendor="SELECT cust_id FROM customer WHERE cust_id='$issuingh_cust_vendor' AND cust_type='1' AND branch_id='$branch_id'";
	$exec_customer_vendor=mysqli_query($db_connection,$q_check_customer_vendor);
	
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
   if ($issuingh_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$issuingh_code=get_no_transaction($db_connection, 'CYO',$branch_id);
		if ($issuingh_code!='')
		{
			$issuingh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_issuing_header="select * from issuing_header where issuingh_code='$issuingh_code' and branch_id='$branch_id'";
			$exec_check_issuing_header=mysqli_query($db_connection,$q_check_issuing_header);
			if (mysqli_num_rows($exec_check_issuing_header)>0)
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
				 foreach ($data as $field_data)
						 {
						   if ($itemd_id=='')
							   $itemd_id="'$field_data'";
						   else
							   $itemd_id=$itemd_id.",'$field_data'";
							   
						   $itemd_id_1='issuingd_id_'.$field_data;
						   $issuingd_status='txt_issuingd_status_'.$field_data;
						   $issuingd_notes='issuingd_notes_'.$field_data;	
							   
						   if ($input_data=='')
							   $input_data="VALUES ((SELECT issuingh_id FROM issuing_header WHERE branch_id='$branch_id' AND issuingh_code='$issuingh_code'), '".$field_data."','1','".$_POST[$issuingd_status]."','0','".htmlspecialchars($_POST[$issuingd_notes])."')";
						   else	  
							   $input_data=$input_data.", ((SELECT issuingh_id FROM issuing_header WHERE branch_id='$branch_id' AND issuingh_code='$issuingh_code'), '".$field_data."','1','".$_POST[$issuingd_status]."','0','".htmlspecialchars($_POST[$issuingd_notes])."')";
						 }
							 
			  //       echo $rir_storage."<br>"					 
				  
				 $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									   (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, whsl_name,
										itemd_position 
								 FROM item_detail
								 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
								 INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
								 INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
								 WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_is_wo='0' AND itemd_status='0' AND itemd_position='Internal' AND 
									   item_detail.branch_id='$branch_id' AND warehouse_location.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id)";					

				 $exec_check_itemd=mysqli_query($db_connection,$q_check_itemd);
				 if (mysqli_num_rows($exec_check_itemd)==0)
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
					  if (mysqli_num_rows($exec_check_itemd)!=$total) 
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
						   $itemd_code_not_internal="";
						   while ($data_tube_code=mysqli_fetch_array($exec_check_itemd)) 
								 {
								   if ($data_tube_code['itemd_position']!='Internal')
									  {
										if ($itemd_code_not_internal=="")
											$itemd_code_not_internal=$data_tube_code['itemd_code'];
										else
											$itemd_code_not_internal=$itemd_code_not_internal.", ".$data_tube_code['itemd_code'];
									  } 
								 }
					
						   if ($itemd_code_not_internal!="")
							  {
								mysqli_rollback($db_connection);
								?>
								   <script language="javascript">
										 var tube_code='<?php echo $itemd_code_not_internal;?>';
										 alert('Ada aset yang posisinya tidak di Internal!\n'+tube_code);
										 history.back(1);
								   </script>
								<?php
							  }
						   else
							  {	  
								$action="NEW";
								$issuingh_id="";
								$issuingh_date_old="";
								$issuingh_date_new=$issuingh_date;
								$issuingh_created_time=$created_time;
								$messages=is_there_any_new_trans($db_connection, $action, $issuingh_id, $itemd_id, $issuingh_date_old, $issuingh_date_new, $issuingh_created_time);
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
									 $q_input_issuing_header="INSERT INTO issuing_header (branch_id, issuingh_code, issuingh_date, issuingh_ba_no, issuingh_po_no, issuingh_do_no, 
																						  issuingh_sent_by, issuingh_vehicle_no, emp_id, issuingh_type, cust_id, 
																						  issuingh_receiver_name, issuingh_notes, issuingh_is_canceled, created_by, created_time)
																				  VALUES ('$branch_id','$issuingh_code', '$issuingh_date', '$issuingh_ba_no', 
																						  '$issuingh_po_no','$issuingh_do_no', '$issuingh_sent_use', 
																						  '$issuingh_vehicle_no', '$issuingh_sent_by', '$issuingh_type', '$issuingh_cust_vendor', 
																						  '$issuingh_receiver', '$issuingh_notes','0', '$maker', '$created_time')";
									 $q_input_issuing_detail="INSERT INTO issuing_detail (issuingh_id, itemd_id, issuingd_qty, issuingd_status, issuingd_is_canceled, 
																						  issuingd_notes) ".$input_data;
									 //echo "q_input_issuing_header=".$q_input_issuing_header."<br>";
									 //echo "q_input_issuing_detail=".$q_input_issuing_detail."<br>";
									 $exec_input_issuing_header=mysqli_query($db_connection,$q_input_issuing_header);
									 $exec_input_issuing_detail=mysqli_query($db_connection,$q_input_issuing_detail);
									 if ($exec_input_issuing_header && $exec_input_issuing_detail)
										{
										  //$total_tube=0;
										  //$tabung_id="";
										  $q_get_itemd_id="SELECT itemd_id 
														   FROM issuing_detail ID
														   INNER JOIN issuing_header IH On ID.issuingh_id=IH.issuingh_id
														   WHERE branch_id='$branch_id' AND issuingh_code='$issuingh_code'";
										  //echo "q_get_itemd_id=".$q_get_itemd_id."<br>";
										  $exec_get_itemd_id=mysqli_query($db_connection,$q_get_itemd_id);
										  $total_tube=mysqli_num_rows($exec_get_itemd_id);
										  /*while ($id_tabung=mysqli_fetch_array($exec_get_itemd_id))
												{
												  if ($tabung_id=='')
													  $tabung_id="'".$id_tabung['itemd_id']."'";
												  else
													  $tabung_id=$tabung_id.", '".$id_tabung['itemd_id']."'"; 
												  $total_tube++;
												}  */
										 
										  if ($total_tube!=$total)
											 {
											   mysqli_rollback($db_connection);
											   ?>
												  <script language="javascript">
													  alert('Aset tidak ditemukan!');
													  window.location.href='javascript:history.back(1)';
												  </script>
											   <?php
											 }
										  else
											 {
											   if ($issuingh_type=='0')
												   $q_update_item_detail="UPDATE item_detail SET itemd_position='Customer' 
																		  WHERE itemd_id IN ($itemd_id) AND branch_id='$branch_id'";
											   else
												   $q_update_item_detail="UPDATE item_detail SET itemd_position='Vendor' 
																		  WHERE itemd_id IN ($itemd_id) AND branch_id='$branch_id'";
											   //echo $q_input_issuing_header."<br>";
											   //echo $q_input_issuing_detail."<br>";
											   //echo $q_update_item_detail."<br>";
							   
											   $exec_update_item_detail=mysqli_query($db_connection,$q_update_item_detail);
											   if ($exec_update_item_detail)
												  {
													update_runing_no($db_connection, 'CYO',$branch_id);
													mysqli_commit($db_connection);
													//mysqli_rollback($db_connection);
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
		$issuingh_code=htmlspecialchars(trim($_POST['txt_code']));
		$issuingh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_issuing="select issuing_header.*, MONTH(issuingh_date) AS month, YEAR(issuingh_date) AS year 
						  from issuing_header where issuingh_id='$issuingh_id' and branch_id='$branch_id'";
		$exec_check_issuing=mysqli_query($db_connection,$q_check_issuing);
		$field_check_issuing=mysqli_fetch_array($exec_check_issuing);

		$active_period=check_active_period($db_connection, $field_check_issuing['month'], $field_check_issuing['year']);	
	
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
		if ($field_check_issuing['issuingh_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Pengeluaran tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if ($field_check_issuing['issuingh_status']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Pengeluaran tidak dapat dilakukan!\nAset sudah dikembalikan sebagian!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if ($field_check_issuing['issuingh_status']=='2')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Pengeluaran tidak dapat dilakukan!\nAset sudah dikembalikan semua!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_issuing)>0)
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
						   $itemd_id=$itemd_id.", '$field_data'";
					  
					   $issuingd_id='issuingd_id_'.$field_data;
					   $issuingd_is_canceled='cb_issuingd_is_canceled_'.$field_data;
					   if (isset($_POST[$issuingd_is_canceled])=='1')
						   $is_canceled='1';
					   else
						   $is_canceled='0';
					   $issuingd_status="txt_issuingd_status_".$field_data;;	   
					   $issuingd_notes='txt_issuingd_notes_'.$field_data;
						   
					   if ($input_data=='')
						   $input_data="VALUES ('".$issuingh_id."','".$field_data."','1','".$_POST[$issuingd_status]."','".$is_canceled."','".htmlspecialchars($_POST[$issuingd_notes])."')";
					   else	  
						   $input_data=$input_data.", ('".$issuingh_id."', '".$field_data."','1','".$_POST[$issuingd_status]."','".$is_canceled."','".htmlspecialchars($_POST[$issuingd_notes])."')";
						 }
			 $id_tabung="";
			 $q_get_issuing_detail="SELECT itemd_id 
									FROM issuing_detail 
									INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
									WHERE branch_id='$branch_id' AND issuing_detail.issuingh_id='$issuingh_id' AND issuingd_is_canceled='0' AND issuingd_status='0'";
			 $exec_get_issuing_detail=mysqli_query($db_connection,$q_get_issuing_detail);
			 while ($tabung_id=mysqli_fetch_array($exec_get_issuing_detail))
				   {
					 if ($id_tabung=='')
						 $id_tabung="'".$tabung_id['itemd_id']."'";
					 else
						 $id_tabung=$id_tabung.", '".$tabung_id['itemd_id']."'";
				   }		
			
			 $action="EDIT";
			 $issuingh_id=$issuingh_id;
			 $issuingh_date_old=$field_check_issuing['issuingh_date'];	
			 $issuingh_date_new=$issuingh_date;
			 $issuingh_created_time=$field_check_issuing['created_time'];	
			 $messages=is_there_any_new_trans($db_connection, $action, $issuingh_id, $itemd_id, $issuingh_date_old, $issuingh_date_new, $issuingh_created_time);
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
				  $q_update_status_item="UPDATE item_detail SET itemd_position='Internal' WHERE branch_id='$branch_id' AND itemd_id IN ($id_tabung)";	
				  $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
										(SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
										(SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, whsl_name,
										 itemd_position 
								  FROM item_detail
								  INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
								  INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
								  INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
								  WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_status='0' AND item_detail.branch_id='$branch_id' AND 
										warehouse_location.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id) AND itemd_position='Internal'";
				  $exec_update_status_item=mysqli_query($db_connection,$q_update_status_item);
				  $exec_check_itemd=mysqli_query($db_connection,$q_check_itemd);
				  if (!$exec_update_status_item)
					 {
					   mysqli_rollback($db_connection);
					   ?>
						  <script language="javascript">
							 alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
							 window.location.href='javascript:history.back(1)';
						  </script>
					   <?php
					 }
				  else
				  if (mysqli_num_rows($exec_check_itemd)==0)
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
						if (mysqli_num_rows($exec_check_itemd)!=$total) 
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
							 $itemd_code_not_internal="";
							 while ($data_tube_code=mysqli_fetch_array($exec_check_itemd)) 
								   {
									 if ($data_tube_code['itemd_position']!='Internal')
										{
										  if ($itemd_code_not_internal=="")
											  $itemd_code_not_internal=$data_tube_code['itemd_code'];
										  else
											  $itemd_code_not_internal=$itemd_code_not_internal.", ".$data_tube_code['itemd_code'];
										} 
								   }
				
							 if ($itemd_code_not_internal!="")
								{
								  mysqli_rollback($db_connection);
								  ?>
									<script language="javascript">
									   var tube_code='<?php echo $itemd_code_not_internal;?>';
									   alert('Ada aset yang posisinya tidak di Internal!\n'+tube_code);
									   history.back(1);
									</script>
								  <?php
								}
							 else
								{  
								  $q_delete_issuing_detail="DELETE FROM issuing_detail WHERE issuingh_id='$issuingh_id'";
								  $exec_delete_issuing_detail=mysqli_query($db_connection,$q_delete_issuing_detail);
								  if ($exec_delete_issuing_detail)
									 {
									   if ($issuingh_code==$issuingh_code_1)
										   input_issuing($branch_id, $issuingh_id, $issuingh_code, $issuingh_date, $issuingh_ba_no, $issuingh_po_no, $issuingh_do_no, 
														 $issuingh_sent_use, $issuingh_vehicle_no, $issuingh_sent_by, $issuingh_type, $issuingh_cust_vendor, 
														 $issuingh_receiver, $issuingh_notes, $maker, $input_data, $db_connection, $total);
									   else
										  {
											$q_check_new_issuing="SELECT * FROM issuing_header WHERE branch_id='$branch_id' AND issuingh_code='$issuingh_code'";
											$exec_check_new_issuing=mysqli_query($db_connection,$q_check_new_issuing);
											if (mysqli_num_rows($exec_check_new_issuing)>0)
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
											   input_issuing($branch_id, $issuingh_id, $issuingh_code, $issuingh_date, $issuingh_ba_no, $issuingh_po_no, $issuingh_do_no, 
															 $issuingh_sent_use, $issuingh_vehicle_no, $issuingh_sent_by, $issuingh_type, $issuingh_cust_vendor, 
															 $issuingh_receiver, $issuingh_notes, $maker, $input_data, $db_connection, $total);
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
		else
		   {
			 ?>
				<script language="javascript">
				  alert('Transaksi Pengeluaran yang akan diupdate tidak ditemukan!');
				  window.close();
				</script>
			  <?php  
		   }     
	  }	

		  
 function input_issuing($branch_id, $issuingh_id, $issuingh_code, $issuingh_date, $issuingh_ba_no, $issuingh_po_no, $issuingh_do_no, $issuingh_sent_use, $issuingh_vehicle_no, 
                        $issuingh_sent_by, $issuingh_type, $issuingh_cust_vendor, $issuingh_receiver, $issuingh_notes, $maker, $input_data, $db_connection, $total)
          {
		    $current_date=date('Y-m-d');
            $q_input_issuing_header="UPDATE issuing_header SET issuingh_code='$issuingh_code', issuingh_date='$issuingh_date', issuingh_do_no='$issuingh_do_no', 
			                                issuingh_sent_by='$issuingh_sent_use', issuingh_vehicle_no='$issuingh_vehicle_no', emp_id='$issuingh_sent_by', 
											issuingh_type='$issuingh_type', cust_id='$issuingh_cust_vendor', issuingh_receiver_name='$issuingh_receiver', 
											issuingh_notes='$issuingh_notes', updated_by='$maker', updated_time=NOW(),
											issuingh_ba_no='$issuingh_ba_no', issuingh_po_no='$issuingh_po_no'
                                     WHERE branch_id='$branch_id' AND issuingh_id='$issuingh_id'";
			$q_input_issuing_detail="INSERT INTO issuing_detail (issuingh_id, itemd_id, issuingd_qty, issuingd_status, issuingd_is_canceled, issuingd_notes) ".$input_data;
			$exec_input_issuing_header=mysqli_query($db_connection,$q_input_issuing_header);
			$exec_input_issuing_detail=mysqli_query($db_connection,$q_input_issuing_detail);
			if ($exec_input_issuing_header && $exec_input_issuing_detail)
			   {	
			     $total_tube=0;	
			     $tabung_id="";
				 $q_get_itemd_id="SELECT itemd_id, issuingd_is_canceled  FROM issuing_detail WHERE issuingh_id='$issuingh_id'";
				// echo $q_get_itemd_id;
				 $exec_get_itemd_id=mysqli_query($db_connection,$q_get_itemd_id);
				 while ($id_tabung=mysqli_fetch_array($exec_get_itemd_id))
					   {
					     if ($id_tabung['issuingd_is_canceled']=='0')
						    {
						      if ($tabung_id=='')
							      $tabung_id="'".$id_tabung['itemd_id']."'";
						      else
							      $tabung_id=$tabung_id.", '".$id_tabung['itemd_id']."'";
						    }
						 $total_tube++;
					   }				
				 if ($total_tube!=$total)
				    {
					  mysqli_rollback($db_connection);
					  ?>
						 <script language="javascript">
						    alert('Aset tidak ditemukan!');
						    window.location.href='javascript:history.back(1)';
					     </script>
					  <?php			
					  exit;		  
					}
				 else
				    {
			          if ($issuingh_type=='0')
			              $q_update_item_status="UPDATE item_detail SET itemd_position='Customer' WHERE branch_id='$branch_id' AND itemd_id IN ($tabung_id)";
			          else
			              $q_update_item_status="UPDATE item_detail SET itemd_position='Vendor' WHERE branch_id='$branch_id' AND itemd_id IN ($tabung_id)";	
				    }

				 $exec_update_item_status=mysqli_query($db_connection,$q_update_item_status);
                 if ($exec_update_item_status)
				    {
			          $q_check_item_status="SELECT * FROM issuing_detail WHERE issuingd_is_canceled='0' AND issuingh_id='$issuingh_id'";
			          $exec_check_item_status=mysqli_query($db_connection,$q_check_item_status);
                      $total_check_item_status=mysqli_num_rows($exec_check_item_status); 

			          if ($total_check_item_status==0)
				         {
					       $q_update_issuing_header="UPDATE issuing_header SET issuingh_is_canceled='1', issuingh_canceled_date='$current_date', 
					                                        issuingh_canceled_reason='Semua Item dibatalkan'
												     WHERE branch_id='$branch_id' AND issuingh_id='$issuingh_id'";
					       $exec_update_issuing_header=mysqli_query($db_connection,$q_update_issuing_header);
					       if ($exec_update_issuing_header)
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
				  else
				     {
				        mysqli_rollback($db_connection);
				        ?>
                           <script language="javascript">
						      alert('5. Terjadi kesalahan!\nSilahkan hubungi Programmer anda!');
					          window.location.href='javascript:history.back(1)';
					       </script>
				        <?php
					 }  
				}
		  }    
?>		  