<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $field=mysqli_real_escape_string($db_connection, $_GET['f']);
  $operator=mysqli_real_escape_string($db_connection, $_GET['o']);
  $text_to_find_1=htmlspecialchars($_GET['t1']);
  if ($field=='woh_date' && $operator=='between')
     $text_to_find_2=htmlspecialchars($_GET['t2']);
     
  if ($operator=='between')
      $find_text=$field." between '".$text_to_find_1."' and '".$text_to_find_2."'"; 
  else	 
  if ($operator=='like')
	  $find_text=$field." like '%".$text_to_find_1."%'"; 
  else
	  $find_text=$field." ".$operator."'".$text_to_find_1."'";	
  
  if ($text_to_find_1!='')  
      $q_get_report="SELECT woh_code, woh_date, brokh_code, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name,
                                      itemd_qty, cati_name, 
                                      CASE wod_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END wod_is_canceled, wod_notes
                                      FROM write_off_detail
                                      INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
                                      LEFT JOIN broken_header ON broken_header.brokh_id=write_off_header.brokh_id AND broken_header.branch_id='$branch_id'
                                      LEFT JOIN broken_detail ON broken_header.brokh_id=broken_detail.brokh_id AND broken_detail.brokd_id=write_off_detail.brokd_id 
                                      INNER JOIN item_detail ON write_off_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      WHERE write_off_header.branch_id='$branch_id' AND $find_text
									  ORDER BY woh_code, woh_date DESC, itemd_code, masti_name ASC";
  else
      $q_get_report="SELECT woh_code, woh_date, brokh_code, itemd_code, masti_name, itemd_serial_no, itemd_capacity, uom_name,
                                      itemd_qty, cati_name, 
                                      CASE wod_is_canceled
                                           WHEN '0' THEN 'Tidak'
                                           WHEN '1' THEN 'Ya'
                                      END wod_is_canceled, wod_notes
                                      FROM write_off_detail
                                      INNER JOIN write_off_header ON write_off_header.woh_id=write_off_detail.woh_id
                                      LEFT JOIN broken_header ON broken_header.brokh_id=write_off_header.brokh_id AND broken_header.branch_id='$branch_id'
                                      LEFT JOIN broken_detail ON broken_header.brokh_id=broken_detail.brokh_id AND broken_detail.brokd_id=write_off_detail.brokd_id 
                                      INNER JOIN item_detail ON write_off_detail.itemd_id=item_detail.itemd_id
									  INNER JOIN uom ON uom.uom_id=item_detail.uom_id
                                      INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                                      INNER JOIN category_item ON category_item.cati_id=master_item.cati_id
                                      WHERE write_off_header.branch_id='$branch_id'
									  ORDER BY woh_code, woh_date DESC, itemd_code, masti_name ASC";
  //echo $q_get_report;
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_brokening_Report.xls';
       $header="DATA LAPORAN PENGHAPUSAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."No Ref Kerusakan"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial no"."\t"."Kapasitas"."\t"."Qty"."\t"."Kategori"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   $rows_data=$rows_data.$field_data['woh_code']."\t".$field_data['woh_date']."\t".$field_data['brokh_code']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t"." ".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['cati_name']."\t".$field_data['wod_is_canceled']."\t".$field_data['wod_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Write_Off_Report.xls");
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