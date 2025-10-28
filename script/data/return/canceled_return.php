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
	  $q_get_reason="SELECT reth_code, reth_canceled_date, reth_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name  
	                 FROM return_header 
					 WHERE reth_id='$id' AND reth_is_canceled='1'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $reth_code=$field_reason['reth_code'];
		   $reth_canceled_date=get_date_1($field_reason['reth_canceled_date']);
		   $reth_canceled_reason=$field_reason['reth_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
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
 else
    {
	  $q_get_reason="SELECT reth_code
	                 FROM return_header 
					 WHERE reth_id='$id' AND reth_is_canceled='0' AND branch_id='$branch_id'";
	 // echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $current_date=date('d-m-Y');
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $reth_code=$field_reason['reth_code'];
		   $reth_canceled_date=$current_date;
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
 $current_date=date('Y-m-d');	
?>

<form id="form1" name="form1" method="post" action="">
  <table width="100%" border="0">
    <tr>
      <td colspan="3" align="center">PEMBATALAN PENGEMBALIAN ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Pengembalian</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $reth_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $reth_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $reth_canceled_reason;?></textarea></td>
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
	  $branch_id=$_SESSION['ses_id_branch'];
	  compare_branch($branch_id, $branch_id_transaction);
	  $reason=htmlspecialchars(trim($_POST['txt_canceled_reason']));
	  if (!empty($reason))
	     {
	       $q_check_return="SELECT return_header.*, issuingh_type, MONTH(reth_date) AS month, YEAR(reth_date) AS year 
		                    FROM return_header 
		                    INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id 
							WHERE reth_id='$id'";
	       $exec_check_return=mysqli_query($db_connection, $q_check_return);
	       if (mysqli_num_rows($exec_check_return)>0)
	          {
		        $field_return=mysqli_fetch_array($exec_check_return);
				$issuingh_id=$field_return['issuingh_id'];
					 
			    $active_period=check_active_period($db_connection, $field_return['month'], $field_return['year']);
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
		        if ($field_return['reth_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Pengembalian sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
			   	     $issuingh_id=$field_return['issuingh_id'];
			   	     $reth_id=$field_return['reth_id'];
			   	     $reth_date=$field_return['reth_date'];
			   	     $reth_created_time=$field_return['created_time'];
					 if ($field_return['issuingh_type']=='0')
					     $issuingh_type='Customer';
					 else
					     $issuingh_type='Vendor';
					 
				     $q_get_tube_id_return="SELECT issuing_detail.itemd_id, retd_id, return_detail.issuingd_id 
					                        FROM return_detail 
					                        INNER JOIN issuing_detail ON issuing_detail.issuingd_id=return_detail.issuingd_id
					                        WHERE reth_id='$id' AND retd_is_canceled='0'";
					 //echo $q_get_tube_id_return."<br>";
					 $exec_get_tube_id_return=mysqli_query($db_connection, $q_get_tube_id_return);
					 $tube_id="";
					 $issuingd_id="";
					 while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id_return))
					       {
						     if ($tube_id=="")
							     $tube_id="'".$field_tube_id['itemd_id']."'";
							 else
							     $tube_id=$tube_id.",'".$field_tube_id['itemd_id']."'";
							 
							 if ($issuingd_id=="")
							     $issuingd_id="'".$field_tube_id['issuingd_id']."'";
							 else
							     $issuingd_id=$issuingd_id.",'".$field_tube_id['issuingd_id']."'";
							 
						   }
					 $action="EDIT";
					 $reth_date_old=$reth_date;
					 $reth_date_new=$reth_date;
					 $reth_created_time=$reth_created_time;
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
				          mysqli_autocommit($db_connection, false);
				          $q_update_item_detail_status="UPDATE item_detail, issuing_detail, return_detail 
						                                       SET itemd_position='$issuingh_type', item_detail.whsl_id=whsl_id_first
													    WHERE item_detail.itemd_id=issuing_detail.itemd_id AND issuing_detail.issuingd_id=return_detail.issuingd_id AND
														      retd_is_canceled='0' AND reth_id='$id'"; 
			              $q_update_issuing_detail="UPDATE issuing_detail SET issuingd_is_return='0' 
                                                    WHERE issuingd_id IN ($issuingd_id)";
						  $q_update_issuing_header="UPDATE issuing_header 
                                                           SET issuingh_status=(IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingd_is_return='0' AND 
															   issuingh_id='$issuingh_id' AND issuingd_is_canceled='0')=0,'2',
                                                               IF((SELECT COUNT(*) FROM issuing_detail WHERE issuingh_id='$issuingh_id' AND 
															   issuingd_is_canceled='0' AND issuingd_is_return='0')!=
															  (SELECT COUNT(*) FROM issuing_detail 
															   WHERE issuingh_id='$issuingh_id' AND issuingd_is_canceled='0'),'1','0')))
                                                    WHERE issuingh_id='$issuingh_id'";
													
					      $canceled_return="UPDATE return_header SET reth_is_canceled='1', reth_canceled_date=now(),
					                               reth_canceled_reason='".htmlspecialchars(trim($_POST['txt_canceled_reason']))."', canceled_by='$maker'
									        WHERE reth_id='$id'";
						  $canceled_return_detail="UPDATE return_detail SET retd_is_canceled='1' WHERE reth_id='$id'";
					      $exec_update_item_detail_status=mysqli_query($db_connection, $q_update_item_detail_status);
						  $exec_issuing_detail=mysqli_query($db_connection, $q_update_issuing_detail);
						  $exec_issuing_header=mysqli_query($db_connection, $q_update_issuing_header);
						  $exec_canceled_return=mysqli_query($db_connection, $canceled_return);
						  $exec_canceled_return_detail=mysqli_query($db_connection, $canceled_return_detail);
						  if ($exec_update_item_detail_status && $exec_issuing_detail && $exec_issuing_header && $exec_canceled_return && $exec_canceled_return_detail) 
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
	       else
	          {
		        ?>
			       <script language="javascript">
			     	alert('Pengembalianyang akan dibatalkan tidak ditemukan!');
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
