<?php
/*
 * Copyright (c) 2013 - 2015, PGPSender.org
 */
session_start ();
require_once ("include/Random.class.php");

$_SESSION["access_token"] = Random::read (16);
$_SESSION["hit_time"] = time ();

?>
<!DOCTYPE html>
<html>
	<head>
	<title>PGPSender</title>
<?php
	require_once ("include/head.php");
?>
	</head>
	<body>
		<div id="content">
			<h1> <a href="/">PGPSender</a> </h1>
			<div id="quote">
<?php
	require_once ("include/quote.php");
?>
			</div>

			<div id="email_form">
				<div>
					<div> <strong>Recipient</strong> <span id="alert_recipient" class="hidden"></span> </div>
					<div>
						<input type="text" name="in_email_recipient" placeholder="m.brod@example.com" style="width:20%;"> <button name="btn_email_paste_key" title="Paste public key"> <span class="fa fa-lock"></span> </button>
					</div>

					<div> <strong>Sender</strong> <span id="alert_sender" class="hidden"></span> </div>
					<div>
						<input type="text" name="in_email_sender" placeholder="f.kafka@example.com" style="width:20%;">
					</div>
				</div>

				<div>
					<div> <strong>Subject</strong> </div>
					<input type="text" name="in_email_subject" style="width:30%;">
					<div style="float:right;"> <button name="btn_email_preview">Preview</button> </div>
				</div>

				<textarea name="in_email_body" rows="16"></textarea>

				<input type="hidden" name="access_token" value="<?php echo $_SESSION["access_token"];  ?>">
			</div>

			<div id="email_preview" class="hidden">
				<div>
					<strong>Recipient:</strong> <span id="email_preview_recipient"></span>
				</div>

				<div>
					<strong>Sender:</strong> <span id="email_preview_sender"></span>
				</div>

				<div>
					<strong>Subject:</strong> <span id="email_preview_subject"></span>
					<div style="float:right">
						<span id="email_preview_store"> <input type="checkbox" id="email_preview_store_opt" name="in_email_preview_store"> <label for="email_preview_store_opt"> <small>Store public key</small> </label> </span>
						<button name="btn_email_preview_cancel">Cancel</button> <button name="btn_email_preview_send">Send</button>
					</div>
				</div>

				<div style="clear:both"></div>

				<div>
					<p id="email_preview_body" style="white-space:pre-wrap;"></p>
				</div>
			</div>

			<div id="email_paste_key" class="hidden">
				<div class="row">
					<div class="left"> <strong>Paste recipient's public key</strong> </div>
					<div class="left text-right"> <button name="btn_email_paste_key_ok">OK</button> <span id="alert_pubkey" class="hidden"></span> </div>
				</div>
				<div>
					<textarea name="in_email_pk" rows="16"></textarea>
				</div>
			</div>

			<div id="email_success" class="hidden">
				<h2>Email sent!</h2>

				<p>Your email was sent successfully. It may take a few minutes before being delivered.</p>

				<button name="btn_email_success_next">Send another email</button>
			</div>

			<div id="email_failure" class="hidden">
				<h2>Email NOT sent!</h2>

				<p>Your email could not be sent, an internal error occured. Please, try it again later.
				If this problem persists, please consider reporting it to us.</p>
			</div>
		</div>
		
		<div id="footer">
<?php
	require_once ("include/footer.php");
?>
		</div>

		<script type="text/javascript" src="js/sender.js"></script>
	</body>
</html>

