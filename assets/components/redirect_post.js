/*
 * Function to redirect using  method post
 * @var url URL to redirect
 * @var parameters Parameters to send format { param : value }
 */
function lotdg_redirect_post(url, parameters)
{
    parameters = (typeof parameters == 'undefined') ? {} : parameters;

    var form = document.createElement("form");
    $(form).attr("id", "reg-form")
		.attr("name", "reg-form")
		.attr("action", url)
		.attr("method", "post")
		.attr("enctype", "multipart/form-data")
	;

    $.each(parameters, function(key) {
        $(form).append('<input type="text" name="' + key + '" value="' + this + '" />');
    });
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    return false;
}