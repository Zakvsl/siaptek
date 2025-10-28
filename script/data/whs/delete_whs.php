<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_branch'];
 include "../../library/db_connection.php";
 
 if (isset($_GET['id']))
    {
	  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	  $q_check_data="SELECT whsl_parent_path, whsl_type FROM warehouse_location WHERE whsl_id='$id'";
	  echo $q_check_data."<br>";
	  $exec_check_data=mysqli_query($db_connection, $q_check_data);
	  $field_check_data=mysqli_fetch_array($exec_check_data);
	  if (mysqli_num_rows($exec_check_data)>0)
	     {
		   if ($field_check_data['whsl_type']=='1')
		      {
			    $path=$field_check_data['whsl_parent_path'];
				$q_check_whs="SELECT whsl_code FROM warehouse_location 
                              WHERE whsl_id='$id' AND whsl_id IN (SELECT whsl_id FROM item_detail)";
				echo $q_check_whs."<br>";
				$q_exec_check_whs=mysqli_query($db_connection, $q_check_whs);
				if (mysqli_num_rows($q_exec_check_whs)>0)
				   {
				     ?>
	                    <script language="javascript">
		                  alert('Lokasi Gudang tidak dapat dihapus, sudah digunakan pada table Item Detail!');
		                  window.location='../../index/index.php?page=whs';
		                </script>
	                 <?php
				   }
				else
				   {
				     mysqli_autocommit($db_connection, false);
					 $l_path=strlen($path);
					 $x=-1;
					 $parent_id='';
					 for ($i=0; $i<$l_path; $i++)
					     {
						   $char=(substr($path,$i,1));
						   if ($char==',')
						      { 
							    $x_1=$x;
							    $parent_id=substr($path,$x_1+1,$i-$x_1-1);
								$x=$i;
							  }
						 }
				     $q_delete_child="DELETE FROM warehouse_location WHERE whsl_id='$id'";
					 $q_check_whs="SELECT whsl_id FROM warehouse_location WHERE whsl_parent_id='$parent_id'";
					 echo $q_delete_child."<br>";
					 echo $q_check_whs."<br>";
					 $exec_delete_child=mysqli_query($db_connection, $q_delete_child);
					 $exec_check_whs=mysqli_query($db_connection, $q_check_whs);
					 if ($exec_delete_child)
					    {
						  if (mysqli_num_rows($exec_check_location)==0)
						     {
							   $q_update_parent="UPDATE warehouse_location SET whsl_type='1' WHERE whsl_id='$parent_id'";
							   echo $q_update_parent;
							   $exec_update_parent=mysqli_query($db_connection, $q_update_parent);
							   if ($exec_update_parent)
							      {
							        mysqli_commit($db_connection);   
									?>
	                                  <script language="javascript">
		                           //     window.location='../../index/index.php?page=whs';
		                              </script>
	                                <?php
								  }
							   else
							      {
								    mysqli_rollback($db_connection);
						            ?>
						              <script language="javascript">
							            alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda!');
							            window.location='../../index/index.php?page=whs';
							          </script>
						            <?php
								  }	  	
							 }
						  else	
						     { 
						       mysqli_commit($db_connection);
						       ?>
	                             <script language="javascript">
		                     //      window.location='../../index/index.php?page=whs';
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
							   window.location='../../index/index.php?page=whs';
							 </script>
						  <?php
						}		
				   }   
			  }
		   else
		      {
			    ?> 
	               <script language="javascript">
		             alert('Sub Lokasi Gudang harus dihapus terlebih dahulu!');
		             window.location='../../index/index.php?page=whs';
		           </script>
	            <?php 
			  }	  
		 }
	  else 
	     {
		   ?>
	          <script language="javascript">
		        alert('Data yang akan dihapus tidak ditemukan!');
		        window.location='../../index/index.php?page=whs';
		      </script>
	       <?php
		 }	 
	}
 else
    {
	  ?>
	    <script language="javascript">
		//  window.location='../../index/index.php?page=whs';
		</script>
	  <?php
	}  
 			
?>