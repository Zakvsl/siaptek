<?php
   session_start();
   if (!isset($_SESSION['ses_user_id']))
      {
	    ?>
		   <script language="javascript">
		     window.opener.location.href='../../data/log_in_out/log_out.php';
			 window.close();
		   </script>
		<?php
	  }
   else
      {
	    include "../../library/db_connection.php";
        $id=$_SESSION['ses_user_id'];
	    $q_check_user="SELECT * FROM users WHERE users_id='$id'";
		$exec_check_user=mysqli_query($db_connection, $q_check_user);
		if (mysqli_num_rows($exec_check_user)>0)
		   {
		     $field_users=mysqli_fetch_array($exec_check_user);
             //if (isset($_SESSION['ses_super_admin']))
			 if ($field_users['users_level']=='0')
                 $q_check_branch="SELECT branch_id, branch_code, branch_name
                                   FROM branch";
             else
                 $q_check_branch="SELECT branch.branch_id, branch_code, branch_name
                                   FROM branch
                                   INNER JOIN users_branch ON branch.branch_id=users_branch.branch_id
                                   WHERE users_id='$id'";
             $exec_check_branch=mysqli_query($db_connection, $q_check_branch);	
             $total=mysqli_num_rows($exec_check_branch);
             if ($total==0)
                {
	              ?>
		             <script language="javascript">
		               window.opener.location.href='../../data/log_in_out/log_out.php';
			           alert('Maaf!! Anda tidak diizinkan untuk masuk!\nSilahkan hubungi bagian Administrator!');
			           window.close();
		             </script>
		          <?php
		        }		
		   }
		else
		   {
		     ?>
			    <script language="javascript">
				   alert('User tidak terdaftar!\nSilahkan coba log in kembali!');
		           window.opener.location.href='../../data/log_in_out/log_out.php';
			       window.close();
				</script>
			 <?php
		   }
	  }	 			  
?>
<form id="f_pilih_project" name="f_pilih_project" method="post" action="" onsubmit="return call_validation_user(this)">
  <table width="27%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="3" scope="col">PILIH KANTOR CABANG</th>
    </tr>
    <tr>
      <td width="44%">&nbsp;</td>
      <td width="1%">&nbsp;</td>
      <td width="54%">&nbsp;</td>
    </tr>
    <tr>
      <td width="44%" nowrap="nowrap">Nama Kantor Cabang</td>
      <td width="1%">:&nbsp;&nbsp;</td>
      <td>
        <select name="s_branch" id="s_branch" style="width:300px">
		  <?php
		      while ($field_data=mysqli_fetch_array($exec_check_branch))
			        {
					  echo "<option value='".$field_data['branch_id']."'>".$field_data['branch_code']."-".$field_data['branch_name']."</option>";
					}
		  ?>
        </select>
      </td>
    </tr>
    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td nowrap="nowrap"><input name="btn_enter" type="submit" id="btn_enter" value="Masuk" />
                          <input type="button" name="btn_exit" value="Keluar" onclick="log_out()"/></td>
    </tr>
  </table>
</form>

