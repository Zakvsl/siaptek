<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field_1=mysqli_real_escape_string($db_connection, $_GET['f1']);
  $field_2=mysqli_real_escape_string($db_connection, $_GET['f2']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field_1=='itemd_acquired_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
     {
       $find_text_1=$field_1." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
	   $find_text_2=$field_2." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
	 }
  else	 
  if ($operator=='like')
     {
	   $find_text_1=$field_1." like '%".$text_to_find_1."%'"; 
	   $find_text_2=$field_2." like '%".$text_to_find_1."%'"; 
	 }
  else
     {
	   $find_text_1=$field_1." ".$operator."'".$text_to_find_1."'";	
	   $find_text_2=$field_2." ".$operator."'".$text_to_find_1."'";	
	 }
  
  if ($text_to_find_1!='')  
      $q_get_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,  itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                     FROM item_detail 
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                 WHERE itemd_position='Internal' AND itemd_status='0' AND itemd_is_dispossed='0' AND item_detail.branch_id='$branch_id' AND $find_text_1
                                 UNION
                                 SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								            CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								            CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								            CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                     FROM issuing_detail 
                                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                 WHERE issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                                       item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0' AND issuingh_status!='2'  AND 
									   $find_text_2
							     ORDER BY itemd_code, masti_name ASC";
  else
      $q_get_report="SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name,  itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken, 
											itemd_position, '-' AS vendor_name, '-' AS customer_name
                                     FROM item_detail 
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
                                 WHERE itemd_position='Internal' AND itemd_status='0' AND itemd_is_dispossed='0' AND item_detail.branch_id='$branch_id'
                                 UNION
                                 SELECT item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                            CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, 
                                            CASE itemd_is_broken WHEN '0' THEN 'Tidak' WHEN '1' THEN 'Yes' END itemd_is_broken,
								            CASE issuingh_type WHEN '0' THEN 'Customer' WHEN '1' THEN 'Vendor' END itemd_position,
								            CASE cust_type WHEN '0' THEN '-' WHEN '1' THEN cust_name END vendor_name,
								            CASE cust_type WHEN '0' THEN cust_name WHEN '1' THEN '-' END customer_name
                                     FROM issuing_detail 
                                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                                     INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id
									 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                                 WHERE issuingd_is_return='0' AND issuingd_is_canceled='0' AND issuing_header.branch_id='$branch_id' AND 
                                       item_detail.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_is_canceled='0' AND issuingh_status!='2'
							     ORDER BY itemd_code, masti_name ASC";
  //echo $q_get_report;
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Issuing_Report.xls';
       $header="DATA LAPORAN POSISI ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial no"."\t"."Kapasitas"."\t"."Qty"."\t"."Tanggal Perolehan"."\t"."Kategori"."\t"."Status"."\t"."Posisi"."\t"."Nama Vendor"."\t"."Nama Customer"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['itemd_acquired_date']."\t".$field_data['cati_name']."\t".$field_data['itemd_status']."\t".$field_data['itemd_position']."\t".$field_data['vendor_name']."\t".$field_data['customer_name']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Position_Report.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ditemukan!');
			   window.close();
			</script>
		<?php
	 } 	   
?>