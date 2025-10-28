<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Stock Aset</title></head>
<body>
 <link type="text/css" rel="stylesheet" href="../../library/development-bundle/themes/ui-lightness/ui.all.css" />   
    <script src="../../library/development-bundle/jquery-1.8.0.min.js"></script>
    <script src="../../library/development-bundle/ui/ui.core.js"></script>
    <script src="../../library/development-bundle/ui/ui.datepicker.js"></script>
    <script src="../../library/development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready (function()
		                  {
                            $("#txt_date_1").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );		
							$("#txt_date_2").datepicker(
							 {
                               dateFormat : "dd-mm-yy",
                               changeMonth : true,
                               changeYear : true
                             }
							                          );				  
                          }
						  );
    </script>
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  $whsl_id=mysqli_real_escape_string($db_connection, $_GET['id']);
  $q_check_whs="SELECT * FROM warehouse_location WHERE whsl_id='$whsl_id'";
  $exec_check_whs=mysqli_query($db_connection, $q_check_whs);
  $field_data_whs=mysqli_fetch_array($exec_check_whs);
  $whsl_name=$field_data_whs['whsl_name'];
//  echo $q_check_whs;
  if ($field_data_whs['whsl_level']=='5')
      {
	   $x="'$whsl_id'";
       $find_whs="AND warehouse_location.whsl_id IN ($x)";
	  } 
  else
  if (($field_data_whs['whsl_level']=='2') || ($field_data_whs['whsl_level']=='3') || ($field_data_whs['whsl_level']=='4'))
      {
	    $q_get_whs="SELECT * FROM warehouse_location WHERE whsl_parent_path LIKE '%,$whsl_id' OR whsl_parent_path LIKE '%,$whsl_id,%'";
		$exec_get_whs=mysqli_query($db_connection, $q_get_whs);
		if (mysqli_num_rows($exec_get_whs)>0)
		   {
		     $x="'".$whsl_id."'";
		     while ($field_data_whs=mysqli_fetch_array($exec_get_whs))
		           {
			        if ($x=='')
                        $x="'".$field_data_whs['whsl_id']."'";
			        else
				        $x=$x.", '".$field_data_whs['whsl_id']."'";
				   }		
			 $find_whs="AND warehouse_location.whsl_id IN ($x)";
		   }	
		else   
           $find_whs="AND warehouse_location.whsl_id IN ($whsl_id)";
	  }	
  else
  if  ($field_data_whs['whsl_level']=='1')
      {
	    $q_get_whs="SELECT * FROM warehouse_location WHERE whsl_parent_path LIKE '$whsl_id,%'";
		$exec_get_whs=mysqli_query($db_connection, $q_get_whs);
		if (mysqli_num_rows($exec_get_whs)>0)
		   {
		     $x="'".$whsl_id."'";
		     while ($field_data_whs=mysqli_fetch_array($exec_get_whs))
		           {
			         if ($x=='')
                         $x="'".$field_data_whs['whsl_id']."'";
			         else
				         $x=$x.", '".$field_data_whs['whsl_id']."'";
			       }	
             $find_whs="AND warehouse_location.whsl_id IN ($x)";
		   }
		else 
		   {
		     $find_whs="AND warehouse_location.whsl_id IN ($whsl_id)";    
		   }
	  }	
  
  $current_date=date('d-m-Y');
  $array_field=array("itemd_code", "masti_name", "itemd_serial_no", "itemd_capacity", "itemd_qty", "itemd_acquired_date", "cati_name", "itemd_is_broken","whsl_name");
  $array_operator=array("=",">","<",">=","<=","!=","between","like");
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
  			 
  $ordered_by="ORDER BY itemd_code ASC";							
  $field="";
  $field_to_convert="";
  $operator="";
  $text_to_find_1="";		
  $text_to_find_2="";		
				
  if (isset($_POST['btn_find']))
	 {
	   $field=$_POST['s_field'];
	   $field_to_convert=$_POST['s_field'];
	   $operator=$_POST['s_operator'];
	   if ($field=='itemd_acquired_date')
	      {
		    if ($operator=='between')
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1']))); 
				 $text_to_find_2=get_date_2(htmlspecialchars(trim($_POST['txt_date_2']))); 
			   }
			else
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1'])));
			   }
		  }
	   else
	      {  
	        $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
		  } 	
	   
	   			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	   if ($operator=='between')
	      { 
	       $find_text=" between '".$text_to_find_1."' AND '".$text_to_find_2."'";
		  } 
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   if ($field=='itemd_acquired_date' && $operator=='between' && ($text_to_find_2<$text_to_find_1))
	      {
			 $continue='1';
			 ?>
			    <script language="javascript">
				  alert('Batas tanggal pertama harus lebih kecil daripada batas tanggal kedua!');
				  window.location.href='javascript:history.back(1)';
			    </script>
		     <?php  
			 exit;
		  }
	   else
	   if ($text_to_find_1=='')	  
	      {
		    $continue='1';
		    ?>
			   <script language="javascript">
				 alert('Kata yang akan dicari harus diisi!');
				 window.location.href='javascript:history.back(1)';
			   </script>
		    <?php 
			exit;
		  }
	   else
	   	  {		
		    $continue='0';			 
			$find_data=$field.$find_text;	
			$q_show_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                           CASE itemd_is_broken 
                                                WHEN '0' THEN 'Tidak' 
                                                WHEN '1' THEN 'Ya' 
                                           END itemd_is_broken, whsl_name
                                    FROM item_detail 
									INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                    INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                    INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                    INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                                    INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                                    WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND 
									      warehouse_location.branch_id='$branch_id' AND
									      item_detail.branch_id='$branch_id' $find_whs AND $find_data $ordered_by
									LIMIT $first_data,$row";
			$q_page="SELECT count(*) as total_page 
	                 FROM item_detail 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                     INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                     INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                     WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND 
					       warehouse_location.branch_id='$branch_id' AND
					       item_detail.branch_id='$branch_id' $find_whs AND $find_data";  	 
		  }
	 }
  else
  if (isset($_POST['s_sorting_field']) || isset($_POST['s_sort']))
     {
	   if ($field=='itemd_acquired_date')
	      {
		    if ($operator=='between')
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1']))); 
				 $text_to_find_2=get_date_2(htmlspecialchars(trim($_POST['txt_date_2']))); 
			   }
			else
			   {
			     $text_to_find_1=get_date_2(htmlspecialchars(trim($_POST['txt_date_1'])));
			   }
		  }
	   else
	      {  
	        $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
		  } 
	//   $text_to_find_1=htmlspecialchars(trim($_POST['txt_find_1']));
	   $sorting="ORDER BY ".$_POST['s_sorting_field']." ".$_POST['s_sort'];
			 
	   if ($operator=='like')
	       $find_text=" like '%".$text_to_find_1."%'";
	   else
	   if ($operator=='between')
	       $find_text=" between '".$text_to_find_1."' AND '".$text_to_find_2."'";
	   else
	      $find_text=" ".$operator."'".$text_to_find_1."'";	
	   
	   $continue='0';
	   if ($text_to_find_1=='')
	      {
		    $q_show_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                           CASE itemd_is_broken 
                                                WHEN '0' THEN 'Tidak' 
                                                WHEN '1' THEN 'Ya' 
                                           END itemd_is_broken, whsl_name
                                    FROM item_detail 
									INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                    INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                    INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                    INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                                    INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                                    WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
									      item_detail.branch_id='$branch_id' $find_whs
                                    ORDER BY itemd_code ASC, $sorting
									LIMIT $first_data,$row";
			$q_page="SELECT count(*) as total_page 
	                 FROM item_detail 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                     INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                     INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                     WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
					       item_detail.branch_id='$branch_id' $find_whs"; 
		  }
	   else 
	      {	  
	        $find_data=$field.$find_text;	
	        $q_show_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                           CASE itemd_is_broken 
                                                WHEN '0' THEN 'Tidak' 
                                                WHEN '1' THEN 'Ya' 
                                           END itemd_is_broken, whsl_name
                                    FROM item_detail 
									INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                    INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                    INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                    INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                                    INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                                    WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
									      item_detail.branch_id='$branch_id' $find_whs AND $find_data 
								    $ordered_by 
									LIMIT $first_data,$row";
			$q_page="SELECT count(*) as total_page 
	                 FROM item_detail 
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                     INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                     INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                     WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
					       item_detail.branch_id='$branch_id' $find_whs AND $find_data";  
		  }			 
	 }
  else	 
  	 {
	   $q_show_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                           CASE itemd_is_broken 
                                                WHEN '0' THEN 'Tidak' 
                                                WHEN '1' THEN 'Ya' 
                                           END itemd_is_broken, whsl_name
                                    FROM item_detail 
									INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                    INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                    INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                    INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                                    INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                                    WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
									      item_detail.branch_id='$branch_id' $find_whs $ordered_by 
									LIMIT $first_data,$row";
	   $q_page="SELECT count(*) as total_page 
	            FROM item_detail 
				INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                INNER JOIN customer ON customer.cust_id=item_detail.vend_id
                WHERE itemd_position='Internal' AND itemd_is_dispossed='0' AND itemd_status='0' AND warehouse_location.branch_id='$branch_id' AND
					  item_detail.branch_id='$branch_id' $find_whs";
	 }	
