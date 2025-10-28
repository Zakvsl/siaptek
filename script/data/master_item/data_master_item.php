<?php
 include "../library/check_session.php";
 $branch_id=$_SESSION['ses_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../library/style.css";
  include "../library/db_connection.php";
  
  $array_field=array("masti_code", "masti_name", "masti_capacity", "cati_name");
  $array_operator=array("=","!=","like");
  $array_sorting=array("asc", "desc");
  $continue='0';
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
	   $field_to_sort='masti_code';
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
	
  $field="";
  $field_to_convert="";	
  $operator="";		
  $text_to_find_1="";	
				
  if (isset($_POST['btn_find']))
	 {
	   $field=$_POST['s_field'];
	   $field_to_convert=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   if ($text_to_find_1=='')	  
	      {
		    $continue='1';
		    ?>
			   <script language="javascript">
				 alert('Kata yang akan dicari harus diisi!');
				 window.location.href='javascript:history.back(1)';
			   </script>
		     <?php 
		  }
	   else
	   	  {		
		    $continue='0';			 
			$find_data=$field.$find_text." ".$ordered_by;	
			$q_show_master_item="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                                 FROM master_item
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
							     WHERE $find_data LIMIT $first_data,$row"; 
			$q_page="SELECT count(masti_id) as total_page 
	                 FROM master_item
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
					 WHERE $find_data";  	 
		  }
	 }
  else
  if (isset($_POST['s_sorting_field']) || isset($_POST['s_sort']))
     {
	   $field=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   $sorting="ORDER BY ".$_POST['s_sorting_field']." ".$_POST['s_sort'];
			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   $continue='0';
	   if ($text_to_find_1=='')
	      {
		    $q_show_master_item="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                                 FROM master_item
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT count(masti_id) as total_page 
	                 FROM master_item
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id"; 
		  }
	   else 
	      {	  
	        $find_data=$field.$find_text;	
	        $q_show_master_item="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                       (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                                 FROM master_item
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
							     WHERE $find_data $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT count(masti_id) as total_page 
	                 FROM master_item
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
				     WHERE $find_data";  
		  }			 
	 }
  else	 
  	 {
	   $q_show_master_item="SELECT masti_id, masti_code, masti_name, masti_capacity, uom_id_1, uom_id_2,
                                  (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_name_1, 
                                  (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_name_2,cati_name 
                            FROM master_item
                            INNER JOIN category_item ON category_item.cati_id=master_item.cati_id $ordered_by LIMIT $first_data,$row"; 
	   $q_page="SELECT count(masti_id) as total_page 
	            FROM master_item
                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id"; 
	 }	

// echo $q_show_master_item."<br>";	
// echo $q_page; 
	
?>
<style>
            #action {
                background-color : black;
                width : 120px;
                height : 28px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul {
                padding:0;
                margin:0;
                list-style-type:none;
            }
             
            #action ul li {
                float:left;
                position : relative;
            }
             
            #action ul li a {
                display:block;
                padding:5px 10px;
                color:#fff;
                text-decoration:none;
                font-family: calibri;
				font-size:14px;
            }
             
            #action ul li a:hover {
                background-color:#72b626;
            }
             
            /* Menu Dropdown */
            
            #action ul li ul {
                display: none;
            }
             
            #action ul li:hover ul {
                display:block;
                position: absolute;
            }
            
            #action ul li:hover ul li a {
                display:block;
                background-color : black;
                color : #fff;
                width : 100px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul li:hover ul li a:hover {
                background-color : #72b626;
            }
            
            #action ul li:hover > a {
			    background: #72b626;
	  	    }
        </style>
		
<form id="f_master_item" name="f_master_item" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2" class="table-list-home">
    <tr>
      <td width="69%" scope="col"><h2>DAFTAR DESKRIPSI ISI ASET </h2></td>
      <td colspan="2" align="right">Kantor Cabang    : 
        <select id="s_branch" name="s_branch" style="width:300px">
		  <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td scope="col"><div align="left">
	    <?php 
		   if (isset($_POST['s_field']))
		      {
			    $field=$_POST['s_field'];
			    echo "<select name='s_field' id='s_field'>";
				foreach ($array_field as $fields)
				        {
						  if ($field==$fields)
						      $selected_field="selected='selected'";
						  else
						      $selected_field="";
						  ?>	  
						     <option value='<?php echo $fields;?>' <?php echo $selected_field;?>>
						  <?php	 
						         if ($fields=='masti_code')
								     echo "Kode Isi Aset";
								 else
								 if ($fields=='masti_name')
								     echo "Deskripsi Isi Aset";
								 else
								 if ($fields=='masti_capacity')
								     echo "Isi";
							     else
								 if ($fields=='cati_name')
								     echo "Kategori";    
						   ?>			 
						     </option>
						  <?php	 
						}     
				echo "</select>";
			  }
		   else
		   	  {
		?>	   
        <select name="s_field" id="s_field">
		  <option value="masti_code">Kode Isi Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="masti_capacity">Isi</option>
		  <option value="cati_name">Kategori</option>
        </select>
		<?php } 
		if (isset($_POST['s_operator']))
		   { 
		     $operator=$_POST['s_operator'];
			 echo "<select name='s_operator' id='s_operator'>";
			 foreach($array_operator as $operators)
			        {
					  if ($operator==$operators)
					      $selected_operator="selected='selected'";
					  else
					      $selected_operator="";
					  ?>
					     <option value='<?php echo $operators;?>' <?php echo $selected_operator;?>><?php echo $operators?></option>
					  <?php	  	  
					}
			 echo "</select>";
		   }
		else
		   {   	 
		?>
        <select name="s_operator">
		  <option value="=">=</option>
		  <option value="!=">!=</option>
		  <option value="like">Any</option>
        </select>
		<?php }?>
        <input type="text" name="txt_find_1" id="txt_find_1"
		 value="<?php 
		         if (isset($_POST['txt_find_1']))
				     echo $_POST['txt_find_1']; 
				 else 
				     echo "";	    
		       ?>">
        <input type="submit" name="btn_find" id="btn_find" value="Cari"/>
      </div></td>
      <td width="21%" align="right" nowrap="nowrap">Urut Berdasarkan    :
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
          <option value="<?php echo $field;?>" <?php echo $selected_field; ?>>
          <?php 
									          if ($field=='masti_code')
								                  echo "Kode Isi Aset";
								              else
								              if ($field=='masti_name')
								                  echo "Deskripsi Isi Aset";
								              else
								              if ($field=='masti_capacity')
								                  echo "Isi";
							                  else
								              if ($field=='cati_name')
								                  echo "Kategori";
											?>
          </option>
          <?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
          <option value="masti_code">Kode Isi Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="masti_capacity">Isi</option>
		  <option value="cati_name">Kategori</option>
          <?php
					}		
		   ?>
        </select>
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
          <option value="<?php echo $sorted; ?>" <?php echo $selected_sort; ?>>
            <?php if ($sorted=="asc") echo "Asc"; else echo "Desc"; ?>
          </option>
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
        </select></td>
