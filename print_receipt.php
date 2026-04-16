<?php
// print_receipt.php — Sends ESC/POS receipt to Windows shared thermal printer
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// ================================================================
// CONFIG — PALITAN NG INYONG PRINTER SHARE NAME
// Paano makuha: Devices and Printers → right-click printer → Sharing tab → Share name
// ================================================================
define('PRINTER_SHARE_NAME', 'POS80 Printer'); // <-- PALITAN ITO
// ================================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$name     = trim($_POST['name']         ?? '');
$contact  = trim($_POST['contact_no']   ?? '');
$email    = trim($_POST['email']        ?? '');
$company  = trim($_POST['company_name'] ?? '');
$guestNo  = trim($_POST['guest_number'] ?? '');

if (!$name || !$guestNo) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit;
}

// ─── ESC/POS COMMANDS ───
$ESC      = "\x1B";
$GS       = "\x1D";
$INIT     = $ESC . "\x40";           // Initialize printer
$CENTER   = $ESC . "\x61\x01";       // Center align
$LEFT     = $ESC . "\x61\x00";       // Left align
$BOLD_ON  = $ESC . "\x45\x01";       // Bold on
$BOLD_OFF = $ESC . "\x45\x00";       // Bold off
$BIG      = $ESC . "\x21\x30";       // Double width + height
$NORMAL   = $ESC . "\x21\x00";       // Normal size
$CUT      = $GS  . "\x56\x42\x05";  // Feed 5 lines + partial cut
$LF       = "\n";
$DIV      = "================================" . $LF; // 32 chars wide

// Truncate text to fit 80mm line after label prefix (label = 10 chars)
function trunc($str, $max = 40) {
    return mb_strlen($str) > $max
        ? mb_substr($str, 0, $max - 1) . chr(133) // ellipsis
        : $str;
}

$dateStr = date('M j, Y \a\t g:i A');

// ─── BUILD RECEIPT ───
$receipt =
    $INIT .
    $CENTER .
    $BOLD_ON . $BIG . "* 6 YEARS & BEYOND *" . $LF . $NORMAL . $BOLD_OFF .
    "Ziontech Anniversary 2026" . $LF .
    "        RAFFLE ENTRY        " . $LF .
    $DIV .
    $BOLD_ON . $BIG . $guestNo . $LF . $NORMAL . $BOLD_OFF .
    $DIV .
    $LEFT .
    $BOLD_ON . "Name    :" . $BOLD_OFF . " " . trunc($name)    . $LF .
    $BOLD_ON . "Contact :" . $BOLD_OFF . " " . trunc($contact) . $LF .
    $BOLD_ON . "Email   :" . $BOLD_OFF . " " . trunc($email)   . $LF .
    $BOLD_ON . "Company :" . $BOLD_OFF . " " . trunc($company) . $LF .
    $CENTER .
    $DIV .
    $CENTER .
    $dateStr . $LF .
    $LF .
    $BOLD_ON . "Welcome! Enjoy the party." . $BOLD_OFF . $LF .
    $LF . $LF . $LF .
    $CUT;

// ─── METHOD 1: Direct fopen to shared printer ───
$printerPath = '\\\\localhost\\' . PRINTER_SHARE_NAME;
$handle = @fopen($printerPath, 'wb');

if ($handle !== false) {
    fwrite($handle, $receipt);
    fclose($handle);
    echo json_encode(['success' => true, 'method' => 'fopen']);
    exit;
}

// ─── METHOD 2: Fallback — write to temp file + copy /B to printer ───
$tmpFile = tempnam(sys_get_temp_dir(), 'receipt_') . '.bin';
file_put_contents($tmpFile, $receipt);

$cmd    = 'copy /B "' . $tmpFile . '" "' . $printerPath . '" > NUL 2>&1';
$output = null;
$result = null;

if (function_exists('shell_exec')) {
    shell_exec($cmd);
    @unlink($tmpFile);
    echo json_encode(['success' => true, 'method' => 'shell_exec']);
    exit;
}

// ─── Both methods failed ───
@unlink($tmpFile);
echo json_encode([
    'success' => false,
    'message' => 'Could not connect to printer. Check if it is shared in Windows.'
]);
?>