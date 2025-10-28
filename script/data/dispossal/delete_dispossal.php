<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch']; 
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 $data=$_POST['check_data'];
 $x=count($data);
 $delete='';
 $brokh_id='';
 $is_continue=1;
 
 compare_branch($branch_id, $branch_id_transaction); 
  
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";			   
		 }
	
 $q_check_dispossal="SELECT dispossal_header.*, MONTH(disph_date) AS month, YEAR(disph_date) AS year  
                     FROM dispossal_header WHERE disph_id IN ($delete) AND branch_id='$branch_id' ORDER BY disph_date, created_time DESC";
 $exec_check_dispossal=mysqli_query($db_connection,$q_check_dispossal);

 if (mysqli_num_rows($exec_check_dispossal)>0)
    {
	  $brokh_id=array();
	  $delete_id_disph='';
	  $delete_id_broken='';
	  $delete_id_direct='';
	  $not_delete_code='';
	  while ($field_data=mysqli_fetch_array($exec_check_dispossal))
	        {
			  $active_period=check_active_period($db_connection, $field_data['month'], $field_data['year']);
		      if ($active_period!='OK')
		         {
				   $is_continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_data['disph_code'];?>';
				          alert(x+'\n'+y);
				      </script>
			       <?php
				   break;
				   exit;		     
		         }	
				 			  
			  if ($field_data['disph_is_canceled']=='0')
			     {
				   if ($field_data['disph_sources']=='0')
				      {
				        if ($delete_id_disph=='')
				            $delete_id_disph="'".$field_data['disph_id']."'";
				        else
				            $delete_id_disph=$delete_id_disph.", '".$field_data['disph_id']."'"; 
					  }
				   else
				      {
					    $brokh_id[]=$field_data['brokh_id'];
						if ($delete_id_broken=='')
				            $delete_id_broken="'".$field_data['disph_id']."'";
				        else
				            $delete_id_broken=$delete_id_broken.", '".$field_data['disph_id']."'"; 
					  }
				 }
			  else
			     {
				   if ($delete_id_direct=='')
				       $delete_id_direct="'".$field_data['disph_id']."'";
				   else
				       $delete_id_direct=$delete_id_direct.", '".$field_data['disph_id']."'";
				 } 
			}      
	}	

 if ($is_continue=='1')
    {
      if ($delete_id_direct!='')
         {
           mysqli_autocommit($db_connection, false);
           $q_delete_disph_detail_direct="DELETE FROM dispossal_detail WHERE disph_id IN ($delete_id_direct)";	
           $q_delete_disph_header_direct="DELETE FROM dispossal_header WHERE disph_id IN ($delete_id_direct)";
           $exec_delete_disph_detail_direct=mysqli_query($db_connection,$q_delete_disph_detail_direct);
           $exec_delete_disph_header_direct=mysqli_query($db_connection,$q_delete_disph_header_direct);
           if ($exec_delete_disph_detail_direct && $exec_delete_disph_header_direct)
               mysqli_commit($db_connection);
           else
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
	}


 if ($is_continue=='1')
    {
      mysqli_autocommit($db_connection, false);
      if ($delete_id_broken!='')
         {
		   $q_update_item="UPDATE item_detail, dispossal_detail, dispossal_header 
                                  SET itemd_status='0', itemd_is_dispossed='0', itemd_position='Internal' 
                           WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND
                                 item_detail.branch_id='$branch_id' AND dispossal_header.branch_id='$branch_id' AND dispossal_detail.disph_id IN ($delete_id_broken) AND 
			                     dispd_is_canceled='0'";
           $q_update_broken="UPDATE broken_detail, dispossal_detail SET brokd_is_dispossed='0'
			                 WHERE broken_detail.brokd_id=dispossal_detail.brokd_id AND disph_id IN ($delete_id_broken) AND dispd_is_canceled='0'";	 
           $q_delete_disph_detail="DELETE dispossal_detail FROM dispossal_detail, dispossal_header
                                   WHERE dispossal_detail.disph_id=dispossal_header.disph_id AND branch_id='$branch_id' AND dispossal_detail.disph_id IN ($delete_id_broken)";
           $q_delete_disph_header="DELETE FROM dispossal_header
                                   WHERE disph_id IN ($delete_id_broken) AND branch_id='$branch_id'";
	       $exec_update_item=mysqli_query($db_connection,$q_update_item);
		   $exec_update_broken=mysqli_query($db_connection,$q_update_broken);
	       $exec_delete_disph_detail=mysqli_query($db_connection,$q_delete_disph_detail);
	       $exec_delete_disph_header=mysqli_query($db_connection,$q_delete_disph_header);
		   if ($exec_update_item && $exec_update_broken && $exec_delete_disph_detail && $exec_delete_disph_header)
	          {
		        foreach($brokh_id as $id_brokh)
		               {
				         $q_get_broken_1="SELECT COUNT(*) AS total FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND brokd_is_wo='0' AND 
				                                 brokd_is_dispossed='0'";	 
				         $q_get_broken_2="SELECT COUNT(*) AS total_wo FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND brokd_is_wo='1'";
				         $q_get_broken_3="SELECT COUNT(*) AS total_dispossed FROM broken_detail WHERE brokh_id='".$id_brokh."' AND brokd_is_canceled='0' AND 
				                                 brokd_is_dispossed='1'";  
				         $exec_get_broken_1=mysqli_query($db_connection,$q_get_broken_1);
				         $exec_get_broken_2=mysqli_query($db_connection,$q_get_broken_2);
				         $exec_get_broken_3=mysqli_query($db_connection,$q_get_broken_3);
						 
				         $field_broken_1=mysqli_fetch_array($exec_get_broken_1);
				         $field_broken_2=mysqli_fetch_array($exec_get_broken_2);
				         $field_broken_3=mysqli_fetch_array($exec_get_broken_3);
				         $total_broken=$field_broken_1['total'];
				         $total_wo=$field_broken_2['total_wo'];
			             $total_dispossed=$field_broken_3['total_dispossed'];   
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
                                                         WHERE brokh_id='".$id_brokh."'";
					     //	echo $q_update_broken_header_status;
			             $exec_update_broken_header_status=mysqli_query($db_connection,$q_update_broken_header_status);
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
		      } 
	       else
	          {
		        $is_continue='0';
		        mysqli_rollback($db_connection);
	            ?>
                  <script language="javascript">
		             alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		             history.back();
	              </script>
	            <?php 
		      }
		 }
		 
      if ($is_continue=='1')
         {
		   if ($delete_id_disph!='')
		      {
                $q_update_item="UPDATE item_detail, dispossal_detail, dispossal_header 
                                       SET itemd_status='0', itemd_is_dispossed='0', itemd_position='Internal' 
                                WHERE item_detail.itemd_id=dispossal_detail.itemd_id AND dispossal_detail.disph_id=dispossal_header.disph_id AND
                                      item_detail.branch_id='$branch_id' AND dispossal_header.branch_id='$branch_id' AND dispossal_detail.disph_id IN ($delete_id_disph) AND 
			                          dispd_is_canceled='0'";
                $q_delete_dispossal_detail="DELETE dispossal_detail FROM dispossal_detail, dispossal_header
                                            WHERE dispossal_detail.disph_id=dispossal_header.disph_id AND branch_id='$branch_id' AND 
											      dispossal_detail.disph_id IN ($delete_id_disph)";
                $q_delete_dispossal_header="DELETE FROM dispossal_header
                                            WHERE disph_id IN ($delete_id_disph) AND branch_id='$branch_id'";	
		        $exec_update_item=mysqli_query($db_connection,$q_update_item);
	            $exec_delete_dispossal_detail=mysqli_query($db_connection,$q_delete_dispossal_detail);
	            $exec_delete_dispossal_header=mysqli_query($db_connection,$q_delete_dispossal_header);
	            if ($q_update_item && $exec_delete_dispossal_detail && $exec_delete_dispossal_header)
	               {
				     mysqli_commit($db_connection);
	                 ?>
	                    <script language="javascript">
		                  window.location='../../index/index.php?page=dispossal';
		                </script>
	                 <?php	
			       }	 
		        else
		           {
		             mysqli_rollback($db_connection);
	                 ?>
                       <script language="javascript">
		                  alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
		                  history.back();
	                   </script>
	                 <?php 	
				   }		    
			  }
		   else
		      {
			    mysqli_commit($db_connection); 
	            ?>
	                <script language="javascript">
		              window.location='../../index/index.php?page=dispossal';
		            </script>
	            <?php				    
			  }  
		 } 			
	}	
 else
    {
	  ?>
	     <script language="javascript">
		     window.location='../../index/index.php?page=dispossal';
		 </script>
	  <?php	  
	}		
?>