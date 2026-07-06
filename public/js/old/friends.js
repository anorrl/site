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

ANORRL.Friends = {
	Remove: function(id) {
		$.post("/api/user", { id: id, request: "unfriend"}, function(data) {
			if(data['error']) {
				alert(data['reason']);
			} else {
				window.location.reload();
			}
		});
	},
	Reject: function(id) {
		this.Remove(id);
	},
	Cancel: function(id) {
		this.Remove(id);
	},
	Accept: function(id) {
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
