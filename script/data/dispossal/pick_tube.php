<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  
  $array_field=array("itemd_code", "masti_name", "itemd_serial_no", "itemd_capacity", "cati_name", "whsl_name");
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
	   $field_to_sort='itemd_code';
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
				
				
  if (isset($_POST['btn_find']))
	 {
	   $field=$_POST['s_field'];
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
				 alert('Find text must be filled!');
				 window.location.href='javascript:history.back(1)';
			   </script>
		     <?php 
		  }
	   else
	   	  {		
		    $continue='0';			 
			$find_data=$field.$find_text." ".$ordered_by;	
			$q_show_pick_tube="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, whsl_name 
                               FROM item_detail
							   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                               INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                               WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND itemd_status='0' AND
							         $find_data LIMIT $first_data,$row";
			$q_page="SELECT COUNT(*) AS total_page
                     FROM item_detail
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
					 INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                     WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND itemd_status='0' AND $find_data";
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
		    $q_show_pick_tube="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, whsl_name 
                               FROM item_detail
							   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                               INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                               WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND itemd_status='0' 
							         $sorting LIMIT $first_data,$row"; 
			$q_page="SELECT COUNT(*) AS total_page
                     FROM item_detail
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
					 INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                     WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND itemd_status='0'";
		  }
	   else 
	      {	  
	        $find_data=$field.$find_text;	
	        $q_show_pick_tube="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, whsl_name 
                               FROM item_detail
							   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                               INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                               WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND $find_data 
							         AND itemd_status='0' $sorting 
						       LIMIT $first_data,$row"; 
			$q_page="SELECT COUNT(*) AS total_page
                     FROM item_detail
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
					 INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                     WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND $find_data AND itemd_status='0'";
		  }			 
	 }
  else	 
  	 {
	   $q_show_pick_tube="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, whsl_name 
                               FROM item_detail
							   INNER JOIN uom ON uom.uom_id=item_detail.uom_id 
                               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                               INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
							   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                               WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id'  AND itemd_status='0'
							         $ordered_by LIMIT $first_data,$row"; 
	   $q_page="SELECT COUNT(*) AS total_page
                FROM item_detail
			    INNER JOIN uom ON uom.uom_id=item_detail.uom_id	
                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
				INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND itemd_status='0'"; 
	 }	 
  //echo $q_show_pick_tube;   
  //echo $q_page;
?>

