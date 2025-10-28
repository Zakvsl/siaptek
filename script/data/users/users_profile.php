<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  $id=mysqli_real_escape_string($db_connection, $_GET['id']);
      
  $query_check_users="select * from users where users_id='$id'"; 
  $exec_check_users=mysqli_query($db_connection, $query_check_users); 
  $total_check_users=mysqli_num_rows($exec_check_users);
  $field_check_users=mysqli_fetch_array($exec_check_users);
  if ($total_check_users==0)
     {
       ?>
          <script language="javascript">
            alert('User tidak ditemukan!');
            window.close();
          </script>
       <?php	
     }
  else
     {
       $users_id=$field_check_users['users_id'];
       $users_code=$field_check_users['users_code'];
       $users_name=$field_check_users['users_name'];
       $users_naming=$field_check_users['users_names'];
       $users_email=$field_check_users['users_email'];
       $users_phone=$field_check_users['users_phones'];
	   if ($field_check_users['users_level']=='0')
	       $users_level='Super Administrator';
	   else  
	   if ($field_check_users['users_level']=='1')
	       $users_level='Administrator';
	   else
	       $users_level='Public';  
       
     }	 
	 
  if (isset($_POST['btn_save']))
     {
	   $users_id=htmlspecialchars($_POST['txt_id_1']);
	   $users_code=htmlspecialchars($_POST['txt_code']);
	   $users_name=htmlspecialchars($_POST['txt_users_name']);
	   $users_naming=htmlspecialchars($_POST['txt_users_naming']); 
	   $users_password=htmlspecialchars($_POST['txt_password']); 
	   $users_password_1=htmlspecialchars($_POST['txt_password_1']); 
	   $users_email=htmlspecialchars($_POST['txt_users_email']); 
	   $users_phone=htmlspecialchars($_POST['txt_users_phone']);
	   if ($users_password!=$users_password_1)
	      {
		    ?>
			  <script language="javascript">
			    alert('Password tidak sama!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php
		  }
	   else
	      {
		    $qcheck_users="select * from users where users_id='$id'";
			$qexec_check_users=mysqli_query($db_connection, $qcheck_users);
			if (mysqli_num_rows($qexec_check_users)==0)
			   {
			     ?>
			       <script language="javascript">
		             alert('User tidak ditemukan!');
					 window.location.="../../data/log_in_out/log_out.php";
			       </script>
		         <?php 
			   }
			else
			   {
				 mysqli_autocommit($db_connection, 'false');
			     $qupdate_users="UPDATE users SET users_name='$users_name', users_password=md5('$users_password'), users_names='$users_naming', 
				                       users_email='$users_email', users_phones='$users_phone'
								WHERE users_id='$id'";
				// echo $qupdate_users;
				 $qexec_update_users=mysqli_query($db_connection, $qupdate_users);
				 if ($qexec_update_users)
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
						   opener.location.reload(true);
					     </script>
				      <?php  
					}
			   }   
		  }
	 }
?>


<form id="f_profile" name="f_profile" method="post" onsubmit="return call_validation_users()" action="<?php $_SERVER['PHP_SELF'];?>">
  <table width="523" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left">PROFILE USER</th>
    </tr>
    <tr>
      <td width="30%">Kode User </td>
      <td width="1%">:</td>
      <td width="69%"><?php echo $users_code;?><input name="txt_id_1" type="hidden" id="txt_id_1" value="<?php echo $users_id;?>"/></td>
    </tr>
    
    <tr>
      <td>User Name</td>
      <td>:</td>
      <td><label>
      <input name="txt_users_name" type="text" id="txt_users_name" value="<?php echo $users_name;?>" size='30' />
      </label></td>
    </tr>
    
    <tr>
      <td>Password</td>
      <td>:</td>
      <td><input name="txt_password" type="password" id="txt_password" size="30" maxlength="25"  
	     value=""/></td>
    </tr>
    <tr>
      <td>Konfirm Password</td>
      <td>:</td>
      <td><input name="txt_password_1" type="password" id="txt_password_1" size="30" maxlength="25"  
	     value=""/></td>
    </tr>
    <tr>
      <td>Nama</td>
      <td>:</td>
      <td><input name="txt_users_naming" type="text" id="txt_users_naming" value="<?php echo $users_naming;?>" size='30' /></td>
    </tr>
    <tr>
      <td>Alamat E-mail</td>
      <td>:</td>
      <td><input name="txt_users_email" type="text" id="txt_users_email" value="<?php echo $users_email;?>" size='30' /></td>
    </tr>
    <tr>
      <td>Phone No</td>
      <td>:</td>
      <td><input name="txt_users_phone" type="text" id="txt_users_phone" value="<?php echo $users_phone;?>" size='30' /></td>
    </tr>
    
    <tr>
      <td>Level</td>
      <td>:</td>
      <td><label></label><?php echo $users_level;?></label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onclick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>


<script language="javascript">
  function call_validation_users()
           {
			  var name_users=document.getElementById('txt_users_name').value.trim();
			  var names_users=document.getElementById('txt_users_naming').value.trim();
			  var password_users=document.getElementById('txt_password').value;
			  var error_message=new Array();
			  if (name_users=='' && names_users=='' && password_users.trim()=='') 
		         { 
				   error_message[0]='Name Of User, User Name and Password must be filled!';
				 } 
			  else
			   if (name_users=='' && names_users=='' && password_users.trim()!='') 
		         { 
				   error_message[0]='Name Of User and User Name must be filled!';
				 } 
			  else
			   if (name_users=='' && names_users!='' && password_users.trim()=='') 
		         { 
				   error_message[0]='Name Of User and Password must be filled!';
				 } 
			  else
			   if (name_users!='' && names_users=='' && password_users.trim()=='') 
		         { 
				   error_message[0]='User Name and Password must be filled!';
				 } 
			  else
			   if (name_users=='' && names_users!='' && password_users.trim()!='') 
		         { 
				   error_message[0]='Name Of User must be filled!';
				 } 
			  else
			   if (name_users!='' && names_users=='' && password_users.trim()!='') 
		         { 
				   error_message[0]='User Name must be filled!';
				 } 
			  else
			   if (name_users!='' && names_users!='' && (password_users.trim()=='' || password_users.length<6)) 
		         { 
				   error_message[0]='Password must be filled (Min 6 characters)!';
				 } 
				 	 
			  var length_error=error_message.length;
			  if (length_error>0)
			     {
				   if (length_error==1)
				      {
					    message_error=error_message[0]
					  }
				   else
				   if (length_error>1)
				      {
					    message_error=error_message[0]
				        for (i=1; i<length_error;i++)
				            {
				              message_error=message_error+', '+error_message[i]
					        }
					  }	  
				    alert(message_error);
				    if (name_users=="")
				       {
				         document.f_profile.txt_users_name.focus();
					   }	  
				    else
				    if (names_users=="")
				       {
					     document.f_profile.txt_users_names.focus();
					   }
					return (false);   
				 }  
			  else
			     {
				   return (true);
				 }	 
		   }
		   
   function close_users()
           {
		     window.close();
		   }
</script>