// echo $q_show_stock_material."<br>";
// echo $q_page;
?>

<form id="f_stock_material" name="f_stock_material" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td scope="col"><h2>DAFTAR STOK ASET DI WAREHOUSE (<?php echo $whsl_name;?>)</h2></td>
      <td align="right">&nbsp;</td>
    </tr>
    <tr>
      <td scope="col"><div align="left">
	    <?php 
		   if (isset($_POST['s_field']))
		      {
			    $field=$_POST['s_field'];
			    echo "<select name='s_field' id='s_field'  onchange='show_hide()'>";
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
								 if ($fields=='itemd_qty')
								     echo "Qty";
								 else
								 if ($fields=='itemd_acquired_date')
								     echo "Tgl Perolehan";
								 else
								 if ($fields=='cati_name')
								     echo "Kategori";
								 else
								 if ($fields=='itemd_is_broken')
								     echo "Rusak";
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
        <select name="s_field" id="s_field" onchange="show_hide()">
		  <option value="itemd_code">Kode Aset</option>
		  <option value="masti_name">Deskripsi Isi Aset</option>
		  <option value="itemd_serial_no">Serial No</option>
		  <option value="itemd_capacity">Kapasitas</option>
		  <option value="itemd_qty">Qty</option>
		  <option value="itemd_acquired_date">Tgl Perolehan</option>
		  <option value="cati_name">Kategori</option>
		  <option value="itemd_is_broken">Rusak</option>
		  <option value="whsl_name">Lokasi Gudang</option>
        </select>
		<?php } 
		if (isset($_POST['s_operator']))
		   { 
		     $operator=$_POST['s_operator'];
			 echo "<select name='s_operator' id='s_operator' onchange='show_hide()'>";
			 foreach($array_operator as $operators)
			        {
					  $selected_operator="";
					  $id_operator='';
					  if ($operator==$operators)
					     {
						   $selected_operator="selected='selected'";
						   if ($field=='reth_date')
						      {
							    if ($operators=='=') 
								    $id_operator="id='op_e' style='display:list-item'";
							    else
								if ($operators=='>') 
								    $id_operator="id='op_gt' style='display:list-item'";
							    else
								if ($operators=='<') 
								    $id_operator="id='op_lt' style='display:list-item'";
							    else
								if ($operators=='>=') 
								    $id_operator="id='op_gte' style='display:list-item'";
							    else
								if ($operators=='<=') 
								    $id_operator="id='op_lte' style='display:list-item'";
							    else
								if ($operators=='!=') 
								    $id_operator="id='op_neq' style='display:list-item'";
							    else
								if ($operators=='between') 
								    $id_operator="id='op_between' style='display:list-item'";
							    else
								if ($operators=='like') 
								    $id_operator="id='op_like' style='display:list-item'";
							  }
						   else
						      { 
							    if ($operators=='=') 
								    $id_operator="id='op_e' style='display:list-item'";
							    else
								if ($operators=='>') 
								    $id_operator="id='op_gt' style='display:none'";
							    else
								if ($operators=='<') 
								    $id_operator="id='op_lt' style='display:none'";
							    else
								if ($operators=='>=') 
								    $id_operator="id='op_gte' style='display:none'";
							    else
								if ($operators=='<=') 
								    $id_operator="id='op_lte' style='display:none'";
							    else
								if ($operators=='!=') 
								    $id_operator="id='op_neq' style='display:list-item'";
							    else
								if ($operators=='between') 
								    $id_operator="id='op_between' style='display:none'";
							    else
								if ($operators=='like') 
								    $id_operator="id='op_like' style='display:list-item'";
							  } 
						   
						 }  
					  else
					     {
						   if ($operators=='=') 
						      {
							    $selected_operator="selected='selected'";
							    $id_operator="id='op_e' style='display:list-item'";
							  }	
						   else
						   if ($operators=='>') 
							   $id_operator="id='op_gt' style='display:none'";
						   else
						   if ($operators=='<') 
						       $id_operator="id='op_lt' style='display:none'";
						   else
					       if ($operators=='>=') 
						       $id_operator="id='op_gte' style='display:none'";
						   else
					       if ($operators=='<=') 
						       $id_operator="id='op_lte' style='display:none'";
					       else
					       if ($operators=='!=') 
						       $id_operator="id='op_neq' style='display:list-item'";
					       else
					       if ($operators=='between') 
						       $id_operator="id='op_between' style='display:none'";
					       else
					       if ($operators=='like') 
						       $id_operator="id='op_like' style='display:list-item'";
						 }
					  ?>
					     <option value='<?php echo $operators;?>' <?php echo $selected_operator; echo $id_operator;?>><?php echo $operators;?></option>
					  <?php	  	  
					}
			 echo "</select>";
		   }
		else
		   {   	 
		?>
           <select name="s_operator" id="s_operator" onchange="show_hide()">
              <option value="=" id="op_e" style="display:list-item">=</option>
              <option value="&gt;" id="op_gt" style="display:none">></option>
              <option value="&lt;" id="op_lt" style="display:none"><</option>
              <option value="&gt;=" id="op_gte" style="display:none">>=</option>
              <option value="&lt;=" id="op_lte" style="display:none"><=</option>
              <option value="!=" id="op_neq" style="display:list-item">!=</option>
              <option value="between" id="op_between" style="display:none">between</option>
              <option value="like"  id="op_like" style="display:list-item">like</option>
           </select>
		<?php }?>
        <div id="div_1" style="display:<?php if ($field!='itemd_acquired_date') echo "inline"; else echo "none";?>"><input type="text" name="txt_find_1" id="txt_find_1"
		 value="<?php 
		         if (isset($_POST['txt_find_1']))
				     echo $_POST['txt_find_1']; 
				 else 
				     echo "";	    
		       ?>"></div>
        <div id="div_2" style="display:<?php if ($field=='itemd_acquired_date') echo 'inline'; else echo 'none';?>">
		   <input name="txt_date_1" type="text" id="txt_date_1" readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_1']))
												      echo "value='".$_POST['txt_date_1']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
		<div id="div_3" style="display:<?php if ($field=='itemd_acquired_date' && $operator=='between') echo 'inline'; else echo 'none';?>">
		       <input name="txt_date_2" type="text" id="txt_date_2"  readonly="readonly" size="10" 
		                                       <?php 
											      if (isset($_POST['txt_date_2']))
												      echo "value='".$_POST['txt_date_2']."'";
												  else
											          echo "value='$current_date'";
											   ?>/></div>
        <input type="submit" name="btn_find" id="btn_find" value="Cari"/><div id="div_4" style="display:none">&nbsp;0=Tidak, 1=Ya</div>
      </div></td>
      <td align="right"><?php echo "<input name='btn_create' type='button' id='btn_create' value='Ekspor Data' onclick='call_export($branch_id)'/>";?></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="11%" scope="col" class="th_ltb">Kode Aset </th>
          <th width="22%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="6%" scope="col" class="th_ltb">Serial No</th>
          <th width="6%" scope="col" class="th_ltb">Kapasitas</th>
          <th width="5%" scope="col" class="th_ltb">Qty</th>
          <th width="8%" scope="col" class="th_ltb">Tgl Perolehan</th>
          <th width="8%" scope="col" class="th_ltb">Kategori</th>
          <th width="4%" scope="col" class="th_ltb">Rusak</th>
          <th width="10%" scope="col" class="th_ltb">Lokasi Gudang</th>
          </tr>
		  <?php
		     if ($continue=='0')
			    {
				  //echo $q_show_stock_material;
			 	  $exec_query=mysqli_query($db_connection, $q_show_stock_material);
				  $total_stock_material=mysqli_num_rows($exec_query);
				  $no=0;
				  $dwgd_id='';
				  while ($data_stock_material=mysqli_fetch_array($exec_query))
				        {
					      $no++;
						  $dwgd_id_1=$data_stock_material['itemd_id'];
   		  ?>
        <tr>
          <td align="center" class="td_lb"><?php echo $no;?></td>
            <td class="td_lb"><?php echo $data_stock_material['itemd_code'];?></td>
            <td class="td_lb"><?php echo $data_stock_material['masti_name'];?></td>
            <td class="td_lb"><?php echo $data_stock_material['itemd_serial_no'];?></td>
            <td class="td_lb"><?php echo $data_stock_material['itemd_capacity']." ".$data_stock_material['uom_name'];?></td>
            <td class="td_lb"><?php echo $data_stock_material['itemd_qty']." Cylinder";?></td>
            <td class="td_lb"><?php echo get_date_1($data_stock_material['itemd_acquired_date']);?></td>
            <td class="td_lb"><?php echo $data_stock_material['cati_name'];?></td>
            <td class="td_lb"><?php echo $data_stock_material['itemd_is_broken'];?></td>
            <td width="2%" class="td_lbr"><span class="td_lb"><?php echo $data_stock_material['whsl_name'];?></span></td>
          </tr><?php $dwgd_id=$data_stock_material['itemd_id']; }} ?>
      </table></td>
    </tr>
	      <?php
              $query_exec=mysqli_query($db_connection, $q_page,$db_connection) or die (mysqli_error());
              $total_rows=mysqli_fetch_array($query_exec);
              $maks_rows=ceil($total_rows['total_page']/$row);
		  ?>	  
																								   
	<tr>
        <td align="left">Total : <?php echo $total_rows['total_page'];?>&nbsp;data</td>
        <td align="right">Halaman ke  : 
		                                                                               <select name="s_page" id="s_page" onchange="document.f_stock_material.submit()">
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
</body>
</html>

