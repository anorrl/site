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

ANORRL.People = {
	CurrentStatusPage: 1,
	CurrentStatusQuery: "",
	AdvanceFeed: function() {
		this.GrabFeed(this.CurrentStatusQuery, this.CurrentStatusPage + 1);
	},
	DeadvanceFeed: function() {
		this.GrabFeed(this.CurrentStatusQuery, this.CurrentStatusPage - 1);
	},

	Submit: function() {
		this.GrabFeed($("#SearchBox[name=query]").val(), 1);
	},

	CreatePlayerRow: function(data) {
		var $template = $("<tr></tr>");

		var $userprofile = $("<td></td>");
		$userprofile.css("text-align", "center");
		
		var $userprofilelink = $("<a></a>");
		$userprofilelink.attr("href", "/users/"+data['id']+"/profile");
		$userprofilelink.attr("title", data['name']);
		$userprofilelink.append($("<img src=\""+data['thumbnail']+"\" style=\"width:64px;height:64px;border:2px solid black;\">"));
		$userprofile.append($userprofilelink);


		var statusLabel = data['online'] ? "Online" : "Offline";
		

		var $username = $("<td></td>");
		$username.css("text-align", "center");
		$username.append($("<img src='/public/images/OnlineStatusIndicator_Is"+statusLabel+".png'> <a href='/users/"+data['id']+"/profile'>"+data['name']+"</a>"))

		var $userbio = $("<td></td>");
		$userbio.css("word-break", "break-word");
		$userbio.css("overflow-wrap", "anywhere");

		//word-break: break-word;overflow-wrap: anywhere;
		if(data['blurb'] == "") {
			$userbio.html("<b>No blurb set</b>");
		} else {
			$userbio.html(data['blurb']);
		}
		
		var $userstatus = $("<td></td>");
		$userstatus.css("text-align", "center");
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

		var feedscontainer = $("#UsersDataTable");
		var tbody = feedscontainer.find("tbody");
		
		tbody.children().each(function() {
			if(!$(this).html().includes("</th>"))
				$(this).remove();
		});

		var pagercontainer = $("#Users #UsersNavLinks");
		var fetchingRow = $("<tr></tr>");
		var fetchingCell = $("<td colspan='4' style='text-align:center;padding:20px;font-weight:bold;'></td>");
		fetchingCell.text("Loading vandals...");
		fetchingRow.append(fetchingCell);
		tbody.append(fetchingRow);;
		
		var backPager = pagercontainer.find("#BackPager");
		var nextPager = pagercontainer.find("#NextPager");

		$.get("/api/people", {q: query, p : page}, function(data) {
			fetchingRow.remove();
			if(pagercontainer.css("display") == "none") {
				pagercontainer.css("display", "block");
			}
			var users = data['users'];
			ANORRL.People.CurrentStatusPage = data['page'];
			var current_page = ANORRL.People.CurrentStatusPage;
			var total_pages = data['total_pages'];

			var index = 0;
			
			for (var key in users) {
				var user = users[key];

				feedscontainer.append(ANORRL.People.CreatePlayerRow(user));

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

			pagercontainer.find("#Counter").html(total_pages);
			pagercontainer.find("#NumberPutter").val(current_page);
		});
	}
}

$(function(){
	ANORRL.People.GrabFeed();

	$("#UsersNavLinks").find("input").on("change", function() {
		ANORRL.People.GrabFeed(ANORRL.People.CurrentStatusQuery, Number($(this).val()));
	});

	$("#SearchBox").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.People.Submit();
		}
	});
});
