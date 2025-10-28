<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $maker=$_SESSION['ses_user_id'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php"; 
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);	
 $action=mysqli_real_escape_string($db_connection, $_GET['a']);	
 $maker=$_SESSION['ses_user_id'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);	
 
 compare_branch($branch_id, $branch_id_transaction);
 
 if ($action=='d')
    {
	  $q_get_reason="SELECT woh_code, woh_canceled_date, woh_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name
	                 FROM write_off_header 
					 WHERE woh_id='$id' AND woh_is_canceled='1'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $woh_code=$field_reason['woh_code'];
		   $woh_canceled_date=get_date_1($field_reason['woh_canceled_date']);
		   $woh_canceled_reason=$field_reason['woh_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Penghapusan Aset tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 else
    {
	  $q_get_reason="SELECT woh_code, woh_sources, brokh_id 
	                 FROM write_off_header 
					 WHERE woh_id='$id' AND woh_is_canceled='0'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $current_date=date('d-m-Y');
		   while ($field_reason=mysqli_fetch_array($exec_get_reason))
		         {
		           $woh_code=$field_reason['woh_code'];
				   $woh_sources=$field_reason['woh_sources'];
				   $brokh_id=$field_reason['brokh_id'];
				 }
		   $woh_canceled_date=$current_date;
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Penghapusan Aset tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 $current_date=date('Y-m-d');	
?>

<form id="form1" name="form1" method="post" action="">
  <table width="100%" border="0">
    <tr>
      <td colspan="3" align="center">PEMBATALAN PENGHAPUSAN ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">No Transaksi </td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $woh_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $woh_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $woh_canceled_reason;?></textarea></td>
    </tr>
	<?php
	    if ($action=='d')
		   {
		     echo "<tr>";
			 echo "<td nowrap='nowrap'>Dibatalkan Oleh</td>";
			 echo "<td>:</td>";
			 echo "<td>".$canceled_by."</td>"; 
			 echo "</tr>";
		   }
	  ?>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>
        <input type="submit" id="btn_simpan" name="btn_simpan" value="Simpan" <?php if ($action=='d') echo "disabled='disabled'";?>/>
        <input type="button" name="Submit2" value="Tutup" onclick="window.close()"/></td>
    </tr>
  </table>
</form>

<?php
 
 if (isset($_POST['btn_simpan']))
    {
	  compare_branch($branch_id, $branch_id_transaction);
	  
	  $reason=htmlspecialchars(trim($_POST['txt_canceled_reason']));
	  if (!empty($reason))
	     {
	       $q_check_write_off="SELECT write_off_header.*, MONTH(woh_date) AS month, YEAR(woh_date) AS year  
		                       FROM write_off_header WHERE woh_id='$id' AND branch_id='$branch_id'";
	       $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
	       if (mysqli_num_rows($exec_check_write_off)>0)
	          {
		        $field_write_off=mysqli_fetch_array($exec_check_write_off);
			    $active_period=check_active_period($db_connection, $field_write_off['month'], $field_write_off['year']);
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
		        if ($field_write_off['woh_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Transaksi Penghapusan Aset sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
			   	     $woh_id=$field_write_off['woh_id'];
			   	     $woh_date=$field_write_off['woh_date'];
					 $q_get_itemd_id="SELECT itemd_id, wod_id FROM write_off_detail WHERE wod_is_canceled='0' AND woh_id='$woh_id'";
					 $exec_get_itemd_id=mysqli_query($db_connection, $q_get_itemd_id);
					 if (mysqli_num_rows($exec_get_itemd_id)==0)
					    {
						  ?>
			                <script language="javascript">
				                alert('Semua aset sudah ditabatalkan!');
				                window.close();
		                    </script>
		                  <?php
 						}
					 else
					    {
						  $itemd_id="";
						  $wod_id="";
						  while ($field_itemd_id=mysqli_fetch_array($exec_get_itemd_id))
						        {
								  if ($itemd_id=='')
								      $itemd_id="'".$field_itemd_id['itemd_id']."'";
								  else
								      $itemd_id=$itemd_id.", '".$field_itemd_id['itemd_id']."'";
								  
								  if ($wod_id=='')
								      $wod_id="'".$field_itemd_id['wod_id']."'";
								  else
								      $wod_id=$wod_id.", '".$field_itemd_id['wod_id']."'";
								}

						  $is_continue='1';
				          mysqli_autocommit($db_connection, false);
						  if ($woh_sources=='1')
						     {
							   $q_upate_broken_detail="UPDATE broken_detail, write_off_detail SET brokd_is_wo='0'
					                                   WHERE broken_detail.brokd_id=write_off_detail.brokd_id AND woh_id='$id' AND wod_is_canceled='0'";
							   $exec_upate_broken_detail=mysqli_query($db_connection, $q_upate_broken_detail);
						       if (!$exec_upate_broken_detail)
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
							   else
								  {
							        $q_check_broken="SELECT brokh_id AS brokh_id_1, 
                                                           (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0') AS total,
                                                           (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND 
															brokd_is_wo='1') AS total_wo,
                                                           (SELECT COUNT(*) FROM broken_detail WHERE brokh_id=brokh_id_1 AND brokd_is_canceled='0' AND 
															brokd_is_dispossed='1') AS total_dispossed
                                                     FROM broken_header
                                                     WHERE brokh_id IN ('$brokh_id') AND brokh_is_canceled='0'";
										 
								    $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
				                    $total_check_broken=mysqli_num_rows($exec_check_broken);
				                    if ($total_check_broken>0)
				                       {											
					                     $field_broken=mysqli_fetch_array($exec_check_broken);
					                     $total_broken=$field_broken['total'];
					                     $total_wo=$field_broken['total_wo'];
					                     $total_dispossed=$field_broken['total_dispossed'];
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
									     if (!$exec_update_broken_header_status)
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
										 $is_continue=='0';
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
								
							 }
									   
						  if ($is_continue=='1')  
							 {
					           $canceled_write_off_header="UPDATE write_off_header SET woh_is_canceled='1', woh_canceled_date=NOW(),
					                                              woh_canceled_reason='".htmlspecialchars(trim($_POST['txt_canceled_reason']))."', canceled_by='$maker' 
									                       WHERE woh_id='$id'";
							   $canceled_write_off_detail="UPDATE write_off_detail SET wod_is_canceled='1' WHERE woh_id='$id'";
					           $q_update_item_detail_status="UPDATE item_detail SET itemd_position='Internal', itemd_is_wo='0', itemd_status='0'  
						                                     WHERE itemd_id IN ($itemd_id)";
                      							   
					           $exec_canceled_write_off_header=mysqli_query($db_connection, $canceled_write_off_header);
							   $exec_canceled_write_off_detail=mysqli_query($db_connection, $canceled_write_off_detail);
				               $exec_update_item_detail_status=mysqli_query($db_connection, $q_update_item_detail_status);
					           
					           if ($exec_canceled_write_off_header && $exec_canceled_write_off_detail && $exec_update_item_detail_status)
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
				                         window.close();
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
			     	alert('Transaksi Penghapusan Aset yang akan dibatalkan tidak ditemukan!');
			     	window.close();
		           </script>
		        <?php
		      }	
		 }
	  else
	     {
		   ?>
	          <script language="javascript">
		        alert('Alasan Pembatalan harus diisi!');
		        window.location.href='javascript:history.back(1)';
		      </script>
	       <?php 
		 } 
	}      
?>