<form id="f_pick_tube" name="f_pick_tube" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td scope="col"><h2>DAFTAR ASET </h2></td>
      <td align="right">&nbsp;</td>
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
						         if ($fields=='itemd_code')
								     echo "Kode Aset";
								 else
								 if ($fields=='masti_name')
								     echo "Deskripsi Isi Aset";
								 else
								 if ($fields=='itemd_serial_no')
								     echo "Serial No";
								 else
								 if ($fields=='itemd_capacity')
								     echo "Kapasitas";
								 else
								 if ($fields=='cati_name')
								     echo "Kategori";
								 else
								 if ($fields=='whsl_name')
								     echo "Lokasi Gudang";
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
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="cati_name">Kategori</option>
		  <option value="whsl_name">Lokasi Gudang</option>
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
      <td align="right">Urut berdasarkan    : 
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
								     <option value="<?php echo $field;?>" <?php echo $selected_field; ?>>
									       <?php 
									           if ($field=='itemd_code')
								                   echo "Kode Aset";
								               else
								               if ($field=='masti_name')
								                   echo "Deskripsi Isi Aset";
								               else
								               if ($field=='itemd_serial_no')
								                   echo "Serial No";
								               else
								               if ($field=='itemd_capacity')
								                   echo "Kapasitas";
								               else
								               if ($field=='cati_name')
								                   echo "Kategori";
								               else
								               if ($field=='whsl_name')
								                   echo "Lokasi Gudang";
											?></option>
								<?php    
							  }
					  endforeach;  
					} 
			     else
				    {
					  ?>
					     <option value="itemd_code">Kode Aset</option>
		                 <option value="masti_name">Deskripsi Isi Aset</option>
		                 <option value="itemd_serial_no">Serial No</option>
		                 <option value="itemd_capacity">Kapasitas</option>
		                 <option value="cati_name">Kategori</option>
		                 <option value="whsl_name">Lokasi Gudang</option>
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
        </label> 
		<input type="submit" id="btn_select" name="btn_select" value="Pilih" onclick="call_select_data()"/>
		<input type="button" id="btn_close" name="btn_close" value="Tutup" onclick="window.close()" /> 
		</td>
    </tr>

    <tr>
      <td colspan="2">
	  <div STYLE=" height: 100%; width: 100%; font-size: 12px; overflow: auto;"> 
	  <table width="125%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="3%" scope="col" class="th_ltb">No</th>
          <th width="12%" scope="col" class="th_ltb">Kode Aset</th>
          <th width="32%" scope="col" class="th_ltb">Deskripsi Isi Aset</th>
          <th width="8%" scope="col" class="th_ltb">Serial No</th>
          <th width="14%" scope="col" class="th_ltb">Kategori</th>
          <th width="7%" scope="col" class="th_ltb">Kapasitas</th>
          <th width="6%" scope="col" class="th_ltb">Qty</th>
          <th width="16%" scope="col" class="th_ltb">Lokasi Gudang</th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
			 	  $exec_query=mysqli_query($db_connection,$q_show_pick_tube);
				  $total_pick_tube=mysqli_num_rows($exec_query);
				  $no=0;
				  while ($data_pick_tube=mysqli_fetch_array($exec_query))
				        {
					      $no++;
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data_pick_tube['itemd_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data_pick_tube['itemd_code'];?></td>
          <td class="td_lb"><?php echo $data_pick_tube['masti_name'];?></td>
          <td class="td_lb"><?php echo $data_pick_tube['itemd_serial_no'];?></td>
          <td class="td_lb"><?php echo $data_pick_tube['cati_name'];?></td>
          <td class="td_lb"><?php echo $data_pick_tube['itemd_capacity']." ".$data_pick_tube['uom_name'];?></td>
          <td class="td_lb"><?php echo $data_pick_tube['itemd_qty']." Cylinder";?></td>
          <td class="td_lb"><?php echo $data_pick_tube['whsl_name'];?></td>
          </tr><?php }} ?>
      </table>
	  </div></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection,$q_page) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td align="right">Halaman ke  : 
		                                                                               <select name="s_page" id="s_page" onchange="document.f_pick_tube.submit()">
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
<?php
 if (isset($_POST['btn_select']))
    {
	  $itemd_id='';
	  if (!isset($_POST['check_data']))
	      $itemd_id='';
	  else
	     {
	       foreach ($_POST['check_data'] as $data) :
	               {
			         if ($itemd_id=="")
			             $itemd_id="'".$data."'";
			         else
				         $itemd_id=$itemd_id.",'".$data."'";
			       } 
	       endforeach;	  
		 }
		 
	  if ($itemd_id!='')
	     { 
	       $q_check_item="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, cati_name, itemd_qty, whsl_name 
                          FROM item_detail
						   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                          INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                          INNER JOIN category_item ON master_item.cati_id=category_item.cati_id
				          INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id
                          WHERE itemd_is_broken='0' AND itemd_is_dispossed='0' AND itemd_position='Internal' AND item_detail.branch_id='$branch_id' AND 
					            item_detail.itemd_id IN ($itemd_id)";	 		    
	        $q_exec_check_item=mysqli_query($db_connection,$q_check_item);
	        $total=mysqli_num_rows($q_exec_check_item);
	        if ($total==0)
	           {
		          ?>
			         <script language="javascript">
				       alert('Item tidak ditemukan!');
				     </script>
			      <?php
		       }
	        else
	           {
		         while ($field_item=mysqli_fetch_array($q_exec_check_item))
			           {
				         $itemd_code=$field_item['itemd_code'];
					     $itemd_id=$field_item['itemd_id'];
					     $masti_name=$field_item['masti_name'];
					     $itemd_serial_no=$field_item['itemd_serial_no'];
					     $cati_name=$field_item['cati_name'];
					     $masti_capacity=$field_item['itemd_capacity']." ".$field_item['uom_name'];
					     $itemd_qty=$field_item['itemd_qty']." Cylinder";
					     $whsl_name=$field_item['whsl_name'];
					     ?>
					        <script language="javascript">
					          var itemd_code='<?php echo $itemd_code;?>';
						      var itemd_id='<?php echo $itemd_id;?>';
						      var masti_name='<?php echo $masti_name;?>';
						      var itemd_serial_no='<?php echo $itemd_serial_no;?>';
						      var cati_name='<?php echo $cati_name;?>';
						      var masti_capacity='<?php echo $masti_capacity;?>';
						      var itemd_qty='<?php echo $itemd_qty;?>';
						      var whsl_name='<?php echo $whsl_name;?>';
						      var x=opener.document.getElementById("txt_rows").value;
			                  var y=opener.document.getElementsByName('cb_data[]');
						      if (x==0)
						         {
						           add_row(itemd_code, itemd_id, masti_name, itemd_serial_no, cati_name, masti_capacity, itemd_qty, whsl_name); 
							     } 
						      else
						         {
							       z=0;
						           for (i=0; i<x; i++)
						               { 
							             if (itemd_id!=y[i].value)
									         z++;  
								       }		
							       if (z==x)
							           add_row(itemd_code, itemd_id, masti_name, itemd_serial_no, cati_name, masti_capacity, itemd_qty, whsl_name); 		  
							      }  

						                  
						      function add_row(itemd_code, itemd_id, masti_name, itemd_serial_no, cati_name, masti_capacity, itemd_qty, whsl_name)	
                                       {
			                             objTbl = opener.document.getElementById("tbl_detail");
 			                             var row = parseInt(opener.document.getElementById("txt_rows").value) + 1;
			                             newTR = objTbl.insertRow(objTbl.rows.length);
 			
			                             newTD = newTR.insertCell(newTR.cells.length);  // Checkbox
			                             newTD.align="center";
			                             newTD.innerHTML = '<input type="checkbox" id="cb_data[]" name="cb_data[]" value="'+itemd_id+'" /><input type="hidden" id="txt_brokd_id_'+itemd_id+'" name="txt_brokd_id_'+itemd_id+'" value="0"><input type="hidden" id="txt_brokd_id_"'+itemd_id+'" name="txt_brokd_id_"'+itemd_id+'" value="0">';
			                             newTD = newTR.insertCell(newTR.cells.length);  // item id
			                             newTD.align="left";
									     newTD.innerHTML =itemd_code;
			                        
			                             x="'"+itemd_id+"'";
			                             newTD = newTR.insertCell(newTR.cells.length);  // qty
			                             newTD.align="left";
									     newTD.innerHTML =masti_name;
			  
			                             newTD = newTR.insertCell(newTR.cells.length);  // unit
			                             newTD.align="left";
			                             newTD.innerHTML =itemd_serial_no;
									
									     newTD = newTR.insertCell(newTR.cells.length);  // unit
			                             newTD.align="left";
			                             newTD.innerHTML =masti_capacity;
									
									     newTD = newTR.insertCell(newTR.cells.length);  // unit
			                             newTD.align="left";
			                             newTD.innerHTML =cati_name;
			  
			                             newTD = newTR.insertCell(newTR.cells.length);  // price			  
			                             newTD.align="left";
			                             newTD.innerHTML = itemd_qty;
									
									     newTD = newTR.insertCell(newTR.cells.length);  // price			  
			                             newTD.align="left";
			                             newTD.innerHTML ='<input type="checkbox" id="cb_dispd_is_canceled_'+itemd_id+'" name="cb_dispd_is_canceled_'+itemd_id+'" value="1" disabled="disabled"/> Tidak';
									
									     newTD = newTR.insertCell(newTR.cells.length);  // price			  
			                             newTD.align="left";
			                             newTD.innerHTML = '<input type="text" id="txt_dispd_notes_'+itemd_id+'" name="txt_dispd_notes_'+itemd_id+'" value="" size="35"/>';   
								
			                             opener.document.getElementById("txt_rows").value = parseInt(row); 
		                               }  
					        </script>
					     <?php 
				       }
			     ?>
			        <script language="javascript">
			          window.close();
			        </script>
			     <?php 	 
			   } 
		  }   
	}
?> 
<script language="javascript">
    var open_child=null;
   
    function select_unselect_all()
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
			   var h=425;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   open_child=window.open("../data/pick_tube/cru_pick_tube.php?c=i", "f_cru_pick_tube", "location=no,resizable=no, height="+h+", width="+w+", left="+l+", top="+t+", toolbar=0");
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
			   var h=425;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   var x=document.getElementsByName('check_data').length;
			   var y=document.getElementsByName('check_data');
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
				    alert('No record selected!');
				  }
			   else
			   if (z>1)
			      {
				    alert('Choose only one record!');  
				  }	  
		       else
			      { 
				    open_child=window.open('../data/pick_tube/cru_pick_tube.php?c=u&id='+value_id, 'f_cru_pick_tube', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  }
			 }    
</script>

</body>
</html>
