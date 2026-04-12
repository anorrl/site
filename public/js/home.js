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

ANORRL.Home = {
	CurrentStatusPage: 1,
	AdvanceFeed: function() {
		this.GrabFeed(this.CurrentStatusPage + 1);
	},
	DeadvanceFeed: function() {
		this.GrabFeed(this.CurrentStatusPage - 1);
	},
	GrabFeed: function(page) {
		if(page === undefined) {
			page = 1;
		}

		var feedscontainer = $("#FeedsContainer #Feeds");

		feedscontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#FeedsContainer #Pager");
		
		var backPager = pagercontainer.find("#BackPager");
		var nextPager = pagercontainer.find("#NextPager");

		$.get("/api/feeds", {p : page}, function(data) {
			if(pagercontainer.css("display") == "none") {
				pagercontainer.css("display", "block");
			}
			var statuses = data['feed'];
			ANORRL.Home.CurrentStatusPage = data['page'];
			var current_page = ANORRL.Home.CurrentStatusPage;
			var total_pages = data['total_pages'];

			var index = 0;
			
			for (var key in statuses) {
				var status = statuses[key];

				var template = $($("#FeedItem[template]").clone().prop('outerHTML'));
				template.removeAttr("template");
				template.removeAttr("class");

				if(index % 2 == 0) {
					template.attr("other", "");
				}

				template.find("#Content code").html(status['content']);
				template.find(".User").find("a").attr("href", "/users/"+status['poster']['id']+"/profile");
				template.find(".User").find("a").find("img").attr("src", status['poster']['thumbnail']);
				template.find(".User #Name").html(status['poster']['name']);
				template.find("#Content #DatePosted #Date").html(status['time_posted_label']);
				template.find("#Content #DatePosted a").attr("href", "/report?statusid="+status['id']);
				
				feedscontainer.append($(template));

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

			pagercontainer.find("#PageCounter").html("Page " + current_page + " of " + total_pages);
		});
	}
}

$(function(){
	ANORRL.Home.GrabFeed();
});