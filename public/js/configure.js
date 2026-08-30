if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Configure  = {
	RemoveThumbnail: function() {
		$.post("/asset/"+$("body").data("asset")+"/resetthumbs", function(data) {
			if(!data['success'])
				ANORRL.MessageBox.Show(2, data['reason']);
			else
				ANORRL.MessageBox.Show(0, "Successfully reset thumbnail!");
		});
	}
};

$(function() {
	// go thru each data-action thing, gather all inputs and check if the submit button has been pressed and create formdata...
	// JSON!!!

	$("form[data-action]").submit(function(e) {
		e.preventDefault();

		$.ajax($(this).attr("data-action"), {
			method: 'POST',
			data: $(this).serializeFiles(),
			processData: false,
			contentType: false,
			success(data) {
				if(!data['success'])
					ANORRL.MessageBox.Show(2, data['reason']);
				else {
					if(data["reason"]) {
						ANORRL.MessageBox.Show(1, data['reason']);
					} else {
						window.location.href = $("body").data("redirect");
					}
				}
					
			},
			error() {
				ANORRL.MessageBox.Show(2, "Something went wrong with the changes!");
			},
		});
	});
})