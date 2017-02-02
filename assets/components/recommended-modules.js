/**
 * Select all recommended modules
 */
function chooseRecommendedModules()
{
	$(':radio[data-recommended]').each(function()
    {
		$(this).prop('checked', true);
	});
}