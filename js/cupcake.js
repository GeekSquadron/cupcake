jQuery(document).ready(function(){
	jQuery(".cupcake-like").click(function(e){
		e.preventDefault();

		jQuery.ajax({
			type: "POST",
			url: cup_vars.ajaxurl,
			data: {
				action: "cupcake-like",
				nonce: cup_vars.nonce,
				pid: jQuery(this).attr("id")
			},
			success: function(response)
			{
				jQuery(".cupcake-like").html(response).attr("disabled", true);
			}
		});
	});
});