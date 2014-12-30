function PGPSender ()
{
	this.email_send = function (access_token, recipient, sender, subject, message, public_key, callback)
	{
		//$.post ("api/email_send.php", "access_token="+access_token+"&recipient="+recipient+"&sender="+sender+"&subject="+subject+"&message="+message+"&public_key="+encodeURIComponent (public_key), function (response){
		$.post ("api/email_send.php", { "access_token": access_token, "recipient": recipient, "sender": sender, "subject": subject, "message": message, "public_key": public_key }, function (response){
			var data = null;

			try {
				data = $.parseJSON (response);
			} catch (e){
				console.log ("JSON parser failed - invalid data!");	
			}

			callback (data);
		});
	};

	this.email_get_pubkey = function (access_token, recipient, sender, callback)
	{
		//$.post ("api/email_get_pubkey.php", "access_token="+access_token+"&recipient="+recipient+"&sender="+sender, function (response){
		$.post ("api/email_get_pubkey.php", { "access_token": access_token, "recipient": recipient, "sender": sender }, function (response){
			var data = null;

			try {
				data = $.parseJSON (response);
			} catch (e){
				console.log ("JSON parser failed - invalid data!");
			}

			callback (data);
		});
	};
};

