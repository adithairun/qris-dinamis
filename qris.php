<?php
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$qris_statis = "00020101021126610014COM.GO-JEK.WWW01189360091430679224840210G0679224840303UMI51440014ID.CO.QRIS.WWW0215ID10243569168730303UMI5204729953033605802ID5916KlickFlow, DEPOK6006SLEMAN61055528162070703A0163041191";

$nominal = intval($_GET['nominal'] ?? 0);

// === Konversi ke QRIS Dinamis ===
$qris = substr($qris_statis, 0, -4);
$qris = str_replace("010211", "010212", $qris);

$uang = "54".sprintf("%02d", strlen($nominal)).$nominal;
$parts = explode("5802ID", $qris);
$payload = $parts[0].$uang."5802ID".$parts[1];
$payload .= crc16($payload);

// === Ambil Merchant Name (Tag 59) ===
preg_match('/59(\d{2})(.{1,50})/', $payload, $m);
$merchant = $m ? substr($m[2], 0, intval($m[1])) : '-';

// === Ambil NMID (kasar, aman tampil) ===
preg_match('/ID\d{12,16}/', $payload, $nmid);
$nmid = $nmid[0] ?? '-';

// === Output JSON untuk index.php ===
if (isset($_GET['meta'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'merchant' => $merchant,
        'nmid' => $nmid,
        'nominal' => $nominal
    ]);
    exit;
}

// === Generate QR ===
$result = Builder::create()
    ->writer(new PngWriter())
    ->data($payload)
    ->size(280)
    ->margin(10)
    ->build();

header("Content-Type: image/png");
echo $result->getString();
exit;

function crc16($str) {
    $crc = 0xFFFF;
    for ($i=0; $i<strlen($str); $i++) {
        $crc ^= ord($str[$i]) << 8;
        for ($j=0; $j<8; $j++) {
            $crc = ($crc & 0x8000) ? ($crc << 1) ^ 0x1021 : ($crc << 1);
        }
    }
    return strtoupper(sprintf("%04X", $crc & 0xFFFF));
}