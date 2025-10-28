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
	  $q_get_reason="SELECT brokh_code, brokh_canceled_date, brokh_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name
	                 FROM broken_header 
					 WHERE brokh_id='$id' AND brokh_is_canceled='1'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $brokh_code=$field_reason['brokh_code'];
		   $brokh_canceled_date=get_date_1($field_reason['brokh_canceled_date']);
		   $brokh_canceled_reason=$field_reason['brokh_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Kerusakan tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 else
    {
	  $q_get_reason="SELECT brokh_code
	                 FROM broken_header 
					 WHERE brokh_id='$id' AND brokh_is_canceled='0'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $current_date=date('d-m-Y');
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $brokh_code=$field_reason['brokh_code'];
		   $brokh_canceled_date=$current_date;
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Kerusakan tidak ditemukan!');
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
      <td colspan="3" align="center">PEMBATALAN KERUSAKAN ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Kerusakan</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $brokh_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $brokh_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $brokh_canceled_reason;?></textarea></td>
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
	       $q_check_broken="SELECT broken_header.*, MONTH(brokh_date) AS month, YEAR(brokh_date) AS year  
		                    FROM broken_header WHERE brokh_id='$id'";
	       $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
	       if (mysqli_num_rows($exec_check_broken)>0)
	          {
		        $field_broken=mysqli_fetch_array($exec_check_broken);
			    $active_period=check_active_period($db_connection, $field_broken['month'], $field_broken['year']);
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
		        if ($field_broken['brokh_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Kerusakan sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
				     $q_check_broken_write_off="SELECT * FROM write_off_header WHERE brokh_id='$id' AND woh_is_canceled='0'";
			         $q_check_broken_dispossal="SELECT * FROM dispossal_header WHERE brokh_id='$id' AND disph_is_canceled='0'";
					 $exec_broken_write_off=mysqli_query($db_connection, $q_check_broken_write_off);
				     $exec_broken_dispossal=mysqli_query($db_connection, $q_check_broken_dispossal);
				     if (mysqli_num_rows($exec_broken_write_off)==0 && mysqli_num_rows($exec_broken_dispossal)==0)
				        {
				          mysqli_autocommit($db_connection, false);
					      $canceled_broken_header="UPDATE broken_header SET brokh_is_canceled='1', brokh_canceled_date=now(),
					                                      brokh_canceled_reason='".htmlspecialchars(trim($_POST['txt_canceled_reason']))."', canceled_by=$maker 
									               WHERE brokh_id='$id'";
						  $canceled_broken_detail="UPDATE broken_detail SET brokd_is_canceled='1' WHERE brokh_id='$id'";
						  $q_update_item_detail_status="UPDATE item_detail SET itemd_is_broken='0' WHERE itemd_id IN (SELECT itemd_id FROM broken_detail 
						                                                                                              WHERE brokh_id='$id')";
					      $exec_canceled_broken_header=mysqli_query($db_connection, $canceled_broken_header);
						  $exec_canceled_broken_detail=mysqli_query($db_connection, $canceled_broken_detail);
						  $exec_update_item_detail_status=mysqli_query($db_connection, $q_update_item_detail_status);
						  if ($exec_canceled_broken_header && $exec_canceled_broken_detail && $exec_update_item_detail_status)
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
						  if (mysqli_num_rows($exec_broken_write_off)>0 && mysqli_num_rows($exec_broken_dispossal)==0)
						     {
				               ?>
	                             <script language="javascript">
		                           alert('Item yang akan dibatalkan sudah diteruskan ke Transaksi Penghapusan!');
		                           window.location.href='javascript:history.back(1)';
		                         </script>
	                           <?php 
				             }
						  else
						  if (mysqli_num_rows($exec_broken_write_off)==0 && mysqli_num_rows($exec_broken_dispossal)>0)
						     {
				               ?>
	                             <script language="javascript">
		                           alert('Item yang akan dibatalkan sudah diteruskan ke Transaksi Penjualan');
		                           window.location.href='javascript:history.back(1)';
		                         </script>
	                           <?php 
				             }
						  else
						  if (mysqli_num_rows($exec_broken_write_off)>0 && mysqli_num_rows($exec_broken_dispossal)>0)
				             {
				               ?>
	                             <script language="javascript">
		                           alert('Item yang akan dibatalkan sudah diteruskan ke Transaksi Penghapusan dan Penjualan!');
		                           window.location.href='javascript:history.back(1)';
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
			     	alert('Kerusakan yang akan dibatalkan tidak ditemukan!');
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
