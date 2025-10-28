<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	  
  $type_1=$_POST['rb_type_1'];
  $start_date=get_date_2($_POST['txt_start_date']);
  $end_date=get_date_2($_POST['txt_end_date']);
  if (isset($_POST['s_item_1']))
      $item=$_POST['s_item_1'];
  else
      $item="";
  $item_list='';
  if ($type_1=='1')
     {
       foreach ($item as $tube)
               {
		        if ($item_list=='')
		            $item_list="'".$tube."'";
		        else
		            $item_list=$item_list.",'".$tube."'";
			   }
     } 
  else
     {
	   $item_list="SELECT item_detail.itemd_id 
                   FROM item_detail 
                   WHERE branch_id='$branch_id' ";
	 }
  $q_get_data="SELECT issuingh_code AS transaction_code, issuingh_date AS transaction_date, 'Pengeluaran Aset' AS transaction_type, 
                      CASE issuingd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
               INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
               WHERE issuing_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id' AND issuingh_date BETWEEN '$start_date' AND '$end_date' AND 
			         item_detail.itemd_id IN ($item_list)
               UNION
               SELECT reth_code AS transaction_code, reth_date AS transaction_date, 'Pengembalian Aset' AS transaction_type, 
                      CASE retd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN issuing_detail ON issuing_detail.itemd_id=item_detail.itemd_id
               INNER JOIN issuing_header ON issuing_header.issuingh_id=issuing_detail.issuingh_id
               INNER JOIN return_detail ON return_detail.issuingd_id=issuing_detail.issuingd_id
               INNER JOIN return_header ON return_header.reth_id=return_detail.reth_id
               WHERE issuing_header.branch_id='$branch_id' AND return_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND 
			         reth_date BETWEEN '$start_date' AND '$end_date' AND item_detail.itemd_id IN ($item_list)
               UNION
               SELECT tth_code AS transaction_code, tth_date AS transaction_date, 'Perpindahan Aset' AS transaction_type, 
                      CASE ttd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id
               INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
               WHERE transfer_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND tth_date BETWEEN '$start_date' AND '$end_date'  AND 
			         item_detail.itemd_id IN ($item_list)
               UNION
               SELECT disph_code AS transaction_code, disph_date AS transaction_date, 'Penjualan Aset' AS transaction_type, 
                      CASE dispd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END is_canceled, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN dispossal_detail ON dispossal_detail.itemd_id=item_detail.itemd_id
               INNER JOIN dispossal_header ON dispossal_header.disph_id=dispossal_detail.disph_id
               WHERE dispossal_header.branch_id='$branch_id' AND item_detail.branch_id='$branch_id'  AND disph_date BETWEEN '$start_date' AND '$end_date'  AND 
			         item_detail.itemd_id IN ($item_list)
               ORDER BY itemd_code, transaction_date ASC";
  $exec_get_data=mysqli_query($db_connection,$q_get_data); 
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   $filename='Movement_Item.xls';
       $header="LAPORAN PERGERAKAN ASET"."\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Kapasitas"."\t"."Qty"."\t"."Serial No"."\t"."Tgl Perolehan"."\t"."Kategori"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Jenis Transaksi"."\t"."Dibatalkan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_data))
			 {
			   $rows_data=$rows_data.$field_data['itemd_code']."\t".$field_data['masti_name']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['itemd_serial_no']."\t".$field_data['itemd_acquired_date']."\t".$field_data['cati_name']."\t".$field_data['transaction_code']."\t".$field_data['transaction_date']."\t".$field_data['transaction_type']."\t".$field_data['is_canceled']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=Movement_Item.xls");
       echo  $header.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ada yang ditemukan!');
			   history.back();
			</script>
		<?php
	 } 	   
?>