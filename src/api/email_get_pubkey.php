<?php
/*
 * Copyright (c) 2013, PGPSender.org
 */
require_once ("../include/ApiResponse.class.php");
require_once ("../include/Keychain.class.php");
require_once ("../include/config.php");

if ( ! isset ($_POST["recipient"]) || empty ($_POST["recipient"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Recipient is empty");

if ( ! isset ($_POST["sender"]) || empty ($_POST["sender"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Sender is empty");

$_POST["recipient"] = strtolower ($_POST["recipient"]);
$_POST["sender"] = strtolower ($_POST["sender"]);

$keychain = new Keychain (Config::DB_KEYCHAIN);

$data = $keychain->get (md5 ($_POST["recipient"]), md5 ($_POST["sender"]));

if ( $data === false )
	ApiResponse::json_exit (ApiResponse::E_INTERNAL, "Failed to obtain the public key");

if ( empty ($data["key"]) )
	ApiResponse::json_exit (ApiResponse::E_EMPTY, "Key not found");

ApiResponse::json_exit_data (ApiResponse::E_OK, $data["key"]);
?>

