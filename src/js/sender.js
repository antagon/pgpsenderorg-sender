var recipient_pubkey = null;

// Reference: http://stackoverflow.com/questions/2855865/jquery-regex-validation-of-e-mail-address
function is_email (emailAddress) {
	var pattern = new RegExp (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);

	return pattern.test (emailAddress);
}

function show_preview ()
{
	var recipient = $("[name=in_email_recipient]").val ();
	var sender = $("[name=in_email_sender]").val ();
	var subject = $("[name=in_email_subject]").val ();
	var body = $("[name=in_email_body]").val ();

	hide_alert ("alert_recipient");
	hide_alert ("alert_sender");

	if ( ! is_email (recipient) ){
		show_alert ("alert_recipient", "error", "* Invalid format");
		return;
	}

	if ( ! is_email (sender) ){
		show_alert ("alert_sender", "error", "* Invalid format");
		return;
	}

	$("#email_preview_recipient").text (recipient);
	$("#email_preview_sender").text (sender);
	$("#email_preview_subject").text (subject);

	if ( recipient_pubkey == null ){
		$("[name=in_email_preview_store_opt]").prop ("checked", false);
		$("#email_preview_store").hide ();
		$("#email_preview_body").text (body);
	} else {
		$("#email_preview_store").show ();
		openpgp.encryptMessage (recipient_pubkey.keys[0], body).then (function (body_armored){
			$("#email_preview_body").text (body_armored);
		});
	}

	$("#email_form").hide ();
	$("#email_paste_key").hide ();
	$("#email_success").hide ();
	$("#email_preview").show ();
}

function show_form ()
{
	hide_alert ("alert_pubkey_fetch");

	$("#email_preview").hide ();
	$("#email_paste_key").hide ();
	$("#email_success").hide ();
	$("#email_form").show ();
}

function show_public_key ()
{
	hide_alert ("alert_pubkey");

	$("#email_preview").hide ();
	$("#email_form").hide ();
	$("#email_success").hide ();
	$("#email_paste_key").show ();
}

function show_success ()
{
	$("#email_preview").hide ();
	$("#email_form").hide ();
	$("#email_paste_key").hide ();
	$("#email_success").show ();
}

function show_failure ()
{
	$("#email_preview").hide ();
	$("#email_form").hide ();
	$("#email_paste_key").hide ();
	$("#email_success").hide ();
	$("#email_failure").show ();
}

function fetch_public_key ()
{
	var pgpsender = new PGPSender ();
	var recipient = $("[name=in_email_recipient]").val ();
	var sender = $("[name=in_email_sender]").val ();
	var pubkey_armored = $.trim ($("[name=in_email_pk]").val ());

	if ( (recipient.length == 0) || (sender.length == 0) || (pubkey_armored.length > 0) )
		return;

	pgpsender.email_get_pubkey ("not_used", recipient, sender, function (response){
		if ( response.status != 0 ){
			return;
		}

		$("[name=in_email_pk]").val (response.data);

		load_public_key ();

		show_alert ("alert_pubkey_fetch", "info", "* public key restored");
	});
}

function load_public_key ()
{
	var pubkey_armored = $.trim ($("[name=in_email_pk]").val ());

	if ( pubkey_armored.length == 0 ){
		recipient_pubkey = null;
		show_form ();
		return;
	}

	recipient_pubkey = openpgp.key.readArmored (pubkey_armored);

	console.log (recipient_pubkey);

	if ( recipient_pubkey.keys.length == 0 ){
		show_alert ("alert_pubkey", "error", "* Invalid key");
		$("[name=in_email_pk]").select ();
		recipient_pubkey = null;
		return;
	}

	show_form ();

	return;
}

function queue_email ()
{
	var pgpsender = new PGPSender ();
	var access_token = $("[name=access_token]").val ();
	var recipient = $("[name=in_email_recipient]").val ();
	var sender = $("[name=in_email_sender]").val ();
	var subject = $("[name=in_email_subject]").val ();
	var body = $("[name=in_email_body]").val ();
	var pubkey_armored = $.trim ($("[name=in_email_pk]").val ());
	var store_pubkey = $("[name=in_email_preview_store_opt]").prop ("checked");

	if ( store_pubkey === false )
		pubkey_armored = "";

	// Send encrypted message
	if ( recipient_pubkey != null ){
		openpgp.encryptMessage (recipient_pubkey.keys[0], body).then (function (body_armored){
			body = body_armored;

			pgpsender.email_send (access_token, recipient, sender, subject, body, pubkey_armored, function (response){
				if ( response.status != 0 ){
					show_failure ();
					return;
				}

				show_success ();
			});
		});

		return;
	}

	// Send plain-text message
	pgpsender.email_send (access_token, recipient, sender, subject, body, pubkey_armored, function (response){
		if ( response.status != 0 ){
			show_failure ();
			return;
		}

		show_success ();
	});
}

$(document).ready (function (){
	openpgp.config.commentstring = "https://pgpsender.org/";

	$("[name=btn_email_preview]").click (show_preview);
	$("[name=btn_email_preview_cancel]").click (show_form);
	$("[name=btn_email_preview_send]").click (queue_email);
	$("[name=btn_email_paste_key]").click (show_public_key);
	$("[name=btn_email_paste_key_ok]").click (load_public_key);
	$("[name=btn_email_success_next]").click (function (){ location.reload (); });
	$("[name=btn_email_failure_next]").click (queue_email);
	$("[name=in_email_recipient]").change (fetch_public_key);
	$("[name=in_email_sender]").change (fetch_public_key);
});

