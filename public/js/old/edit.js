if(ANORRL == undefined) {
	ANORRL = {};
}

ANORRL.EditItem = {
	ShowPricingArea: function(show) {
		 $(".ThePricing").each(function() {
			if(show) {
				$(this).css("display", "revert");
			} else {
				$(this).css("display", "none");
			}
		 })
	}
};

$(function() {
	$("#OnSaleCheckbox").on("change", function() {
		ANORRL.EditItem.ShowPricingArea($(this).is(":checked"));
	});

	$("#thumbfiles").change(function() {
		filename = this.files[0].name;
		$("#thumbfilename").html(filename);
	});

	$("#files").change(function() {
		filename = this.files[0].name;
		$("#filename").html(filename);
	});

	ANORRL.EditItem.ShowPricingArea($("#OnSaleCheckbox").is(":checked"));
})