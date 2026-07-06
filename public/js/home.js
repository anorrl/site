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

		var feedscontainer = $("#feeds");

		feedscontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

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

				var template = $($(".feed-item[template]").clone().prop('outerHTML'));
				template.removeAttr("template");
				template.removeAttr("style");

				if(index % 2 == 0) {
					template.attr("other", "");
				}

				template.find("#content code").html(status['content']);
				template.find("#user a").attr("href", "/users/"+status['poster']['id']+"/profile");
				template.find("#user a").find("img").attr("src", status['poster']['thumbnail']);
				template.find("#content #name").html(status['poster']['name']);
				template.find("#content #date-posted #date").html(status['time_posted_label']);
				template.find("#content #date-posted a").attr("href", "/report?statusid="+status['id']);
				
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

			pagercontainer.find("#page-counter").html("" + current_page + " of " + total_pages);
		});
	}
}

$(function(){
	ANORRL.Home.GrabFeed();
});