<?php
  include "../library/check_session.php";
  $profile='1';
  
  // Initialize authorization variables if not set
  if (!isset($ctg_autho)) $ctg_autho = 'N';
  if (!isset($transfer_autho)) $transfer_autho = 'N';
  if (!isset($issuing_report_autho)) $issuing_report_autho = 'N';
  if (!isset($employee_autho)) $employee_autho = 'N';
?>
<html>
    <head>
        <style>
            #navigation {
                background-color : black;
                width : 100%;
                height : 40px;
                border-bottom : 1px solid #ccc;
            }
            
            #navigation ul {
                padding:0;
                margin:0;
                list-style-type:none;
            }
             
            #navigation ul li {
                float:left;
                position : relative;
            }
             
            #navigation ul li a {
                display:block;
                padding:12px 10px;
                color:#fff;
                text-decoration:none;
                font-family: calibri;
				font-size:12px;
            }
             
            #navigation ul li a:hover {
                background-color:#72b626;
            }
             
            /* Menu Dropdown */
            
            #navigation ul li ul {
                display: none;
            }
             
            #navigation ul li:hover ul {
                display:block;
                position: absolute;
            }
            
            #navigation ul li:hover ul li a {
                display:block;
                background-color : black;
                color : #fff;
                width : 180px;
                border-bottom : 1px solid #ccc;
            }
            
            #navigation ul li:hover ul li a:hover {
                background-color : #72b626;
            }
            
            #navigation ul li:hover > a {
			    background: #72b626;
	  	    }
        </style>
        
    </head>
    <body>
    <div id="navigation">
        <ul id="nav">
		    <li><a href="../index/index.php">Beranda</a>
            </li>
			  <?php
			    if ($branch_autho!='N' || $whs_autho!='N')
				   { 
                     echo "<li><a href=''>Perusahaan</a>";
                     echo    "<ul>";
               /*      if ($comp_autho!="N")
					     echo  "<li><a href='?page=company' title='Company'>Perusahaan</a></li>";   */
					 if ($branch_autho!='N')	 
					     echo  "<li><a href='?page=branch' title='Branch Office'>Kantor Cabang</a></li>";
					 if ($whs_autho!='N')	 
                         echo  "<li><a href='?page=whs' title='Warehouse Location'>Lokasi Gudang</a></li>";
                     echo   "</ul>";
                     echo "</li>";
				  }	
			   if ($ctg_autho!='N' || $masti_autho!='N' || $itemd_autho!='N' || $itemd_all_autho!='N' || $summary_item_autho!='N')
			      {
			        echo "<li><a href=''>Aset</a>";
                    echo "<ul>";
					if ($ctg_autho!='N')
					   echo "<li><a href='?page=category-item' title='Item Category'>Kategori Aset</a></li>";
			        if ($masti_autho!='N')
					    echo "<li><a href='?page=master-item' title='Deskripsi Isi Aset'>Deskripsi Isi Aset</a></li>";
			        if ($itemd_autho!='N')
					    echo "<li><a href='?page=item-detail' title='Daftar Aset'>Aset</a></li>";
				    if ($itemd_all_autho!='N')
					    echo "<li><a href='?page=item-detail-all' title='Daftar Aset Semua Cabang'>Aset Semua Cabang</a></li>";
				/*	if (isset($_SESSION['ses_super_admin'])=="yes")	
					    echo "<li><a href='?page=item-detail-all' title='Daftar Aset Semua Cabang'>Aset Semua Cabang</a></li>"; */
					if ($summary_item_autho!='N')
					    echo "<li><a href='?page=summary-item' title='Summary Aset'>Summary Aset</a></li>";
                    echo "</ul>";
                    echo "</li>";
				  }
			  if ($transfer_autho!='N' || $receipt_transfer_autho!='N' ||$issuing_autho!='N' || $returning_autho!='N' || $broken_autho!='N' || $write_off_autho!='N' || 
			      $dispossal_autho!='N' || $change_description_autho!='N' )
			     {
			       echo "<li><a href=''>Transaksi</a>";
                   echo "<ul>";
				   if ($transfer_autho!='N')
                       echo "<li><a href='?page=transfer' title='Transfer'>Transfer Posisi Aset</a></li>";
				   if ($receipt_transfer_autho!='N')
                       echo "<li><a href='?page=receipt-transfer' title='Penerimaan Transfer Posisi Aset'>Penerimaan Transfer Posisi Aset</a></li>";
				   if ($issuing_autho!='N')
                       echo "<li><a href='?page=issuing' title='Issuing'>Pengeluaran Aset</a></li>";
				   if ($returning_autho!='N')	  
					   echo "<li><a href='?page=return' title='Returning'>Pengembalian Aset</a></li>";
				   if ($broken_autho!='N')		
					   echo "<li><a href='?page=broken' title='Broken'>Aset Rusak</a></li>";
				   if ($write_off_autho!='N') 
					   echo "<li><a href='?page=write-off' title='Write Off'>Penghapusan Aset</a></li>";	   
			       if ($dispossal_autho!='N') 
					   echo "<li><a href='?page=dispossal' title='Dispossal'>Penjualan Aset</a></li>";
				   if ($change_description_autho!='N')	
					   echo "<li><a href='?page=change-description' title='Change Description'>Perubahan Deskripsi Isi Aset</a></li>";
                   echo "</ul>";
                   echo "</li>";
				 }     
			  if ($issuing_report_autho!='N' || $returning_report_autho!='N' || $broken_report_autho!='N' || $write_off_report_autho!='N' || $dispossal_report_autho!='N' ||  
			      $cid_report_autho!='N' || $position_report_autho!='N' || $aging_report_autho!='N' || $vendor_item_report_autho!='N' || $history_movement_report_autho!='N' || 
				  $transfer_report_autho!='N' || $doc_flow_report_autho!='N' || $soa_report_autho!='N')
			     {
			       echo "<li><a href=''>Laporan</a>";
                   echo "<ul>";
				   if ($transfer_report_autho!='N')	
					   echo "<li><a href='?page=transfer-report' title='Transfer Report'>Laporan Perpindahan Aset</a></li>";
				   if ($issuing_report_autho!='N')
                       echo "<li><a href='?page=issuing-report' title='Report Issuing'>Laporan Pengeluaran Aset</a></li>";
				   if ($returning_report_autho!='N')	  
					   echo "<li><a href='?page=returning-report' title='Report Returning'>Laporan Pengembalian Aset</a></li>";
				   if ($broken_report_autho!='N')		
					   echo "<li><a href='?page=broken-report' title='Report Broken'>Laporan Aset Rusak</a></li>";
				   if ($write_off_report_autho!='N') 
					   echo "<li><a href='?page=write-off-report' title='Report Write Off'>Laporan Penghapusan Aset</a></li>";
			       if ($dispossal_report_autho!='N') 
					   echo "<li><a href='?page=dispossal-report' title='Report Dispossal'>Laporan Penjualan Aset</a></li>";
				   if ($cid_report_autho!='N')	
					   echo "<li><a href='?page=change-description-report' title='Report Change Description'>Laporan Perubahan Deskripsi Isi Aset</a></li>";
				   if ($position_report_autho!='N')	
					   echo "<li><a href='?page=position-report' title='Report Position of Bottle'>Laporan Posisi Aset</a></li>";   
				   if ($aging_report_autho!='N')	
					   echo "<li><a href='?page=aging-report' title='Report Customer Aging'>Laporan Aging Pelanggan</a></li>";
				   if ($soa_report_autho!='N')	
					   echo "<li><a href='?page=soa-report' title='Report Statement Of Account'>Laporan SOA</a></li>";
				   if ($vendor_item_report_autho!='N')	
					   echo "<li><a href='?page=vendor-item-report' title='Report Vendor - Aset'>Laporan Pengeluaran Aset Ke Vendor</a></li>";
				   if ($history_movement_report_autho!='N')	
					   echo "<li><a href='?page=history-movement-report' title='History Movement Report'>Laporan Pergerakan Aset</a></li>";	
				   if ($doc_flow_report_autho!='N')	
					   echo "<li><a href='?page=document-flow-report' title='History Document Flow Report'>Laporan Histori Transaksi</a></li>";	
                   echo "</ul>";
                   echo "</li>";
				 }    
			  if ($employee_autho!='N' || $uom_autho!='N' || $customer_type_autho!='N' || $customer_autho!='N' || $vendor_autho!='N' || 
			     (isset($_SESSION['ses_super_admin'])=="yes") || 
			      $profile=='1')
			     { 
		  	       echo "<li><a href=''>Setting</a>";
                   echo  "<ul>";
				   if ($employee_autho!='N') 
					    echo "<li><a href='?page=employee' title='Employee'>Karyawan</a></li>";
                   if ($uom_autho!='N') 
					   echo  "<li><a href='?page=uom' title='UOM'>Satuan Unit</a></li>";
				   if ($customer_type_autho!='N')	
					   echo "<li><a href='?page=customer-type' title='Customer Type'>Tipe Pelanggan</a></li>";
				   if ($customer_autho!='N')	
					   echo "<li><a href='?page=customer' title='Customer'>Pelanggan</a></li>";
				   if ($vendor_autho!='N')	
					   echo "<li><a href='?page=vendor' title='Vendor'>Vendor</a></li>";
				   echo "<li><a href='#' title='Profile' onclick='call_profile()'>Profile</a></li>"; 
				   if (isset($_SESSION['ses_super_admin'])=="yes")	
					   echo "<li><a href='?page=users' title='User'>User</a></li>";
				   if ($apt_period_autho!='N')	
					   echo "<li><a href='?page=period' title='Period Transaksi'>Period Transaksi</a></li>";
                   echo "</ul>";
                   echo "</li>";
				  }
			?> 	   
        </ul>
    </div>
    </body>
    </html>
 <script language="javascript">
    function call_profile()
	         {
			   var w=400;
			   var h=300;
			   var l=(screen.width/2)-(w/2);
			   var t=(screen.height/2)-(h/2);
			   id="<?php if (isset($_SESSION['ses_user_id']))
			                 echo $_SESSION['ses_user_id'];
						 else
						     echo "";
			      ;?>";
			   if (id=='')
			      {
				    alert('User tidak terdaftar!');
				  }
			   else
			      { 
			        open_child=window.open('../data/users/users_profile.php?id='+id, 'f_frofile', 'height='+h+', width='+w+', left='+l+', top='+t+', toolbar=0');
				  } 
			 }
 </script>	
	
	