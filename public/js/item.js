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
		OpenPurchasePanel: function() {
			$("#PurchasePanel").css("display", "block");
		},
		PurchaseItem: function() {
			if($("#ModalPopup > div:visible").size() == 1) {
				var prompt = $("#ModalPopup > div:visible");

				prompt.css("display","none");

				ANORRL.Item.State = 1;
				$("#ModalPopup #PurchaseProcessing").css("display", "block");
				window.setTimeout(function() {
					$.post("/api/purchase", { asset_id: Number($("#ModalPopup").attr("data-assetid")) }, function(data) {
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
	},
	Has3DEnabled: function() {
		return $(".thumbnail-span").length != 0;
	},
	Load3D: function() {
		if(!this.Has3DEnabled())
			return;

		$(".thumbnail-holder > img ").css("display", "none");
		$(".thumbnail-span").css("display", "block");

		$(".thumbnail-span").load3DThumbnail("asset", function(canvas) {
			console.log("3D: complete!");
		}, function() {
			console.log("3D: I dont like you");
			$(".thumbnail-holder > img ").css("display", "block");
			$(".thumbnail-span").css("display", "none");
		});
	},
	Load2D: function() {
		if(!this.Has3DEnabled())
			return;

		$(".thumbnail-holder > img ").css("display", "block");
		$(".thumbnail-span").css("display", "none");

		$(".thumbnail-span canvas").remove();
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

	if(ANORRL.Item.Has3DEnabled()) {
		$("#ThumbnailSwitcher").on("click", function() {
			if($(this).attr("data-3d") == "true") {
				$(this).attr("data-3d", false);
				ANORRL.Item.Load2D();
			} else {
				$(this).attr("data-3d", true);
				ANORRL.Item.Load3D();
			}
		})
	}

})