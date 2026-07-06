if(ANORRL == undefined) {
	ANORRL = {};
}

ANORRL.Showcase = {
	ChangeShowcaseBox: function(url) {
		if(url.endsWith(".jpg")) {
			$("#ShowcaseBox img#Showcase").css("display", "none");
			$("#ShowcaseBox video#Showcase").css("display", "block");
			$("#ShowcaseBox video#Showcase").attr("src", url.replaceAll(".jpg", ".mp4"));
		} else {
			$("#ShowcaseBox video#Showcase").css("display", "none");
			$("#ShowcaseBox img#Showcase").css("display", "block");
			$("#ShowcaseBox img#Showcase").attr("src", url);
		}
	}
};

$(function() {
	$("#ShowcaseBox #Carousel img").on("click", function() {
		$("#ShowcaseBox #Carousel img").each(function() {
			$(this).removeClass("selected");
		})
		ANORRL.Showcase.ChangeShowcaseBox($(this).attr("src"));
		$(this).addClass("selected");
		console.log("selected");
	})
})
