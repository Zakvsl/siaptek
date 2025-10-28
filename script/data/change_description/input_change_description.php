<?php
  include "../../library/check_session.php";
  $branch_id=$_SESSION['ses_id_branch'];
  $maker=$_SESSION['ses_user_id'];
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  $is_continue=0;
  
	$branch_id=htmlspecialchars($_GET['b']);
	$cidh_id=htmlspecialchars($_POST['txt_id']);
	$cidh_date=htmlspecialchars($_POST['txt_cidh_date']);
	$d=substr($cidh_date,0,2);
	$m=substr($cidh_date,3,2);
	$y=substr($cidh_date,6,4); 
	$cidh_date=$y."-".$m."-".$d;
	$cidh_notes=htmlspecialchars($_POST['txt_cidh_notes']);

   if ($cidh_id=='')  //jika tambah data
	  {
		mysqli_autocommit($db_connection, false);
		$cidh_code=get_no_transaction($db_connection, 'CHG',$branch_id);
		if ($cidh_code!='')
		{
			$cidh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
			$q_check_change_item_description_header="select * from change_item_description_header where cidh_code='$cidh_code' and branch_id='$branch_id'";
			$exec_check_change_item_description_header=mysqli_query($db_connection, $q_check_change_item_description_header);
			if (mysqli_num_rows($exec_check_change_item_description_header)>0)
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
				 $masti_id_new='';
				 $total=count($data);
				 $array_new_masti=array();
				 $itemd_id_not_found=array();
				 foreach ($data as $field_data)
						 {
						   if ($itemd_id=='')
							   $itemd_id="'$field_data'";
						   else
							   $itemd_id=$itemd_id.",'$field_data'";
							   
						   $itemd_id_1='cidd_id_'.$field_data;
						   $new_masti_id='s_new_item_name_'.$field_data;
						   if ($masti_id_new=='')
							   $masti_id_new="'".$_POST[$new_masti_id]."'";
						   else
							   $masti_id_new=$masti_id_new.", '".$_POST[$new_masti_id]."'";
						   
						   $total_new_masti=count($array_new_masti);
						   if ($total_new_masti==0)
							   $array_new_masti[]=$_POST[$new_masti_id];
						   else
							  {
								$j=0;
								for ($i=0; $i<$total_new_masti; $i++)
									{
									  if ($_POST[$new_masti_id]==$array_new_masti[$i])
										  break;
									  else
										  $j++;
									  if ($j-$total_new_masti==0)
										 {
										   $array_new_masti[]=$_POST[$new_masti_id];
										   $itemd_id_not_found[]=$field_data;
										 }  
									}
							  }
						   
							   
						   $cidd_notes='cidd_notes_'.$field_data;
							   
						   if ($input_data=='')
							   $input_data="VALUES ((SELECT cidh_id FROM change_item_description_header WHERE branch_id='$branch_id' AND cidh_code='$cidh_code'), '".$field_data."',(SELECT masti_id FROM item_detail WHERE itemd_id='$field_data'), '".$_POST[$new_masti_id]."','0','".htmlspecialchars($_POST[$cidd_notes])."')";
						   else	  
							   $input_data=$input_data.", ((SELECT cidh_id FROM change_item_description_header WHERE branch_id='$branch_id' AND cidh_code='$cidh_code'), '".$field_data."',(SELECT masti_id FROM item_detail WHERE itemd_id='$field_data'), '".$_POST[$new_masti_id]."','0','".htmlspecialchars($_POST[$cidd_notes])."')";
						 }
							 
					 $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									(SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
									 FROM item_detail
									 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
									 INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
									 WHERE itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
										   item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN($itemd_id)";
					 $q_check_new_masti="SELECT masti_id FROM master_item WHERE masti_id IN ($masti_id_new)";						
										
					// echo $q_check_new_masti;
					// echo $q_check_itemd;
					 $exec_check_itemd=mysqli_query($db_connection, $q_check_itemd);
					 $exec_check_new_masti=mysqli_query($db_connection, $q_check_new_masti);
					 $total_new_masti=mysqli_num_rows($exec_check_new_masti);
					 if ($total_new_masti>0)
						{
						  $new_masti_total=count($array_new_masti);
						  $masti_not_found='';
						  for ($i=0; $i<$new_masti_total; $i++)
								{
								  $k=0;
								  while ($field_new_masti=mysqli_fetch_array($exec_check_new_masti))
										{
										  if ($field_new_masti['masti_id']==$array_new_masti[$i])
											  break;
										  else
											  $k++;      
										} 
								  mysqli_data_seek($exec_check_new_masti, 0);   
								  if ($k-$new_masti_total==0)
									 {
									   if ($masti_not_found=='')
										   $masti_not_found="'".$itemd_id_not_found[$i]."'";
									   else
										   $masti_not_found=$masti_not_found.",'".$itemd_id_not_found[$i]."'";
									 } 
								}
						}
						
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
						  $q_check_change_description="SELECT change_item_description_detail.itemd_id, itemd_code 
													   FROM change_item_description_detail 
													   INNER JOIN item_detail ON item_detail.itemd_id=change_item_description_detail.itemd_id 
													   INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id
													   WHERE cidd_is_canceled='0' AND cidh_is_canceled='0' AND cidh_date>='$cidh_date' AND 
															 change_item_description_detail.itemd_id IN ($itemd_id)
													   GROUP BY itemd_id";
													   
						  $q_check_transfer="SELECT transfer_detail.itemd_id, itemd_code
											 FROM transfer_detail
											 INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
											 INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
											 WHERE transfer_detail.itemd_id IN ($itemd_id) AND branch_id_to='$branch_id' AND tth_is_canceled='0' AND 
												   ttd_is_canceled='0' AND tth_date>'$cidh_date'
											 GROUP BY itemd_id";
						  $q_check_issuing="SELECT issuing_detail.itemd_id, itemd_code
											 FROM issuing_detail 
											 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
											 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
											 WHERE issuing_detail.itemd_id IN ($itemd_id) AND issuingh_is_canceled='0' AND 
												   issuingd_is_canceled='0' AND issuingh_date>'$cidh_date'
											 GROUP BY itemd_id";
						  $q_check_broken="SELECT broken_detail.itemd_id, itemd_code
											 FROM broken_detail 
											 INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id
											 INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
											 WHERE broken_detail.itemd_id IN ($itemd_id) AND brokh_is_canceled='0' AND 
												   brokd_is_canceled='0' AND brokh_date>'$cidh_date'
											 GROUP BY itemd_id";
						  $q_check_dispossal="SELECT dispossal_detail.itemd_id, itemd_code
											  FROM dispossal_detail 
											  INNER JOIN item_detail ON item_detail.itemd_id=dispossal_detail.itemd_id
											  INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
											  WHERE dispossal_detail.itemd_id IN ($itemd_id) AND disph_is_canceled='0' AND 
													dispd_is_canceled='0' AND disph_date>'$cidh_date'
											  GROUP BY itemd_id";
						  $q_check_write_off="SELECT write_off_detail.itemd_id, itemd_code
											 FROM write_off_detail 
											 INNER JOIN item_detail ON item_detail.itemd_id=write_off_detail.itemd_id
											 INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
											 WHERE write_off_detail.itemd_id IN ($itemd_id) AND woh_is_canceled='0' AND 
												   wod_is_canceled='0' AND woh_date>'$cidh_date'
											 GROUP BY itemd_id";									
															
						 //echo $q_check_change_description."<br>";
						 //echo $q_check_transfer."<br>";
						 //echo $q_check_issuing."<br>";
						 //echo $q_check_broken."<br>";
						 //echo $q_check_dispossal."<br>";
						 //echo $q_check_write_off."<br>";
						 
						 $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
						 $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
						 $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
						 $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
						 $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
						 $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
						
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
						  if ($total_new_masti==0)
							 {
							   mysqli_rollback($db_connection);
							   ?>
								  <script language="javascript">
									alert('Semua Deskripsi Item Baru yang dipilih tidak ditemukan!');
									window.location.href='javascript:history.back(1)';
								  </script>
							   <?php
							 }
						  else
						  if ($masti_not_found!='')
							 {
							   mysqli_rollback($db_connection);
							   $q_get_item_not_found="SELECT itemd_code FROM item_detail WHERE itemd_id IN ($masti_not_found)";
							   $exec_get_item_not_found=mysqli_query($db_connection, $q_get_item_not_found);
							   $item_id_not_found='';
							   while ($field_item_not_found=mysqli_fetch_array($exec_get_item_not_found))
									 {
									   if ($item_id_not_found=='')
										   $item_id_not_found=$field_item_not_found['item_dcode'];
									   else
										   $item_id_not_found=$item_id_not_found.", ".$field_item_not_found['item_dcode'];
									 }
							   ?>
								  <script language="javascript">
									var x='<?php echo $item_id_not_found;?>';
									alert('Deskripsi Baru untuk Aset berikut ini tidak ditemukan : \n'+x);
									history.back();
								  </script>
							   <?php
							 }
						  else
						  if (mysqli_num_rows($exec_check_change_description)>0 || mysqli_num_rows($exec_check_transfer)>0 || mysqli_num_rows($exec_check_issuing)>0 || 
							  mysqli_num_rows($exec_check_broken)>0 || mysqli_num_rows($exec_check_dispossal)>0 || mysqli_num_rows($exec_check_write_off)>0)	
							  {
								mysqli_rollback($db_connection);
								$change_description_item_code='';
								$transfer_item_code='';
								$issuing_item_code='';
								$broken_item_code='';
								$dispossal_item_code='';
								$write_off_item_code='';
								while ($field_change_description=mysqli_fetch_array($exec_check_change_description)) 
									  {
										if ($change_description_item_code=='')
											$change_description_item_code=$field_change_description['itemd_code'];
										else
											$change_description_item_code=$change_description_item_code.", ".$field_change_description['itemd_code'];
									  }
								while ($field_transfer=mysqli_fetch_array($exec_check_transfer)) 
									  {
										if ($transfer_item_code=='')
											$transfer_item_code=$field_transfer['itemd_code'];
										else
											$transfer_item_code=$transfer_item_code.", ".$field_transfer['itemd_code'];
									  }
								while ($field_issuing=mysqli_fetch_array($exec_check_issuing)) 
									  {
										if ($issuing_item_code=='')
											$issuing_item_code=$field_issuing['itemd_code'];
										else
											$issuing_item_code=$issuing_item_code.", ".$field_issuing['itemd_code'];
									  }
								while ($field_broken=mysqli_fetch_array($exec_check_broken)) 
									  {
										if ($broken_item_code=='')
											$broken_item_code=$field_broken['itemd_code'];
										else
											$broken_item_code=$broken_item_code.", ".$field_broken['itemd_code'];
									  }
								while ($field_dispossal=mysqli_fetch_array($exec_check_dispossal)) 
									  {
										if ($dispossal_item_code=='')
											$dispossal_item_code=$field_dispossal['itemd_code'];
										else
											$dispossal_item_code=$dispossal_item_code.", ".$field_dispossal['itemd_code'];
									  } 
								while ($field_write_off=mysqli_fetch_array($exec_check_write_off)) 
									  {
										if ($write_off_item_code=='')
											$write_off_item_code=$field_write_off['itemd_code'];
										else
											$write_off_item_code=$write_off_item_code.", ".$field_write_off['itemd_code'];
									  } 
								?>
								   <script language="javascript">
									 var a='<?php echo $change_description_item_code;?>';
									 var b='<?php echo $transfer_item_code;?>';
									 var c='<?php echo $issuing_item_code;?>';
									 var d='<?php echo $broken_item_code;?>';
									 var e='<?php echo $dispossal_item_code;?>';
									 var f='<?php echo $write_off_item_code;?>';
									 alert('Tanggal Perubahan Deskripsi Aset harus lebih besar atau sama dengan tanggal Transaksi : \n'+'1. Perubahan Deskripsi terbaru : '+a+'\n 2. Transfer : '+b+'\n3. Pengeluaran : '+c+'\n4. Kerusakan : '+d+'\n5. Penjualan : '+e+'\n6. Penghapusan : '+f);
									 window.location.href='javascript:history.back(1)';
								   </script>
								<?php 
							  }
						  else
							 {
							   mysqli_autocommit($db_connection, false);
							   $q_input_change_item_description_header="INSERT INTO change_item_description_header (branch_id, cidh_code, cidh_date, cidh_is_canceled, cidh_notes,
																													created_by, created_time)
																		  VALUES ('$branch_id','$cidh_code', '$cidh_date', '0', '$cidh_notes','$maker',NOW())";
							   $q_input_change_item_description_detail="INSERT INTO change_item_description_detail (cidh_id, itemd_id, masti_id_old, masti_id_new, cidd_is_canceled,                                                                                                                    cidd_notes) ".$input_data;
							   $q_update_item_detail="UPDATE item_detail, change_item_description_detail, change_item_description_header
															 SET masti_id=masti_id_new 
													  WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND 
															change_item_description_detail.cidh_id=change_item_description_header.cidh_id
															AND cidh_code='$cidh_code' AND item_detail.branch_id='$branch_id' AND 
																change_item_description_header.branch_id='$branch_id'";
						  //      $q_input_change_item_description_header."<br>";
						  //      $q_input_change_item_description_detail."<br>";
						  //      echo $q_update_item_detail;
							   $exec_input_change_item_description_header=mysqli_query($db_connection, $q_input_change_item_description_header);
							   $exec_input_change_item_description_detail=mysqli_query($db_connection, $q_input_change_item_description_detail);
							   $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
							   if ($exec_input_change_item_description_header && $exec_input_change_item_description_detail && $exec_update_item_detail)
								  {
									update_runing_no($db_connection, 'CHG',$branch_id);
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
   else   // jika update data
	  {
		$cidh_code=htmlspecialchars(trim($_POST['txt_code']));
		$cidh_code_1=htmlspecialchars(trim($_POST['txt_code_1']));
		$q_check_change_item_description_header="select * from change_item_description_header where cidh_id='$cidh_id' and branch_id='$branch_id'";
		$exec_check_change_item_description_header=mysqli_query($db_connection, $q_check_change_item_description_header);
		$field_check_change_item_description_header=mysqli_fetch_array($exec_check_change_item_description_header);
		if ($field_check_change_item_description_header['cidh_is_canceled']=='1')
		   {
			 ?>
				<script language="javascript">
				  alert('Update Transaksi Perubahan Deskripsi Aset tidak dapat dilakukan!\nStatus Transaksi sudah dibatalkan!');
				  window.close();
				</script>
			 <?php
		   } 
		else
		if (mysqli_num_rows($exec_check_change_item_description_header)>0)
		   {	
			 $input_data='';
			 $itemd_id='';
			 $data=$_POST['cb_data'];
			 $detail_qty=0;
			 $masti_id_new='';
			 $total=count($data);
			 $array_new_masti=array();
			 $itemd_id_not_found=array();
			 foreach ($data as $field_data)
					 {
					   if ($itemd_id=='')
						   $itemd_id="'$field_data'";
					   else
						   $itemd_id=$itemd_id.",'$field_data'";
						   
					   $itemd_id_1='cidd_id_'.$field_data;
					   $new_masti_id='s_new_item_name_'.$field_data;
					   if ($masti_id_new=='')
						   $masti_id_new="'".$_POST[$new_masti_id]."'";
					   else
						   $masti_id_new=$masti_id_new.", '".$_POST[$new_masti_id]."'";
					   
					   $total_new_masti=count($array_new_masti);
					   if ($total_new_masti==0)
						   $array_new_masti[]=$_POST[$new_masti_id];
					   else
						  {
							$j=0;
							for ($i=0; $i<$total_new_masti; $i++)
								{
								  if ($_POST[$new_masti_id]==$array_new_masti[$i])
									  break;
								  else
									  $j++;
								  if ($j-$total_new_masti==0)
									 {
									   $array_new_masti[]=$_POST[$new_masti_id];
									   $itemd_id_not_found[]=$field_data;
									 }  
								}
						  }
					   $cidd_is_canceled='cb_cidd_is_canceled_'.$field_data;
					   if (isset($_POST[$cidd_is_canceled])=='')
						   $cidd_is_canceled='0';
					   else
						   $cidd_is_canceled='1';
					   $cidd_notes='cidd_notes_'.$field_data;
						   
					   if ($input_data=='')
						   $input_data="VALUES ('$cidh_id', '".$field_data."',(SELECT masti_id FROM item_detail WHERE itemd_id='$field_data'), '".$_POST[$new_masti_id]."','$cidd_is_canceled','".htmlspecialchars($_POST[$cidd_notes])."')";
					   else	  
						   $input_data=$input_data.", ('$cidh_id', '".$field_data."',(SELECT masti_id FROM item_detail WHERE itemd_id='$field_data'), '".$_POST[$new_masti_id]."','$cidd_is_canceled','".htmlspecialchars($_POST[$cidd_notes])."')";
					 }
			 
				 $q_check_itemd="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, masti_capacity, uom_id_1,  cati_name, itemd_qty, uom_id_2,
									   (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2 
								 FROM item_detail
								 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
								 INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
								 WHERE itemd_is_dispossed='0' AND itemd_status='0' AND itemd_position='Internal' AND 
									   item_detail.branch_id='$branch_id' AND item_detail.itemd_id IN ($itemd_id)";
				 $q_check_new_masti="SELECT masti_id FROM master_item WHERE masti_id IN ($masti_id_new)";
				 $q_check_transfer="SELECT transfer_detail.itemd_id, itemd_code
									FROM transfer_detail
									INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
									INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
									WHERE transfer_detail.itemd_id IN ($itemd_id) AND branch_id_to='$branch_id' AND tth_is_canceled='0' AND 
										  ttd_is_canceled='0' AND tth_date>'$cidh_date'
									GROUP BY itemd_id";
				// echo $q_check_new_masti;
				 $exec_check_itemd=mysqli_query($db_connection, $q_check_itemd);
				 $exec_check_new_masti=mysqli_query($db_connection, $q_check_new_masti);
				 $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
				 $total_new_masti=mysqli_num_rows($exec_check_new_masti);
				 if ($total_new_masti>0)
					{
					  $new_masti_total=count($array_new_masti);
					  $masti_not_found='';
					  for ($i=0; $i<$new_masti_total; $i++)
							{
							  $k=0;
							  while ($field_new_masti=mysqli_fetch_array($exec_check_new_masti))
									{
									  if ($field_new_masti['masti_id']==$array_new_masti[$i])
										  break;
									  else
										  $k++;      
									} 
							  mysqli_data_seek($exec_check_new_masti, 0);   
							  if ($k-$new_masti_total==0)
								 {
								   if ($masti_not_found=='')
									   $masti_not_found="'".$itemd_id_not_found[$i]."'";
								   else
									   $masti_not_found=$masti_not_found.",'".$itemd_id_not_found[$i]."'";
								 } 
							}
					}
					
				 if (mysqli_num_rows($exec_check_itemd)==0)
					{
					  ?>
						 <script language="javascript">
						   alert('Semua detail data tidak ditemukan!\nSilahkan cek posisinya!');
						   window.location.href='javascript:history.back(1)';
						 </script>
					  <?php
					}
				 else	
					{
					  if (mysqli_num_rows($exec_check_itemd)!=$total) 
						 {
						   $no=1;
						   echo "<b>Beberapa detail data tidak ditemukan!</b><br>";
						   echo "<table>";  
						   echo "<tr><td>No</td><td>Kode Item</td><td>Nama Item</td><td>Serial No</td><td>Kapasitas</td></tr>";
						   while ($field_data=mysqli_fetch_array($exec_check_itemd))
								 {
								   echo "<tr><td>".$no++."</td><td>".$field_data['itemd_code']."</td><td>".$field_data['masti_name']."</td><td>".$field_data['itemd_serial_no']."</td><td>".$field_data['masti_capacity']."</td></tr>";
								 }
						   echo "</table>";
						   echo "<br>".$q_check_itemd;
						 }
					  else
					  if ($total_new_masti==0)
						 {
						   ?>
							  <script language="javascript">
								alert('Semua Deskripsi Item Baru yang dipilih tidak ditemukan!');
								window.location.href='javascript:history.back(1)';
							  </script>
						   <?php
						 }
					  else
					  if ($masti_not_found!='')
						 {
						   $q_get_item_not_found="SELECT itemd_code FROM item_detail WHERE itemd_id IN ($masti_not_found)";
						   $exec_get_item_not_found=mysqli_query($db_connection, $q_get_item_not_found);
						   $item_id_not_found='';
						   while ($field_item_not_found=mysqli_fetch_array($exec_get_item_not_found))
								 {
								   if ($item_id_not_found=='')
									   $item_id_not_found=$field_item_not_found['item_dcode'];
								   else
									   $item_id_not_found=$item_id_not_found.", ".$field_item_not_found['item_dcode'];
								 }
						   ?>
							  <script language="javascript">
								var x='<?php echo $item_id_not_found;?>';
								alert('Deskripsi Baru untuk Aset berikut ini tidak ditemukan : \n'+x);
								history.back();
							  </script>
						   <?php
						 }
					  else
					  if (mysqli_num_rows($exec_check_transfer)>0)	
						 {
						   $transfer_item_code='';
						   while ($field_transfer=mysqli_fetch_array($exec_check_transfer)) 
								 {
								   if ($transfer_item_code=='')
									   $transfer_item_code=$field_transfer['itemd_code'];
								   else
									   $transfer_item_code=$transfer_item_code.", ".$field_transfer['itemd_code'];
								 }
						   ?>
							  <script language="javascript">
								var x='<?php echo $transfer_item_code;?>';
								alert('Tanggal Pengeluaran harus lebih besar atau sama dengan tanggal Transfer atas Aset berikut ini : \n'+x);
								window.location.href='javascript:history.back(1)';
							  </script>
						   <?php 
						 }
					  else
						 {
						   $q_check_issuing="SELECT *
											 FROM issuing_detail 
											 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
											 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
											 WHERE issuingd_is_canceled='0' AND issuingh_is_canceled='0' AND issuingh_date>='$cidh_date' AND 
												   issuing_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND 
												   cidh_id='$cidh_id')";
						   $q_check_broken="SELECT * 
											FROM broken_detail 
											INNER JOIN item_detail ON item_detail.itemd_id=broken_detail.itemd_id
											INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
											WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND brokh_date>='$cidh_date' AND 
												  broken_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND 
												  cidh_id='$cidh_id')";
						   $q_check_write_off="SELECT * 
											   FROM write_off_detail 
											   INNER JOIN item_detail ON item_detail.itemd_id=write_off_detail.itemd_id
											   INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
											   WHERE wod_is_canceled='0' AND woh_is_canceled='0' AND woh_date>='$cidh_date' AND 
													 write_off_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND 
													 cidh_id='$cidh_id')";
						   $q_check_dispossal="SELECT *
											   FROM dispossal_detail 
											   INNER JOIN item_detail ON item_detail.itemd_id=dispossal_detail.itemd_id
											   INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
											   WHERE dispd_is_canceled='0' AND disph_is_canceled='0' AND disph_date>='$cidh_date' AND 
													 dispossal_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND 
													 cidh_id='$cidh_id')";
						   $q_check_transfer="SELECT *
											  FROM transfer_detail 
											  INNER JOIN item_detail ON item_detail.itemd_id=transfer_detail.itemd_id
											  INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
											  WHERE ttd_is_canceled='0' AND tth_is_canceled='0' AND tth_date>='$cidh_date' AND 
													transfer_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND 
													cidh_id='$cidh_id')";
						   $q_check_change_description="SELECT *
														FROM change_item_description_detail 
														INNER JOIN change_item_description_header ON 
																   change_item_description_header.cidh_id=change_item_description_detail.cidh_id
														WHERE cidd_is_canceled='0' AND cidh_is_canceled='0' AND cidh_date>='$cidh_date' AND 
														change_item_description_detail.cidh_id!='$cidh_id' AND 
														itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$cidh_id')";
						   //echo $q_check_issuing."<br>";
						   //echo $q_check_broken."<br>";
						   //echo $q_check_dispossal."<br>";
						   //echo $q_check_transfer."<br>";
						   //echo $q_check_change_description."<br>";
						   
						   $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
						   $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
						   $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
						   $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
						   $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
						   $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
						   if (mysqli_num_rows($exec_check_issuing)>0 || mysqli_num_rows($exec_check_broken)>0 || mysqli_num_rows($exec_check_write_off)>0 || 
							   mysqli_num_rows($exec_check_dispossal)>0 || mysqli_num_rows($exec_check_transfer)>0 || mysqli_num_rows($exec_check_change_description)>0)
							  {
								if (mysqli_num_rows($exec_check_issuing)>0)
									$issuing='Ya';
								else
									$issuing='Tidak';
								if (mysqli_num_rows($exec_check_broken)>0)
									$broken='Ya';
								else
									$broken='Tidak';
								if (mysqli_num_rows($exec_check_write_off)>0)
									$write_off='Ya';
								else
									$write_off='Tidak';
								if (mysqli_num_rows($exec_check_dispossal)>0)
									$dispossal='Ya';
								else
									$dispossal='Tidak';
								if (mysqli_num_rows($exec_check_transfer)>0) 
									$transfer='Ya';
								else
									$transfer='Tidak';
								if (mysqli_num_rows($exec_check_change_description)>0)
									$change='Ya';
								else
									$change='Tidak';
								?>
								   <script language="javascript">
									  var issuing='<?php echo $issuing;?>';
									  var broken='<?php echo $broken;?>';
									  var write_off='<?php echo $write_off;?>';
									  var dispossal='<?php echo $dispossal;?>';
									  var transfer='<?php echo $transfer;?>';
									  var change='<?php echo $change;?>';
									  alert('Transaksi Perubahan Deskripsi Aset tidak dapat dilakukan. Ada Aset yang sudah digunakan pada transaksi berikut ini : \n1. Pengeluaran Aset : '+issuing+'\n2. Kerusakan : '+broken+'\n3. Penghapusan : '+write_off+'\n4. Penjualan : '+dispossal+'\n5. Perpindahan Aset : '+transfer+'\n6. Perubahan Deskripsi : '+change);
									  history.back();
								   </script>
								<?php	 
							  }  
						   else
							  {
								mysqli_autocommit($db_connection, false);
								$q_update_item_detail="UPDATE item_detail, change_item_description_detail SET masti_id=masti_id_old
													   WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND cidh_id='$cidh_id' AND cidd_is_canceled='0'";
								$q_delete_change_item_description_detail="DELETE change_item_description_detail FROM change_item_description_detail,                   
																				 change_item_description_header
																		  WHERE change_item_description_detail.cidh_id=change_item_description_header.cidh_id AND 
																				branch_id='$branch_id' AND change_item_description_detail.cidh_id='$cidh_id' ";
							//	echo $q_update_item_detail;
								$q_exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
								$exec_delete_change_item_description_detail=mysqli_query($db_connection, $q_delete_change_item_description_detail);
								if ($q_update_item_detail && $exec_delete_change_item_description_detail)
								   {
									if ($cidh_code==$cidh_code_1)
										 input_change_item_description($branch_id, $cidh_id, $cidh_code, $cidh_date, $cidh_notes, $maker, $input_data);
									else
									   {
								  //	 echo $cidh_code."<br>";
								  //	 echo $cidh_code_1;
										 $q_check_new_change_description="SELECT * FROM change_item_description_header WHERE branch_id='$branch_id' AND cidh_code='$cidh_code'";
										 $exec_check_new_change_description=mysqli_query($db_connection, $q_check_new_change_description);
										 if (mysqli_num_rows($exec_check_new_change_description)>0)
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
										   input_change_item_description($branch_id, $cidh_id, $cidh_code, $cidh_date, $cidh_notes, $maker, $input_data);
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
	  
	 
 function input_change_item_description($branch_id, $cidh_id, $cidh_code, $cidh_date, $cidh_notes, $maker, $input_data, $db_connection)
          {
		    $current_date=date('Y-m-d');
            $q_input_change_item_description_header="UPDATE change_item_description_header SET cidh_code='$cidh_code', cidh_date='$cidh_date', cidh_notes='$cidh_notes',
			                                                updated_by='$maker', updated_time=NOW()
                                                     WHERE branch_id='$branch_id' AND cidh_id='$cidh_id'";
			$q_input_change_item_description_detail="INSERT INTO change_item_description_detail (cidh_id, itemd_id, masti_id_old, masti_id_new, cidd_is_canceled,                                                                                                 cidd_notes) ".$input_data;
			$q_update_item_detail="UPDATE item_detail, change_item_description_detail,change_item_description_header SET masti_id=masti_id_new 
                                   WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND 
								         change_item_description_header.cidh_id=change_item_description_detail.cidh_id AND
                                         cidh_code='$cidh_code' AND item_detail.branch_id='$branch_id' AND change_item_description_header.`branch_id`='$branch_id'";
		//	echo $q_input_change_item_description_header."<br>";
		//	echo $q_input_change_item_description_detail ."<br>";
		//	echo $q_update_item_detail;
			$exec_input_change_item_description_header=mysqli_query($db_connection, $q_input_change_item_description_header);
			$exec_input_change_item_description_detail=mysqli_query($db_connection, $q_input_change_item_description_detail);
			$exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
			if ($exec_input_change_item_description_header && $exec_input_change_item_description_detail && $exec_update_item_detail)
			   {
			      if ($total_check_item_status==0)
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

?>		  