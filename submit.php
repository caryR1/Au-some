<?php
header('Content-Type: application/json; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'message'=>'Method not allowed.']); exit; }
if (!empty($_POST['website'] ?? '')) { echo json_encode(['ok'=>true]); exit; }
function clean($v,$max=500){ return trim(substr(strip_tags((string)$v),0,$max)); }
$name=clean($_POST['fullName']??'',120); $phone=clean($_POST['phone']??'',40);
$email=filter_var(trim($_POST['email']??''),FILTER_VALIDATE_EMAIL); $method=clean($_POST['contactMethod']??'',20);
$time=clean($_POST['contactTime']??'',160); $service=clean($_POST['service']??'',120);
$availability=clean($_POST['availability']??'',240); $notes=clean($_POST['notes']??'',2000);
if(!$name||!$phone||!$email||!$method||!$time||!$service||!$availability){ http_response_code(422); echo json_encode(['ok'=>false,'message'=>'Please complete all required fields.']); exit; }
$to='ausomenotarific@gmail.com';
$subject='Website service request - '.$service;
$body="New website request\n\nName: $name\nPhone: $phone\nEmail: $email\nPreferred contact: $method\nBest time: $time\nService: $service\nAvailability: $availability\n\nNotes:\n".($notes?:'None');
$headers="From: Au-Some Website <no-reply@".($_SERVER['HTTP_HOST']??'localhost').">\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
if(@mail($to,$subject,$body,$headers)){ echo json_encode(['ok'=>true,'message'=>'Thank you. Your request has been sent to Melicia.']); }
else { http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Your request could not be sent right now. Please call or email Melicia directly.']); }
