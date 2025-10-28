<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $maker=$_SESSION['ses_user_id'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);	
 $action=mysqli_real_escape_string($db_connection, $_GET['a']);
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);	
 
 compare_branch($branch_id, $branch_id_transaction);
  
 if ($action=='d')
    {
	  $q_get_reason="SELECT rth_code, rth_canceled_date, rth_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name  
	                 FROM receipt_transfer_header 
					 WHERE rth_id='$id' AND rth_is_canceled='1'";
	 // echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $rth_code=$field_reason['rth_code'];
		   $rth_canceled_date=get_date_1($field_reason['rth_canceled_date']);
		   $rth_canceled_reason=$field_reason['rth_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Penerimaan Transfer tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 else
    {
	  $q_get_reason="SELECT rth_code
	                 FROM receipt_transfer_header 
					 WHERE rth_id='$id' AND rth_is_canceled='0'";
	  //echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $current_date=date('d-m-Y');
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $rth_code=$field_reason['rth_code'];
		   $rth_canceled_date=$current_date;
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Penerimaan Transfer tidak ditemukan!');
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
      <td colspan="3" align="center">PEMBATALAN PENERIMAAN TRANSFER ASET<td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Transfer</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $rth_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $rth_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $rth_canceled_reason;?></textarea></td>
    </tr>
	  <?php
	    if ($action=='d')
		   {
		     echo "<tr>";
			 echo "<td>Dibatalkan Oleh</td>";
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
	       $q_check_receipt_transfer="SELECT receipt_transfer_header.*, MONTH(rth_date) AS month, YEAR(rth_date) AS year  
		                              FROM receipt_transfer_header WHERE rth_id='$id'";
	       $exec_check_receipt_transfer=mysqli_query($db_connection, $q_check_receipt_transfer);
	       if (mysqli_num_rows($exec_check_receipt_transfer)>0)
	          {
		        $field_receipt_transfer=mysqli_fetch_array($exec_check_receipt_transfer);
				$active_period=check_active_period($db_connection, $field_receipt_transfer['month'], $field_receipt_transfer['year']);
				$tth_id=$field_receipt_transfer['tth_id'];
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
		        if ($field_receipt_transfer['rth_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Penerimaan Transfer Aset sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
			   	     $tth_id=$field_receipt_transfer['tth_id'];
			   	     $rth_id=$field_receipt_transfer['rth_id'];
			   	     $rth_date=$field_receipt_transfer['rth_date'];
					 $branch_id_from=$field_receipt_transfer['branch_id_from'];
			   	     $rth_created_time=$field_receipt_transfer['created_time'];
					 $q_get_itemd_id="SELECT itemd_id, ttd_id FROM receipt_transfer_detail WHERE rtd_is_canceled='0' AND rth_id='$rth_id'";
					 $exec_get_itemd_id=mysqli_query($db_connection, $q_get_itemd_id);
					 if (mysqli_num_rows($exec_get_itemd_id)==0)
					    {
						  ?>
			                <script language="javascript">
				                alert('Semua Aset sudah ditabatalkan!');
				                window.close();
		                    </script>
		                  <?php
 						}
					 else
					    {
						  $itemd_id="";
						  $ttd_id="";
						  while ($field_itemd_id=mysqli_fetch_array($exec_get_itemd_id))
						        {
								  if ($itemd_id=='')
								      $itemd_id="'".$field_itemd_id['itemd_id']."'";
								  else
								      $itemd_id=$itemd_id.", '".$field_itemd_id['itemd_id']."'";
								  
								  if ($ttd_id=='')
								      $ttd_id="'".$field_itemd_id['ttd_id']."'";
								  else
								      $ttd_id=$ttd_id.", '".$field_itemd_id['ttd_id']."'";
								}
						  
						  $action="EDIT";
						  $rth_date_old=$rth_date;
						  $rth_date_new=$rth_date;
						  $rth_created_time=$rth_created_time; 
						  $messages=is_there_any_new_trans($db_connection, $action, $rth_id, $itemd_id, $rth_date_old, $rth_date_new, $rth_created_time);
						  
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
					                          WHERE itemd_id IN ($itemd_id) AND (itemd_position!='Internal' OR itemd_status='1' OR 
									                itemd_is_broken='1' OR itemd_is_wo='1' OR itemd_is_dispossed='1' OR branch_id!='$branch_id')";
					           //echo $q_check_item;
							   $exec_check_item=mysqli_query($db_connection, $q_check_item);
					           if (mysqli_num_rows($exec_check_item)>0)
					              {
						            $tube_code="";
						            while ($tube_id_error=mysqli_fetch_array($exec_check_item))
						                  {
								            if ($tube_code=="")
								                $tube_code=$tube_id_error['itemd_code'];
								            else
								                $tube_code=$tube_code.",".$tube_id_error['itemd_code'];
								          }
						            ?>
						               <script language="javascript">
							              var x='<?php echo $tube_code;?>';
								          alert('Mohon cek status Aset berikut ini :\n'+x);
								          history.back(1);
							           </script>
						            <?php
						          } 
					           else
					              {
									     mysqli_autocommit($db_connection, false);
				                         $q_update_transfer_detail="UPDATE transfer_detail SET ttd_status='0' 
                                                                    WHERE ttd_id IN ($ttd_id)";
									     $exec_update_transfer_detail=mysqli_query($db_connection, $q_update_transfer_detail);
									     if (!$exec_update_transfer_detail)						   
									        {
							                  mysqli_rollback($db_connection); 
							                  ?>
	                                               <script language="javascript">
								                      alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                                              history.back(1);
		                                           </script>
	                                          <?php									     
									        }
									     else
									        {
									          $q_check_transfer_detail_active="SELECT COUNT(*) AS total_active 
										                                       FROM transfer_detail 
																		       WHERE ttd_is_canceled='0' AND tth_id='$tth_id'";
									   	      $q_check_transfer_detail_receipt="SELECT COUNT(*) AS total_receipt
										                                        FROM transfer_detail 
																		        WHERE ttd_is_canceled='0' AND ttd_status='1' AND tth_id='$tth_id'";	
										      $exec_check_transfer_detail_active=mysqli_query($db_connection, $q_check_transfer_detail_active);
										      $exec_check_transfer_detail_receipt=mysqli_query($db_connection, $q_check_transfer_detail_receipt);					   
										      $field_1=mysqli_fetch_array($exec_check_transfer_detail_active);
										      $field_2=mysqli_fetch_array($exec_check_transfer_detail_receipt);
										      if ($field_2['total_receipt']=='0')
										          $q_update_transfer_header_status="UPDATE transfer_header SET tth_status='0' WHERE tth_id='$tth_id'";  
										      else
										      if ($field_1['total_active']==$field_2['total_receipt'])
										          $q_update_transfer_header_status="UPDATE transfer_header SET tth_status='2' WHERE tth_id='$tth_id'"; 
										      else
										           $q_update_transfer_header_status="UPDATE transfer_header SET tth_status='1' WHERE tth_id='$tth_id'"; 
                                              $exec_update_transfer_header_status=mysqli_query($db_connection, $q_update_transfer_header_status);
										      if (!$exec_update_transfer_header_status)
										         {
											       mysqli_rollback($db_connection); 
							                       ?>
	                                                  <script language="javascript">
								                         alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                                                 history.back(1);
		                                              </script>
	                                               <?php
											     }
										      else
										         {
											       $tube_id="";
											       $q_get_tube_transfer_detail="SELECT * FROM transfer_detail TD
																		        INNER JOIN receipt_transfer_detail RTD ON RTD.ttd_id=td.ttd_id 
																		        WHERE  rtd_is_canceled='0' AND tth_id='$tth_id' AND TD.ttd_id IN ($ttd_id) AND rth_id='$rth_id'";
											       $exec_get_tube_transfer_detail=mysqli_query($db_connection, $q_get_tube_transfer_detail);
											       //echo $q_get_tube_transfer_detail."<br>";
											       while ($field_3=mysqli_fetch_array($exec_get_tube_transfer_detail))
											             {
													       $q_update_item_detail="UPDATE item_detail SET branch_id='$branch_id_from',                         
													                                      item_detail.whsl_id='".$field_3['whsl_id_old']."', 
																                  itemd_position='In Transit' 
																			      WHERE itemd_id='".$field_3['itemd_id']."'";
													       $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
													       //echo $q_update_item_detail;
													       if (!$exec_update_item_detail)
													          {
														        mysqli_rollback($db_connection); 
							                                    ?>
	                                                               <script language="javascript">
								                                      alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                                                              history.back(1);
		                                                           </script>
	                                                            <?php 
														        exit;
														      }
													       else
													          {
														        if ($tube_id=='')
														            $tube_id="'".$field_3['itemd_id']."'";
														        else
														            $tube_id=$tube_id.",'".$field_3['itemd_id']."'";
														      }
													     }
													
						                           $q_update_receipt_transfer_header_status="UPDATE receipt_transfer_header
                                                                                                    SET rth_is_canceled='1', rth_canceled_date=NOW(), 
																		                            rth_canceled_reason='".$_POST['txt_canceled_reason']."',
																					                canceled_by='".$maker."'
                                                                                             WHERE rth_id='$rth_id' AND branch_id='$branch_id'";
									               $q_update_receipt_transfer_detail_status="UPDATE receipt_transfer_detail SET rtd_is_canceled='1' WHERE rth_id='$rth_id'";
						                           $exec_update_receipt_transfer_header_status=mysqli_query($db_connection, $q_update_receipt_transfer_header_status);
									               $exec_update_receipt_transfer_detail_status=mysqli_query($db_connection, $q_update_receipt_transfer_detail_status);
						                           if ($exec_update_receipt_transfer_header_status && $exec_update_receipt_transfer_detail_status)
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
		                                                      history.back(1);
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
