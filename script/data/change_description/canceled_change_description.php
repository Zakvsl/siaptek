<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $maker=$_SESSION['ses_user_id'];
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $id=mysqli_real_escape_string($db_connection, $_GET['id']);	
 $action=mysqli_real_escape_string($db_connection, $_GET['a']);	
 if ($action=='d')
    {
	  $q_get_reason="SELECT cidh_code, cidh_canceled_date, cidh_canceled_reason, canceled_by as user_id, (select users_names from users where users_id=user_id) as users_name 
	                 FROM change_item_description_header 
					 WHERE cidh_id='$id' AND cidh_is_canceled='1'";
	//  echo $q_get_reason;
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $cidh_code=$field_reason['cidh_code'];
		   $cidh_canceled_date=get_date_1($field_reason['cidh_canceled_date']);
		   $cidh_canceled_reason=$field_reason['cidh_canceled_reason'];
		   $canceled_by=$field_reason['users_name'];
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Perubahan Deskripsi Aset tidak ditemukan!');
				  window.close();
			   </script>
		   <?php
		 }
	}
 else
    {
	  $q_get_reason="SELECT cidh_code, cidh_date
	                 FROM change_item_description_header 
					 WHERE cidh_id='$id' AND cidh_is_canceled='0'";
	  $exec_get_reason=mysqli_query($db_connection, $q_get_reason);
	  if (mysqli_num_rows($exec_get_reason)>0)
	     {
		   $current_date=date('d-m-Y');
		   $field_reason=mysqli_fetch_array($exec_get_reason);
		   $cidh_code=$field_reason['cidh_code'];
		   $cidh_date=$field_reason['cidh_date'];
		   $cidh_canceled_date=$current_date;
		 }
	  else
	     {
		   ?>
			   <script language="javascript">
				  alert('Transaksi Perubahan Deskripsi Aset tidak ditemukan!');
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
      <td colspan="3" align="center">PEMBATALAN PERUBAHAN DESKRIPSI ASET</td>
    </tr>
    <tr>
      <td nowrap="nowrap">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="12%" nowrap="nowrap">Kode Perubahan</td>
      <td width="1%">:</td>
      <td width="87%"><?php echo $cidh_code;?></td>
    </tr>
    <tr>
      <td>Tanggal</td>
      <td>:</td>
      <td><?php echo $cidh_canceled_date;?></td>
    </tr>
    <tr>
      <td valign="top">Alasan</td>
      <td valign="top">:</td>
      <td><textarea name="txt_canceled_reason" <?php if ($action=='d') echo "readonly='readonly'";?>><?php if ($action=='d') echo $cidh_canceled_reason;?></textarea></td>
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
	  $reason=htmlspecialchars(trim($_POST['txt_canceled_reason']));
	  if (!empty($reason))
	     {
	       $q_check_change_description="SELECT * FROM change_item_description_header WHERE cidh_id='$id'";
	       $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
	       if (mysqli_num_rows($exec_check_change_description)>0)
	          {
		        $field_change_description=mysqli_fetch_array($exec_check_change_description);
		        if ($field_change_descriptioin['cidh_is_canceled']=='1')
		           {
		             ?>
			            <script language="javascript">
				          alert('Perubahan sudah dibatalkan!');
				          window.close();
		                </script>
		             <?php
			       }	
		        else
		           {
				     $cidh_id=$field_get_change_item_description_detail['cidh_id'];
		   			 $itemd_id=$field_get_change_item_description_detail['itemd_id'];
		   
		  			 $q_check_issuing="SELECT *
		              			       FROM issuing_detail 
							 		   INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
									   WHERE issuingd_is_canceled='0' AND issuingh_is_canceled='0' AND issuingh_date>='$cidh_date' AND 
                                             itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$id')";
		 			 $q_check_broken="SELECT * 
		                			  FROM broken_detail 
									  INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
						   			  WHERE brokd_is_canceled='0' AND brokh_is_canceled='0' AND brokh_date>='$cidh_date' AND 
                                            itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$id')";
		   			 $q_check_dispossal="SELECT *
		                       			 FROM dispossal_detail 
							   			 INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
						       			 WHERE dispd_is_canceled='0' AND disph_is_canceled='0' AND disph_date>='$cidh_date' AND 
                                               itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$id')";
		   			 $q_check_transfer="SELECT *
		                      			FROM transfer_detail 
							  			INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
						      			WHERE ttd_is_canceled='0' AND tth_is_canceled='0' AND tth_date>='$cidh_date' AND 
                                              itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$id')";
		   			 $q_check_change_description="SELECT *
		                                		  FROM change_item_description_detail 
							            	      INNER JOIN change_item_description_header ON change_item_description_header.cidh_id=change_item_description_detail.cidh_id
						                		  WHERE cidd_is_canceled='0' AND cidh_is_canceled='0' AND cidh_date>='$cidh_date' AND 
										      	  change_item_description_detail.cidh_id!='$id' AND change_item_description_detail.cidh_id>$id AND
                                                  itemd_id IN (SELECT itemd_id FROM change_item_description_detail 
												               WHERE cidd_is_canceled='0' AND cidh_id='$id')";
		   		//	 echo $q_check_change_description;
					 $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
		   			 $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
		   			 $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
		   			 $exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
		   			 $exec_check_change_description=mysqli_query($db_connection, $q_check_change_description);
		   			 if (mysqli_num_rows($exec_check_issuing)>0 || mysqli_num_rows($exec_check_broken)>0 || mysqli_num_rows($exec_check_dispossal)>0 || 
		       			 mysqli_num_rows($exec_check_transfer)>0 || mysqli_num_rows($exec_check_change_description)>0)
			   			{
			    		  if (mysqli_num_rows($exec_check_issuing)>0)
				     		  $issuing='Ya';
						  else
				    	      $issuing='Tidak';
				 		  if (mysqli_num_rows($exec_check_broken)>0)
				     		  $broken='Ya';
				 		  else
				     		  $broken='Tidak';
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
					  		  	var dispossal='<?php echo $dispossal;?>';
					  			var transfer='<?php echo $transfer;?>';
					  			var change='<?php echo $change;?>';
					  			alert('Transaksi Perubahan Deskripsi Aset tidak dapat dibatalkan. Ada Aset yang sudah digunakan pada transaksi berikut ini : \n1. Pengeluaran Aset : '+issuing+'\n2. Kerusakan : '+broken+'\n3. Penjualan : '+dispossal+'\n4. Perpindahan Aset : '+transfer+'\n5. Perubahan Deskripsi : '+change);
					  			history.back();
							 </script>
				 		  <?php	 
			   			}  
		   			 else
		     			 {
			    		     mysqli_autocommit($db_connection, 'false');
			    		     $q_update_item="UPDATE change_item_description_header SET cidh_is_canceled='1', cidh_canceled_date=NOW(), canceled_by='$maker' 
							                 WHERE cidh_id='$id'";
							 $q_update_item_detail="UPDATE item_detail, change_item_description_detail SET masti_id=masti_id_old
                                       			    WHERE item_detail.itemd_id=change_item_description_detail.itemd_id AND cidh_id='$id' AND 
                                                    item_detail.itemd_id IN (SELECT itemd_id FROM change_item_description_detail WHERE cidd_is_canceled='0' AND cidh_id='$id')";
							 $exec_update_item=mysqli_query($db_connection, $q_update_item);
							 $exec_update_item_detail=mysqli_query($db_connection, $q_update_item_detail);
							 if ($exec_update_item && $exec_update_item_detail)
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
			     	alert('Perubahan Deskripsi Aset yang akan dibatalkan tidak ditemukan!');
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
