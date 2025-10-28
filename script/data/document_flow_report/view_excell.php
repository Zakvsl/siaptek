<?php
  ob_start();
  include "../../library/check_session.php";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  include "../../library/excel_reader.php";
  $branch_id=$_SESSION['ses_id_branch'];	 
  $trans_type=$_POST['s_transaction_type'];
  $trans_no=$_POST['txt_transaction_no'];
  $q_get_branch_name="SELECT branch_name FROM branch WHERE branch_id='$branch_id'";
  $exec_get_branch_name=mysqli_query($db_connection,$q_get_branch_name);
  $field_branch=mysqli_fetch_array($exec_get_branch_name);
  
  if ($trans_type=='1')
     {
	   $transaction_name='TRANSFER POSISI ASET';
	   $q_get_data="SELECT tth_id, tth_code, tth_date,
                    CASE tth_status
                         WHEN '0' THEN 'Belum Diterima'
                         WHEN '1' THEN 'Diterima Sebagian'
                         WHEN '2' THEN 'Diterima Semua'
                    END tth_status,
                    CASE tth_is_canceled
                         WHEN '0' THEN 'Tidak'
                         WHEN '1' THEN 'Ya'
                    END tth_is_canceled, tth_notes
                    FROM transfer_header
					WHERE tth_code='$trans_no' AND branch_id='$branch_id'";
	   $q_get_data_1="SELECT branch_name, receipt_transfer_header.tth_id, rth_id, rth_code, rth_date, 
                             CASE rth_is_canceled
                                  WHEN '0' THEN 'Tidak'
                                  WHEN '1' THEN 'Ya'
                             END rth_is_canceled, rth_notes
                      FROM receipt_transfer_header
                      INNER JOIN branch ON branch.branch_id=receipt_transfer_header.branch_id   
                      INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id                   
                      WHERE branch_id_from='$branch_id' AND tth_code='$trans_no'                      
                      ORDER BY branch_name ASC, rth_code DESC, rth_date DESC";
	 }
  else
  if ($trans_type=='2')
     {
	   $transaction_name='PENERIMAAN TRANSFER POSISI ASET';
	   $q_get_data="SELECT branch_name, receipt_transfer_header.tth_id, rth_id, rth_code, rth_date, 
                           CASE rth_is_canceled
                                WHEN '0' THEN 'Tidak'
                                WHEN '1' THEN 'Ya'
                           END rth_is_canceled, rth_notes
                    FROM receipt_transfer_header
                    INNER JOIN branch ON branch.branch_id=receipt_transfer_header.branch_id_from   
                    INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id                   
                    WHERE rth_code='$trans_no'                      
                    ORDER BY branch_name ASC, rth_code DESC, rth_date DESC";
	   $q_get_data_1="SELECT tth_id, tth_code, tth_date,
                    CASE tth_status
                         WHEN '0' THEN 'Belum Diterima'
                         WHEN '1' THEN 'Diterima Sebagian'
                         WHEN '2' THEN 'Diterima Semua'
                    END tth_status,
                    CASE tth_is_canceled
                         WHEN '0' THEN 'Tidak'
                         WHEN '1' THEN 'Ya'
                    END tth_is_canceled, tth_notes
                    FROM transfer_header
					WHERE tth_id=(SELECT tth_id FROM receipt_transfer_header WHERE rth_code='$trans_no' AND branch_id='$branch_id')";
	 }
  else
  if ($trans_type=='3')
     {
	   $transaction_name='PENGELUARAN ASET';
	   $q_get_data="SELECT issuingh_id, issuingh_code, issuingh_date, 
                           CASE issuingh_type 
                                WHEN '0' THEN 'Customer' 
                                WHEN '1' THEN 'Vendor'  
                           END issuingh_type, 
                           CASE issuingh_status 
                                WHEN '0' THEN 'Belum Kembali' 
                                WHEN '1' THEN 'Kembali Sebagian' 
                                WHEN '2' THEN 'Kembali Semua' 
                           END issuingh_status, 
                           CASE issuingh_is_canceled 
                                WHEN '0' THEN 'Tidak' 
                                WHEN '1' THEN 'Ya' 
                           END issuingh_is_canceled, issuingh_notes, cust_name 
                    FROM issuing_header 
                    INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                    WHERE issuing_header.branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM issuing_header WHERE issuingh_code='$trans_no' AND branch_id='$branch_id')
                          AND customer.branch_id='$branch_id'
					ORDER BY issuingh_code DESC, issuingh_date DESC";
	   $q_get_data_1="SELECT return_header.issuingh_id, reth_id, reth_code, reth_date, 
                             CASE reth_is_canceled 
							      WHEN '0' THEN 'Tidak' 
								  WHEN '1' THEN 'Ya' 
						     END reth_is_canceled, reth_notes 
                       FROM return_header 
                       WHERE branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM issuing_header WHERE issuingh_code='$trans_no' AND branch_id='$branch_id')
                       ORDER BY reth_code DESC, reth_date DESC";
	 }
  else
  if ($trans_type=='4')
     {
	   $transaction_name='PENGEMBALIAN ASET';
	   $q_get_data="SELECT cust_name, return_header.issuingh_id, reth_id, reth_code, reth_date, 
                           CASE reth_is_canceled 
							    WHEN '0' THEN 'Tidak' 
								WHEN '1' THEN 'Ya' 
						   END reth_is_canceled, reth_notes 
                    FROM return_header
					INNER JOIN issuing_header ON issuing_header.issuingh_id=return_header.issuingh_id
					INNER JOIN customer ON customer.cust_id=issuing_header.cust_id 
                    WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND reth_code='$trans_no'
                    ORDER BY reth_code DESC, reth_date DESC";
	   $q_get_data_1="SELECT issuingh_id, issuingh_code, issuingh_date, 
                           CASE issuingh_type 
                                WHEN '0' THEN 'Customer' 
                                WHEN '1' THEN 'Vendor'  
                           END issuingh_type, 
                           CASE issuingh_status 
                                WHEN '0' THEN 'Belum Kembali' 
                                WHEN '1' THEN 'Kembali Sebagian' 
                                WHEN '2' THEN 'Kembali Semua' 
                           END issuingh_status, 
                           CASE issuingh_is_canceled 
                                WHEN '0' THEN 'Tidak' 
                                WHEN '1' THEN 'Ya' 
                           END issuingh_is_canceled, issuingh_notes, cust_name 
                    FROM issuing_header 
                    INNER JOIN customer ON customer.cust_id=issuing_header.cust_id
                    WHERE issuing_header.branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM return_header WHERE reth_code='$trans_no' AND branch_id='$branch_id') AND
					      customer.branch_id='$branch_id'
                    ORDER BY issuingh_code DESC, issuingh_date DESC";
	 }
  else
  if ($trans_type=='5')
     {
	   $transaction_name='ASET RUSAK';
	   $q_get_data="SELECT brokh_id, brokh_code, brokh_date,
                    CASE brokh_status
                         WHEN '0' THEN 'Tidak Dihapus dan Tidak Dijual'
                         WHEN '1' THEN 'Dihapus Sebagian'
                         WHEN '2' THEN 'Dihapus Semua'
						 WHEN '3' THEN 'Dijual Sebagian'
						 WHEN '4' THEN 'Dijual Semua'
						 WHEN '5' THEN 'Dihapus dan Dijual Sebagian'
						 WHEN '6' THEN 'Dihapus dan Dijual Semua'
                    END brokh_status,
                    CASE brokh_is_canceled
                         WHEN '0' THEN 'Tidak'
                         WHEN '1' THEN 'Ya'
                    END brokh_is_canceled, brokh_notes
                    FROM broken_header
					WHERE brokh_code='$trans_no' AND branch_id='$branch_id'";
	   $q_get_data_1="SELECT woh_id, woh_code, woh_date,
                      CASE woh_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END woh_is_canceled, woh_notes
                      FROM write_off_header                  
                      WHERE branch_id='$branch_id' AND brokh_id=(SELECT brokh_id FROM broken_header WHERE brokh_code='$trans_no' AND branch_id='$branch_id')                      
                      ORDER BY woh_code DESC, woh_date DESC";
	   $q_get_data_2="SELECT disph_id, disph_code, disph_date,
                      CASE disph_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END disph_is_canceled, disph_notes, cust_name
                      FROM dispossal_header                  
					  INNER JOIN customer ON customer.cust_id=dispossal_header.cust_id
                      WHERE dispossal_header.branch_id='$branch_id' AND brokh_id=(SELECT brokh_id FROM broken_header WHERE brokh_code='$trans_no' AND branch_id='$branch_id') AND
					        customer.branch_id='$branch_id'
                      ORDER BY disph_code DESC, disph_date DESC";
	 }
  else
  if ($trans_type=='6')
     {
	   $transaction_name='PENGHAPUSAN ASET';
	   $q_get_data="SELECT woh_id, woh_code, woh_date,
                    CASE woh_sources
                         WHEN '0' THEN 'Internal'
                         WHEN '1' THEN 'Kerusakan'
                    END woh_sources,
					CASE woh_is_canceled
                         WHEN '0' THEN 'Tidak'
                         WHEN '1' THEN 'Ya'
                    END woh_is_canceled, woh_notes
                    FROM write_off_header                  
                    WHERE branch_id='$branch_id' AND woh_code='$trans_no'                      
                    ORDER BY woh_code DESC, woh_date DESC";
	   $q_get_data_1="SELECT brokh_id, brokh_code, brokh_date,
                      CASE brokh_status
                           WHEN '0' THEN 'Tidak Dihapus dan Tidak Dijual'
                           WHEN '1' THEN 'Dihapus Sebagian'
                           WHEN '2' THEN 'Dihapus Semua'
						   WHEN '3' THEN 'Dijual Sebagian'
						   WHEN '4' THEN 'Dijual Semua'
						   WHEN '5' THEN 'Dihapus dan Dijual Sebagian'
						   WHEN '6' THEN 'Dihapus dan Dijual Semua'
                      END brokh_status,
                      CASE brokh_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END brokh_is_canceled, brokh_notes
                      FROM broken_header
					  WHERE brokh_id=(SELECT brokh_id FROM write_off_header	where woh_code='$trans_no' AND branch_id='$branch_id') AND branch_id='$branch_id'";
	 }
  else
  if ($trans_type=='7')
     {
	   $transaction_name='PENJUALAN ASET';
	   $q_get_data="SELECT disph_id, disph_code, disph_date,
                    CASE disph_sources
                         WHEN '0' THEN 'Internal'
                         WHEN '1' THEN 'Kerusakan'
                    END disph_sources,
					CASE disph_is_canceled
                         WHEN '0' THEN 'Tidak'
                         WHEN '1' THEN 'Ya'
                    END disph_is_canceled, disph_notes
                    FROM dispossal_header                  
                    WHERE branch_id='$branch_id' AND disph_code='$trans_no'                      
                    ORDER BY disph_code DESC, disph_date DESC";
	   $q_get_data_1="SELECT brokh_id, brokh_code, brokh_date,
                      CASE brokh_status
                           WHEN '0' THEN 'Tidak Dihapus dan Tidak Dijual'
                           WHEN '1' THEN 'Dihapus Sebagian'
                           WHEN '2' THEN 'Dihapus Semua'
						   WHEN '3' THEN 'Dijual Sebagian'
						   WHEN '4' THEN 'Dijual Semua'
						   WHEN '5' THEN 'Dihapus dan Dijual Sebagian'
						   WHEN '6' THEN 'Dihapus dan Dijual Semua'
                      END brokh_status,
                      CASE brokh_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END brokh_is_canceled, brokh_notes
                      FROM broken_header
					  WHERE brokh_id=(SELECT brokh_id FROM dispossal_header	where disph_code='$trans_no' AND branch_id='$branch_id') AND branch_id='$branch_id'";
	 }

  //echo $q_get_data."<br>";	 
  //echo $q_get_data_1."<br>";	 
  //echo $q_get_data_2;
  $exec_get_data=mysqli_query($db_connection,$q_get_data);
  $exec_get_data_1=mysqli_query($db_connection,$q_get_data_1);
  if ($trans_type=='5')
      $exec_get_data_2=mysqli_query($db_connection,$q_get_data_2);
  
  if (mysqli_num_rows($exec_get_data)>0)
     {
	   if ($trans_type=='1')
          {
	        $filename='Transfer_Posisi_Aset.xls';
			$header="LAPORAN HISTORI TRANSAKSI (TRANSFER POSISI ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['tth_code']."\t".$field_data['tth_date']."\t".$field_data['tth_status']."\t".$field_data['tth_is_canceled']."\t".$field_data['tth_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL TRANSAKSI REFERENSI"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kantor Cabang"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['branch_name']."\t".$field_data_1['rth_code']."\t".$field_data_1['rth_date']."\t".$field_data_1['rth_is_canceled']."\t".$field_data_1['rth_notes']."\n";
	      }
       else 
       if ($trans_type=='2')
          {
		    $filename='Penerimaan_Transfer_Posisi_Aset.xls';
			$header="LAPORAN HISTORI TRANSAKSI (PENERIMAAN TRANSFER POSISI ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="Kantor Cabang Asal"."\t"."No Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['branch_name']."\t".$field_data['rth_code']."\t".$field_data['rth_date']."\t".$field_data['rth_is_canceled']."\t".$field_data['rth_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL SUMBER TRANSAKSI"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['tth_code']."\t".$field_data_1['tth_date']."\t".$field_data_1['tth_status']."\t".$field_data_1['tth_is_canceled']."\t".$field_data_1['tth_notes']."\n";
	      }
       else
       if ($trans_type=='3')
          {
		    $filename='Pengeluaran_Aset.xls';
			$header="LAPORAN HISTORI TRANSAKSI (PENGELUARAN ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="Nama Customer/Vendor"."\t"."No Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Tipe"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['cust_name']."\t".$field_data['issuingh_code']."\t".$field_data['issuingh_date']."\t".$field_data['issuingh_status']."\t".$field_data['issuingh_is_canceled']."\t".$field_data['issuingh_type']."\t".$field_data['issuingh_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL TRANSAKSI PENGEMBALIAN"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['reth_code']."\t".$field_data_1['reth_date']."\t".$field_data_1['reth_is_canceled']."\t".$field_data_1['reth_notes']."\n";
	      }
       else
       if ($trans_type=='4')
          {
		    $filename='Pengembalian_ASER.xls';
			$header="LAPORAN HISTORI TRANSAKSI (PENGEMBALIAN ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="Nama Customer/Vendor"."\t"."No Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['cust_name']."\t".$field_data['reth_code']."\t".$field_data['reth_date']."\t".$field_data['reth_is_canceled']."\t".$field_data['reth_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL SUMBER TRANSAKSI"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Tipe"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['issuingh_code']."\t".$field_data_1['issuingh_date']."\t".$field_data_1['issuingh_status']."\t".$field_data_1['issuingh_is_canceled']."\t".$field_data_1['issuingh_type']."\t".$field_data_1['issuingh_notes']."\n";
	      }
       else
       if ($trans_type=='5')
          {
		    $filename='Aset_Rusak.xls';
			$header="LAPORAN HISTORI TRANSAKSI (ASET RUSAK)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['brokh_code']."\t".$field_data['brokh_date']."\t".$field_data['brokh_status']."\t".$field_data['brokh_is_canceled']."\t".$field_data['brokh_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL TRANSAKSI PENGHAPUSAN"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['woh_code']."\t".$field_data_1['woh_date']."\t".$field_data_1['woh_is_canceled']."\t".$field_data_1['woh_notes']."\n";
		    
			$rows_data=$rows_data."\n"."\t"."DETAIL TRANSAKSI PENJUALAN"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Nama Customer"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_2=mysqli_fetch_array($exec_get_data_2))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_2['cust_name']."\t".$field_data_2['disph_code']."\t".$field_data_2['disph_date']."\t".$field_data_2['disph_is_canceled']."\t".$field_data_1['disph_notes']."\n";		   
	      }
       else
       if ($trans_type=='6')
          {
		    $filename='Penghapusan_Aset.xls';
			$header="LAPORAN HISTORI TRANSAKSI (PENGHAPUSAN ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Sumber Transaksi"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['woh_code']."\t".$field_data['woh_date']."\t".$field_data['woh_sources']."\t".$field_data['woh_is_canceled']."\t".$field_data['woh_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL SUMBER TRANSAKSI"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['brokh_code']."\t".$field_data_1['brokh_date']."\t".$field_data_1['brokh_status']."\t".$field_data_1['brokh_is_canceled']."\t".$field_data_1['brokh_notes']."\n";
	      }
       else
       if ($trans_type=='7')
          {
		    $filename='Penjualan_Aswt.xls';
			$header="LAPORAN HISTORI TRANSAKSI (PENJUALAN ASET)"."\n";
            $date='Retrived Date : '.date("d-m-Y")."\n"; 
			$branch_name="Kantor Cabang : ".$field_branch['branch_name']."\n";
	        $rows_data="No Transaksi"."\t"."Tanggal"."\t"."Sumber Transaksi"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	        $field_data=mysqli_fetch_array($exec_get_data);
		    $rows_data=$rows_data.$field_data['disph_code']."\t".$field_data['disph_date']."\t".$field_data['disph_sources']."\t".$field_data['disph_is_canceled']."\t".$field_data['disph_notes']."\n";
			$rows_data=$rows_data.""."\t"."DETAIL SUMBER TRANSAKSI"."\n";
			$rows_data=$rows_data.""."\t"."No"."\t"."Kode Transaksi"."\t"."Tanggal"."\t"."Status"."\t"."Dibatalkan"."\t"."Keterangan"."\n";
			$no=1;
			while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				   $rows_data=$rows_data.""."\t".$no++."\t".$field_data_1['brokh_code']."\t".$field_data_1['brokh_date']."\t".$field_data_1['brokh_status']."\t".$field_data_1['brokh_is_canceled']."\t".$field_data_1['brokh_notes']."\n";
		  } 
		  
	   $rows_data = str_replace( "\r" , "" , $rows_data);
       header("Content-type: application/vnd.ms-excel");
       header("Content-disposition: xls" . date("Y-m-d") . ".xls");
       header("Content-disposition: filename=List_Of_Transfer_Report.xls");
       echo  $header.$date.$branch_name."\n".$rows_data;
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
  
  
  
  
  if ($type_1=='0')
      $type_1="KELUARAN";
  else
      $type_1="MASUKAN";
	  
  $item=$_POST['s_item'];
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
	 
  $q_get_report="SELECT rtd_id, rth_code, tth_code, rth_date, itemd_code, masti_name, masti_capacity, uom_id_1,
                     (SELECT uom_name FROM uom WHERE uom_id=uom_id_1) AS uom_id_1, itemd_serial_no, itemd_acquired_date, itemd_qty, uom_id_2, 
					 (SELECT uom_name FROM uom WHERE uom_id=uom_id_2) AS uom_id_2, cati_name, whsl_id_new, branch_id_from,
				     (SELECT branch_name FROM branch WHERE branch_id=branch_id_from) AS branch_name_from,
				     (SELECT whsl_name FROM warehouse_location WHERE whsl_id=whsl_id_new AND branch_id='$branch_id') AS whsl_id_new,
					  CASE rth_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END AS rth_is_canceled, 
					  CASE rtd_is_canceled
                           WHEN '0' THEN 'Tidak'
                           WHEN '1' THEN 'Ya'
                      END AS rtd_is_canceled, rtd_notes
			   		  FROM item_detail 
               		  INNER JOIN master_item ON master_item.masti_id=item_detail.masti_id
               		  INNER JOIN category_item ON category_item.cati_id=master_item.cati_id 
               		  INNER JOIN receipt_transfer_detail ON receipt_transfer_detail.itemd_id=item_detail.itemd_id
               		  INNER JOIN receipt_transfer_header ON receipt_transfer_header.rth_id=receipt_transfer_detail.rth_id
					  INNER JOIN transfer_header ON transfer_header.tth_id=receipt_transfer_header.tth_id
               		  WHERE receipt_transfer_header.branch_id='$branch_id' AND rth_date<='$as_of_date' AND
			         		  item_detail.itemd_id IN ($item_list)
			   		  ORDER BY rth_code DESC";	
  $exec_get_report=mysqli_query($db_connection,$q_get_report);
  if (mysqli_num_rows($exec_get_report)>0)
     {
	   $filename='List_Of_Transfer_Report.xls';
       $header="LAPORAN PERPINDAHAN ASET (MASUKAN)"."\n";
	   $as_of_dates="AS Of Date : ".$as_of_date."\n\n";
       $date='Retrived Date : '.date("d-m-Y")."\n"; 
	   $rows_data="No Transaksi"."\t"."No Referensi"."\t"."Tanggal"."\t"."Kode Item"."\t"."Nama Item"."\t"."Serial"."\t"."Kapasitas"."\t"."Kategori"."\t"."Qty"."\t"."Kantor Cabang Asal"."\t"."Lokasi Gudang"."\t"."Dibatalkan"."\t"."Keterangan"."\n"; 
	   while ($field_data=mysqli_fetch_array($exec_get_report))
			 {
			   if ($field_data['rth_is_canceled']=='Ya')
				   $is_canceled=$field_data['rth_is_canceled'];
			   else 
				   $is_canceled=$field_data['rtd_is_canceled'];
			   $rows_data=$rows_data.$field_data['rth_code']."\t".$field_data['tth_code']."\t".$field_data['rth_date']."\t".$field_data['itemd_code']."\t".$field_data['masti_name']."\t".$field_data['itemd_serial_no']."\t".$field_data['masti_capacity']." ".$field_data['uom_id_1']."\t".$field_data['cati_name']."\t".$field_data['itemd_qty']." ".$field_data['uom_id_2']."\t".$field_data['branch_name_from']."\t".$field_data['whsl_id_new']."\t".$is_canceled."\t".$field_data['rtd_notes']."\n";
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