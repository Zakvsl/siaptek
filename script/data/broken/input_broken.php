<?php
  include "../../library/check_session.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  $branch_id_transaction=$_POST["s_branch_trans"];
  $is_continue=0;
  compare_branch($branch_id, $branch_id_transaction);
  $created_time=get_create_time($db_connection); 
      //  $branch_id=htmlspecialchars($_GET['b']);
	$brokh_id=htmlspecialchars($_POST['txt_id']);
	$brokh_date=htmlspecialchars($_POST['txt_brokh_date']);
	$d=substr($brokh_date,0,2);
	$m=substr($brokh_date,3,2);
	$y=substr($brokh_date,6,4); 
	$brokh_date=$y."-".$m."-".$d;
	$brokh_notes=htmlspecialchars($_POST['txt_brokh_notes']);

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
   if ($brokh_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$brokh_code=get_no_transaction($db_connection, 'BRO',$branch_id);
		if ($brokh_code!='')
		{
			$brokh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_broken_header="select * from broken_header where brokh_code='$brokh_code' and branch_id='$branch_id'";
			$exec_check_broken_header=mysqli_query($db_connection, $q_check_broken_header);
			if (mysqli_num_rows($exec_check_broken_header)>0)
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
							   
						   $itemd_id_1='brokd_id_'.$field_data;
						   $brokd_notes='txt_brokd_notes_'.$field_data;
							   
						   if ($input_data=='')
							   $input_data="VALUES ((SELECT brokh_id FROM broken_header WHERE branch_id='$branch_id' AND brokh_code='$brokh_code'), '".$field_data."','1', '0','".htmlspecialchars($_POST[$brokd_notes])."')";
						   else	  
							   $input_data=$input_data.", ((SELECT brokh_id FROM broken_header WHERE branch_id='$branch_id' AND brokh_code='$brokh_code'), '".$field_data."','1', '0','".htmlspecialchars($_POST[$brokd_notes])."')";
						 }
				
				
							 
			  //       echo $rir_storage."<br>";
					 $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									(SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, whsl_name,
									 itemd_position 
									 FROM item_detail
									 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
									 INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
									 INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
									 WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_is_wo='0' AND itemd_status='0' AND itemd_position='Internal' AND 
										   item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id)";

				//	 echo $q_check_itemd;
					 //echo $q_check_transfer;
					 $exec_check_itemd=mysqli_query($db_connection, $q_check_itemd);
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
							   $action="NEW";
							   $brokh_id="";
							   $brokh_date_old="";
							   $brokh_date_new=$brokh_date;
							   $brokh_created_time=$created_time;
							   $messages=is_there_any_new_trans($db_connection, $action, $brokh_id, $itemd_id, $brokh_date_old, $brokh_date_new, $brokh_created_time);
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
									$q_input_broken_header="INSERT INTO broken_header (branch_id, brokh_code, brokh_date, brokh_status, brokh_is_canceled, 
																					   brokh_notes, created_by, created_time)
																			   VALUES ('$branch_id','$brokh_code', '$brokh_date', '0', '0',
																					   '$brokh_notes','$maker','$created_time')";
									$exec_input_broken_header=mysqli_query($db_connection, $q_input_broken_header);
									if ($exec_input_broken_header)
									   {									  
										 $q_input_broken_detail="INSERT INTO broken_detail (brokh_id, itemd_id, brokd_qty, brokd_is_canceled, brokd_notes) ".$input_data;
										 $exec_input_broken_detail=mysqli_query($db_connection, $q_input_broken_detail);
										 if ($exec_input_broken_detail)
											{
											  $q_update_item_detail="UPDATE item_detail, broken_detail, broken_header SET itemd_is_broken='1'
																	 WHERE item_detail.itemd_id=broken_detail.itemd_id AND broken_header.brokh_id=broken_detail.brokh_id AND
																		   item_detail.branch_id='$branch_id' AND broken_header.branch_id='$branch_id' AND 
																		   brokh_code='$brokh_code'";
											  $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
											  if ($exec_update_item_detail)
												 {
												   update_runing_no($db_connection, 'BRO',$branch_id);
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
		$brokh_code=htmlspecialchars(trim($_POST['txt_code']));
		$brokh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_broken="select broken_header.*, MONTH(brokh_date) AS month, YEAR(brokh_date) AS year 
						 from broken_header where brokh_id='$brokh_id' and branch_id='$branch_id'";
		$q_check_broken_dispossal="select * from dispossal_header where brokh_id='$brokh_id' and branch_id='$branch_id' AND disph_is_canceled='0'";
		$q_check_broken_write_off="select * from write_off_header where brokh_id='$brokh_id' and branch_id='$branch_id' AND woh_is_canceled='0'";
		$exec_check_broken=mysqli_query($db_connection, $q_check_broken);
		$exec_check_broken_dispossal=mysqli_query($db_connection, $q_check_broken_dispossal);
		$exec_check_broken_write_off=mysqli_query($db_connection, $q_check_broken_write_off);
		$field_check_broken=mysqli_fetch_array($exec_check_broken);

		$active_period=check_active_period($db_connection, $field_check_broken['month'], $field_check_broken['year']);	
	
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
		if ($field_check_broken['brokh_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Kerusakan tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_broken_write_off)>0)
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Kerusakan tidak dapat dilakukan!\nTransaksi sudah diteruskan ke Transaksi Penghapusan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_broken_dispossal)>0)
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Kerusakan tidak dapat dilakukan!\nTransaksi sudah diteruskan ke Transaksi Penjualan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_broken)>0)
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
					  
					   $brokd_id='brokd_id_'.$field_data;
					   $brokd_is_canceled='cb_brokd_is_canceled_'.$field_data;
					   if (isset($_POST[$brokd_is_canceled])=='1')
						   $brokd_is_canceled='1';
					   else
						   $brokd_is_canceled='0';
					   $brokd_notes='txt_brokd_notes_'.$field_data;
						  
					   if ($input_data=='')
						   $input_data="VALUES ('$brokh_id', '".$field_data."','1','$brokd_is_canceled','".htmlspecialchars($_POST[$brokd_notes])."')";
					   else	  
						   $input_data=$input_data.", ('$brokh_id', '".$field_data."','1','$brokd_is_canceled','".htmlspecialchars($_POST[$brokd_notes])."')";
					 }
				
			 $tube_id="";
			 $q_get_tube_id="SELECT itemd_id FROM broken_detail WHERE brokh_id='$brokh_id' and brokd_is_canceled='0'";
			 $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
			 while ($field_tube=mysqli_fetch_array($exec_get_tube_id))
				   {
					 if ($tube_id=="")
						 $tube_id="'".$field_tube['itemd_id']."'";
					 else
						 $tube_id=$tube_id.",'".$field_tube['itemd_id']."'";
				   }
						
			 mysqli_autocommit($db_connection, false);
			 $q_update_item_status="UPDATE item_detail SET itemd_is_broken='0' WHERE itemd_id IN ($tube_id)";
			 $exec_update_item_status=mysqli_query($db_connection, $q_update_item_status);
			 if ($exec_update_item_status)
				{
				  $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
										(SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2, 
										 whsl_name 
								  FROM item_detail
								  INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
								  INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
								  INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
								  WHERE itemd_status='0' AND itemd_is_broken='0' AND itemd_is_dispossed='0' AND item_detail.branch_id='$branch_id' AND 
										item_detail.itemd_id IN($itemd_id) AND itemd_position='Internal'"; 
				  $exec_check_itemd=mysqli_query($db_connection, $q_check_itemd);
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
							$q_check_broken_status="SELECT brokh_status FROM broken_header WHERE branch_id='$branch_id' AND brokh_id='$brokh_id'";
							$exec_check_broken_status=mysqli_query($db_connection, $q_check_broken_status);
							$field_broken_status=mysqli_fetch_array($exec_check_broken_status);
							if ($field_broken_status['brokh_status']=='1' || $field_broken_status['brokh_status']=='2')
							   {
								 mysqli_rollback($db_connection);
								 ?>
									<script language="javascript">
									   alert('Transaksi tidak dapat diupdate!\nSudah ada transaksi Penjualan!');
									   window.location.href='javascript:history.back(1)';
									</script>
								 <?php
							   }
							else
							   { 
								 $action="EDIT";
								 $brokh_date_old=$field_check_broken['brokh_date'];
								 $brokh_date_new=$brokh_date;
								 $brokh_created_time=$field_check_broken['created_time'];
								 $messages=is_there_any_new_trans($db_connection, $action, $brokh_id, $itemd_id, $brokh_date_old, $brokh_date_new, $brokh_created_time);
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
									  $q_delete_broken_detail="DELETE broken_detail FROM broken_detail, broken_header
															   WHERE broken_detail.brokh_id=broken_header.brokh_id AND branch_id='$branch_id' AND 
																	 broken_detail.brokh_id='$brokh_id'";
									  $exec_delete_broken_detail=mysqli_query($db_connection, $q_delete_broken_detail);
									  if ($exec_delete_broken_detail)
										 {
										   if ($brokh_code==$brokh_code_1)
											   input_broken($branch_id, $brokh_id, $brokh_code, $brokh_date, $brokh_notes, $maker, $input_data);
										   else
											  {
												$q_check_new_broken="SELECT * FROM broken_header WHERE branch_id='$branch_id' AND brokh_code='$brokh_code'";
												$exec_check_new_broken=mysqli_query($db_connection, $q_check_new_broken);
												if (mysqli_num_rows($exec_check_new_broken)>0)
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
												   input_broken($branch_id, $brokh_id, $brokh_code, $brokh_date, $brokh_notes, $maker, $input_data);
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
			 ?>
				<script language="javascript">
				  alert('Transaksi Pengeluaran yang akan diupdate tidak ditemukan!');
				  window.close();
				</script>
			  <?php  
		   }     
	  }	
	  
	 
 function input_broken($branch_id, $brokh_id, $brokh_code, $brokh_date, $brokh_notes, $maker, $input_data, $db_connection)
          {
		    $current_date=date('Y-m-d');
            $q_input_broken_header="UPDATE broken_header SET brokh_code='$brokh_code', brokh_date='$brokh_date', brokh_notes='$brokh_notes', updated_by='$maker', 
			                               updated_time=NOW()
                                    WHERE branch_id='$branch_id' AND brokh_id='$brokh_id'";
			$exec_input_broken_header=mysqli_query($db_connection, $q_input_broken_header);
			if ($exec_input_broken_header)
			   {
			     $q_input_broken_detail="INSERT INTO broken_detail (brokh_id, itemd_id, brokd_qty, brokd_is_canceled, brokd_notes) ".$input_data;
			     $exec_input_broken_detail=mysqli_query($db_connection, $q_input_broken_detail);
			     if ($exec_input_broken_detail)
				    {
			          $q_update_item_status="UPDATE item_detail, broken_detail, broken_header SET itemd_is_broken='1' 
                                             WHERE item_detail.itemd_id=broken_detail.itemd_id AND broken_detail.brokh_id=broken_header.brokh_id AND
                                                   item_detail.branch_id='$branch_id' AND broken_header.branch_id='$branch_id' AND 
												   brokd_is_canceled='0' AND broken_detail.brokh_id='$brokh_id'";	
			          $exec_update_item_status=mysqli_query($db_connection, $q_update_item_status);
			          if ($exec_update_item_status)
					     {
			               $q_check_item_status="SELECT * FROM broken_detail WHERE brokd_is_canceled='0' AND brokh_id='$brokh_id'";
			               $exec_check_item_status=mysqli_query($db_connection, $q_check_item_status);
                           $total_check_item_status=mysqli_num_rows($exec_check_item_status); 
						   if ($total_check_item_status==0)
						      {
							    $q_update_broken_header="UPDATE broken_header SET brokh_is_canceled='1', brokh_canceled_date='$current_date', 
					                                     brokh_canceled_reason='Semua Item dibatalkan'
												         WHERE brokh_id='$brokh_id'";
					            $exec_update_broken_header=mysqli_query($db_connection, $q_update_broken_header);
					            if ($exec_update_broken_header)
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