function nsg_submit()
{
	if(document.nsg_form.nsg_path.value=="")
	{
		alert(nsg_adminscripts.nsg_path);
		document.nsg_form.nsg_path.focus();
		return false;
	}
	else if(document.nsg_form.nsg_link.value=="")
	{
		alert(nsg_adminscripts.nsg_link);
		document.nsg_form.nsg_link.focus();
		return false;
	}
	else if(document.nsg_form.nsg_target.value=="")
	{
		alert(nsg_adminscripts.nsg_target);
		document.nsg_form.nsg_target.focus();
		return false;
	}
	else if(document.nsg_form.nsg_type.value=="")
	{
		alert(nsg_adminscripts.nsg_type);
		document.nsg_form.nsg_type.focus();
		return false;
	}
	else if(document.nsg_form.nsg_status.value=="")
	{
		alert(nsg_adminscripts.nsg_status);
		document.nsg_form.nsg_status.focus();
		return false;
	}
	else if(document.nsg_form.nsg_order.value=="")
	{
		alert(nsg_adminscripts.nsg_order);
		document.nsg_form.nsg_order.focus();
		return false;
	}
	else if(isNaN(document.nsg_form.nsg_order.value))
	{
		alert(nsg_adminscripts.nsg_order);
		document.nsg_form.nsg_order.focus();
		return false;
	}
}

function nsg_delete(id)
{
	if(confirm(nsg_adminscripts.nsg_delete))
	{
		document.frm_nsg_display.action="options-general.php?page=new-simple-gallery&ac=del&did="+id;
		document.frm_nsg_display.submit();
	}
}	

function nsg_redirect()
{
	window.location = "options-general.php?page=new-simple-gallery";
}

function nsg_help()
{
	window.open("http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/");
}