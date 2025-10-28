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
	       $q_get_reason="SELECT tth_code, tth_canceled_date, tth_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name  
	                      FROM transfer_header 
					      WHERE tth_id='$id' AND tth_is_canceled='1' AND branch_id='$branch_id'";
	       //echo $q_get_reason;
	       $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	       if (mysqli_num_rows($exec_get_reason)>0)
	          {
		        $field_reason=mysqli_fetch_array($exec_get_reason);
		        $tth_code=$field_reason['tth_code'];
		        $tth_canceled_date=get_date_1($field_reason['tth_canceled_date']);
		        $tth_canceled_reason=$field_reason['tth_canceled_reason'];
		        $canceled_by=$field_reason['users_name'];
		      }
	       else
	          {
		        ?>
			        <script language="javascript">
				       alert('Transaksi Perpindahan Aset tidak ditemukan!');
				       window.close();
			        </script>
		        <?php
		      }
	     }
      else
         {
	       $q_get_reason="SELECT *
	                      FROM transfer_header 
					      WHERE tth_id='$id' AND branch_id='$branch_id'";
	       $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
		   $field_data=mysqli_fetch_array($exec_get_reason);
	       if (mysqli_num_rows($exec_get_reason)>0)
	          {
			    if ($field_data['tth_is_canceled']=='0')
				   {
		             $current_date=date('d-m-Y');
		             $tth_code=$field_data['tth_code'];
		             $tth_canceled_date=$current_date;
				   }
				else
				   {
		             ?>
			           <script language="javascript">
				         alert('Transaksi Perpindahan sudah dibatalkan!');
				         window.close();
			           </script>
		             <?php				     
				   }
		      }
	       else
	          {
		        ?>
			        <script language="javascript">
				       alert('Transaksi Perpindahan Aset tidak ditemukan!');
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
      <td colspan="3" align="center">PEMBATALAN PERPINDAHAN ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Perpindahan</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $tth_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $tth_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $tth_canceled_reason;?></textarea></td>
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
	            $q_check_transfer="SELECT transfer_header.*, MONTH(tth_date) AS month, YEAR(tth_date) AS year 
				                   FROM transfer_header WHERE tth_id='$id' AND branch_id='$branch_id'";
	            $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
	            if (mysqli_num_rows($exec_check_transfer)>0)
	               {
		             $field_transfer=mysqli_fetch_array($exec_check_transfer);
					 $active_period=check_active_period($db_connection, $field_transfer['month'], $field_transfer['year']);
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
		             if ($field_transfer['tth_is_canceled']=='1')
		                {
		                  ?>
			                 <script language="javascript">
				               alert('Perpindahan Aset sudah dibatalkan!');
				               window.close();
		                     </script>
		                  <?php
			            }	
		             else
				     if ($field_transfer['tth_status']!='0')
		                {
		                  ?>
			                 <script language="javascript">
				               alert('Transaksi tidak dapat dibatalkan!\nSudah diteruskan ke Transaksi Penerimaan Transfer!');
				               window.close();
		                     </script>
		                  <?php
			            }	
		             else
		                {
						  $tube_id="";
						  $q_get_tube_id="SELECT * FROM transfer_detail WHERE tth_id='$id' AND ttd_is_canceled='0'";
						  $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
						  while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
						        {
								  if ($tube_id=="")
								      $tube_id="'".$field_tube_id['itemd_id']."'";
								  else
								      $tube_id=$tube_id.",'".$field_tube_id['itemd_id']."'";
								}
				          mysqli_autocommit($db_connection, false);
				          $q_update_item_detail_status="UPDATE item_detail SET itemd_position='Internal'
                                                        WHERE itemd_id IN ($tube_id)";
		                  $canceled_transfer="UPDATE transfer_header SET tth_is_canceled='1', tth_canceled_date=now(),
					                                 tth_canceled_reason='".htmlspecialchars(trim($_POST['txt_canceled_reason']))."', canceled_by=$maker 
									          WHERE tth_id='$id' AND branch_id='$branch_id'";
						  $canceled_transfer_detail="UPDATE transfer_detail SET ttd_is_canceled='1' WHERE tth_id='$id'";
				          $exec_update_item_detail_status=mysqli_query($db_connection, $q_update_item_detail_status);
					      $exec_canceled_transfer=mysqli_query($db_connection, $canceled_transfer);
						  $exec_canceled_transfer_detail=mysqli_query($db_connection, $canceled_transfer_detail);
						  if ($q_update_item_detail_status && $canceled_transfer && $exec_canceled_transfer_detail)
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
	            else
	               {
		             ?>
			            <script language="javascript">
			     	     alert('Perpindahan Aset yang akan dibatalkan tidak ditemukan!');
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
		// } 
	}      
?>
