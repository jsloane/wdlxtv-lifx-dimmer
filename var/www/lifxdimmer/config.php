<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<link rel="stylesheet" href="/css/dhtmlwindowcontent.css" type="text/css">
	<style>
		label {
			display: inline-block;
			width: 180px;
			text-align: right;
		}
		input[type="submit"] {
			display: block;
			margin: 10px auto;
		}
	</style>
</head>
<body>
	<div class="viewPort" id="update" style="position: relative; text-align: left; margin: auto; width: 400px; border-style: solid; border-color: white; border-width: 1px; padding: 7px;">
		<h4>Enter configuration details for LiFx Dimmer plugin</h4>
		<form method="post" action="?">
			<input type="hidden" name="task" value="save">
			<?php
				function addField($readArray, $field, $label){
					echo "<label for=\"".$field."\">".$label.":</label>";
					echo "<input type=\"text\" name=\"".$field."\" value=\"".$readArray[$field]."\"/><br/>";
				}
				
				$filename = "/apps/lifxdimmer/conf/lifxdimmer.conf";
				$TASK = $_POST["task"];
				
				if ($TASK == "save") {
					// save config and restart service
					$saveArray = parse_ini_file($filename);
					$filetext = "";
					foreach($saveArray as $key => $value)
					{
						if ($key == "CONFDEBUG") {
							$strTemp = $_POST[$key];
							if($strTemp == 'on') {
								$filetext .= $key."=true\n";
							} else {
								$filetext .= $key."=false\n";
							}
						} else {
							$filetext .= $key."=".$_POST[$key]."\n";
						}
					}
					$fh = fopen($filename, "w") or die("Could not open configuration file.");
					fwrite($fh, $filetext) or die("Could not write configuration file.");
					fclose($fh);
					exec("/apps/lifxdimmer/etc/init.d/S99lifxdimmer restart>/dev/null 2>&1");
					echo "Configuration saved and service restarted.<br/><br/>";
				}
				
				$readArray = parse_ini_file($filename);
				addField($readArray, "CONFHOST", "Hostname or IP address");
				addField($readArray, "CONFBULBGROUP", "Bulb Group ID");
				addField($readArray, "CONFBRIGHT", "Brightness level (0-100)");
				addField($readArray, "CONFDIM", "Dim level (0-100)");
				$debugChecked = "";
				if ($readArray['CONFDEBUG']) {
					$debugChecked = "checked=\"checked\"";
				}
				echo "<label for=\"CONFDEBUG\">Debug enabled:</label>";
				echo "<input type=\"checkbox\" name=\"CONFDEBUG\" ".$debugChecked."/><br/>";
				
			?>
			<input type="submit" value="Save configuration">
		</form>
	</div>
</body>
</html>