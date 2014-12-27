<?php
/*
 * Copyright (c) 2013 - 2015, PGPSender.org
 */
 
class Config
{
	const DB_MAIL_QUEUE = "/tmp/mailqueue.dat";
	const DB_KEYCHAIN = "/tmp/keychain.dat";
	
	// How many emails sent per cycle
	const SEND_PER_CYCLE = 30;
	// How many seconds wait before the email can be send
	const HIT_SPEED_MAX = 10;
	
	// SMTP configuration
	const SMTP_HOST = "";
	const SMTP_PORT = 25;
	const SMTP_USER = "";
	const SMTP_PASS = "";
	
	const SENDER_EMAIL = "";
	const SENDER_NAME = "";
};

?>