<?php
 if (isset($_POST['btn_find']) || (isset($_POST['s_sorting_field']) || isset($_POST['s_sort'])))
    {
	  $field=$_POST['s_field'];
	  $operator=$_POST['s_operator'];
		    ?>
			 <script language="javascript">
			   var x='<?php echo $field;?>';
			   var y='<?php echo $operator;?>';
			 	
		       if (x=='itemd_is_broken')
			      {
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
				    document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='inline';
				  }
			   else
			   if (x=='itemd_acquired_date' && y=='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='inline';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			   if (x=='itemd_acquired_date' && y!='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			      {
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
			        document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }
			  </script>	  	
			<?php
    }
?>

<script language="javascript">
	function call_export(x)
	         {
			   var whsl_id='<?php echo $whsl_id;?>';
			   var field='<?php echo $field_to_convert;?>';
	           var operator='<?php echo $operator;?>';
			   var text_to_find_1='<?php echo $text_to_find_1;?>';
			   if (field=='itemd_acquired_date')
			      {
				    if (operator=='between')
					   { 
					     var text_to_find_2='<?php echo $text_to_find_2;?>';
					     open_child=window.open("../../data/whs/export_to_excel.php?id="+whsl_id+"&b="+x+"&f="+field+"&o="+operator+"&t1="+text_to_find_1+"&t2="+text_to_find_2);
					   }
					else
					   open_child=window.open("../../data/whs/export_to_excel.php?id="+whsl_id+"&b="+x+"&f="+field+"&o="+operator+"&t1="+text_to_find_1); 
				  }
			   else 
			      open_child=window.open("../../data/whs/export_to_excel.php?id="+whsl_id+"&b="+x+"&f="+field+"&o="+operator+"&t1="+text_to_find_1); 
			   
	         //  var text_to_find='<?php echo $text_to_find_1;?>';
			 //  window.location="../../data/whs/export_to_excel.php?id="+whsl_id+"&b="+x+"&f="+field+"&o="+operator+"&t="+text_to_find;
			 }

	function show_hide()
	         {
			   var x=document.getElementById('s_field').value;
			   var y=document.getElementById('s_operator').value;
			   if (x=='itemd_is_broken')
			      {
				    var j=document.getElementById('s_operator').value.trim();
					if (j!='=' && j!='!=' && j!='like')
					     document.getElementById('s_operator').value='=';
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
				    document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='inline';
				  }
			   else
			   if (x=='itemd_acquired_date' && y=='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
				    document.getElementById('txt_find_1').value='';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='inline';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			   if (x=='itemd_acquired_date' && y!='between')
			      {
				    document.getElementById('op_gt').style.display='list-item';
					document.getElementById('op_lt').style.display='list-item';
					document.getElementById('op_gte').style.display='list-item';
					document.getElementById('op_lte').style.display='list-item';
					document.getElementById('op_between').style.display='list-item';
				    document.getElementById('txt_find_1').value='';
			        document.getElementById('div_1').style.display='none';
					document.getElementById('div_2').style.display='inline';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			   else
			      {
				    var j=document.getElementById('s_operator').value.trim();
					if (j!='=' && j!='!=' && j!='like')
					     document.getElementById('s_operator').value='=';
				    document.getElementById('op_gt').style.display='none';
					document.getElementById('op_lt').style.display='none';
					document.getElementById('op_gte').style.display='none';
					document.getElementById('op_lte').style.display='none';
					document.getElementById('op_between').style.display='none';
			        document.getElementById('div_1').style.display='inline';
					document.getElementById('div_2').style.display='none';
					document.getElementById('div_3').style.display='none';
					document.getElementById('div_4').style.display='none';
				  }	
			 }
</script>
