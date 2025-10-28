<?php
  setcookie("id","");
  setcookie("code","");
  setcookie("name","");
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  $cru=htmlspecialchars($_GET['c']);
  $br_id=htmlspecialchars($_GET['brid']);
  $wh_id=htmlspecialchars($_GET['whid']);	
  
  if ($cru=='i')
     {
	   if ($br_id=='' && $wh_id=='')
	      {
	        ?>
			  <script language="javascript">
			     alert('Kantor Cabang belum ditentukan!');
				 window.close();
			  </script>
		    <?php 
		  }
	   else
	   if ($br_id!='' && $wh_id=='')
	      {
		    $q_get_branch="select branch_id, branch_name from branch where branch_id='$br_id'";
			$exec_get_branch=mysqli_query($db_connection, $q_get_branch);
			$field_location=mysqli_fetch_array($exec_get_branch);
			if (mysqli_num_rows($exec_get_branch)>0)
			   {
			     $branch_id=$field_location['branch_id'];
				 $branch_name=$field_location['branch_name'];
			   }
			else 
			   {
			     ?>
			       <script language="javascript">
			         alert('Kantor Cabang belum ditentukan!');
				     window.close();
			       </script>
		         <?php 
			   }  
		  }
	   else	 
	   if ($br_id=='' && $wh_id!='')
	      {
		    $q_get_whs_location="SELECT branch_id, whsl_id, whsl_code, whsl_name, whsl_parent_id, whsl_level FROM warehouse_location 
                                 WHERE whsl_id='$wh_id'";
			$exec_get_whs_location=mysqli_query($db_connection, $q_get_whs_location);
			$field_whsl=mysqli_fetch_array($exec_get_whs_location);
			if (mysqli_num_rows($exec_get_whs_location)>0)
			   {
			     $branch_id=$field_whsl['branch_id'];
				 $parent_id=$field_whsl['whsl_id'];
				 $parent_name=$field_whsl['whsl_name'];
				 $parent_code=$field_whsl['whsl_code'];
			//	 $parent_id=$field_whsl['whsl_parent_id'];
			   }
			else
			   {
			     ?>
			        <script language="javascript">
			          alert('Lokasi Gudang belum ditentukan!');
				      window.close();
			        </script>
		         <?php
			   }   
		  }	   
	 }
  else
  if ($cru=='u')
     {
	   $q_get_whs_location="SELECT branch_id, whsl_id, whsl_code, whsl_name, whsl_level, whsl_parent_id 
	                        FROM warehouse_location 
						    WHERE whsl_id='$wh_id'";
	   $exec_get_whs_location=mysqli_query($db_connection, $q_get_whs_location);
	   $total_get_whs_location=mysqli_num_rows($exec_get_whs_location);
	   $field_whs_location=mysqli_fetch_array($exec_get_whs_location);
	   if ($total_get_whs_location==0)
	      {
		    ?>
			   <script language="javascript">
		         alert('Lokasi Gudang yang akan diupdate tidak ditemukan!');
				 window.close();
			   </script>
		    <?php	
		  }
	   else
	      {
		    $whsl_id=$field_whs_location['whsl_id'];
		    $whsl_code=$field_whs_location['whsl_code'];
			$branch_id=$field_whs_location['branch_id'];
			$whsl_name=$field_whs_location['whsl_name'];
			$whsl_parent=$field_whs_location['whsl_parent_id']; 
			$whsl_level=$field_whs_location['whsl_level'];
			if  ($whsl_level=='1')
			     $q_get_parent="SELECT branch_id, branch_name FROM branch WHERE branch_id='$br_id'";
			else
			     $q_get_parent="SELECT whsl_id, whsl_code, whsl_name FROM warehouse_location WHERE whsl_id='$whsl_parent'";
			$q_exec_get_parent=mysqli_query($db_connection, $q_get_parent);
			$field_get_parent=mysqli_fetch_array($q_exec_get_parent);
			if  ($whsl_level=='1')
			    {
				  $parent_id=$field_get_parent['branch_id'];
			      $parent_code=$field_get_parent['branch_id'];
			      $parent_name=$field_get_parent['branch_name'];
				}
		    else
			    {
				  $parent_id=$field_get_parent['whsl_id'];
			      $parent_code=$field_get_parent['whsl_code'];
			      $parent_name=$field_get_parent['whsl_name'];
				}	
		  }	  
	 } 
	 
  if (isset($_POST['btn_save']))
     {
		 $wh_id=$_GET['whid'];
		 if ($wh_id=='')
		 	$check_parent="SELECT *, 1 AS whsl_level FROM branch WHERE branch_id='".htmlspecialchars($_POST['s_parent'])."'";
		else
			$check_parent="SELECT * FROM warehouse_location WHERE whsl_id='".htmlspecialchars($_POST['s_parent'])."'";
	   $exec_check_parent=mysqli_query($db_connection, $check_parent);
	   $field_data=mysqli_fetch_array($exec_check_parent);
	   if (mysqli_num_rows($exec_check_parent)==0)
	      {
		    ?>
			   <script language="javascript">
			      alert('Header tidak ditemukan!');
				  window.close();
			   </script>
			<?php 
		  }
	   else
	      {
	        $parent_id=htmlspecialchars($_POST['s_parent']);
	        $whsl_code=htmlspecialchars($_POST['txt_id']);
	        $whsl_code_1=htmlspecialchars($_POST['txt_id_1']);
	        $whsl_name=htmlspecialchars($_POST['txt_whsl_name']);
	        $special_char_code=strlen($_POST['txt_id']);
			$whsl_level=$field_data['whsl_level'];
	        $char_special_code=0;
	        for ($i=0;$i<$special_char_code;$i++)
	            {
		          $char_code=substr($_POST['txt_id'],$i,1);
			      if ($char_code=='/' || $char_code=='\\' || $char_code==':' || $char_code=='"' || $char_code=='?' || $char_code=='*' || $char_code=='<' || $char_code=='>' || 
			          $char_code=='|')  
			          $char_special_code=1;  
		        }
	        $special_char_name=strlen($_POST['txt_whsl_name']);
	        $char_special_name=0;
	        for ($i=0;$i<$special_char_name;$i++)
	            {
		          $char_name=substr($_POST['txt_whsl_name'],$i,1);
			      if ($char_name=='/' || $char_name=='\\' || $char_name==':' || $char_name=='"' || $char_name=='?' || $char_name=='*' || $char_name=='<' || $char_name=='>' || 
			          $char_name=='|')  
			          $char_special_name=1;  
		        }

	        if (trim($whsl_code==''))
	           {
		         ?>
			       <script language="javascript">
			         alert('Kode Lokasi Gudang harus diisi!');
				     window.location.href='javascript:history.back(1)';
			       </script>
			     <?php 
		       }
	        else 
	        if (trim($whsl_name==''))
	           {
		         ?>
			       <script language="javascript">
			         alert('Nama Lokasi Gudang harus diisi!');
				     window.location.href='javascript:history.back(1)';
			       </script>
			     <?php 
		       }
	        else	 
	        if ($char_special_code==1 || $char_special_name==1)
	           {
		         ?>
			       <script language="javascript">
			         alert('Karakter berikut ini tidak diizinkan untuk diinput :  " / \\ : ? * < > |');
				     window.location.href='javascript:history.back(1)';
			       </script>
			     <?php  
		       }
	        else 
	           {
		         $parent_id=htmlspecialchars($_POST['s_parent']);
			     $whsl_code=htmlspecialchars($_POST['txt_id']);
			     $whsl_code_1=htmlspecialchars($_POST['txt_id_1']);
			     $whsl_code_2=htmlspecialchars($_POST['txt_id_2']);
			     $whsl_name=htmlspecialchars($_POST['txt_whsl_name']);
			     if ($br_id!='' && $whsl_level=='1')
		             $q_get_parent="SELECT branch_id AS whsl_id, '' AS whsl_parent_path, 1 AS whsl_level FROM branch WHERE branch_id='$parent_id'";
		         else
			         $q_get_parent="SELECT whsl_id, whsl_parent_path, whsl_level FROM warehouse_location 
				                    WHERE whsl_id='$parent_id'";	
			     $exec_get_parent=mysqli_query($db_connection, $q_get_parent);
			     if (mysqli_num_rows($exec_get_parent)==0)
			        {
			          ?>
			             <script language="javascript">
			              alert('Header tidak ditemukan!');
				          window.location.href='javascript:history.back(1)';
			             </script>
			          <?php   
			        }
			     else
			        {
		              if ($cru=='i')
			             {
					       $q_get_whs_location="SELECT whsl_code FROM warehouse_location WHERE whsl_code='$whsl_code'";		
						   $exec_get_whs_location=mysqli_query($db_connection, $q_get_whs_location);
					       if (mysqli_num_rows($exec_get_whs_location)>0)
			                  {
			                    ?>
			                       <script language="javascript">
			                         alert('Duplikasi Kode Lokasi Gudang!');
				                     window.location.href='javascript:history.back(1)';
			                       </script>
			                    <?php   
			                  }
			               else
					          {
					            mysqli_autocommit($db_connection, false);
					            $field_parent=mysqli_fetch_array($exec_get_parent);
					            if ($field_parent['whsl_parent_path']=='0')
					                $path=$field_parent['whsl_id'];
					            else  
					                $path=$field_parent['whsl_parent_path'];
					            $level=$field_parent['whsl_level']+1;
					            if ($br_id!='')
								   {
					                 $q_input_whs_location="INSERT INTO warehouse_location (branch_id, whsl_code, whsl_name, whsl_level, whsl_parent_id, 
									                                                        whsl_parent_path, whsl_type)
                                                           VALUE('$branch_id','$whsl_code','$whsl_name','1','0','0','1')";
								   }
								else
					               {
					                 $q_input_whs_location="INSERT INTO warehouse_location (branch_id, whsl_code, whsl_name, whsl_level, whsl_parent_id, 
									                                                        whsl_parent_path, whsl_type)
                                                            VALUE('$branch_id','$whsl_code','$whsl_name','$level','".$field_parent['whsl_id']."','$path','1')";
						             $q_update_parent="UPDATE warehouse_location SET whsl_type='0' WHERE whsl_id='".$field_parent['whsl_id']."'";							
						             $q_update_whs_location="UPDATE warehouse_location set whsl_parent_path=CONCAT(whsl_parent_path,',',whsl_id) 
								                             WHERE whsl_code='$whsl_code'";
						           }
				                
								
					            $exec_1=mysqli_query($db_connection, $q_input_whs_location);	
					            if ($br_id!='')  
					               {			  
						             if ($exec_1)
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
			                                   alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 1!');
				                               window.close();
			                                 </script>
			                              <?php  
							            }	   
						           } 
					            else
					               {
					                 $exec_2=mysqli_query($db_connection, $q_update_parent);
						             $exec_3=mysqli_query($db_connection, $q_update_whs_location);
					                 if ($exec_1 && $exec_2 && $exec_3)
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
			                                    alert('Terjadi kesalahan!\nSilahkan hubungi programmer anda 2!');
				                                window.close();
			                                  </script>
			                               <?php  
							             }
							        }	   	   
						      }
			             }
			          else
			          if ($cru=='u')    
			             {
					       $q_check_whs_location="SELECT whsl_id from warehouse_location where whsl_id='$whsl_code_1'";
					       $exec_check_whs_location=mysqli_query($db_connection, $q_check_whs_location);
					       if (mysqli_num_rows($exec_check_whs_location)==0)
					          {
						        ?>
			                       <script language="javascript">
			                          alert('Lokasi Gudang yang akan diupdate tidak ditemukan!');
				                      window.close();
			                       </script>
			                    <?php  
						      }
					       else
					          {
						        $field_whs_location=mysqli_fetch_array($exec_check_whs_location);
						        $id_strga=$field_whs_location['whsl_id'];
						        if ($whsl_code==$whsl_code_2)
						           {
								     mysqli_autocommit($db_connection, false);
								     $q_update_whs_location="UPDATE warehouse_location SET whsl_name='$whsl_name' WHERE whsl_id='$whsl_code_1'";
							         $exec_update_whs_location=mysqli_query($db_connection, $q_update_whs_location);
							         if ($exec_update_whs_location)
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
				                               window.close();
			                                 </script>
			                              <?php
								        }   
							       }
						        else
						           {
								     $q_check_whs_location="SELECT whsl_id FROM warehouse_location WHERE whsl_code='$whsl_code'";
							         $exec_check_whs_location=mysqli_query($db_connection, $q_check_whs_location);
							         $field_whsl=mysqli_fetch_array($exec_check_whs_location);
							         if ($field_whsl['whsl_id']!='')
								        {
								          ?>
			                                 <script language="javascript">
			                                    alert('Duplikasi Kode Lokasi Gudang!');
				                                window.location.href='javascript:history.back(1)';
			                                 </script>
			                              <?php  
								        }
							         else 
								        { 
								          mysqli_autocommit($db_connection, false);
								          $q_update_whs_location="UPDATE warehouse_location SET whsl_code='$whsl_code', whsl_name='$whsl_name' 
										                          WHERE whsl_id='$whsl_code_1'";
								          $exec_update_whs_location=mysqli_query($db_connection, $q_update_whs_location);
								          if ($exec_update_whs_location)
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
				                                      window.close();
			                                       </script>
			                                    <?php   							
									         }								
								        }   
							       }
							  }
						 }	 
			        }
			   }		
		  }	  
	 }
