if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Ratings = {
	Rate: function(positive) {
		var DataStyuug =  positive ? {'ANORRL$Rate$Positive' : true} : {'ANORRL$Rate$Negative': true};
		$.post(this.GetURL().replace("ratings", "rate"), DataStyuug, function(data) {
			if(!data['success'])
				ANORRL.MessageBox.Show(2, data['reason']);

			ANORRL.Ratings.DooDaGet();
		});
	},
	UpdateRatings: function(data) {
		if(!data['can_vote'])
			$("#controls").css("pointer-events","none");
		else
			$("#controls").removeAttr("style");

		var upvotes = data['positives'];
		var downvotes = data['negatives'];
		var total = upvotes+downvotes;

		$("#up-count").html(upvotes);
		$("#down-count").html(downvotes);

		if(total != 0) {
			if(upvotes == 0) {
				$(".ratings-bar").attr("red", "yeah")
				$(".ratings-bar").removeAttr("green")
			}
			else if(downvotes == 0) {
				$(".ratings-bar").removeAttr("red");
				$(".ratings-bar").attr("green", "yeah");
			}
			else {
				$(".ratings-bar").attr("red", "yeah")
				var progress = $("<div></div>");

				progress.width((upvotes/total)*100+"%");

				$(".ratings-bar").append(progress);
			}
		}
		else {
			$(".ratings-bar").removeAttr("red")
			$(".ratings-bar").removeAttr("green")
		}
	},
	GetURL: function() {
		return "/asset/"+$("body").data("asset")+"/ratings";
	},
	DooDaGet: function() {
		$.get(this.GetURL(), ANORRL.Ratings.UpdateRatings);
	}
}

$(function(){
	ANORRL.Ratings.DooDaGet();

	
});