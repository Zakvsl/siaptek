<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
      
  if ($cru=='u')
     {
	   $id_1=mysqli_real_escape_string($db_connection, $_GET['id']);	
	   $query_check_users="select * from users where users_id='$id_1'";
	   $exec_check_users=mysqli_query($db_connection, $query_check_users);
	   $total_check_users=mysqli_num_rows($exec_check_users);
	   $field_check_users=mysqli_fetch_array($exec_check_users);
	   if ($total_check_users==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('User yang akan diupdate tidak ditemukan!');
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
			$users_level=$field_check_users['users_level']; 
		  }	  
	 }
  else
     $id_1="";	
   
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
	   $users_level=htmlspecialchars($_POST['s_level']); 
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
	   if ($users_id=='')  //jika tambah data
	      {
		    $qcheck_users="select * from users where users_name='$users_name' or users_code='$users_code'";
		//	echo $qcheck_users;
			$qexec_check_users=mysqli_query($db_connection, $qcheck_users);
			if (mysqli_num_rows($qexec_check_users)>0)
			   {
			     ?>
			       <script language="javascript">
		             alert('Kode User atau User Name sudah digunakan!\nSilahkan diganti!');
					 window.location.href='javascript:history.back(1)';
			       </script>
		         <?php 
			   }
			else
			   {
				 mysqli_autocommit($db_connection, false);
			     $qinput_users="insert into users (users_code, users_name, users_password, users_names, users_email, users_phones, users_level, users_status)  
				                           values ('$users_code','$users_name',md5('$users_password'),'$users_naming','$users_email','$users_phone','$users_level','0')";
				 $qexec_input_users=mysqli_query($db_connection, $qinput_users);
				 if ($qexec_input_users)
				    {
					  mysqli_commit($db_connection);
					  ?>
                        <script language="javascript">
						  opener.location.reload(true);
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
	   else   // jika update data
	      {
		    $q_check_users="select * from users where users_id='$users_id'";
			$q_exec_check_users=mysqli_query($db_connection, $q_check_users);
			$field_users=mysqli_fetch_array($q_exec_check_users);
			if (mysqli_num_rows($q_exec_check_users)>0)
			   {
			     if ($field_users['users_status']=='1')
				    {
					  ?>
					     <script language="javascript">
						   alert('User tidak dapat diupdate!\nStatus InActive!');
						   window.close();
						 </script>
					  <?php
					}
				 else
				    {	
					  if ($users_level=='0')
					     {
						   $q_check_user="SELECT * FROM users WHERE users_level='2' AND users_id!='$users_id'";
						   $exec_check_user=mysqli_query($db_connection, $q_check_user);
						   if (mysqli_num_rows($exec_check_user)>0) 
						      {
							    ?>
			                      <script language="javascript">
		                            alert("User level 'Super Administrator' sudah terdaftar!");
					                window.location.href='javascript:history.back(1)';
			                      </script>
		                        <?php 
							  }
						   else
						      {
							    $q_check_users_1="SELECT users_name FROM users WHERE users_name='$users_name' and users_id!='$users_id'";
				                $q_exec_check_users_1=mysqli_query($db_connection, $q_check_users_1);
				                $total=mysqli_num_rows($q_exec_check_users_1);
				                if ($total==0)
		                           {
				                     mysqli_autocommit($db_connection, false);
						             if ($field_users['users_level']=='0')
					                     $qupdate_users="update users SET users_code='$users_code', users_name='$users_name', users_password=md5('$users_password'), 
										                        users_names='$users_naming', users_email='$users_email', users_phones='$users_phone' 
					                                     where users_id='$users_id'";	
						             else
						                 $qupdate_users="update users SET users_code='$users_code', users_name='$users_name', users_password=md5('$users_password'), 
										                        users_naming='$users_naming', users_email='$users_email', users_phones='$users_phone', users_level='$users_level' 
					                                     where users_id='$users_id'";	
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
						                      alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
						                      opener.location.reload(true);
					                        </script>
				                          <?php
						                } 
				                   }	
				               else
				                  {
					                ?>
			                           <script language="javascript">
		                                 alert('User Name sudah ada yang menggunakan!\nSilahkan diganti!');
				                         window.location.href='javascript:history.back(1)';
			                           </script>
		                            <?php 
						          }		 
							  }	  
						 }
					  else
					     {	 
			               $q_check_users_1="SELECT users_name FROM users WHERE users_name='$users_name' and users_id!='$users_id'";
						   $q_exec_check_users_1=mysqli_query($db_connection, $q_check_users_1);
				           $total=mysqli_num_rows($q_exec_check_users_1);
				           if ($total==0)
		                      {
				                mysqli_autocommit($db_connection, false);
						        if ($field_users['users_level']=='0')
					                $qupdate_users="update users SET users_code='$users_code', users_name='$users_name', users_password=md5('$users_password'), 
									                       users_names='$users_naming', users_email='$users_email', users_phones='$users_phone' 
					                                where users_id='$users_id'";	
						        else
						            $qupdate_users="update users SET users_code='$users_code', users_name='$users_name', users_password=md5('$users_password'), 
									                       users_names='$users_naming', users_email='$users_email', users_phones='$users_phone', users_level='$users_level' 
					                                where users_id='$users_id'";	
					            //echo $qupdate_users;
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
						                 alert('Terjadi Kesalahan!\nSilahkan hubungi programmer anda!');
						                 opener.location.reload(true);
					                   </script>
				                     <?php
						           } 
				              }	
				           else
				              {
					            ?>
			                       <script language="javascript">
									 alert('User Name sudah ada yang menggunakan!\nSilahkan diganti!');
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
		             alert('User yang akan diupdate tidak ditemukan!');
				     window.close();
			       </script>
		         <?php  
			   }     
		  }	  
	 }
?>


<form id="f_cru_users" name="f_cru_users" method="post" onsubmit="return call_validation_users(this)" action="<?php $_SERVER['PHP_SELF'];?>">
  <table width="523" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH USER BARU";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UPDATE USER";
															 }
													?>	  </th>
    </tr>
    <tr>
      <td width="30%">Kode User </td>
      <td width="1%">:</td>
      <td width="69%"><label>
      <input name="txt_code" type="text" id="txt_code" size="30" maxlength="25"  
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_code;
				}		
		 ?>">
      <input name="txt_id_1" type="hidden" id="txt_id_1" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_id;
				}		
		 ?>"/>
      </label></td>
    </tr>
    
    <tr>
      <td>User Name</td>
      <td>:</td>
      <td><label>
      <input name="txt_users_name" type="text" id="txt_users_name" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_name;
				}		
		 ?>" size='30' />
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
      <td><input name="txt_users_naming" type="text" id="txt_users_naming" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_naming;
				}		
		 ?>" size='30' /></td>
    </tr>
    <tr>
      <td>Alamat E-mail</td>
      <td>:</td>
      <td><input name="txt_users_email" type="text" id="txt_users_email" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_email;
				}		
		 ?>" size='30' /></td>
    </tr>
    <tr>
      <td>Phone No</td>
      <td>:</td>
      <td><input name="txt_users_phone" type="text" id="txt_users_phone" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $users_phone;
				}		
		 ?>" size='30' /></td>
    </tr>
    
    <tr>
      <td>Level</td>
      <td>:</td>
      <td><label>
        <select name="s_level" id="s_level">
		 <?php  
		       if ($cru=='i')
				  {  
					echo "<option value='1'>Administrator</option>";
					echo "<option value='2'>Public</option>";   
				  }
			   else
			   if ($cru=='u')
			      {
                    if ($users_level=='1')
				       {
						 echo "<option value='1' >Administrator</option>";
					     echo "<option value='2' >Public</option>";
					   }
					else
					if ($users_level=='2')
				       {
						 echo "<option value='1' >Administrator</option>";
					     echo "<option value='2' selected='selected'>Public</option>";
					   }	 
				  }	  
		 ?>
        </select>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>              
      <input name="btn_new" type="reset" id="btn_new" value="Baru" />
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onclick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>