<td width="9%" align="left"><?php 
                              if ($masti_autho=='W' || $masti_autho=='D')
							     { 
                                   echo "<div id='action' align='left'>";
                                   echo "<ul id='nav'>";
                                   echo "<li><a href='javascript:void(1)'>Aksi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";
                                   echo "<ul>";
								   if ($masti_autho=='W')
								      {
                                       echo "<li><a href='javascript:void(1)' title='Add New Data' onclick='input_data()'>Tambah Data</a></li>";
									   echo "<li><a href='javascript:void(1)' title='Ekspor Data' onclick='call_export()'>Ekspor Data</a></li>";
									  }
								   else
								   if ($masti_autho=='D')
								      {
								        echo "<li><a href='javascript:void(1)' title='Add New Data' onclick='input_data()'>Tambah Data</a></li>";
		                                echo "<li><a href='javascript:void(1)' title='Update Data' onclick='update_data()'>Ubah Data</a></li>";
										echo "<li><a href='javascript:void(1)' title='Delete Data' onclick='delete_data()'>Hapus Data</a></li>";
										echo "<li><a href='javascript:void(1)' title='Ekspor Data' onclick='call_export()'>Ekspor Data</a></li>";
									  }
                                   echo "</ul>";
                                   echo "</li>";
                                   echo "</ul>";
                                   echo "</div>";
								 }
							  else
							     {
								   echo "<div id='action' align='left'>";
                                   echo "<ul id='nav'>";   
								   echo "<li><a href='javascript:void(1)' title='Ekspor Data' onclick='call_export()'>Ekspor Data</a></li>";
								   echo "</ul>";
                                   echo "</div>";  
								 }  
						    ?>
	</td>
    </tr>

    <tr>
      <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="3%" scope="col" class="th_ltb">No</th>
          <th width="17%" scope="col" class="th_ltb">Kode Isi Aset </th>
          <th width="39%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="5%" scope="col" class="th_ltb">Isi</th>
          <th width="9%" scope="col" class="th_ltb">Satuan</th>
          <th width="25%" scope="col" class="th_ltb">Kategori</th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
			 	  $exec_query=mysqli_query($db_connection, $q_show_master_item);
				  $total_master_item=mysqli_num_rows($exec_query);
				  $no=0;
				  while ($data_master_item=mysqli_fetch_array($exec_query))
				        {
					      $no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_master_item['masti_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data_master_item['masti_code'];?></td>
          <td class="td_lb"><?php echo $data_master_item['masti_name'];?></td>
          <td class="td_lb"><?php echo $data_master_item['masti_capacity'];?></td>
          <td class="td_lb"><?php echo $data_master_item['uom_name_1'];?></td>
          <td class="td_lb"><?php echo $data_master_item['cati_name'];?></td>
          </tr><?php }} ?>
      </table></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $q_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td colspan="2" align="right">Halaman ke  : 
		                                                                               <select name="s_page" id="s_page" onchange="document.f_master_item.submit()">
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
	
		   
	function input_data()
	         {
			   var w=420;
			   var h=200;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/master_item/cru_master_item.php?c=i", "f_cru_master_item", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
			 }	
			 
	function disable_parent_window()  // akan dipanggil di tag <body>
	         {
			   if (open_child && !open_child.closed) 
			      {
                    open_child.focus();
				  }	
			 }		    
	 
	function update_data()
	         {
			   var w=420;
			   var h=200;
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
				    alert('Tidak ada data yang dipilih!');
				  }
			   else
			   if (z>1)
			      {
				    alert('Silahkan pilih salah satu data!');  
				  }	  
		       else
			      { 
				    open_child=window.open('../data/master_item/cru_master_item.php?c=u&id='+value_id, 'f_cru_master_item', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  }
			 }  
			 
  function delete_data()
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
				  var answer=confirm('Apakah yakin akan menghapus data terpilih?');
				  if (answer)
				     {
					   document.f_master_item.action="../data/master_item/delete_master_item.php";
					   document.f_master_item.submit();
					 }  
				}
		   }   
			 
	function call_export()
	         {
			   var field='<?php echo $field_to_convert;?>';
	           var operator='<?php echo $operator;?>';
	           var text_to_find='<?php echo $text_to_find_1;?>';
			   open_child=window.open("../data/master_item/export_to_excel.php?f="+field+"&o="+operator+"&t="+text_to_find);
			 }  
</script>

</body>
</html>
