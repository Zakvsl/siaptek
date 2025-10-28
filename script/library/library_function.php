<?php
// include "db_connection.php";
 
 function get_date_1($date)
          {
	        $d=substr($date,8,2);
	        $m=substr($date,5,2);
	        $y=substr($date,0,4);
	        $dates="$d-$m-$y";
	        return $dates;
          }
		  
 function get_date_2($date)
          {
	        $y=substr($date,6,4);
	        $m=substr($date,3,2);
	        $d=substr($date,0,2);
	        $dates="$y-$m-$d";
	        return $dates;
          } 
 
 function check_active_period($db_connection, $month, $year)
          {
		    $closing_status="";
		    $q_check_active_period="SELECT * FROM apt_period WHERE aptp_is_active_period='0' AND aptp_is_closed='0'";
			$exec_check_active_period=mysqli_query($db_connection, $q_check_active_period);
			$data_period=mysqli_fetch_array($exec_check_active_period);
			if (mysqli_num_rows($exec_check_active_period)=='0')
			    $closing_status='Period aktif transaksi tidak ditemukan!';
			else
			   {
			     if ($month==$data_period['aptp_month_1'] && $year==$data_period['aptp_year'])
				     $closing_status='OK';
				 else
				    {
					  $q_check_active_period="SELECT * FROM apt_period WHERE aptp_month_1='$month' AND aptp_year='$year'";
			          $exec_check_active_period=mysqli_query($db_connection, $q_check_active_period);
			          $data_period=mysqli_fetch_array($exec_check_active_period);
					  if (mysqli_num_rows($exec_check_active_period)=='0') 
					      $closing_status='Period aktif transaksi tidak ditemukan!';
					  else
					     {
				           if ($data_period['aptp_is_active_period']=='1' && $data_period['aptp_is_closed']=='0')
				               $closing_status='OK';
				           else
						   if ($data_period['aptp_is_active_period']=='1' && $data_period['aptp_is_closed']=='1')
				               $closing_status='Period transaksi sudah ditutup!';
				           else
				               $closing_status='Mohon dicek period aktif transaksi!';
						 }
					}
			   }
			return $closing_status;
		  }
		  
 function get_format_number($db_connection, $fn_name, $branch_id)
          {
		    $q_check_format_number="SELECT CONCAT(fn_name,'',branch_code,'',DATE_FORMAT(NOW(),'%m'),'',YEAR,'-XXXXXXX') format_number
                                    FROM format_number
                                    INNER JOIN branch ON format_number.branch_id=branch.branch_id
                                    WHERE fn_name='$fn_name' AND format_number.branch_id='$branch_id'";
			$exec_check_format_number=mysqli_query($db_connection, $q_check_format_number);
			$field=mysqli_fetch_array($exec_check_format_number);
			$format_number=$field['format_number'];
			return $format_number;
		  }

 function get_no_transaction($db_connection, $fn_name, $branch_id)
          {
		    $no_trans='';
		    $is_continue=0;
		    $curr_year=date('Y');
			$q_check_year="SELECT year, fn_no_inc
                           FROM format_number
                           INNER JOIN branch ON format_number.branch_id=branch.branch_id
                           WHERE fn_name='$fn_name' AND format_number.branch_id='$branch_id'";
		    $exec_check_year=mysqli_query($db_connection, $q_check_year);
			$field_year=mysqli_fetch_array($exec_check_year);
			if ($field_year['fn_no_inc']=='9999999')
			   {
			     $q_update_format_no="UPDATE format_number fn_no_inc=0 WHERE fn_name='$fn_name' AND branch_id='$branch_id'";
				 $exec_update_format_no=mysqli_query($db_connection, $q_update_format_no);
				 if (!$exec_update_format_no)
		            { 
					  $is_continue=1;
		              mysqli_rollback($db_connection);
				      ?>
				         <script language="javascript">
				           alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
				           window.close();
				         </script>
				      <?php
					  exit;
		            }	
			   }	
			   
			if ($is_continue==0)
			{
				if ($curr_year-$field_year['year']!=0)
				   {
					 $q_update_format_no="UPDATE format_number SET YEAR='$curr_year', fn_no_inc=0 WHERE fn_name='$fn_name' AND branch_id='$branch_id'";
					 $exec_update_format_no=mysqli_query($db_connection, $q_update_format_no);
					 if (!$exec_update_format_no)
						{ 
						  $is_continue=1;
						  mysqli_rollback($db_connection);
						  ?>
							 <script language="javascript">
							   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
							   window.close();
							 </script>
						  <?php
						  exit;
						}	
				   }	
			}		   
			
			if ($is_continue==0)
			{
				$q_check_format_number="SELECT CONCAT(fn_name,'',branch_code,'',DATE_FORMAT(NOW(),'%m'),'',YEAR,'-') format_number, fn_no_inc+1 AS nomor
										FROM format_number
										INNER JOIN branch ON format_number.branch_id=branch.branch_id
										WHERE fn_name='$fn_name' AND format_number.branch_id='$branch_id'";
				$exec_check_format_number=mysqli_query($db_connection, $q_check_format_number);
				$field=mysqli_fetch_array($exec_check_format_number);
				$format_number=$field['format_number'];
				$lg_no=strlen($field['nomor']);
				if ($lg_no==7)
					$nomor="";
				else
				if ($lg_no==6)
					$nomor="0";
				else
				if ($lg_no==5)
					$nomor="00";
				else
				if ($lg_no==4)
					$nomor="000";
				else
				if ($lg_no==3)
					$nomor="0000";
				else
				if ($lg_no==2)
					$nomor="00000";
				else
				if ($lg_no==1)
					$nomor="000000";
				$no_trans=$format_number.$nomor.$field['nomor'];
			}
			return $no_trans;
		  }
		  
 function update_runing_no($db_connection, $fn_name, $branch_id)
          {
		   $q_update_runing_no="UPDATE format_number SET fn_no_inc=fn_no_inc+1 WHERE branch_id='$branch_id' AND fn_name='$fn_name'";   
		   $exec_update_runing_no=mysqli_query($db_connection, $q_update_runing_no);
		   if (!$exec_update_runing_no)
		      { 
		        mysqli_rollback($db_connection);
				?>
				   <script language="javascript">
				     alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
				     window.close();
				   </script>
				<?php
				exit;
		      }	
	      }  
		  
 function is_there_any_new_trans($db_connection, $action, $trans_id, $itemd_id, $trans_date_old, $trans_date_new, $created_time)
          {
		    if ($action=='DELETE' || $action=='EDIT' || $action=='UPDATE')
			   {
			     /* $transfer_action="transfer_header.tth_id!='$trans_id' AND created_time>='$created_time' AND tth_date>='$trans_date' AND ";
				 $receipt_transfer_action="receipt_transfer_header.rth_id!='$trans_id' AND created_time>='$created_time' AND rth_date>='$trans_date' AND ";
				 $issuing_action="issuing_header.issuingh_id!='$trans_id' AND created_time>='$created_time' AND issuingh_date>='$trans_date' AND ";
				 $return_action="return_header.reth_id!='$trans_id' AND created_time>='$created_time' AND reth_date>='$trans_date' AND ";
				 $broken_action="broken_header.brokh_id!='$trans_id' AND created_time>='$created_time' AND brokh_date>='$trans_date' AND ";
				 $write_off_action="write_off_header.woh_id!='$trans_id' AND created_time>='$created_time' AND woh_date>='$trans_date' AND ";
				 $dispossal_action="dispossal_header.disph_id!='$trans_id' AND created_time>='$created_time' AND disph_date>='$trans_date' AND ";
				 $change_description_action="change_item_description_header.cidh_id!='$trans_id' AND created_time>='$created_time' AND cidh_date>='$trans_date' AND ";	 */
				 
				 $transfer_action="transfer_header.tth_id!='$trans_id' AND 
				                  ((tth_date>='$trans_date_old' AND created_time>='$created_time') OR
								   (tth_date='$trans_date_new' AND created_time>='$created_time') OR 
								   (tth_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								  ) AND ";
				 $receipt_transfer_action="receipt_transfer_header.rth_id!='$trans_id' AND 
				                          ((rth_date>='$trans_date_old' AND created_time>='$created_time') OR
										   (rth_date='$trans_date_new' AND created_time>='$created_time') OR
										   (rth_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time'))) 
										  ) AND ";
				 $issuing_action="issuing_header.issuingh_id!='$trans_id' AND 
				                 ((issuingh_date>='$trans_date_old' AND created_time>='$created_time') OR
								  (issuingh_date='$trans_date_new' AND created_time>='$created_time') OR
								  (issuingh_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								 ) AND ";
				 $return_action="return_header.reth_id!='$trans_id' AND 
				                ((reth_date>='$trans_date_old' AND created_time>='$created_time') OR
								 (reth_date='$trans_date_new' AND created_time>='$created_time') OR
								 (reth_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								) AND ";
				 $broken_action="broken_header.brokh_id!='$trans_id' AND 
				                ((brokh_date>='$trans_date_old' AND created_time>='$created_time') OR
								 (brokh_date='$trans_date_new' AND created_time>='$created_time') OR
								 (brokh_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								) AND ";
				 $write_off_action="write_off_header.woh_id!='$trans_id' AND 
				                   ((woh_date>='$trans_date_old' AND created_time>='$created_time') OR
								    (woh_date='$trans_date_new' AND created_time>='$created_time') OR
									(woh_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								   ) AND ";
				 $dispossal_action="dispossal_header.disph_id!='$trans_id' AND 
				                   ((disph_date>='$trans_date_old' AND created_time>='$created_time') OR
									(disph_date='$trans_date_new' AND created_time>='$created_time') OR
								    (disph_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time')))
								   ) AND ";
				 $change_description_action="change_item_description_header.cidh_id!='$trans_id' AND 
				                            ((cidh_date>='$trans_date_old' AND created_time>='$created_time') OR  
											 (cidh_date='$trans_date_new' AND created_time>='$created_time') OR 
											 (cidh_date>'$trans_date_new' AND ((created_time>='$created_time') OR (created_time<='$created_time'))) 
											) AND ";
			   }
			else
			   {
			     /*$transfer_action="tth_date>'$trans_date' AND ";
				 $receipt_transfer_action="rth_date>'$trans_date' AND ";
				 $issuing_action="issuingh_date>'$trans_date' AND ";
				 $return_action="reth_date>'$trans_date' AND ";
				 $broken_action="brokh_date>'$trans_date' AND ";
				 $write_off_action="woh_date>'$trans_date' AND ";
				 $dispossal_action="disph_date>'$trans_date' AND ";
				 $change_description_action="cidh_date>'$trans_date' AND ";		 */   
				 $transfer_action="((tth_date='$trans_date_new' AND created_time>='$created_time') OR 
				                    (tth_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $receipt_transfer_action="((rth_date='$trans_date_new' AND created_time>='$created_time') OR 
				                            (rth_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $issuing_action="((issuingh_date='$trans_date_new' AND created_time>='$created_time') OR 
				                   (issuingh_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $return_action="((reth_date='$trans_date_new' AND created_time>='$created_time') OR 
				                  (reth_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $broken_action="((brokh_date='$trans_date_new' AND created_time>='$created_time') OR 
				                  (brokh_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $write_off_action="((woh_date='$trans_date_new' AND created_time>='$created_time') OR 
				                     (woh_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $dispossal_action="((disph_date='$trans_date_new' AND created_time>='$created_time') OR 
				                     (disph_date>'$trans_date_new' AND created_time<='$created_time')) AND ";
				 $change_description_action="((cidh_date='$trans_date_new' AND created_time>='$created_time') OR 
				                              (cidh_date>'$trans_date_new' AND created_time<='$created_time')) AND ";  
			   }
			   
		    $q_check_transfer="SELECT COUNT(*) AS total FROM transfer_detail
                               INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
                               WHERE $transfer_action itemd_id IN ($itemd_id) AND tth_is_canceled='0' AND ttd_is_canceled='0'"; 
			$exec_check_transfer=mysqli_query($db_connection, $q_check_transfer);
			$field_check_transfer=mysqli_fetch_array($exec_check_transfer);
		    //echo $q_check_transfer;
			if ($field_check_transfer['total']>0) 
			   {
			     $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Transfer Aset!";
				 return $messages;
			   }
			else
			   {
		         $q_check_receipt_transfer="SELECT COUNT(*) AS total FROM receipt_transfer_detail
                                            INNER JOIN receipt_transfer_header ON receipt_transfer_header.rth_id=receipt_transfer_detail.rth_id
                                            WHERE $receipt_transfer_action itemd_id IN ($itemd_id) AND rth_is_canceled='0' AND rtd_is_canceled='0'"; 
			     $exec_check_receipt_transfer=mysqli_query($db_connection, $q_check_receipt_transfer);
		   	     $field_check_receipt_transfer=mysqli_fetch_array($exec_check_receipt_transfer);			     
				 //echo $q_check_receipt_transfer;
		         if ($field_check_receipt_transfer['total']>0) 
			        {
			          $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Receipt Transfer Aset!";
				      return $messages;
			        }
			     else
			        {	
				      $q_check_issuing="SELECT COUNT(*) AS total FROM issuing_detail
                                        INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                        WHERE $issuing_action itemd_id IN ($itemd_id) AND issuingh_is_canceled='0' AND issuingd_is_canceled='0'";
				      $exec_check_issuing=mysqli_query($db_connection, $q_check_issuing);
				      $field_check_issuing=mysqli_fetch_array($exec_check_issuing); 
					  //echo $q_check_issuing;
				      if ($field_check_issuing['total']>0) 
					     {
					       $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Pengeluaran Aset!";
				           return $messages;
				         }
				      else
				         {
						   $q_check_return="SELECT COUNT(*) AS total FROM return_detail
                                            INNER JOIN return_header ON return_header.`reth_id`=return_detail.`reth_id`
                                            INNER JOIN issuing_detail ON issuing_detail.`issuingd_id`=return_detail.`issuingd_id`
                                            WHERE $return_action reth_is_canceled='0' AND retd_is_canceled='0' AND issuing_detail.`itemd_id` IN ($itemd_id)";
						   $exec_check_return=mysqli_query($db_connection, $q_check_return);
						   $field_check_return=mysqli_fetch_array($exec_check_return);
						   //echo $q_check_return;
						   if ($field_check_return['total']>0)
						      {
							    $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Pengembalian Aset!";
				                return $messages; 
							  }
						   else
						      { 
					            $q_check_broken="SELECT COUNT(*) AS total FROM broken_detail
                                                 INNER JOIN broken_header ON broken_header.brokh_id=broken_detail.brokh_id
                                                 WHERE $broken_action itemd_id IN ($itemd_id) AND brokh_is_canceled='0' AND brokd_is_canceled='0'";
					          //  echo $q_check_broken;
								$exec_check_broken=mysqli_query($db_connection, $q_check_broken);
				                $field_check_broken=mysqli_fetch_array($exec_check_broken); 
					            if ($field_check_broken['total']>0)
						           {
						             $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Aset Rusak!";
				                     return $messages;									     
				                   }
					            else
						           {
						             $q_check_write_off="SELECT COUNT(*) AS total FROM write_off_detail
                                                         INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
                                                         WHERE $write_off_action itemd_id IN ($itemd_id) AND woh_is_canceled='0' AND wod_is_canceled='0'";
					                 $exec_check_write_off=mysqli_query($db_connection, $q_check_write_off);
						             $field_check_write_off=mysqli_fetch_array($exec_check_write_off); 
						             //echo $q_check_write_off;
									 if ($field_check_write_off['total']>0)
							            {
							              $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Penghapusan Aset!";
				                          return $messages;									     
							            }
						             else
							            {
							              $q_check_dispossal="SELECT COUNT(*) AS total FROM dispossal_detail
                                                              INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
                                                              WHERE $dispossal_action itemd_id IN ($itemd_id) AND disph_is_canceled='0' AND dispd_is_canceled='0'";
					                      $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
							              $field_check_dispossal=mysqli_fetch_array($exec_check_dispossal); 
								          if ($field_check_dispossal['total']>0)
								             {
								               $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Penjualan Aset!";
				                               return $messages;								     
								             }											  
								          else			
								             {   
							                   $q_check_cid="SELECT COUNT(*) AS total FROM change_item_description_detail
                                                             INNER JOIN change_item_description_header ON 
															            change_item_description_header.cidh_id=change_item_description_detail.cidh_id
                                                             WHERE $change_description_action itemd_id IN ($itemd_id) AND cidh_is_canceled='0' AND cidd_is_canceled='0'";
					                           $exec_check_cid=mysqli_query($db_connection, $q_check_cid);
					                           $field_check_cid=mysqli_fetch_array($exec_check_cid); 
									           if ($field_check_cid['total']>0) 
									              {
										            $messages="Transaksi tidak dapat dilakukan! Silahkan cek tanggal transaksi terakhir untuk masing-masing Kode Aset pada transaksi Perubahan Deskripsi Aset!";
				                                    return $messages;
										          }
								               else
									              {
										            $messages='1';
										            return $messages;
												  }
											 }
										}
								   } 
							  }
                         }
                     }
				}    
		  }   
		  
  function compare_branch($branch_id, $branch_id_transaction)
           {
             if ($branch_id!=$branch_id_transaction)
                { 
	              ?>
                    <script language="javascript">
                      alert('Ada perbedaan akses kantor cabang!\nSilahkan refresh terlebih dahulu!');
			          window.close();
		            </script>
	              <?php
				  exit;
	            }
		   }
		      
  function get_create_time($db_connection)
           {
		     $q_get_time="SELECT now() AS create_time";
			 $exec_get_time=mysqli_query($db_connection, $q_get_time);
			 $field_get_time=mysqli_fetch_array($exec_get_time);
			 $created_time=$field_get_time['create_time'];
			 return $created_time;
		   }
         
?>




