<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='issuingh_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='')   
      $q_get_report="SELECT issuingh_code, issuingh_date, 
                            CASE issuingh_type
                                  WHEN '0' THEN 'Customer'
                                  WHEN '1' THEN 'Vendor' 
                            END issuingh_type, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                            itemd_qty, cati_name, 
							CASE issuingd_status
                                 WHEN '0' THEN 'Normal'
                                 WHEN '1' THEN 'Rental'
							     WHEN '2' THEN 'UJM'
                            END issuingd_status,
                            CASE issuingd_is_canceled
                                 WHEN '0' THEN 'Tidak'
                                 WHEN '1' THEN 'Ya'
                            END issuingd_is_canceled, 
                            CASE issuingd_is_return
                                 WHEN '0' THEN 'Belum'
                                 WHEN '1' THEN 'Sudah'
                            END issuingd_is_return,
							CASE issuingh_is_canceled
							     WHEN '0' THEN 'Tidak Dibatalkan'
								 WHEN '1' THEN 'Dibatalkan'
							END issuingh_is_canceled
                     FROM issuing_detail
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                     INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_text
					 ORDER BY issuingh_code, issuingh_date DESC, cust_name, itemd_code, masti_name ASC";
  else
      $q_get_report="SELECT issuingh_code, issuingh_date, 
                            CASE issuingh_type
                                  WHEN '0' THEN 'Customer'
                                  WHEN '1' THEN 'Vendor' 
                            END issuingh_type, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                            itemd_qty, cati_name, 
							CASE issuingd_status
                                 WHEN '0' THEN 'Normal'
                                 WHEN '1' THEN 'Rental'
							     WHEN '2' THEN 'UJM'
                            END issuingd_status,
                            CASE issuingd_is_canceled
                                 WHEN '0' THEN 'Tidak'
                                 WHEN '1' THEN 'Ya'
                            END issuingd_is_canceled, 
                            CASE issuingd_is_return
                                 WHEN '0' THEN 'Belum'
                                 WHEN '1' THEN 'Sudah'
                            END issuingd_is_return,
							CASE issuingh_is_canceled
							     WHEN '0' THEN 'Tidak Dibatalkan'
								 WHEN '1' THEN 'Dibatalkan'
							END issuingh_is_canceled
                     FROM issuing_detail
                     INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
                     INNER JOIN item_detail ON issuing_detail.itemd_id=item_detail.itemd_id
					 INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                     INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                     INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                     INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                     WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' 
					 ORDER BY issuingh_code, issuingh_date DESC, cust_name, itemd_code, masti_name ASC";
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Issuing_Report.xls';
       $header="DATA LAPORAN PENGELUARAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Tipe"."\t"."Nama Customer/Vendor"."\t"."Status Transaksi"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial no"."\t"."Kapasitas"."\t"."Qty"."\t"."Kategori"."\t"."Status"."\t"."Dibatalkan"."\t"."Sudah Kembali"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['issuingh_type']."\t".$field_data['cust_name']."\t".$field_data['issuingh_is_canceled']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['cati_name']."\t".$field_data['issuingd_status']."\t".$field_data['issuingd_is_canceled']."\t".$field_data['issuingd_is_return']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Issuing_Report.xls");
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