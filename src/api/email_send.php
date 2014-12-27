<?php
/*
 * Copyright (c) 2013, PGPSender.org
 */
session_start ();

require_once ("../include/ApiResponse.class.php");
require_once ("../include/MailQueue.class.php");
require_once ("../include/Keychain.class.php");
require_once ("../include/config.php");

if ( ! isset ($_SESSION["access_token"]) || ! isset ($_SESSION["hit_time"]) )
	ApiResponse::json_exit (ApiResponse::E_ILEGAL, "Permission denied");

//
// Validate POST data
//
if ( ! isset ($_POST["recipient"]) || empty ($_POST["recipient"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Recipient is empty");

if ( ! isset ($_POST["sender"]) || empty ($_POST["sender"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Sender is empty");

if ( ! isset ($_POST["subject"]) || empty ($_POST["subject"]) )
	$_POST["subject"] = "No subject";

if ( ! isset ($_POST["access_token"]) || empty ($_POST["access_token"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Access token is empty");


if ( ($_SESSION["hit_time"] + Config::HIT_SPEED_MAX) > time () )
	ApiResponse::json_exit (ApiResponse::E_ILEGAL, "request too fast");

if ( $_SESSION["access_token"] != $_POST["access_token"] )
	ApiResponse::json_exit (ApiResponse::E_ILEGAL, "token mismatch");

if ( ! filter_var ($_POST["recipient"], FILTER_VALIDATE_EMAIL) )
	ApiResponse::json_exit (ApiResponse::E_FORMAT, "recipient is not an email address");

if ( ! filter_var ($_POST["sender"], FILTER_VALIDATE_EMAIL) )
	ApiResponse::json_exit (ApiResponse::E_FORMAT, "sender is not an email address");

$mail_queue = new MailQueue (Config::DB_MAIL_QUEUE);

if ( ! $mail_queue->push (strtolower ($_POST["recipient"]), strtolower ($_POST["sender"]), $_POST["subject"], $_POST["message"]) )
	ApiResponse::json_exit (ApiResponse::E_INTERNAL, "failed to enqueue new email");

$_SESSION["hit_time"] = time ();

//
// Store public key
//
if ( ! empty ($_POST["public_key"]) ){
	$keychain = new Keychain (Config::DB_KEYCHAIN);
	
	if ( ! $keychain->insert (md5 ($_POST["recipient"]), md5 ($_POST["sender"]), $_POST["public_key"]) )
		ApiResponse::json_exit (ApiResponse::E_INTERNAL, "failed to store the public key");
}

ApiResponse::json_exit (ApiResponse::E_OK);
?>

