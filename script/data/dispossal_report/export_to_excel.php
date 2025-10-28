<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='disph_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='')   
      $q_get_report="SELECT disph_code, disph_date, brokh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                     itemd_qty, cati_name, 
                                      CASE dispd_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END dispd_is_canceled
                                      FROM dispossal_detail
                                      INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
                                      LEFT JOIN broken_header ON broken_header.brokh_id=dispossal_header.brokh_id AND broken_header.branch_id='$branch_id'
                                      LEFT JOIN broken_detail ON broken_header.brokh_id=broken_detail.brokh_id AND broken_detail.brokd_id=dispossal_detail.brokd_id 
                                      INNER JOIN item_detail ON dispossal_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      INNER JOIN customer ON customer.cust_id=dispossal_header.cust_id
                                      WHERE dispossal_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND $find_text
									  ORDER BY disph_code, disph_date DESC, cust_name, itemd_code, masti_name ASC";
  else
      $q_get_report="SELECT disph_code, disph_date, brokh_code, cust_name, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name, 
                                     itemd_qty, cati_name, 
                                      CASE dispd_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END dispd_is_canceled
                                      FROM dispossal_detail
                                      INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
                                      LEFT JOIN broken_header ON broken_header.brokh_id=dispossal_header.brokh_id AND broken_header.branch_id='$branch_id'
                                      LEFT JOIN broken_detail ON broken_header.brokh_id=broken_detail.brokh_id AND broken_detail.brokd_id=dispossal_detail.brokd_id 
                                      INNER JOIN item_detail ON dispossal_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      INNER JOIN customer ON customer.cust_id=dispossal_header.cust_id
                                      WHERE dispossal_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' 
									  ORDER BY disph_code, disph_date DESC, cust_name, itemd_code, masti_name ASC";
  $exec_get_report=mysqli_query($db_connection,$q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_brokening_Report.xls';
       $header="DATA LAPORAN PENJUALAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."No Ref Kerusakan"."\t"."Nama Customer/Vendor"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial no"."\t"."Kapasitas"."\t"."Qty"."\t"."Kategori"."\t"."Dibatalkan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['disph_code']."\t".$field_data['disph_date']."\t".$field_data['brokh_code']."\t".$field_data['cust_name']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['cati_name']."\t".$field_data['dispd_is_canceled']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_brokening_Report.xls");
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