if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Vandals = {
	CurrentStatusPage: 1,
	CurrentStatusQuery: "",
	NextPage: function() {
		this.GrabFeed(this.CurrentStatusQuery, this.CurrentStatusPage + 1);
	},
	PrevPage: function() {
		this.GrabFeed(this.CurrentStatusQuery, this.CurrentStatusPage - 1);
	},

	Submit: function() {
		this.GrabFeed($("#search-box[name=query]").val(), 1);
	},

	CreatePlayerRow: function(data) {
		var $template = $("<tr></tr>");
		$template.attr("class", "user")

		var $userprofile = $("<td></td>");
		$userprofile.attr("id", "profile");
		
		var $userprofilelink = $("<a></a>");
		$userprofilelink.attr("href", "/users/"+data['id']+"/profile");
		$userprofilelink.attr("title", data['name']);

		var image = $("<img>");
		image.attr("data-src", data["thumbnail"]);
		image.attr("src", "/public/images/spinner100x100_white.gif");
		image.attr("width", "64");
		image.attr("height", "64");

		image.lazy();

		$userprofilelink.append(image);
		$userprofile.append($userprofilelink);


		var statusLabel = data['online'] ? "Online" : "Offline";
		

		var $username = $("<td></td>");
		$username.attr("id", "username");
		$username.append($("<img src='/public/images/OnlineStatusIndicator_Is"+statusLabel+".png'> <a href='/users/"+data['id']+"/profile'>"+data['name']+"</a>"))

		var $userbio = $("<td></td>");
		$userbio.attr("id", "status");

		//word-break: break-word;overflow-wrap: anywhere;
		if(data['blurb'] == "") {
			$userbio.html("<b>No blurb set</b>");
		} else {
			$userbio.html(data['blurb']);
		}
		
		var $userstatus = $("<td></td>");
		$userstatus.attr("id", "activity")
		$userstatus.html(data['online'] ? data['status'] : "Offline");
		if($userstatus.html().includes("In Game") || $userstatus.html().includes("In Team Create") || $userstatus.html().includes("'s profile")) {

		} else {
			$userstatus.find("a").attr("style","width: 180px;display: inline-block;text-overflow: ellipsis;overflow: hidden;");
		}
		

		$template.append($userprofile);
		$template.append($username);
		$template.append($userbio);
		$template.append($userstatus);

		return $template;

	},

	GrabFeed: function(query, page) {
		if(query === undefined) {
			query = this.CurrentStatusQuery;
		} else {
			this.CurrentStatusQuery = query;
		}
		if(page === undefined) {
			page = this.CurrentStatusPage;
		} else {
			this.CurrentStatusPage = page;
		}

		var feedscontainer = $("#users-container");
		var tbody = feedscontainer.find("tbody");
		
		tbody.children().each(function() {
			if(!$(this).html().includes("</th>"))
				$(this).remove();
		});

		var pagercontainer = $("#pager");
		var fetchingRow = $("<tr></tr>");
		var fetchingCell = $("<td colspan='4' style='text-align:center;padding:20px;font-weight:bold;'></td>");
		fetchingCell.text("Loading vandals...");
		fetchingRow.append(fetchingCell);
		tbody.append(fetchingRow);
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		$.get("/api/vandals", {q: query, p : page}, function(data) {
			
			
			var users = data['users'];
			if(users.length == 0) {
				fetchingCell.text("No vandals found!");
			} else {
				if(data['total_pages'] > 1) {
					if(pagercontainer.css("display") == "none") {
						pagercontainer.css("display", "block");
					}
				} else {
					if(pagercontainer.css("display") == "block") {
						pagercontainer.css("display", "none");
					}
				}
				fetchingRow.remove();
				ANORRL.Vandals.CurrentStatusPage = data['page'];
				var current_page = ANORRL.Vandals.CurrentStatusPage;
				var total_pages = data['total_pages'];

				var index = 0;
				
				for (var key in users) {
					var user = users[key];

					feedscontainer.append(ANORRL.Vandals.CreatePlayerRow(user));

					index += 1;
				}

				if(current_page == 1) {
					backPager.css("display", "none");
				} else {
					backPager.css("display", "inline");
				}

				if(current_page == total_pages) {
					nextPager.css("display", "none");
				} else {
					nextPager.css("display", "inline");
				}

				pagercontainer.find("#page-counter").html(total_pages);
				pagercontainer.find("input").val(current_page);
			}
			
		});
	}
}

$(function(){
	ANORRL.Vandals.GrabFeed();

	$("#pager").find("input").on("change", function() {
		ANORRL.Vandals.GrabFeed(ANORRL.Vandals.CurrentStatusQuery, Number($(this).val()));
	});

	$("#search-box[name=query]").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Vandals.Submit();
		}
	});
});
