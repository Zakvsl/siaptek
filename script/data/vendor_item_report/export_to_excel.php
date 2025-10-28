<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find=htmlspecialchars($_GET['t']);
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find."'";	
  
  if ($text_to_find!='') 
      $q_get_report="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                                 FROM issuing_detail
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                                 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								       issuing_header.branch_id='$branch_id' AND issuingh_type='1' AND $find_text
					 ORDER BY issuingh_code, issuingh_date DESC, itemd_code, masti_name, cust_name ASC";
  else
      $q_get_report="SELECT issuingh_code, issuingh_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
			                            itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, 
                                        CASE itemd_status WHEN '0' THEN 'Active' WHEN '1' THEN 'InActive' END itemd_status, cust_name, 
										DATEDIFF(NOW(), issuingh_date) AS issuing_aging
                                 FROM issuing_detail
                                 INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id 
                                 INNER JOIN item_detail ON item_detail.itemd_id=issuing_detail.itemd_id 
								 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                 INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id 
                                 INNER JOIN category_item ON category_item.cati_id=master_item.cati_id  
                                 INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                                 WHERE issuingd_is_canceled='0' AND issuingd_is_return='0' AND issuingh_is_canceled='0' AND issuingh_status!='2' AND 
								       issuing_header.branch_id='$branch_id' AND issuingh_type='1'
					 ORDER BY issuingh_code, issuingh_date DESC, itemd_code, masti_name, cust_name ASC";
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Issuing_Report.xls';
       $header="DATA LAPORAN PENGELUARAN ASET KE VENDOR"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial no"."\t"."Kapasitas"."\t"."Qty"."\t"."Umur"."\t"."Kategori"."\t"."Status"."\t"."Posisi"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['issuing_aging']." hari"."\t".$field_data['cati_name']."\t".$field_data['itemd_status']."\t".$field_data['cust_name']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Refill_Report.xls");
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