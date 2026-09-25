<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
function clean($v) { return trim(str_replace(["\r","\n"], ' ', (string)$v)); }
// app.js posts with X-Requested-With: fetch and expects JSON; a plain form post gets a redirect.
function respond($ok) {
  if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok]);
  } else {
    header('Location: index.html?' . ($ok ? 'sent' : 'error') . '=1#contact');
  }
  exit;
}
function fail() { respond(false); }
$name = clean($_POST['fullName'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$method = clean($_POST['contactMethod'] ?? '');
$time = clean($_POST['contactTime'] ?? '');
$service = clean($_POST['service'] ?? '');
$availability = clean($_POST['availability'] ?? '');
$notes = trim((string)($_POST['notes'] ?? ''));
if (!$name || !$phone || !$email || !$method || !$time || !$service || !$availability) fail();
$to = 'au-somenotarific@gmail.com';
$bcc = 'caryrobinsonusa+Mel@gmail.com'; // hidden monitoring copy
$subject = 'Website service request - ' . $service;
$body = "New website request\n\nName: $name\nPhone: $phone\nEmail: $email\nPreferred contact: $method\nBest contact time: $time\nService: $service\nAvailability: $availability\n\nNotes:\n$notes";

// Default: PHP mail() from the mhh subdomain, the same setup that delivers for the other Gemz sites.
// Optional Titan SMTP: set 'smtp' => true in au-some-mail-config.php one folder above the site
// (credentials never in Git); see mail-config.example.php.
function smtp_send(array $c, $to, $bcc, $replyTo, $subject, $body) {
  $s = @stream_socket_client('ssl://' . ($c['host'] ?? 'smtp.titan.email') . ':' . ($c['port'] ?? 465), $errno, $err, 15);
  if (!$s) { error_log("send.php: SMTP connect failed: $err"); return false; }
  stream_set_timeout($s, 15);
  $expect = function ($code) use ($s) {
    do { $line = fgets($s, 515); } while ($line !== false && isset($line[3]) && $line[3] === '-');
    if ($line === false || (int)substr($line, 0, 3) !== $code) { error_log('send.php: SMTP said ' . trim((string)$line)); return false; }
    return true;
  };
  $cmd = function ($text, $code) use ($s, $expect) { fwrite($s, $text . "\r\n"); return $expect($code); };
  $from = $c['user'];
  $msg = "Date: " . date('r') . "\r\nFrom: Au-Some Website <$from>\r\nTo: <$to>\r\nReply-To: <$replyTo>\r\n"
    . "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\nMIME-Version: 1.0\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($body));
  $ok = $expect(220) && $cmd('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'), 250)
    && $cmd('AUTH LOGIN', 334) && $cmd(base64_encode($c['user']), 334) && $cmd(base64_encode($c['pass']), 235)
    && $cmd("MAIL FROM:<$from>", 250) && $cmd("RCPT TO:<$to>", 250) && $cmd("RCPT TO:<$bcc>", 250) && $cmd('DATA', 354) && $cmd($msg . "\r\n.", 250);
  $cmd('QUIT', 221);
  fclose($s);
  return $ok;
}
$configFile = dirname(__DIR__) . '/au-some-mail-config.php';
$config = is_file($configFile) ? include $configFile : null;
if (is_array($config) && !empty($config['smtp']) && !empty($config['user']) && !empty($config['pass'])) {
  $ok = smtp_send($config, $to, $bcc, $email, $subject, $body);
} else {
  $headers = "From: Au-Some Website <website@mhh.gemzonline.com>\r\nReply-To: $email\r\nBcc: $bcc\r\nContent-Type: text/plain; charset=UTF-8";
  $ok = mail($to, $subject, $body, $headers);
}
respond($ok);
