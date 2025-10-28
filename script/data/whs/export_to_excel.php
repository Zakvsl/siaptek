<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  if (isset($_GET['b']))
      $branch_id=mysqli_real_escape_string($db_connection, $_GET['b']);
  else
      $branch_id=$_SESSION['ses_id_branch'];	 
  $whsl_id=mysqli_real_escape_string($db_connection, $_GET['id']); 
  $q_check_whs="SELECT * FROM warehouse_location WHERE whsl_id='$whsl_id'";
  $exec_check_whs=mysqli_query($db_connection, $q_check_whs);
  $field_data_whs=mysqli_fetch_array($exec_check_whs);
  $whsl_name=$field_data_whs['whsl_name'];
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
  $ordered_by="ORDER BY itemd_code ASC";		  
	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='itemd_acquired_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='') 
      $q_get_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
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
								   item_detail.branch_id='$branch_id' $find_whs AND $find_text $ordered_by";
  else
      $q_get_stock_material="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
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
								   item_detail.branch_id='$branch_id' $find_whs $ordered_by";
  //echo $q_get_stock_material; 
  $exec_get_stock_material=mysqli_query($db_connection, $q_get_stock_material);
  if (mysqli_num_rows($exec_get_stock_material)>0)
     {
	   $no=1;
	   $filename='Stock_Aset_Per_WHS.xls';
       $header="DAFTAR STOK ASET DI WAREHOUSE ($whsl_name)"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial No"."\t"."Kapasitas"."\t"."Qty"."\t"."Tgl Perolehan"."\t"."Kategori"."\t"."Rusak"."\t"."Lokasi Gudang"."\n"; 
	   $dwgd_id='';
	   while ($field_data=mysqli_fetch_array($exec_get_stock_material))
			 {
			   $rows_data=$rows_data.$no++."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['itemd_acquired_date']."\t".$field_data['cati_name']."\t".$field_data['itemd_is_broken']."\t".$field_data['whsl_name']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Stock_Aset_Per_WHS.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Tidak ada data yang ditemukan!');
			   window.close();
			</script>
		<?php
	 } 	   
?>