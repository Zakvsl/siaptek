<?php
 session_start();
 if ($_GET['page']!='log-in')
    {
      if (!isset($_SESSION['ses_siaptek_admin']))
         {
	       echo "<meta http-equiv='refresh' content='0; url=?page=log-in'>";
		   exit;
	     }	   
	}
?>