<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $maker=$_SESSION['ses_user_id'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);	
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 $action=mysqli_real_escape_string($db_connection, $_GET['a']);	
 
 compare_branch($branch_id, $branch_id_transaction);
  
 if ($action=='d')
    {
	  $q_get_reason="SELECT issuingh_code, issuingh_canceled_date, issuingh_canceled_reason, canceled_by as user_id, 
	                       (select users_names from users where users_id=user_id) as users_name  
	                 FROM issuing_header 
					 WHERE issuingh_id='$id' AND issuingh_is_canceled='1'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection,$q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $issuingh_code=$field_reason['issuingh_code'];
		   $issuingh_canceled_date=get_date_1($field_reason['issuingh_canceled_date']);
		   $issuingh_canceled_reason=$field_reason['issuingh_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Pengeluaran tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 else
    {
	  $q_get_reason="SELECT issuingh_code, issuingh_is_canceled, issuingh_status
	                 FROM issuing_header 
					 WHERE issuingh_id='$id'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection,$q_get_reason);
	  $field_data=mysqli_fetch_array($exec_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   if ($field_data['issuingh_is_canceled']=='0')
		      {
		        $current_date=date('d-m-Y');
		        $field_reason=mysqli_fetch_array($exec_get_reason);
		        $issuingh_code=$field_data['issuingh_code'];
		        $issuingh_canceled_date=$current_date;
			  }
		   else
		      {
		        ?>
			       <script language="javascript">
				      alert('Transaksi Pengeluaran sudah dibatalkan!');
				      window.close();
			       </script>
		        <?php			    
			  }
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Pengeluaran tidak ditemukan!');
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
      <td colspan="3" align="center">PEMBATALAN PENGELUARAN ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Pengeluaran</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $issuingh_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $issuingh_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $issuingh_canceled_reason;?></textarea></td>
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
	       $q_check_issuing="SELECT issuing_header.*, MONTH(issuingh_date) AS month, YEAR(issuingh_date) AS year  
		                     FROM issuing_header WHERE issuingh_id='$id'";
	       $exec_check_issuing=mysqli_query($db_connection,$q_check_issuing);
	       if (mysqli_num_rows($exec_check_issuing)>0)
	          {
		        $field_issuing=mysqli_fetch_array($exec_check_issuing);
				$active_period=check_active_period($db_connection, $field_issuing['month'], $field_issuing['year']);
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
		        if ($field_issuing['issuingh_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Pengeluaran sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
				     if ($field_issuing['issuingh_status']!='0')
					    {
						  ?>
			                 <script language="javascript">
				                alert('Pengeluaran tidak dapat batalkan!\nSudah diteruskan ke transaksi Pengembalian');
				                window.close();
		                     </script>						     
						  <?php
						}
					 else
					    {
			              $q_check_issuing_return="SELECT * FROM return_header WHERE issuingh_id='$id' AND reth_is_canceled='0'";
				          $exec_issuing_return=mysqli_query($db_connection,$q_check_issuing_return);
				          if (mysqli_num_rows($exec_issuing_return)==0)
				             {
						       $itemd_id="";
						       $q_get_tube="SELECT itemd_id FROM issuing_detail 
						                    WHERE issuingh_id='$id' AND issuingd_is_canceled='0' AND issuingd_is_return='0'";
						       $exec_get_tube=mysqli_query($db_connection,$q_get_tube);
						       while ($field_itemd_id=mysqli_fetch_array($exec_get_tube))
						             {
								       if ($itemd_id=='') 
								           $itemd_id="'".$field_itemd_id['itemd_id']."'";
								       else
								           $itemd_id=$itemd_id.",'".$field_itemd_id['itemd_id']."'";
								     }
						       mysqli_autocommit($db_connection, false);
					           $canceled_issuing="UPDATE issuing_header SET issuingh_is_canceled='1', issuingh_canceled_date=NOW(),
					                                     issuingh_canceled_reason='".htmlspecialchars(trim($_POST['txt_canceled_reason']))."', canceled_by=$maker  
									              WHERE issuingh_id='$id'";
						       $q_update_issuing_detail="UPDATE issuing_detail SET issuingd_is_canceled='1' WHERE issuingh_id='$id'";
						       $q_update_item_detail_status="UPDATE item_detail SET itemd_position='Internal' WHERE itemd_id IN ($itemd_id)";
						       $exec_canceled_issuing=mysqli_query($db_connection,$canceled_issuing);
						       $exec_canceled_issuing_detail=mysqli_query($db_connection,$q_update_issuing_detail);
						       $exec_update_item_detail_status=mysqli_query($db_connection,$q_update_item_detail_status);
						       if ($exec_canceled_issuing && $q_update_issuing_detail && $exec_update_item_detail_status)
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
				          else
				             {
							   mysqli_rollback($db_connection);
				               ?>
			                      <script language="javascript">
				                    alert('Pengeluaran tidak dapat batalkan!\nSudah diteruskan ke transaksi Pengembalian');
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
			     	alert('Pengeluaran yang akan dibatalkan tidak ditemukan!');
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
