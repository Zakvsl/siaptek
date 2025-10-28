<?php
 include "../library/check_session.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  
  $array_field=array("users_code", "users_names", "users_email", "users_phones","users_level","users_status");
  $array_sorting=array("asc", "desc");
  
  $row=100;	
  if (isset($_POST['s_page']))
     {
	   $paged=$_POST['s_page']; 
	   $first_data=(($paged-1)*$row);
	 }
  else
     {  
	   $paged=1;
	   $first_data=0;
	 }	  
  				
  if (isset($_POST['s_sorting_field'])) 
     {
	   $field_to_sort=$_POST['s_sorting_field'];
	 }				
  else
     {
	   $field_to_sort='users_names';
	 }
  if (isset($_POST['s_sort']))
     {
	   $type_to_sort=$_POST['s_sort'];
	 } 	 
  else
     {
	   $type_to_sort='asc';
	 }	 
  	 
  $ordered_by="ORDER BY ".$field_to_sort." ".$type_to_sort;				
				
				
  if (isset($_GET['find']))
	 {
	   $find=$_GET['find'];
	   if ($find=='y')
		  {
			$field_to_find=mysqli_real_escape_string($db_connection, $_COOKIE['field_to_find']);
			$operator_to_find=mysqli_real_escape_string($db_connection, $_COOKIE['operator_to_find']);
			$text_to_find=htmlspecialchars($_COOKIE['text_to_find']);
			if ($field_to_find==0)
			   { 
				 $field='users_names';
			   }
			else
			if ($field_to_find==1)
			   {
				 $field='users_name';
			   }
			else
			if ($field_to_find==2)
			   {
				 $field='users_level';
			   }   
			else
			if ($field_to_find==3)
			   {
				 $field='users_status';
			   } 
							 
			if ($operator_to_find=='0')
			   {
				 $find_text="='".$text_to_find."'";
			   }	  
			else
			if ($operator_to_find=='1')
			   {
			     $find_text="<>'".$text_to_find."'";
			   }	
			else
			if ($operator_to_find=='2')
			   {
				 $find_text=" like '%".$text_to_find."%'";  
			   }	
							 
			$find_data=$field.$find_text." ".$ordered_by;	  
			$query_page="SELECT count(users_id) as total_page FROM users WHERE users_level!='0' AND $find_data"; 	 
		  }
	   else
		  {
			 $query_page="SELECT count(users_id) as total_page FROM users WHERE users_level!='0'"; 	
		  }
	   $query_get_users="SELECT users_id, users_code, users_names, users_email, users_phones, 
                         CASE users_level
                         WHEN '1' THEN 'Administrator'
						 WHEN '2' THEN 'Public'
                         END users_level, 
						 CASE users_status
						 WHEN '0' THEN 'Active'
						 WHEN '1' THEN 'InActive' 
						 END users_status
                         FROM users 
						 WHERE users_level!='0' AND $find_data LIMIT $first_data,$row";
	 }   
  else
	 {
	   $query_get_users="SELECT users_id, users_code, users_names, users_email, users_phones, 
                         CASE users_level
                         WHEN '1' THEN 'Administrator'
						 WHEN '2' THEN 'Public'
                         END users_level, 
						 CASE users_status
						 WHEN '0' THEN 'Active'
						 WHEN '1' THEN 'InActive' 
						 END users_status
                         FROM users 
						 WHERE users_level!='0' $ordered_by LIMIT $first_data,$row";
  	   $query_page="SELECT count(users_id) as total_page FROM users WHERE users_level!='0'";
	 } 
// echo $query_get_users;	 			   
?>

