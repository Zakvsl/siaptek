<?php
 include "../../library/check_session.php";
 $branch_id=$_SESSION['ses_id_branch'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title></head>
<body onFocus="disable_parent_window();" onclick="disable_parent_window();">
<?php
  include "../../library/style.css";
  include "../../library/db_connection.php";
  include "../../library/library_function.php";
  $trans_type=$_POST['s_transaction_type'];
  $trans_no=$_POST['txt_transaction_no'];
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
                    WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM issuing_header 
					      WHERE issuingh_code='$trans_no' AND branch_id='$branch_id')
                    ORDER BY issuingh_code DESC, issuingh_date DESC";
	   $q_get_data_1="SELECT return_header.issuingh_id, reth_id, reth_code, reth_date, 
                             CASE reth_is_canceled 
							      WHEN '0' THEN 'Tidak' 
								  WHEN '1' THEN 'Ya' 
						     END reth_is_canceled, reth_notes 
                       FROM return_header 
                       WHERE return_header.branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM issuing_header 
					         WHERE issuingh_code='$trans_no' AND branch_id='$branch_id')
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
                    WHERE return_header.branch_id='$branch_id' AND issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND reth_code='$trans_no'
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
                    WHERE issuing_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND issuingh_id=(SELECT issuingh_id FROM return_header WHERE 
					      reth_code='$trans_no' AND branch_id='$branch_id')
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
                      WHERE dispossal_header.branch_id='$branch_id' AND customer.branch_id='$branch_id' AND 
					        brokh_id=(SELECT brokh_id FROM broken_header WHERE brokh_code='$trans_no' AND branch_id='$branch_id')                      
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
  $field_data=mysqli_fetch_array($exec_get_data);
?>

<style>
            #action {
                background-color : black;
                width : 120px;
                height : 28px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul {
                padding:0;
                margin:0;
                list-style-type:none;
            }
             
            #action ul li {
                float:left;
                position : relative;
            }
             
            #action ul li a {
                display:block;
                padding:5px 10px;
                color:#fff;
                text-decoration:none;
                font-family: calibri;
				font-size:14px;
            }
             
            #action ul li a:hover {
                background-color:#72b626;
            }
             
            /* Menu Dropdown */
            
            #action ul li ul {
                display: none;
            }
             
            #action ul li:hover ul {
                display:block;
                position: absolute;
            }
            
            #action ul li:hover ul li a {
                display:block;
                background-color : black;
                color : #fff;
                width : 100px;
                border-bottom : 1px solid #ccc;
            }
            
            #action ul li:hover ul li a:hover {
                background-color : #72b626;
            }
            
            #action ul li:hover > a {
			    background: #72b626;
	  	    }
        </style>
		
