<?php
 // Suppress warnings for undefined array keys
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
 
 $clean = fopen('error_log', 'w');
 fwrite($clean,'');
 fclose($clean);

 if(!isset($_SESSION))
    session_start();
	
 include "db_connection.php";
 if ((isset($_GET['page']) && $_GET['page']!='log-in') || isset($_SESSION['ses_siaptek_admin']))
    {
      if (!isset($_SESSION['ses_siaptek_admin']))
         {
	       echo "<meta http-equiv='refresh' content='0; url=/siaptek/inventory/script/index/index.php?page=log-in'>";
		   exit;
	     }
	  else
	     {  
		   if (isset($_SESSION['ses_branch']))
		       $branch_autho=$_SESSION['ses_branch'];
		   if (isset($_SESSION['ses_whs']))
		       $whs_autho=$_SESSION['ses_whs'];
		   if (isset($_SESSION['ses_item_category']))
		       $ctg_autho=$_SESSION['ses_item_category'];
		   if (isset($_SESSION['ses_master_item']))
		       $masti_autho=$_SESSION['ses_master_item'];
		   if (isset($_SESSION['ses_item_detail']))
		       $itemd_autho=$_SESSION['ses_item_detail'];
		   if (isset($_SESSION['ses_item_detail_all']))
		       $itemd_all_autho=$_SESSION['ses_item_detail_all'];
		   if (isset($_SESSION['ses_summary_item']))
		       $summary_item_autho=$_SESSION['ses_summary_item'];
		   if (isset($_SESSION['ses_transfer_item']))
		       $transfer_autho=$_SESSION['ses_transfer_item'];
		   if (isset($_SESSION['ses_receipt_transfer_item']))
		       $receipt_transfer_autho=$_SESSION['ses_receipt_transfer_item'];
		   if (isset($_SESSION['ses_issuing']))
		       $issuing_autho=$_SESSION['ses_issuing'];
		   if (isset($_SESSION['ses_returning']))
		       $returning_autho=$_SESSION['ses_returning'];
		   if (isset($_SESSION['ses_broken']))
		       $broken_autho=$_SESSION['ses_broken'];
		   if (isset($_SESSION['ses_write_off']))
		       $write_off_autho=$_SESSION['ses_write_off'];
		   if (isset($_SESSION['ses_dispossal']))
		       $dispossal_autho=$_SESSION['ses_dispossal'];
		   if (isset($_SESSION['ses_change_description']))
		       $change_description_autho=$_SESSION['ses_change_description'];
		   if (isset($_SESSION['ses_issuing_report']))
		       $issuing_report_autho=$_SESSION['ses_issuing_report'];
		   if (isset($_SESSION['ses_returning_report']))
		       $returning_report_autho=$_SESSION['ses_returning_report'];
		   if (isset($_SESSION['ses_broken_report']))
		       $broken_report_autho=$_SESSION['ses_broken_report'];
		   if (isset($_SESSION['ses_write_off_report']))
		       $write_off_report_autho=$_SESSION['ses_write_off_report'];
		   if (isset($_SESSION['ses_dispossal_report']))
		       $dispossal_report_autho=$_SESSION['ses_dispossal_report'];
		   if (isset($_SESSION['ses_change_description_report']))
		       $cid_report_autho=$_SESSION['ses_change_description_report'];
		   if (isset($_SESSION['ses_position_report']))
		       $position_report_autho=$_SESSION['ses_position_report'];
		   if (isset($_SESSION['ses_aging_report']))
		       $aging_report_autho=$_SESSION['ses_aging_report'];
		   if (isset($_SESSION['ses_soa_report']))
		       $soa_report_autho=$_SESSION['ses_soa_report'];
		   if (isset($_SESSION['ses_vendor_item_report']))
		       $vendor_item_report_autho=$_SESSION['ses_vendor_item_report'];
		   if (isset($_SESSION['ses_history_movement_report']))
		       $history_movement_report_autho=$_SESSION['ses_history_movement_report'];
		   if (isset($_SESSION['ses_transfer_report']))
		       $transfer_report_autho=$_SESSION['ses_transfer_report'];
		   if (isset($_SESSION['ses_doc_flow_report']))
		       $doc_flow_report_autho=$_SESSION['ses_doc_flow_report'];
		   if (isset($_SESSION['ses_employee']))   
		       $employee_autho=$_SESSION['ses_employee'];
		   if (isset($_SESSION['ses_uom']))
		       $uom_autho=$_SESSION['ses_uom'];
		   if (isset($_SESSION['ses_customer_type']))
		       $customer_type_autho=$_SESSION['ses_customer_type'];
		   if (isset($_SESSION['ses_customer']))
		       $customer_autho=$_SESSION['ses_customer'];
		   if (isset($_SESSION['ses_vendor']))
		       $vendor_autho=$_SESSION['ses_vendor'];
		   if (isset($_SESSION['ses_apt_period']))
		       $apt_period_autho=$_SESSION['ses_apt_period'];
		   
		   // Only query branch if ses_id_branch is set
		   if (isset($_SESSION['ses_id_branch'])) {
		       $q_get_branch="SELECT branch_id, branch_code, branch_name
                              FROM branch
                              WHERE branch_id='".$_SESSION['ses_id_branch']."'";					
		       $exec_branch=mysqli_query($db_connection, $q_get_branch);
		       if ($exec_branch) {
		           $field_branch=mysqli_fetch_array($exec_branch);
		       }
		   }
		 }	   
	}
 else
   {
     $branch_autho='N';
     $whs_autho='N';
	 $ctg_autho='N';
	 $masti_autho='N';
	 $itemd_autho='N';
	 $itemd_all_autho='N';
	 $summary_item_autho='N';
	 $transfer_autho='N';
	 $receipt_transfer_autho='N';
	 $issuing_autho='N';
	 $returning_autho='N';
	 $broken_autho='N';
     $write_off_autho='N';
	 $dispossal_autho='N';
	 $change_description_autho='N';
	 $issuing_report_autho='N';
	 $returning_report_autho='N';
	 $broken_report_autho='N';
	 $write_off_report_autho='N';
	 $dispossal_report_autho='N';
	 $cid_report_autho='N';
	 $position_report_autho='N';
	 $aging_report_autho='N';
	 $soa_report_autho='N';
     $vendor_item_report_autho='N';
	 $history_movement_report_autho='N';
	 $transfer_report_autho='N';
	 $doc_flow_report_autho='N';
	 $employee_autho='N';
	 $uom_autho='N';
	 $customer_type_autho='N';
	 $customer_autho='N';
	 $vendor_autho='N';
	 $apt_period_autho='N';
   }
?>