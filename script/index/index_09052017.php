<?php
 include "../library/check_session.php";
 include "../library/style.css";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INVENTORY</title>
</head>
<?php
  if (!isset($_GET['page']) || $_GET['page']=='log-in')
      echo "<body background='../images/background/world.jpg'>";
  else
      echo "<body>";
?>
<!---<form id="form" name="form" method="post" action="">  --->
  <table width="100%" border="0" cellspacing="2" cellpadding="2">
    <tr>
  <!--    <th colspan="2" scope="col" background="../images/bg/bg_1.png" height="100" width="100%"><div align="left"><img src="../images/bg/rc.jpg" width="100" height="100"/>RAMEN CEMEN<br />FOOD AND BEVERAGE</div></th>  -->
  <?php
     if (!isset($_GET['page']) || $_GET['page']=='log-in')
	     echo "<th background=''><table border='0'><tr><td><div align='left'><img src='../images/background/logo.jpg' width='100' height='100'/></div></td>";
	 else
	     echo "<th background='../images/background/bg_header.jpg' height='100'><table border='0'><tr><td><div align='left'><img src='../images/background/logo.jpg' width='100' height='100'/></div></td>";
  ?>	  
	     <td valign="top"><font size="50" color="#FFFFFF">PT. PT. SURVEY INSPEKSI AUDIT PRATAMA TEKINDO</font><br /><font color="#FFFFFF">Jln. Minangkabau Barat No 34 - Jakarta Selatan</font></td></tr></table></th> 
    </tr>
  </table>
 <?php 
   if (!isset($_SESSION['ses_siaptek_admin']))
      {
	    ?>
		  <table width="100%">
		    <tr>
			    <td bgcolor="black" height="28px">
				</td>
			</tr>
		  </table>
		<?php
	  }
   else
        include "menu.php";

 ?>
<br>
  <table width="100%">	
    <tr>
      <td valign="top"><?php include "open_page.php";?></td>
    </tr>
    <tr>
      <td>
	  <div align="center">
	    <?php 
	      if (isset($_GET['page'])!='log-in' && isset($_GET['page'])!='')
	          echo  "Copyright by adien.kom_soft"; 
        ?></div></td>
    </tr>
  </table>
<!---</form>--->
</body>
</html>
