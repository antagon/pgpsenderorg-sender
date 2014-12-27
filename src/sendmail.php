#!/usr/bin/php
<?php
/*
 * Copyright (c) 2013 - 2015, PGPSender.org
 */

require_once ("include/MailQueue.class.php");
require_once ("include/phpmailer/PHPMailerAutoload.php");
require_once ("include/config.php");

$p = basename ($argv[0]);

// Fetch emails from queue
$mail_queue = new MailQueue (Config::DB_MAIL_QUEUE);
$emails = $mail_queue->pull (Config::SEND_PER_CYCLE);

if ( $emails === false ){
	echo "$p: error could not pull emails from queue\n";
	exit (1);
}

if ( count ($emails) == 0 )
	exit (0);

$sendmail = new PHPMailer;
$sendmail->CharSet = "UTF-8";
$sendmail->isSMTP ();
$sendmail->SMTPAuth = true;
$sendmail->SMTPSecure = "tls";
$sendmail->Host = Config::SMTP_HOST;
$sendmail->Port = Config::SMTP_PORT;
$sendmail->Username = Config::SMTP_USER;
$sendmail->Password = Config::SMTP_PASS;
$sendmail->XMailer = "PGPSender.org";
$sendmail->WordWrap = 80;
$sendmail->isHTML (false);

foreach ( $emails as $email ){
	echo "sending: ".$email["sender"]." => ".$email["recipient"]." (".$email["subject"].")";

	$sendmail->From = Config::SENDER_EMAIL;
	$sendmail->FromName = Config::SENDER_NAME;
	$sendmail->addReplyTo ($email["sender"]);
	$sendmail->addAddress ($email["recipient"]);
	$sendmail->Subject = $email["subject"];
	$sendmail->Body = $email["message"];
	
	if ( ! $sendmail->send () ){
	   echo " [ERROR] ".$sendmail->ErrorInfo."\n";
	   continue;
	}
	
	echo " [OK]\n";
	$mail_queue->delete ($email["rowid"]);
}

exit (0);
?>
