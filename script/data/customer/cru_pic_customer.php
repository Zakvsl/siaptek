<?php
  include "../../library/check_session.php";
  include "../../library/db_connection.php";	
  $cru=mysqli_real_escape_string($db_connection, $_GET['c']);
  $cid=mysqli_real_escape_string($db_connection, $_GET['cid']);
  
  if ($cru=='u')
     {
	   $id=mysqli_real_escape_string($db_connection, $_GET['id']);
	   $q_get_pic_customer="SELECT *
                            FROM customer_pic
				            WHERE custp_id='$id'";
	 //  echo $q_get_pic_customer;
	   $exec_get_pic_customer=mysqli_query($db_connection,$q_get_pic_customer);
	   $total_pic_customer=mysqli_num_rows($exec_get_pic_customer);
	   $field_pic_customer=mysqli_fetch_array($exec_get_pic_customer);
	   if ($total_pic_customer==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('PIC yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {  
		    $cust_id=$field_pic_customer['cust_id'];
		    $custp_id=$field_pic_customer['custp_id'];
			$custp_name=$field_pic_customer['custp_name'];
			$custp_phone=$field_pic_customer['custp_phone'];
			$custp_email=$field_pic_customer['custp_email'];
			$custp_position=$field_pic_customer['custp_position_name'];
		  }	  
	 }
  else
     $id="";
?>
<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_pic_customer" id="f_cru_customer">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH DATA PIC CUSTOMER";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UBAH DATA PIC CUSTOMER";
															 }
													?>	  </th>
    </tr>
    
    

    <tr>
      <td width="33%">Nama</td>
      <td width="1%">:</td>
      <td width="66%"><input name="txt_custp_name" type="text" id="txt_custp_name"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custp_name;
				}	
		 ?>"/>
        <input name="txt_id" type="hidden" id="txt_id" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custp_id;
				}		
		 ?>"/></td>
    </tr>
    

    
    <tr>
      <td>No Telpon</td>
      <td>:</td>
      <td><input name="txt_custp_phone" type="text" id="txt_custp_phone"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custp_phone;
				}	
		 ?>"/></td>
    </tr>
    
    
    <tr>
      <td>Alamat Email</td>
      <td>:</td>
      <td><input name="txt_custp_email" type="text" id="txt_custp_email"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custp_email;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>Jabatan</td>
      <td>:</td>
      <td><input name="txt_custp_position" type="text" id="txt_custp_position"  size="30"
		 value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $custp_position;
				}	
		 ?>"/></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Save"/>              
      <input name="btn_new" type="reset" id="btn_new" value="New" />
      <input name="btn_close" type="button" id="btn_close" value="Close" onClick="window.close()"/>
      </label></td>
    </tr>
  </table>
</form>

<?php 
  if (isset($_POST['btn_save']))
     {
	   $custp_id=htmlspecialchars($_POST['txt_id']);
	   $custp_name=htmlspecialchars($_POST['txt_custp_name']);
	   $custp_phone=htmlspecialchars($_POST['txt_custp_phone']);
	   $custp_email=htmlspecialchars($_POST['txt_custp_email']);
	   $custp_position=htmlspecialchars($_POST['txt_custp_position']);
	   
	   if (trim($custp_name==''))
	      {
		    ?>
			  <script language="javascript">
			    alert('Nama PIC harus diisi!');
				window.location.href='javascript:history.back(1)';
			  </script>
			<?php 
		  }
	   else
	   if ($custp_id=='')  //jika tambah data
	      {
			mysqli_autocommit($db_connection, false);
			$q_input_pic_customer="INSERT INTO customer_pic (cust_id, custp_name, custp_phone, custp_email, custp_position_name)
					                         VALUES ('$cid', '$custp_name', '$custp_phone', '$custp_email', '$custp_position')";
		    $exec_input_pic_customer=mysqli_query($db_connection,$q_input_pic_customer);
		  //  echo $q_input_pic_customer;
			if ($exec_input_pic_customer)
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
						window.location.href='javascript:history.back(1)';
					 </script>
				  <?php 
			    } 
		  }
	   else   // jika update data
	      { 	     
			 mysqli_autocommit($db_connection, false);
			 $q_update_customer_pic="UPDATE customer_pic SET  custp_name='$custp_name', custp_phone='$custp_phone', custp_email='$custp_email', 
			                                custp_position_name='$custp_position'
					     	  	 WHERE custp_id='$custp_id'";					
				       //    echo $q_update_customer;
			 $exec_update_customer_pic=mysqli_query($db_connection,$q_update_customer_pic);
			 if ($exec_update_customer_pic)
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
?>






