if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
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
				
				var content = template.find("#content code");

				content.html(status['content']);
				template.find("#user a").attr("href", "/users/"+status['poster']['id']+"/profile");
				template.find("#user a").find("img").attr("data-src", status['poster']['thumbnail']);
				template.find("#user a").find("img").lazy();
				template.find("#content #name").html(status['poster']['name']);
				template.find("#content #name").parent().attr("href", "/users/"+status['poster']['id']+"/profile");
				template.find("#content #date-posted #date").html(status['time_posted_label']);
				template.find("#content #date-posted #report").attr("href", "/report?statusid="+status['id']);

				feedscontainer.append($(template));
				
				// this code is AFTER appending because the required properties get calculated when they are finally displayed - grace
				var is_overflowing = content.prop("scrollHeight") > content.innerHeight();
				if(!is_overflowing)
					template.find("#content #scroll-arrow").css("opacity", 0)

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