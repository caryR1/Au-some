<?php
// Copy this file ONE FOLDER ABOVE the site folder on the server, rename it to
// au-some-mail-config.php, and fill in the Titan mailbox password there.
// Never put the real password in this repo.
return [
  'smtp' => false, // true = send via Titan SMTP instead of PHP mail()
  'host' => 'smtp.titan.email',
  'port' => 465,
  'user' => 'support@gemzonline.com',
  'pass' => 'PUT-TITAN-PASSWORD-HERE-ON-SERVER-ONLY',
];
