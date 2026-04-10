if(ANORRL == undefined) {
	ANORRL = {};
}

ANORRL.Item = {
	// frontend shit, 0 = unbought, 1 = processing, 2 = bought
	State: 0,
	Favourite: function(assetid) {
		$.post("/api/favourite", { asset : assetid }, function(data) {
			if(data['error']) {
				alert("Error: " + data['reason']);
			} else {
				window.location.reload();
			}
		});
	},
	Purchasing: {
		AllowedShittyIDs: [
			"Lights",
			"Cones",
			"FreeItem"
		],
		
		OpenPurchasePanel: function(panel) {
			$("#PurchasePanel").css("display", "block");
			if(panel == "cones") {
				$("#PurchaseCones").css("display", "block");
			} else if(panel == "lights") {
				$("#PurchaseLights").css("display", "block");
			}
		},
		PurchaseItem: function() {
			if($("#ModalPopup > div:visible").size() == 1) {
				var prompt = $("#ModalPopup > div:visible");

				prompt.css("display","none");
				
				var id = prompt.attr("id").replaceAll("Purchase", "");
				var AllowedShittyIDsID = {
					"Cones": 1,
					"Lights": 2,
					"FreeItem": 3
				};

				if(this.AllowedShittyIDs.includes(id)) {
					ANORRL.Item.State = 1;
					$("#ModalPopup #PurchaseProcessing").css("display", "block");
					window.setTimeout(function() {
						$.post("/api/purchase", { asset_id: Number($("#ModalPopup").attr("data-assetid")), typatransaction: AllowedShittyIDsID[id] }, function(data) {
							if(data['error']) {
								ANORRL.Item.Purchasing.PresentError(data['message']);
							} else {
								$("#ModalPopup > div:visible").each(function() {
									$(this).css("display", "none");
								});
								ANORRL.Item.State = 2;
								$("#PurchaseSuccess").css("display", "block");
							}
						})
					}, 1500);
				} else {
					ANORRL.Item.Purchasing.PresentError("Something went wrong during the transaction... Sorry...")
				}
			} else {
				ANORRL.Item.Purchasing.PresentError("Something went wrong during the transaction... Sorry...");
			}
		},
		PresentError: function(error) {
			$("#ModalPopup > div:visible").each(function() {
				$(this).css("display", "none");
			});

			var errorpanel = $("#PurchaseError");
			errorpanel.find("#Error").html(error);
			errorpanel.css("display", "block");
		},
		ClosePurchasePanel: function() {
			if(ANORRL.Item.State == 1) {
				return;
			}
			if($("div#PurchaseProcessing:visible").size() == 0) {
				/* idfk why i even wrote this in the first place being dead ass
				$("#ModalPopup > div:visible").each(function() {
					$(this).css("display", "none");
				});*/

				if(ANORRL.Item.State == 2) {
					window.location.reload();
				}
				

				$("#PurchasePanel").css("display", "none");
			}

		}
	}
};

$(function() {
	$(".FavouriteButton").click(function() {
		ANORRL.Item.Favourite($(this).attr("data-assetid"));
	});

	$("#ModalPopup").on("click", function(evt) {
		evt.stopPropagation();
	})

	$("#PurchasePanel").on("click", function() {
		ANORRL.Item.Purchasing.ClosePurchasePanel();
	})
})