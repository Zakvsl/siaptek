<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
 $branch_id_transaction=mysqli_real_escape_string($db_connection, $_GET['b']);
 include "../../library/db_connection.php";
 include "../../library/library_function.php";
 
 compare_branch($branch_id, $branch_id_transaction);
 
 $data=$_POST['check_data'];
 $x=count($data);
 $delete='';
 $is_continue=1;
 foreach ($data as $delete_id)
 		 { 
		   if ($delete=='')
		       $delete="'".$delete_id."'"; 
		   else
		       $delete=$delete.", '".$delete_id."'";
		 }

 $q_check_broken="SELECT broken_header.*, MONTH(brokh_date) AS month, YEAR(brokh_date) AS year  
                  FROM broken_header WHERE brokh_id IN ($delete) AND branch_id='$branch_id' ORDER BY brokh_date, created_time DESC";
 $exec_check_broken=mysqli_query($db_connection, $q_check_broken);
 if (mysqli_num_rows($exec_check_broken)>0)
    {
	  $delete_id='';
	  $delete_id_direct='';
	  $not_delete_code='';
	  while ($field_data=mysqli_fetch_array($exec_check_broken))
	        {
			  $active_period=check_active_period($db_connection, $field_data['month'], $field_data['year']);
		      if ($active_period!='OK')
		         {
				   $is_continue=0;
		           ?>
			          <script language="javascript">
				          var x='<?php echo $active_period;?>';
						  var y='<?php echo $field_data['brokh_code'];?>';
				          alert(x+'\n'+y);
				      </script>
			       <?php
				   break;
				   exit;		     
		         }	
			
			  if ($field_data['brokh_is_canceled']=='0')
			     {
			       if ($field_data['brokh_status']=='0')
			          {
				        if ($delete_id=='')
				            $delete_id="'".$field_data['brokh_id']."'";
				        else
				            $delete_id=$delete_id.", '".$field_data['brokh_id']."'";					    
					  }
				   else
				      {
				        $q_check_wo="SELECT * FROM write_off_header WHERE brokh_id='".$field_data['brokh_id']."'";
				        $exec_check_wo=mysqli_query($db_connection, $q_check_wo);
				        if (mysqli_num_rows($exec_check_wo)>0)
				           {
				             if ($not_delete_code=='')
				                 $not_delete_code=$field_data['brokh_code'];
				             else
				                 $not_delete_code=$not_delete_code.", ".$field_data['brokh_code'];  
				           }
				        else
				           {
					         $q_check_dispossal="SELECT * FROM dispossal_header WHERE brokh_id='".$field_data['brokh_id']."'";
				             $exec_check_dispossal=mysqli_query($db_connection, $q_check_dispossal);
				             if (mysqli_num_rows($exec_check_dispossal)>0)
				                {
				                  if ($not_delete_code=='')
				                      $not_delete_code=$field_data['brokh_code'];
				                  else
				                      $not_delete_code=$not_delete_code.", ".$field_data['brokh_code'];  
				                }
				             else
				                {
				                  if ($delete_id=='')
				                      $delete_id="'".$field_data['brokh_id']."'";
				                  else
				                      $delete_id=$delete_id.", '".$field_data['brokh_id']."'";	
						        }
					       }
				      }
				 }
			  else
			     {
				   if ($delete_id_direct=='')
				       $delete_id_direct="'".$field_data['brokh_id']."'";
				   else
				       $delete_id_direct=$delete_id_direct.", '".$field_data['brokh_id']."'";					   
				 }
		    }
	}
 
 if ($is_continue==1)
    {
      if ($delete_id_direct!="")
         {
	       mysqli_autocommit($db_connection, false);
	       $q_delete_broken_detail="DELETE FROM broken_detail WHERE brokh_id IN ($delete_id_direct)";
	       $q_delete_broken_header="DELETE FROM broken_header WHERE brokh_id IN ($delete_id_direct)";  
	       $exec_delete_broken_detail=mysqli_query($db_connection, $q_delete_broken_detail);
	       $exec_delete_broken_header=mysqli_query($db_connection, $q_delete_broken_header);
	       if (!$exec_delete_broken_detail && !$exec_delete_broken_header)
	          {
		        $is_continue=0;
	            mysqli_rollback($db_connection);
	            ?>
	              <script language="javascript">
		             alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
                     window.location='../../index/index.php?page=broken';
		          </script>
	            <?php		   
		      }
	       else
	           mysqli_commit($db_connection);
	     } 
    }
	
 if ($is_continue==1)
    {
	  if ($delete_id!='')
	     {
	       $update_tube="";
	       $q_get_tube_id="SELECT DISTINCT(itemd_id) FROM broken_detail WHERE brokh_id IN ($delete_id) AND brokd_is_canceled='0'"; 
	       $exec_get_tube_id=mysqli_query($db_connection, $q_get_tube_id);
	       while ($field_tube_id=mysqli_fetch_array($exec_get_tube_id))
	             {
			       if ($update_tube=="")
			           $update_tube="'".$field_tube_id['itemd_id']."'";
			       else
			           $update_tube=$update_tube.",'".$field_tube_id['itemd_id']."'";
			     }
           mysqli_autocommit($db_connection, false);
	       $q_update="UPDATE item_detail SET itemd_is_broken='0' WHERE itemd_id IN ($update_tube)";
	       $q_delete_broken_detail="DELETE FROM broken_detail WHERE brokh_id IN ($delete_id)";
	       $q_delete_broken_header="DELETE FROM broken_header WHERE brokh_id IN ($delete_id)";
	       $exec_update=mysqli_query($db_connection, $q_update);
	       $exec_delete_broken_detail=mysqli_query($db_connection, $q_delete_broken_detail);
	       $exec_delete_broken_header=mysqli_query($db_connection, $q_delete_broken_header);
	       if ($exec_update && $exec_delete_broken_detail && $exec_delete_broken_header)
	          {
		        mysqli_commit($db_connection);
			    if ($not_delete_code!='')
	               {
		             ?>
		                <script language="javascript"> 
			               var data='<?php echo $not_delete_code;?>';
				           alert('Transaksi Kerusakan berikut ini tidak dapat dihapus karena sudah diteruskan ke transaksi Penjualan/Penghapusan :\n'+data);
				           window.location='../../index/index.php?page=broken';
			            </script>		   
		             <?php
		           }
				else
				   {
		             ?>
		                <script language="javascript"> 
				           window.location='../../index/index.php?page=broken';
			            </script>		   
		             <?php
				   }
		      }
	       else
	          {
	            mysqli_rollback($db_connection);
	            ?>
	               <script language="javascript">
		             alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
                     window.location='../../index/index.php?page=broken';
		           </script>
	            <?php
			  }
		 }
	  else
	  if ($not_delete_code!='')
	     {
		   ?>
		      <script language="javascript"> 
			     var data='<?php echo $not_delete_code;?>';
				 alert('Transaksi Kerusakan berikut ini tidak dapat dihapus karena sudah diteruskan ke transaksi Penjualan/Penghapusan :\n'+data);
				 window.location='../../index/index.php?page=broken';
			  </script>		   
		   <?php
		 }
	} 
 else
    {
	  ?>
		  <script language="javascript"> 
				window.location='../../index/index.php?page=broken';
		  </script>		   
	  <?php	  
	}  			
?>