<form id="f_customer" name="f_customer" method="post" action="">
  <table width="100%" border="0" cellspacing="1" cellpadding="2">
    <tr>
      <td scope="col"><h2>LAPORAN HISTORI TRANSAKSI (<?php echo $transaction_name;?>)</h2></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td width="58%" scope="col">&nbsp;</td>
      <td width="41%" align="right"><input type="button" name="btn_back" value="Kembali" onclick="call_back()"/></td>
    </tr>
    <tr>
      <td colspan="2">
	  <?php
	    if ($trans_type=='1')
		   { 
	  ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="6%">Status</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['tth_code'];?></td></td><td><?php echo get_date_1($field_data['tth_date']);?></td><td><?php echo $field_data['tth_status'];?></td><td><?php echo $field_data['tth_is_canceled'];?></td><td><?php echo $field_data['tth_notes'];?></td></tr>
			   <tr><td></td>
			       <td colspan="4"><br /><b>DETAIL TRANSAKSI REFERENSI</b>
	                  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="8%" scope="col" class="th_ltb">Kantor Cabang</th>
                          <th width="5%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_1['branch_name'];?></td>
                            <td class="td_lb"><?php echo $field_data_1['rth_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['rth_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['rth_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['rth_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='2')
		   { 
	  ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="7%">Kantor Cabang Asal</th><th width="6%">No Transaksi</th><th width="4%">Tanggal</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th>
			   </tr>
			   <tr><td><?php echo $field_data['branch_name'];?></td><td><?php echo $field_data['rth_code'];?></td></td><td><?php echo get_date_1($field_data['rth_date']);?></td><td><?php echo $field_data['rth_is_canceled'];?></td><td><?php echo $field_data['rth_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="4"><br /><b>DETAIL SUMBER TRANSAKSI</b>
	                  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="8%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="5%" scope="col" class="th_ltb">Tanggal</th>
                          <th width="7%" scope="col" class="th_ltb">Status</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_1['tth_code'];?></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['tth_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['tth_status'];?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['tth_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['tth_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='3')
		   { 
	  ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="10%">Nama Customer/Vendor</th><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="6%">Status</th><th width="6%">Dibatalkan</th><th width="6%">Tipe</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['cust_name'];?></td><td><?php echo $field_data['issuingh_code'];?></td></td><td><?php echo get_date_1($field_data['issuingh_date']);?></td><td><?php echo $field_data['issuingh_status'];?></td><td><?php echo $field_data['issuingh_is_canceled'];?></td><td><?php echo $field_data['issuingh_type'];?></td><td><?php echo $field_data['issuingh_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="6"><br /><b>DETAIL TRANSAKSI PENGEMBALIAN</b>
	                  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="5%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_1['reth_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['reth_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['reth_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['reth_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='4')
		   { 
	  ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="10%">Nama Customer/Vendor</th><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['cust_name'];?></td><td><?php echo $field_data['reth_code'];?></td><td><?php echo get_date_1($field_data['reth_date']);?></td><td><?php echo $field_data['reth_is_canceled'];?></td><td><?php echo $field_data['reth_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="4"><br /><b>DETAIL SUMBER TRANSAKSI</b>
	                  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="5%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
						  <th width="8%" scope="col" class="th_ltb">Status</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
						  <th width="4%" scope="col" class="th_ltb">Tipe</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_1['issuingh_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['issuingh_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['issuingh_status'];?></td>
							<td class="td_lb"><?php echo $field_data_1['issuingh_is_canceled'];?></td>
							<td class="td_lb"><?php echo $field_data_1['issuingh_type'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['issuingh_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='5')
		   { 
	     ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="6%">Status</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['brokh_code'];?></td></td><td><?php echo get_date_1($field_data['brokh_date']);?></td><td><?php echo $field_data['brokh_status'];?></td><td><?php echo $field_data['brokh_is_canceled'];?></td><td><?php echo $field_data['brokh_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="5"><br /><b>DETAIL TRANSAKSI PENGHAPUSAN</b>
	                  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="5%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_1['woh_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['woh_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['woh_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['woh_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
					  <br /><b>DETAIL TRANSAKSI PENJUALAN</b>
					  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
                          <th width="13%" scope="col" class="th_ltb">Nama Customer</th>
						  <th width="8%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_2=mysqli_fetch_array($exec_get_data_2))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
                            <td class="td_lb"><?php echo $field_data_2['cust_name'];?></a></td>
							<td class="td_lb"><?php echo $field_data_2['disph_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_2['disph_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_2['disph_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_2['disph_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='6')
		   { 
	     ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="7%">Sumber Transaksi</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['woh_code'];?></td></td><td><?php echo get_date_1($field_data['woh_date']);?></td><td><?php echo $field_data['woh_sources'];?></td><td><?php echo $field_data['woh_is_canceled'];?></td><td><?php echo $field_data['woh_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="4"><br /><b>DETAIL TRANSAKSI REFERENSI</b>
					  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
						  <th width="8%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
						  <th width="4%" scope="col" class="th_ltb">Status</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
							<td class="td_lb"><?php echo $field_data_1['brokh_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['brokh_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['brokh_status'];?></td>
							<td class="td_lb"><?php echo $field_data_1['brokh_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['brokh_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		else
		if ($trans_type=='7')
		   { 
	     ?> 
	         <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
			   <tr><th width="7%">No Transaksi</th><th width="4%">Tanggal</th><th width="7%">Sumber Transaksi</th><th width="6%">Dibatalkan</th><th width="40%">Keterangan</th></tr>
			   <tr><td><?php echo $field_data['disph_code'];?></td></td><td><?php echo get_date_1($field_data['disph_date']);?></td><td><?php echo $field_data['disph_sources'];?></td><td><?php echo $field_data['disph_is_canceled'];?></td><td><?php echo $field_data['disph_notes'];?></td></tr>
			   <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			       <td colspan="4"><br /><b>DETAIL TRANSAKSI REFERENSI</b>
					  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
                        <tr bgcolor="#CCCCCC">
                          <th width="2%" scope="col" class="th_ltb">No</th>
						  <th width="5%" scope="col" class="th_ltb">Kode Transaksi</th>
                          <th width="4%" scope="col" class="th_ltb">Tanggal</th>
						  <th width="4%" scope="col" class="th_ltb">Status</th>
                          <th width="5%" scope="col" class="th_ltb">Dibatalkan</th>
                          <th width="40%" scope="col" class="th_ltbr">Keterangan</th>
                        </tr>
		                  <?php
			                 $no=0;
		                     while ($field_data_1=mysqli_fetch_array($exec_get_data_1))
				                   {
					                 $no++;
   		                  ?>
                        <tr>
                            <td align="center" class="td_lb"><?php echo $no;?></td>
							<td class="td_lb"><?php echo $field_data_1['brokh_code'];?></a></td>
                            <td class="td_lb"><?php echo get_date_1($field_data_1['brokh_date']);?></a></td>
                            <td class="td_lb"><?php echo $field_data_1['brokh_status'];?></td>
							<td class="td_lb"><?php echo $field_data_1['brokh_is_canceled'];?></td>
		                    <td class="td_lbr"><?php echo $field_data_1['brokh_notes'];?></td>
                        </tr><?php 
						           } 
							 ?>
				      </table>
			       </td>
			   </tr>
		     </table>
	     <?php
		   }
		 ?>
	    </td>
        <td width="0%" align="left">&nbsp;</td>
        <td width="1%" align="right">&nbsp;</td>
    </tr>
  </table>
</form>

<script language="javascript">
   function call_back()
            {
			  history.back();
			}
</script>

</body>
</html>
