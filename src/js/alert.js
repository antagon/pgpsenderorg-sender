function show_alert (id, type, message)
{
	var alert_class = null;

	switch (type){
		case "error":
			alert_class = "alert-error";
			break;

		case "info":
			alert_class = "alert-info";
			break;

		case "success":
			alert_class = "alert-success";
			break;

		default:
			alert_class = "alert-info";
			break;
	}

	$("#"+id).removeClass ("alert-error alert-info alert-success");

	$("#"+id).text (message);
	$("#"+id).addClass (alert_class);
	$("#"+id).show ();
}

function hide_alert (id)
{
	$("#"+id).hide ();
	$("#"+id).removeClass ("alert-error alert-info alert-success");
	$("#"+id).text ("");
}

