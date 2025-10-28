<?php
  header("Content-type: application/vnd-ms-excel");
  header("Content-Disposition: attachment; filename=SOA_Report.xls");
  include "../../library/library_function.php";
  $start_dates=get_date_2($_POST['txt_soa_date_start']);
  $end_dates=get_date_2($_POST['txt_soa_date_end']);
  $start_dates_1=$_POST['txt_soa_date_start'];
  $end_dates_1=$_POST['txt_soa_date_end'];
  $customers=$_POST['s_customer'];
  $rb_types=$_POST['rb_view_type'];
  include "view_data_soa_detail.php";
?>

 