?>

<body background='../../images/bg/bg.png'>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="f_cru_whs_location" id="f_cru_whs_location">
  <table width="403" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <th colspan="3" scope="col">&nbsp;</th>
    </tr>
    <tr>
      <th colspan="3" scope="col"><div align="left"><?php if ($cru=='i') 
	                                                         {
															   echo "TAMBAH BARU LOKASI GUDANG";
															 }
														  else
														  if ($cru=='u')
														     {
															   echo "UPDATE LOKASI GUDANG";
															 }
													?>	  </th>
    </tr>
    
    <tr>
      <td width="38%">Parent</td>
      <td width="2%">:</td>
      <td width="60%"><select id="s_parent" name="s_parent">
        <?php
		  if ($br_id!='')
		      echo "<option value='$branch_id'>$branch_name</option>"; 		
		  else
		  if ($wh_id!='')
		      echo "<option value='$parent_id'>$parent_name - [$parent_code]</option>"; 		
		?></select></td>
    </tr>
    
    <tr>
      <td>Kode Lokasi Gudang </td>
      <td>:</td>
      <td><label>
        <input name="txt_id" type="text" id="txt_id" size="30" maxlength="25"   
	     value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $whsl_code;
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
				  echo $whsl_id;
				}		
		 ?>"/>
        <input name="txt_id_2" type="hidden" id="txt_id_2" value="<?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $whsl_code;
				}		
		 ?>"/>
      </label></td>
    </tr>
    
    <tr>
      <td nowrap="nowrap">Nama Lokasi Gudang</td>
      <td>:</td>
      <td><textarea name="txt_whsl_name" id="txt_whsl_name" cols="33"><?php 
		     if ($cru=='i')
			    {
				  echo "";
				}
		     else
			 if ($cru=='u')
			    {
				  echo $whsl_name;
				}	
		 ?></textarea></td>
    </tr>
    

    
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><label>
      <input name="btn_save" type="submit" id="btn_save" value="Simpan"/>              
      <input name="btn_new" type="reset" id="btn_new" value="Baru" />
      <input name="btn_close" type="button" id="btn_close" value="Tutup" onClick="window.close()"/>
      </label></td>
    </tr>
  </table>
  <label></label>
</form>