<script language="javascript">
  function call_validation_users(f_cru_users)
           {
			  var name_users=document.getElementById('txt_users_name').value.trim();
			  var names_users=document.getElementById('txt_users_naming').value.trim();
			  var password_users=document.getElementById('txt_password').value;
			  var level_users=document.getElementById('s_level').value;
			  var error_message=new Array();
			  if (name_users=='' && names_users=='' && password_users.trim()=='') 
		         { 
				   error_message[0]='User Name, Password dan Nama harus diisi!';
				 } 
			  else
			   if (name_users=='' && names_users=='' && password_users.trim()!='') 
		         { 
				   error_message[0]='User Name dan Nama harus diisi!';
				 } 
			  else
			   if (name_users=='' && names_users!='' && password_users.trim()=='') 
		         { 
				   error_message[0]='User Name dan Password harus diisi!';
				 } 
			  else
			   if (name_users!='' && names_users=='' && password_users.trim()=='') 
		         { 
				   error_message[0]='Nama dan Password harus diisi!';
				 } 
			  else
			   if (name_users=='' && names_users!='' && password_users.trim()!='') 
		         { 
				   error_message[0]='User Name harus diisi!';
				 } 
			  else
			   if (name_users!='' && names_users=='' && password_users.trim()!='') 
		         { 
				   error_message[0]='Nama harus diisi!';
				 } 
			  else
			   if (name_users!='' && names_users!='' && (password_users.trim()=='' || password_users.length<6)) 
		         { 
				   error_message[0]='Password harus diisi (Min 6 Karakter)!';
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
				         document.f_cru_users.txt_users_name.focus();
					   }	  
				    else
				    if (names_users=="")
				       {
					     document.f_cru_users.txt_users_names.focus();
					   }
					else
				    if (level_users=="")
				       {
					     document.f_cru_users.s_level.focus();
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





