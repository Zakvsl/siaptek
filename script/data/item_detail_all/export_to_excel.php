<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $itemd_status=htmlspecialchars($_GET['v']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($itemd_status=='0')
	  $itemd_status="itemd_status='0'";
  else
	  $itemd_status="itemd_status IN ('0','1')";
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
      $q_get_data="SELECT branch_name, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,
			              CONCAT('\'',itemd_serial_no) AS itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                          CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, itemd_position,
                          CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_broken, 
						  CASE itemd_is_wo WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_wo,
                          CASE itemd_is_dispossed WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_dispossed, whsl_name, cust_name,
						  IF(DATEDIFF(NOW(),itemd_acquired_date)<=0 OR itemd_status='1','0',DATEDIFF(NOW(),itemd_acquired_date)) AS itemd_aging,
					     (SELECT branch_name FROM branch WHERE branch_id= item_detail.original_branch_id) AS original_branch_name
                   FROM item_detail 
				   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                   INNER JOIN customer ON customer.cust_id=item_detail.vend_id
				   INNER JOIN branch ON branch.branch_id=item_detail.branch_id
				   WHERE $itemd_status AND $find_text";
  else
      $q_get_data="SELECT branch_name, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,
			              CONCAT('\'',itemd_serial_no) AS itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                          CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, itemd_position,
                          CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_broken, 
						  CASE itemd_is_wo WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_wo,
                          CASE itemd_is_dispossed WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Ya' END itemd_is_dispossed, whsl_name, cust_name,
						  IF(DATEDIFF(NOW(),itemd_acquired_date)<=0 OR itemd_status='1','0',DATEDIFF(NOW(),itemd_acquired_date)) AS itemd_aging,
					     (SELECT branch_name FROM branch WHERE branch_id= item_detail.original_branch_id) AS original_branch_name
                   FROM item_detail 
				   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                   INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                   INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                   INNER JOIN warehouse_location ON warehouse_location.whsl_id=item_detail.whsl_id 
                   INNER JOIN customer ON customer.cust_id=item_detail.vend_id
				   INNER JOIN branch ON branch.branch_id=item_detail.branch_id
				   WHERE $itemd_status";
  //echo $q_get_data;
  $exec_get_data=mysqli_query($db_connection, $q_get_data);
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Item_Detail.xls';
       $header="DAFTAR ASET DI SEMUA KANTOR CABANG"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kantor Cabang"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial No"."\t"."Kapasitas"."\t"."Qty"."\t"."Tanggal Perolehan"."\t"."Umur"."\t"."Kategori"."\t"."Status"."\t"."Posisi"."\t"."Rusak"."\t"."Dihapus"."\t"."Dijual"."\t"."Lokasi Gudang"."\t"."Vendor"."\t"."Pemilik Aset"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['branch_name']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['itemd_acquired_date']."\t".$field_data['itemd_aging']." hari"."\t".$field_data['cati_name']."\t".$field_data['itemd_status']."\t".$field_data['itemd_position']."\t".$field_data['itemd_is_broken']."\t".$field_data['itemd_is_wo']."\t".$field_data['itemd_is_dispossed']."\t".$field_data['whsl_name']."\t".$field_data['cust_name']."\t".$field_data['original_branch_name']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Mater_Item_All.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			   window.location.href='javascript:history.back(1)';
			</script>
		<?php
	 } 	   
?>