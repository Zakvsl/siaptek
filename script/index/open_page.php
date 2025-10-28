<?php
 // Show login page if not logged in and no page parameter
 if (!isset($_SESSION['ses_siaptek_admin']) && !isset($_GET['page'])) {
     if (file_exists("../data/log_in_out/log_in.php")) {
         include "../data/log_in_out/log_in.php";
     }
 }
 else if ($_GET)
    {
	  switch ($_GET['page'])
	         {
			   case '': if(!file_exists("../script/index/index.php")) die ("Empty main page!");
			            include "index.php";
			   break;
			   
			   case 'company': {  
			                  if (!file_exists("../data/company/data_company.php")) die ("Empty Company page!");
				                  include "../data/company/data_company.php";	   
						   }		   
			   break;
			   case 'branch': {  
			                  if (!file_exists("../data/branch/data_branch.php")) die ("Empty Branch page!");
				                  include "../data/branch/data_branch.php";	   
						   }		   
			   break;
			   case 'whs': {  
			                  if (!file_exists("../data/whs/data_whs.php")) die ("Empty Warehouse page!");
				                  include "../data/whs/data_whs.php";	   
						   }		   
			   break;
			   case 'category-item': {  
			                  if (!file_exists("../data/category_item/data_category_item.php")) die ("Empty Item Category page!");
				                  include "../data/category_item/data_category_item.php";	   
						   }		   
			   break;
			   case 'master-item': {  
			                  if (!file_exists("../data/master_item/data_master_item.php")) die ("Empty Master Item page!");
				                  include "../data/master_item/data_master_item.php";	   
						   }		   
			   break;
			   case 'item-detail': {  
			                  if (!file_exists("../data/item_detail/data_item_detail.php")) die ("Empty Item Detail page!");
				                  include "../data/item_detail/data_item_detail.php";	   
						   }		   
			   break;
			   case 'item-detail-all': {  
			                  if (!file_exists("../data/item_detail_all/data_item_detail_all.php")) die ("Empty Item Detail All Branch page!");
				                  include "../data/item_detail_all/data_item_detail_all.php";	   
						   }		   
			   break;
			   case 'summary-item': {  
			                  if (!file_exists("../data/summary_item/data_summary_item.php")) die ("Empty Summary Item page!");
				                  include "../data/summary_item/data_summary_item.php";	   
						   }		   
			   break;
			   case 'transfer': {  
			                  if (!file_exists("../data/transfer/data_transfer.php")) die ("Empty Transfer page!");
				                  include "../data/transfer/data_transfer.php";	   
						   }		   
			   break;
			   case 'receipt-transfer': {  
			                  if (!file_exists("../data/receipt_transfer/data_receipt_transfer.php")) die ("Empty Receipt Transfer page!");
				                  include "../data/receipt_transfer/data_receipt_transfer.php";	   
						   }		   
			   break;
			   case 'issuing': {  
			                  if (!file_exists("../data/issuing/data_issuing.php")) die ("Empty Issuing page!");
				                  include "../data/issuing/data_issuing.php";	   
						   }		   
			   break;
			   case 'return': {  
			                  if (!file_exists("../data/return/data_return.php")) die ("Empty Returning page!");
				                  include "../data/return/data_return.php";	   
						   }		   
			   break;
			   case 'broken': {  
			                  if (!file_exists("../data/broken/data_broken.php")) die ("Empty Broken page!");
				                  include "../data/broken/data_broken.php";	   
						   }		   
			   break;
			   case 'write-off': {  
			                  if (!file_exists("../data/write_off/data_write_off.php")) die ("Empty Write Off page!");
				                  include "../data/write_off/data_write_off.php";	   
						   }		   
			   break;
			   case 'dispossal': {  
			                  if (!file_exists("../data/dispossal/data_dispossal.php")) die ("Empty Dispossal page!");
				                  include "../data/dispossal/data_dispossal.php";	   
						   }		   
			   break;
			   case 'change-description': {  
			                  if (!file_exists("../data/change_description/data_change_description.php")) die ("Empty Change Description page!");
				                  include "../data/change_description/data_change_description.php";	   
						   }		   
			   break;
			   case 'transfer-report': {  
			                  if (!file_exists("../data/transfer_report/data_transfer_report.php")) die ("Empty Transfer Report page!");
				                  include "../data/transfer_report/data_transfer_report.php";	   
						   }		   
			   break;
			   case 'issuing-report': {  
			                  if (!file_exists("../data/issuing_report/data_issuing_report.php")) die ("Empty Issuing Report page!");
				                  include "../data/issuing_report/data_issuing_report.php";	   
						   }		   
			   break;
			   case 'returning-report': {  
			                  if (!file_exists("../data/returning_report/data_returning_report.php")) die ("Empty Returning Report page!");
				                  include "../data/returning_report/data_returning_report.php";	   
						   }		   
			   break;
			   case 'broken-report': {  
			                  if (!file_exists("../data/broken_report/data_broken_report.php")) die ("Empty Broken Report page!");
				                  include "../data/broken_report/data_broken_report.php";	   
						   }		   
			   break;
			   case 'write-off-report': {  
			                  if (!file_exists("../data/write_off_report/data_write_off_report.php")) die ("Empty Write Off Report page!");
				                  include "../data/write_off_report/data_write_off_report.php";	   
						   }		   
			   break;
			   case 'dispossal-report': {  
			                  if (!file_exists("../data/dispossal_report/data_dispossal_report.php")) die ("Empty Dispossal Report page!");
				                  include "../data/dispossal_report/data_dispossal_report.php";	   
						   }		   
			   break;
			   case 'change-description-report': {  
			                  if (!file_exists("../data/change_description_report/data_change_description_report.php")) die ("Empty Change Description Report page!");
				                  include "../data/change_description_report/data_change_description_report.php";	   
						   }		   
			   break;
			   case 'position-report': {  
			                  if (!file_exists("../data/position_report/data_position_report.php")) die ("Empty Position Report page!");
				                  include "../data/position_report/data_position_report.php";	   
						   }		   
			   break;
			   case 'aging-report': {  
			                  if (!file_exists("../data/aging_report/data_aging_report.php")) die ("Empty Aging Report page!");
				                  include "../data/aging_report/data_aging_report.php";	   
						   }		   
			   break;
			   case 'soa-report': {  
			                  if (!file_exists("../data/soa_report/data_soa_report.php")) die ("Empty Statement Of Account Report page!");
				                  include "../data/soa_report/data_soa_report.php";	   
						   }		   
			   break;
			   case 'vendor-item-report': {  
			                  if (!file_exists("../data/vendor_item_report/data_vendor_item_report.php")) die ("Empty Vendor Item Report page!");
				                  include "../data/vendor_item_report/data_vendor_item_report.php";	   
						   }		   
			   break;
			   case 'history-movement-report': {  
			                  if (!file_exists("../data/history_movement_report/data_history_movement_report.php")) die ("Empty History Movement Report page!");
				                  include "../data/history_movement_report/data_history_movement_report.php";	   
						   }		   
			   break;
			   case 'document-flow-report': {  
			                  if (!file_exists("../data/document_flow_report/data_document_flow_report.php")) die ("Empty Document Flow Report page!");
				                  include "../data/document_flow_report/data_document_flow_report.php";	   
						   }		   
			   break;
			   case 'employee': {  
			                  if (!file_exists("../data/employee/data_employee.php")) die ("Empty Employee page!");
				                  include "../data/employee/data_employee.php";	   
						   }		   
			   break;
			   case 'uom': {  
			                  if (!file_exists("../data/uom/data_uom.php")) die ("Empty Uom page!");
				                  include "../data/uom/data_uom.php";	   
						   }		   
			   break;
			   case 'customer-type': {  
			                  if (!file_exists("../data/customer_type/data_customer_type.php")) die ("Empty Customer Type page!");
				                  include "../data/customer_type/data_customer_type.php";	   
						   }		   
			   break;
			   case 'customer': {  
			                  if (!file_exists("../data/customer/data_customer.php")) die ("Empty Customer page!");
				                  include "../data/customer/data_customer.php";	   
						   }		   
			   break;
			   case 'vendor': {  
			                  if (!file_exists("../data/vendor/data_vendor.php")) die ("Empty Vendor page!");
				                  include "../data/vendor/data_vendor.php";	   
						   }		   
			   break;
			   case 'period': {  
			                  if (!file_exists("../data/apt_period/data_period.php")) die ("Empty Transaction Period page!");
				                  include "../data/apt_period/data_period.php";	   
						   }		   
			   break;
			   case 'profile': {  
			                     if (!file_exists("../data/users/profile.php")) die ("Empty Profile Page!");
				                     include "../data/users/profile.php";	   
						       }		   
			   break;	
			   case 'users': {  
			                  if (!file_exists("../data/users/data_users.php")) die ("Empty Users page!");
				                  include "../data/users/data_users.php";	   
						   }		   
			   break;			   
			   case 'log-in': if (!file_exists("../data/log_in_out/log_in.php")) die ("Empty Log In page!");
			                 include "../data/log_in_out/log_in.php";
			   break;
			 } 
	}

?>