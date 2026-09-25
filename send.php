<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
function clean($v) { return trim(str_replace(["\r","\n"], ' ', (string)$v)); }
function fail() { header('Location: index.html?error=1#contact'); exit; }
$name = clean($_POST['fullName'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$method = clean($_POST['contactMethod'] ?? '');
$time = clean($_POST['contactTime'] ?? '');
$service = clean($_POST['service'] ?? '');
$availability = clean($_POST['availability'] ?? '');
$notes = trim((string)($_POST['notes'] ?? ''));
if (!$name || !$phone || !$email || !$method || !$time || !$service || !$availability) fail();
$to = 'caryrobinsonusa@gmail.com';
$subject = 'Website service request - ' . $service;
$body = "New website request\n\nName: $name\nPhone: $phone\nEmail: $email\nPreferred contact: $method\nBest contact time: $time\nService: $service\nAvailability: $availability\n\nNotes:\n$notes";

// Send through Titan SMTP so the message passes SPF/DKIM for gemzonline.com.
// Credentials live one folder above the site (never in Git); see mail-config.example.php.
function smtp_send(array $c, $to, $replyTo, $subject, $body) {
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
    && $cmd("MAIL FROM:<$from>", 250) && $cmd("RCPT TO:<$to>", 250) && $cmd('DATA', 354) && $cmd($msg . "\r\n.", 250);
  $cmd('QUIT', 221);
  fclose($s);
  return $ok;
}
$configFile = dirname(__DIR__) . '/au-some-mail-config.php';
$config = is_file($configFile) ? include $configFile : null;
if (is_array($config) && !empty($config['user']) && !empty($config['pass'])) {
  $ok = smtp_send($config, $to, $email, $subject, $body);
} else {
  error_log('send.php: no SMTP config found, falling back to mail()');
  $headers = "From: support@gemzonline.com\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
  $ok = mail($to, $subject, $body, $headers);
}
if (!$ok) fail();
header('Location: index.html?sent=1#contact');
