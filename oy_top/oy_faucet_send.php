<?php
$oy_send_address = str_replace("oy_send_address=", "", file_get_contents("php://input"));
if (!$oy_send_address||!oy_address_valid($oy_send_address)) die("ERROR: Invalid address");

function oy_address_valid($oy_node_id) {
    if (strlen($oy_node_id)==86) return true;
    return false;
}

if (!is_dir("/dev/shm/oy_faucet")) mkdir("/dev/shm/oy_faucet");

if (is_file("/dev/shm/oy_faucet/".$_SERVER['REMOTE_ADDR'].".access")&&(time()-filemtime("/dev/shm/oy_faucet/".$_SERVER['REMOTE_ADDR'].".access")) < 300) die("ERROR: One transaction per 5 mins per IP address allowed");

if ($fh = opendir("/dev/shm/oy_faucet")) {
    while (($oy_file = readdir($fh))!==false) {
        if (preg_match('/\.access$/i', $oy_file)&&(time()-filemtime("/dev/shm/oy_faucet/".$oy_file)) >= 300) unlink("/dev/shm/oy_faucet/".$oy_file);
    }
}
closedir($fh);

$fh = fopen("/dev/shm/oy_faucet/".$_SERVER['REMOTE_ADDR'].".access", "w");
fwrite($fh, "OY_VOID");
fclose($fh);

$fh = fopen("/dev/shm/oy_faucet/".$_SERVER['REMOTE_ADDR'].".send", "w");
fwrite($fh, $oy_send_address);
fclose($fh);
echo "OY_FAUCET_PASS";