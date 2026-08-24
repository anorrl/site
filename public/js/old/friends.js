if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
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
		ANORRL.User.GrabPlaceInfo($(this).data("placeid"));
	});

	var place = $("a[data-placeid]").first();
	ANORRL.User.GrabPlaceInfo(place.data("placeid"));
});
