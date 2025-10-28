<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $as_of_date=get_date_2($_POST['txt_date']);
  $type_1=$_POST['rb_type_1'];
  if ($type_1=='0')
      $type_1="KELUARAN";
  else
      $type_1="MASUKAN";

  if (isset($_POST['s_item_1']))
      $item=$_POST['s_item_1'];
  else
      $item="";
  $item_list='';	  
  $type_2=$_POST['rb_type_2'];
  if ($type_2=='1')
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
	   if ($_POST['rb_type_1']=='0')
	       $item_list="SELECT DISTINCT(item_detail.itemd_id) 
                       FROM item_detail
                       INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id 
                       INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
                       INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                       WHERE transfer_header.branch_id='$branch_id'";
	   else
	       $item_list="SELECT DISTINCT(item_detail.itemd_id) 
                       FROM item_detail
                       INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id 
                       INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
                       INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
                       WHERE transfer_header.branch_id_to='$branch_id'";
	 }
  $q_get_report="SELECT ttd_id, transfer_header.tth_code, tth_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
                        itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, whsl_id_from, 
                     (SELECT whsl_name FROM warehouse_location WHERE whsl_id=whsl_id_from AND branch_id='$branch_id') AS whsl_id_from,
                     (SELECT branch_name FROM branch WHERE branch_id=branch_id_to) AS branch_name_to,
					  CASE tth_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END AS tth_is_canceled,
					  CASE ttd_status
                           WHEN '0' THEN 'Belum Diterima'
                           WHEN '1' THEN 'Sudah Diterima'
                      END AS ttd_status,
                      CASE ttd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END AS ttd_is_canceled, ttd_notes
               FROM item_detail 
			   INNER JOIN uom ON uom.uom_id=item_detail.uom_id
               INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               INNER JOIN customer ON customer.cust_id=item_detail.vend_id
               INNER JOIN transfer_detail ON transfer_detail.itemd_id=item_detail.itemd_id
               INNER JOIN transfer_header ON transfer_header.tth_id=transfer_detail.tth_id
               WHERE transfer_header.branch_id='$branch_id' AND tth_date<='$as_of_date' AND
			         item_detail.itemd_id IN ($item_list)
			   ORDER BY tth_code DESC";
  $exec_get_report=mysqli_query($db_connection, $q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Transfer_Report.xls';
       $header="LAPORAN PERPINDAHAN ASET (KELUARAN)"."\n";
	   $as_of_dates="AS Of Date : ".$as_of_date."\n\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Kode Aset"."\t"."Deskripsi Isi Aset"."\t"."Serial"."\t"."Kapasitas"."\t"."Kategori"."\t"."Qty"."\t"."Lokasi Asal"."\t"."Kantor Cabang Tujuan"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   if ($field_data['tth_is_canceled']=='Ya')
				   $is_canceled=$field_data['tth_is_canceled'];
			   else 
				   $is_canceled=$field_data['ttd_is_canceled'];
			   $rows_data=$rows_data.$field_data['tth_code']."\t".$field_data['tth_date']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t".$field_data['itemd_serial_no']."\t".$field_data['itemd_capacity']." ".$field_data['uom_name']."\t".$field_data['cati_name']."\t".$field_data['itemd_qty']." Cylinder"."\t".$field_data['whsl_id_from']."\t".$field_data['branch_name_to']."\t".$field_data['ttd_status']."\t".$is_canceled."\t".$field_data['ttd_notes']."\n";
			 }
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Transfer_Report.xls");
       echo  $header.$as_of_dates.$date."\n".$rows_data;
	   exit;	
	 }
  else
     {
	    ?>
		    <script language="javascript">
			   alert('Data tidak ditemukan!');
			   history.back();
			</script>
		<?php
	 } 	   
?>