<?php
   if (isset($_POST['btn_enter']))
      {
		$query_check_user="SELECT * FROM users WHERE users_id='$id'";
		$exec_check_user=mysqli_query($db_connection, $query_check_user);
		$total_data=mysqli_num_rows($exec_check_user);
		$field_data=mysqli_fetch_array($exec_check_user);
		if ($total_data>0)
		   {
		     $_SESSION['ses_siaptek_admin']=$field_data['users_name'];
			 $_SESSION['ses_user_id']=$field_data['users_id'];
			 $_SESSION['ses_user_naming']=$field_data['users_names'];
			 $_SESSION['ses_user_level']=$field_data['users_level'];				
		     if ($field_data['users_level']=='0')
			    {
				  $_SESSION['ses_super_admin']="yes";
				  $_SESSION['ses_branch']='D';
				  $_SESSION['ses_whs']='D';
				  $_SESSION['ses_item_category']='D';
				  $_SESSION['ses_master_item']='D';
				  $_SESSION['ses_item_detail']='D';
				  $_SESSION['ses_item_detail_all']='D';
				  $_SESSION['ses_summary_item']='D';
				  $_SESSION['ses_transfer_item']='D';
				  $_SESSION['ses_receipt_transfer_item']='D';
				  $_SESSION['ses_issuing']='D';
				  $_SESSION['ses_returning']='D';
				  $_SESSION['ses_broken']='D';
				  $_SESSION['ses_write_off']='D';
				  $_SESSION['ses_dispossal']='D';
				  $_SESSION['ses_change_description']='D';
				  $_SESSION['ses_issuing_report']='D';
				  $_SESSION['ses_returning_report']='D';
				  $_SESSION['ses_broken_report']='D';
				  $_SESSION['ses_write_off_report']='D';
				  $_SESSION['ses_dispossal_report']='D';
				  $_SESSION['ses_change_description_report']='D';
				  $_SESSION['ses_position_report']='D';
				  $_SESSION['ses_aging_report']='D';
				  $_SESSION['ses_soa_report']='D';
				  $_SESSION['ses_vendor_item_report']='D';
				  $_SESSION['ses_history_movement_report']='D';
				  $_SESSION['ses_transfer_report']='D';
				  $_SESSION['ses_doc_flow_report']='D';
				  $_SESSION['ses_employee']='D';
				  $_SESSION['ses_uom']='D';
				  $_SESSION['ses_customer_type']='D';
				  $_SESSION['ses_customer']='D';
				  $_SESSION['ses_vendor']='D';
				  $_SESSION['ses_apt_period']='D';
				}
			 else
			    {
	              $q_check_users="SELECT menu, authorization
                                  FROM users_authorization
                                  INNER JOIN users ON users.users_id=users_authorization.users_id
                                  WHERE users_authorization.users_id='$id' AND users_authorization.branch_id='".$_POST['s_branch']."'";
			     //  echo $q_check_users;
				  $exec_check_users=mysqli_query($db_connection, $q_check_users);			  
			      while ($field_data=mysqli_fetch_array($exec_check_users))
			            {   
					      if ($field_data['menu']=='BRANCH')
					          $_SESSION['ses_branch']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='WHS')
					          $_SESSION['ses_whs']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ITEM_CATEGORY')
					          $_SESSION['ses_item_category']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='MASTER_ITEM')
					          $_SESSION['ses_master_item']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='ITEM_DETAIL')
					          $_SESSION['ses_item_detail']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='ITEM_DETAIL_ALL')
					          $_SESSION['ses_item_detail_all']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='SUMMARY_ITEM')
					          $_SESSION['ses_summary_item']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='TRANSFER_ITEM')
					          $_SESSION['ses_transfer_item']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='RECEIPT_TRANSFER_ITEM')
					          $_SESSION['ses_receipt_transfer_item']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ISSUING')
					          $_SESSION['ses_issuing']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='RETURNING')
					          $_SESSION['ses_returning']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='BROKEN')
					          $_SESSION['ses_broken']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='WRITE_OFF')
					          $_SESSION['ses_write_off']=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='DISPOSSAL')
					          $_SESSION['ses_dispossal']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CHANGE_DESCRIPTION')
					          $_SESSION['ses_change_description']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='ISSUING_REPORT')
					          $_SESSION['ses_issuing_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='RETURNING_REPORT')
					          $_SESSION['ses_returning_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='BROKEN_REPORT')
					          $_SESSION['ses_broken_report']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='WRITE_OFF_REPORT')
					          $_SESSION['ses_write_off_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='DISPOSSAL_REPORT')
					          $_SESSION['ses_dispossal_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CHANGE_DESCRIPTION_REPORT') 
					          $_SESSION['ses_change_description_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='POSITION_REPORT')
					          $_SESSION['ses_position_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='AGING_REPORT')
					          $_SESSION['ses_aging_report']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='SOA_REPORT')
					          $_SESSION['ses_soa_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='VENDOR_ITEM_REPORT')
					          $_SESSION['ses_vendor_item_report']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='HISTORY_MOVEMENT_REPORT')
					          $_SESSION['ses_history_movement_report']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='TRANSFER_REPORT')
					          $_SESSION['ses_transfer_report']=$field_data['authorization'];
					      else
						  if ($field_data['menu']=='DOC_FLOW_REPORT')
					          $_SESSION['ses_doc_flow_report']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='EMPLOYEE')
					          $_SESSION['ses_employee']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='UOM')
					          $_SESSION['ses_uom']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='CUSTOMER_TYPE')
					          $_SESSION['ses_customer_type']=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='CUSTOMER')
					          $_SESSION['ses_customer']=$field_data['authorization'];
					      else
					      if ($field_data['menu']=='VENDOR')
					          $_SESSION['ses_vendor']=$field_data['authorization'];
						  else
					      if ($field_data['menu']=='APT_PERIOD')
					          $_SESSION['ses_apt_period']=$field_data['authorization'];
				        }
				}
			 
			 $_SESSION['ses_id_branch']=$_POST['s_branch'];
			 ?>	
		        <script language="javascript">   
			       window.opener.location.href='../../index/index.php';
				   window.close();
			    </script>
		 	 <?php   
		   }
		else
		   {
		     ?>
			    <script language="javascript">
				   alert('User tidak terdaftar!\nSilahkan Log In ulang!');
				   window.opener.location.href='../../data/log_in_out/log_out.php';
				   window.close();
				</script>
			 <?php
		   }
	  }
?>

<script language="javascript">
  function log_out()
           {
		     window.opener.location="../../data/log_in_out/log_out.php";
			 window.close();
		   } 
</script>
