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
	 
	 
  $q_get_data="SELECT ttd_id, transfer_header.tth_code, tth_date, item_detail.itemd_id, itemd_code, masti_name, itemd_capacity, uom_name, 
                      itemd_serial_no, itemd_acquired_date, itemd_qty, cati_name, whsl_id_from, 
                     (SELECT whsl_name FROM warehouse_location WHERE whsl_id=whsl_id_from AND branch_id='$branch_id') AS whsl_id_from, branch_id_to,
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
	$exec_get_data=mysqli_query($db_connection, $q_get_data); 
  //echo $item_list."<br>";
  //echo $q_get_data;
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
      <td scope="col"><h2>LAPORAN PERPINDAHAN ASET (<?PHP echo $type_1;?>)</h2></td>
      <td align="right">Kantor Cabang    :
        <select id="s_branch" name="s_branch" style="width:300px">
          <option value="<?php echo $field_branch['branch_id'];?>"><?php echo  $field_branch['branch_code']." - ".$field_branch['branch_name'];?></option>
        </select></td>
    </tr>
    <tr>
      <td width="58%" scope="col">Per Tanggal  : <?php echo get_date_1($as_of_date);?></td>
      <td width="41%" align="right"><input type="button" name="btn_back" value="Kembali" onclick="call_back()"/></td>
    </tr>

    <tr>
      <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="table-list">
        <tr bgcolor="#CCCCCC">
          <th width="2%" scope="col" class="th_ltb"><input type="checkbox" id="check_all_data" name="check_all_data" value="1" onclick="select_unselect_all()"/></th>
          <th width="2%" scope="col" class="th_ltb">No</th>
          <th width="11%" scope="col" class="th_ltb">No Transaksi</th>
          <th width="5%" scope="col" class="th_ltb">Tanggal</th>
          <th width="8%" scope="col" class="th_ltb">Kode Aset </th>
          <th width="15%" scope="col" class="th_ltb">Deskripsi Isi Aset </th>
          <th width="4%" scope="col" class="th_ltb">Serial</th>
          <th width="6%" scope="col" class="th_ltb">Kapasitas</th>
          <th width="7%" scope="col" class="th_ltb">Kategori</th>
          <th width="4%" scope="col" class="th_ltbr">Qty</th>
          <th width="7%" scope="col" class="th_ltbr">Lokasi Asal</th>
          <th width="13%" scope="col" class="th_ltbr">Kantor Cabang Tujuan</th>
          <th width="6%" scope="col" class="th_ltbr">Status</th>
          <th width="6%" scope="col" class="th_ltbr">Dibatalkan</th>
          <th width="10%" scope="col" class="th_ltbr">Keterangan</th>
          </tr>
		  <?php
			$no=0;
		    while ($data=mysqli_fetch_array($exec_get_data))
				  {
					$no++;
					if ($data['tth_is_canceled']=='Ya')
					    $is_canceled=$data['tth_is_canceled'];
					else 
					    $is_canceled=$data['ttd_is_canceled'];
   		  ?>
        <tr>
          <td align="center" class="td_lb"><input type="checkbox"  id ="check_data[]" name="check_data[]" value="<?php echo $data['ttd_id'];?>" /></td>
          <td align="center" class="td_lb"><?php echo $no;?></td>
          <td class="td_lb"><?php echo $data['tth_code'];?></td>
          <td class="td_lb"><?php echo get_date_1($data['tth_date']);?></a></td>
          <td class="td_lb"><?php echo $data['itemd_code'];?></a></td>
          <td class="td_lb"><?php echo $data['masti_name'];?></td>
		  <td class="td_lb"><?php echo $data['itemd_serial_no'];?></td>
          <td class="td_lb"><?php echo $data['itemd_capacity']." ".$data['uom_name'];?></td>
          <td class="td_lb"><?php echo $data['cati_name'];?></td>
          <td class="td_lb"><?php echo $data['itemd_qty']." Cylinder";?></td>
          <td class="td_lb"><?php echo $data['whsl_id_from'];?></td>
          <td class="td_lb"><?php echo $data['branch_name_to'];?></td>
          <td class="td_lbr"><?php echo $data['ttd_status'];?></td>
          <td class="td_lbr"><?php echo $is_canceled;?></td>
          <td class="td_lbr"><?php echo $data['ttd_notes'];?></td>
          </tr><?php } ?>
      </table></td>
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
