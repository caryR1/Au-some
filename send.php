<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
function clean($v) { return trim(str_replace(["\r","\n"], ' ', (string)$v)); }
$name = clean($_POST['fullName'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$method = clean($_POST['contactMethod'] ?? '');
$time = clean($_POST['contactTime'] ?? '');
$service = clean($_POST['service'] ?? '');
$availability = clean($_POST['availability'] ?? '');
$notes = trim((string)($_POST['notes'] ?? ''));
if (!$name || !$phone || !$email || !$method || !$time || !$service || !$availability) { http_response_code(400); exit('Please complete all required fields.'); }
$to = 'caryrobinsonusa@gmail.com';
$subject = 'Website service request - ' . $service;
$body = "New website request\n\nName: $name\nPhone: $phone\nEmail: $email\nPreferred contact: $method\nBest contact time: $time\nService: $service\nAvailability: $availability\n\nNotes:\n$notes";
$headers = "From: support@gemzonline.com\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
$ok = mail($to, $subject, $body, $headers);
if (!$ok) { header('Location: index.html?error=1#contact'); exit; }
header('Location: index.html?sent=1#contact');
