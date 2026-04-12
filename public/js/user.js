if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

if (!Object.keys) {
	Object.keys = function(obj) {
		var keys = [];
		for (var i in obj) {
			if (obj.hasOwnProperty(i)) {
				keys.push(i);
			}
		}
		return keys;
	};
}

ANORRL.User = {
	GrabPlaceInfo: function(id) {
		
		$.get("/api/games", { placeid: id }, function(data) {
			if(!data['error']) {
				var place = data['place'];

				$("#NameAndCreator > a").html(place['name']);
				$("#NameAndCreator > a").attr("href","/game/"+place['id']);
				$("#ShowcaseBigImages > img").attr("src", place['thumbnail']);
				$("a#Play").attr("data-placejoinid", place['id']);

				if(place['description'].trim() == "") {
					$("#ShowcaseDetails > code").html("<b>No description provided...</b>");
				} else {
					$("#ShowcaseDetails > code").html(place['description'].replaceAll("\r\n", "<br>"));
				}
			} else {
				alert("Something went wrong, please try again!")
			}
		})
		//
	},
	JoinTheGame: function() {
		ANORRL.PlaceLauncher.LetsJoinAndPlay($("a#Play").attr("data-placejoinid"));
	},
	Follow: function(id) {
		$.post("/api/user", { id: id, request: "follow"}, function(data) {
			if(data['error']) {
				alert(data['reason']);
			} else {
				window.location.reload();
			}
		});
	},
	Friend: function(id) {
		$.post("/api/user", { id: id, request: "friend"}, function(data) {
			if(data['error']) {
				alert(data['reason']);
			} else {
				window.location.reload();
			}
		});
	}
}

$(() => {
	$("a[data-placeid]").on("click", function() {
		ANORRL.User.GrabPlaceInfo($(this).attr("data-placeid"));
	});

	var place = $("a[data-placeid]").first();
	ANORRL.User.GrabPlaceInfo(place.attr("data-placeid"));
});
