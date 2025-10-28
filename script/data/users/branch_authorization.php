<?php
  include "../../library/check_session.php";
  include "../../library/style.css";
  include "../../library/db_connection.php";
  $branch_id=$_SESSION['ses_branch'];
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
  $q_get_user="SELECT users_id, users_name, users_names, 
               case users_level
			    when '0' then 'Super Administrator'
				when '1' then 'Administrator'
				when '2' then 'Public'
			   end users_level, 
			   case users_status
			     when '0' then 'Active'
				 when '1' then 'InActive'
			   end users_status
               FROM users
               WHERE users_id='$id'"; 
  $q_list_branch="SELECT branch_id, branch_code, branch_name FROM branch ORDER BY branch_code asc";
  $exec_get_user=mysqli_query($db_connection, $q_get_user);
  $exec_list_branch=mysqli_query($db_connection, $q_list_branch);
  $field_user=mysqli_fetch_array($exec_get_user);
?>

<form id="f_cru_users" name="f_cru_users" method="post">
  <table width="100%">
    <tr>
      <th colspan="3" scope="col">AUTORISASI KANTOR CABANG </th>
    </tr>
    <tr>
      <td width="41%">&nbsp;</td>
      <td width="2%">&nbsp;</td>
      <td width="57%">&nbsp;</td>
    </tr>
    <tr>
      <td>Nama User</td>
      <td>:</td>
      <td><?php echo $field_user['users_names'];?></td>
    </tr>
	<tr>
       <td nowrap="nowrap">User Name</td>
       <td nowrap="nowrap">:</td>
       <td nowrap="nowrap"><?php echo $field_user['users_name'];?></td>
     </tr>
     <tr>
       <td nowrap="nowrap">Level</td>
       <td nowrap="nowrap">:</td>
       <td nowrap="nowrap"><?php echo $field_user['users_level'];?></td>
     </tr>
     <tr>
       <td nowrap="nowrap">Status</td>
       <td nowrap="nowrap">:</td>
       <td nowrap="nowrap"><?php echo $field_user['users_status'];?></td>
     </tr>
     <tr>
	     <td colspan="3">
	          <table>
			         <tr bgcolor="#CCCCCC">
				       <th width="23"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
					    <th width="43">No</th>
			            <th width="198">Kode Kantor Cabang </th>
			            <th width="285">Nama Kantor Cabang </th>
			         </tr>
	                      <?php 
	                           $no=1;
	                           while ($field=mysqli_fetch_array($exec_list_branch))
	                                 {
			                           $q_get_user_branch="SELECT branch_id FROM users_branch WHERE users_id='$id' AND branch_id='".$field['branch_id']."'";
			                           $exec_get_user_branch=mysqli_query($db_connection, $q_get_user_branch);
			                           if (mysqli_num_rows($exec_get_user_branch)>0)
			                               $checked="checked='checked'";
			                           else
			                               $checked=""; 	   
			                           ?>
                                          <tr>
                                               <td><input type="checkbox" id="cb_branch[]" name="cb_branch[]" value="<?php echo $field['branch_id'];?>" <?php echo $checked;?>/></td>
											   <td><?php echo $no++;?></td>
											   <td><?php echo $field['branch_code'];?></td>
											   <td nowrap="nowrap"><?php echo $field['branch_name'];?></td>
                                          </tr>
			                           <?php 
			                         }
						  ?>			 
	          </table>
        </td>
	</tr>
    <tr>
      <td><input name="btn_save" type="submit" id="btn_save" value="Simpan"/>
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onclick="window.close()"/></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>

<?php
 if (isset($_POST['btn_save']))
    {
	  $q_check_user="SELECT * FROM users
					 WHERE users_id='$id'"; 
	  $exec_check_user=mysqli_query($db_connection, $q_check_user);
	  $field=mysqli_fetch_array($exec_check_user);
	  $total_users=mysqli_num_rows($exec_check_user);
	  if ($total_users>0)
	     {
		   if ($field['users_level']=='0')
		      {
			    ?>
		           <script language="javascript">
				      alert("User level adalah 'Super Administrator'");
				      window.close();
			       </script>
		        <?php 
			  }
		   else
		   if ($field['users_status']=='1')
		      {
			    ?>
		           <script language="javascript">
				      alert("Status User adalah InActive");
				      window.close();
			       </script>
		        <?php
			  }
		   else
		      {	  
			    if (isset($_POST['cb_branch']))
				   {
			         $branch=$_POST['cb_branch'];
					 $count=1;
				   } 
			    else
				    $count=0;
				$q_input=''; 
				$branch_id='';
		        if ($count==1)
		           {
	                 foreach ($branch as $id_branch)
	                         { 
			                   if ($q_input=='')
							      {
				                    $q_input="('".$id."','".$id_branch."')";
									$branch_id="'".$id_branch."'";
								  }	
				               else  
							      {
					                $q_input=$q_input.", ('".$id."','".$id_branch."')";
									$branch_id=$branch_id.",'".$id_branch."'";
								  }	
						     }
					 mysqli_autocommit($db_connection, false);
					 $q_delete="DELETE FROM users_branch WHERE users_id='".$id."'";	
					 $q_delete_authorization="DELETE FROM users_authorization WHERE branch_id NOT IN ($branch_id) AND users_id='$id'";	
			         $q_input_data="INSERT INTO users_branch (users_id, branch_id) VALUES ".$q_input;  
		             $exec_delete=mysqli_query($db_connection, $q_delete);
					 $exec_delete_authorization=mysqli_query($db_connection, $q_delete_authorization);
		             $exec_input_data=mysqli_query($db_connection, $q_input_data);
		             if ($exec_delete && $exec_delete_authorization && $exec_input_data)
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
			                   alert('Terjadi Kesalahan\nSilahkan hubungi programmer anda!');
				               window.location.href='javascript:history.back(1)';
			                 </script>
		                   <?php 	
				        } 
				   }
				else
				   { 
				     ?>
		                <script language="javascript">
						   var x='<?php echo $id;?>';
			               var confirmation=confirm('Apakah yakin akan tetap dilanjutkan tanpa Autorisasi?');
						   if (confirmation)
						      {
							    document.f_cru_users.action='../../data/users/branch_empty_authorization.php?id='+x;
				                document.f_cru_users.submit();	
							  }
			            </script>
		             <?php  
			       }
			  }  
		 }
	  else
	     {
		   ?>
		      <script language="javascript">
			    alert('User yang akan disetting tidak ditemukan!');
				window.close();
			  </script>
		   <?php
		 }	 		   
	}  
	
?>

<script language="javascript">
  function select_unselect_all(x)
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('cb_branch[]');
			 
             for (i = 0; i<select_unselect_all_data.length; i++)
			     {
				   if (check_select[0].checked==true)
				      {
                        select_unselect_all_data[i].checked = true ;
					  }
				   else
				      {
					    select_unselect_all_data[i].checked = false ;
					  }	  	
				 }   
		   }
</script>





