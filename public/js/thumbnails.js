if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Thumbnails = {
	// http://stackoverflow.com/questions/11871077/proper-way-to-detect-webgl-support
	// polygon function
	Supports3D: function() {
		try {
			var canvas = document.createElement("canvas");
			return !!window.WebGLRenderingContext && (
				canvas.getContext("webgl") || canvas.getContext("experimental-webgl"));
		} catch (e) {
			return false;
		}
	},
	Is3DActive: function() {
		if(!this.Has3DEnabled())
			return false;

		return $(".thumbnail-holder button").data("3d") == "true";
	},
	Has3DEnabled: function() {
		return $(".thumbnail-span").length != 0 && this.Supports3D();
	},
	Load3D: function() {
		if(!this.Has3DEnabled())
			return;

		$(".thumbnail-holder button").attr("data-3d", true);

		$(".thumbnail-holder > img").css("display", "none");
		$(".thumbnail-span").css("display", "block");

		$(".thumbnail-span").load3DThumbnail("avatar", function(canvas) {
			console.log("3D: complete!");
		}, function() {
			console.log("3D: I dont like you");

			$(".thumbnail-holder button").hide();
			
			ANORRL.Thumbnails.Load2D();
		});
	},
	Load2D: function() {
		if(!this.Has3DEnabled())
			return;

		$(".thumbnail-holder button").attr("data-3d", false);

		$(".thumbnail-holder > img").css("display", "inline");
		$(".thumbnail-span").css("display", "none");

		$(".thumbnail-span canvas").remove();
	}
}

$(function() {
	if(ANORRL.Thumbnails.Has3DEnabled()) {
		$(".thumbnail-holder button").on("click", function() {
			if($(this).data("3d") == "true") {
				ANORRL.Thumbnails.Load2D();
			} else {
				$(this).attr("data-3d", true);
				ANORRL.Thumbnails.Load3D();
			}
		})
	} else {
		if($(".thumbnail-holder").length != 0) {
			$(".thumbnail-holder").children().each(function() {
				if($(this).tagName() != "img")
					$(this).remove();
			})
		}
	}
});