<form id="f_users" name="f_users" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td scope="col"><div align="left">
        <h2>DAFTAR USER </h2>
      </div></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td scope="col"><input name='btn_create' type='button' id='btn_create' value='Tambah' onclick='input_user()'/>
        <input name='btn_update' type='button' id='btn_update' value='Update' onclick='update_user()'/>
        <input name='btn_inactive' type='button' id='btn_inactive' value='InActived/Activated' onclick='in_active_user()'/></td>
      <td align="right">Urut Berdasarkan   : 
        <label>
		  <select id="s_sorting_field" name="s_sorting_field" onchange="submit()"> 
		   <?php 
		         if (isset($_POST['s_sorting_field']))
				    { 
					  foreach ($array_field as $field) :
					          {
							    if ($_POST['s_sorting_field']==$field)
								   $selected_field="selected";
								else
								   $selected_field="";  
								?>
								     <option value="<?php echo $field;?>" <?php echo $selected_field; ?>><?php 
									          if ($field=="users_code")   
											      echo "Kode User"; 
											  else 
											  if ($field=="users_names")
											      echo "Nama User";
											  else 
											  if ($field=="users_email")
											      echo "Email";
											  else 
											  if ($field=="users_phones")
											      echo "Tlp";
											  else 
											  if ($field=="users_level")
											      echo "Level";
											  else 
											  if ($field=="users_status")
											      echo "Status";
											?></option>
								<?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
					     <option value="users_code" selected="selected">Kode User</option>
						 <option value="users_names">Nama User</option>
						 <option value="users_email">Email</option>
						 <option value="users_phones">Tlp</option>
						 <option value="users_level">Level</option>
						 <option value="users_status">Status</option>
					  <?php
					}		
		   ?>
		  </select>
        </label>
        <label>
		       <select name="s_sort" id="s_sort" onchange="submit()">
			            <?php
						      if (isset($_POST['s_sort']))
							     {
								   foreach ($array_sorting as $sorted) :
								           {
										     if ($_POST['s_sort']==$sorted)
											    $selected_sort="selected";
											 else
											    $selected_sort="";	 
								             ?> 
								                <option value="<?php echo $sorted; ?>" <?php echo $selected_sort; ?>><?php if ($sorted=="asc") echo "Asc"; else echo "Desc"; ?></option>
								             <?php	
										   }
								   endforeach;		    	   
								 }
							  else
							     {
								   ?>
								      <option value="asc">Asc</option>
							          <option value="desc">Desc</option>
								   <?php 
								 }	      
						?>
			   </select>
        </label></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="13%" scope="col" class="th_ltb">Kode User</th>
          <th width="21%" scope="col" class="th_ltb">Nama User</th>
          <th width="15%" scope="col" class="th_ltb">Email</th>
          <th width="15%" scope="col" class="th_ltbr">Tlp</th>
          <th width="10%" scope="col" class="th_ltbr">Level</th>
          <th width="22%" scope="col" class="th_ltbr">Status</th>
          </tr>
		  <?php
				$exec_query=mysqli_query($db_connection, $query_get_users);
				$total_item=mysqli_num_rows($exec_query);
				$no=0;
				while ($data_users=mysqli_fetch_array($exec_query))
				      {
					    $no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_users['users_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><a href="javascript:void(1)" onclick="call_set_authorization('<?php echo $data_users['users_id'];?>')"><?php echo $data_users['users_code'];?></a></td>
          <td class="td_lb"><?php echo $data_users['users_names'];?></td>
          <td class="td_lb"><?php echo $data_users['users_email'];?></td>
          <td class="td_lbr"><?php echo $data_users['users_phones'];?></td>
          <td class="td_lbr"><?php echo $data_users['users_level'];?></td>
          <td class="td_lbr"><?php echo $data_users['users_status'];?></td>
          </tr><?php } ?>
      </table></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $query_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td align="right">Halaman ke: 
		                                                                               <select name="s_page" id="s_page" onchange="submit()">
																								 <?php
																								      $pages=array();
																								      if ($maks_rows==0)
																								         {
																									       $maks_rows=1;
																									     }
                                                                                        		      $i=0;
                                                                                     			      while ($i<$maks_rows)
			                                                                                                {
		                                                                                    		          $i++;
																										      $pages[]=$i;
			                                                                                         	     }
																									  if (isset($_POST['s_page']))
																									     {
																										   foreach ($pages as $page)
																										           {
																												     if ($_POST['s_page']==$page)
																													    {
																														  $selected_page="selected";
																														}
																												     else
																													    {
																														  $selected_page="";
																														}	
																													 ?>
																													    <option value="<?php echo $page;?>" <?php echo $selected_page;?>><?php echo $page;?></option>
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }
																									  else
																									     {
																										   foreach ($pages as $page)
																										           {
																													 ?>
																													    <option value="<?php echo $page;?>"><?php echo $page;?></option>  
																													 <?php		
																												   }
																								//		   endforeach;		   
																										 }	 		 
																								 ?> 
	                                                                         </select>
		                                                                               dari
		                                                                               <?php  echo $maks_rows; ?></td>  
		</td>  
    </tr>
  </table>
</form>

<script language="javascript">
    var open_child=null;
   
    function select_unselect_all(x)
           {
		     var check_select=document.getElementsByName('check_all_data');
		     var select_unselect_all_data = document.getElementsByName('check_data[]');
			 
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
	  
	function input_user()
	         {
			   var w=400;
			   var h=300;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/users/cru_users.php?c=i", "f_cru_users", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }	
			 
	function disable_parent_window()  // akan dipanggil di tag <body>
	         {
			   if (open_child && !open_child.closed) 
			      {
                    open_child.focus();
				  }	
			 }		    
	 
	function update_user()
	         {
			   var w=400;
			   var h=300;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   var x=document.getElementsByName('check_data[]').length;
			   var y=document.getElementsByName('check_data[]');
			   var z=0;
			   var id='';
               for (i=0; i<x; i++)
			       {
				     if (y[i].checked==true)
				        {
					      z++;   
						  if (z==1)
						     {
						       var value_id=[y[i].value];	
						     }
						  else
						     {
					           value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						     }       
					    }   
				   } 	
				 
			   if (z==0)
			      {
				    alert('Tidak ada data yang pilih!');
				  }
			   else
			   if (z>1)
			      {
				    alert('Pilih hanya salah satu data!');  
				  }	  
		       else
			      { 
				    open_child=window.open('../data/users/cru_users.php?c=u&id='+value_id, 'f_cru_users', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  }
			 } 
			 
	function in_active_user()
           {
		     var x=document.getElementsByName('check_data[]').length;
			 var y=document.getElementsByName('check_data[]');
			 var z=0;
			 var id='';
             for (i=0; i<x; i++)
			     {
				   if (y[i].checked==true)
				      {
					    z++;   
						if (z==1)
						   {
						     var value_id=[y[i].value];	
						   }
						else
						   {
					         value_id.push(y[i].value);  // memasukan nilai array pada array yang sudah ada
						   }       
					  }   
				 } 	
				 
			 if (z==0)
			    {
				  alert('Tidak ada data yang dipilih!');
				}
		     else
			    {
				  var answer=confirm('Apakah yakin akan merubah status User?')
				  if (answer)
				     { 
					   document.f_users.action='../data/users/in_active_users.php';
					   document.f_users.submit();
					 }		  
				}			   	
		   }	
		   
  function call_set_authorization(id)
           {
		      var w=1350;
			  var h=600;
			  var l=(screen.width/2)-(w/2);
			  var t=(screen.height/2)-(h/2); 
			  value_id=id;
			  open_child=window.open('../data/users/authorization_users.php?id='+value_id, 'f_set_authorization', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0, scrollBars=Yes');
		   }    
</script>

</body>
</html>
