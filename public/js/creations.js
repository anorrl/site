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

ANORRL.Creations  = {
	CurrentPage: 1,
	CurrentCategory: 8,
	CurrentlyLoadingCrapBruh: false,
	CurrentQuery: "",
	Submit: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage, $("#search-box").val());
	},
	NextPage: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage + 1);
	},
	PrevPage: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage - 1);
	},
	GrabAssets: function(category, page, query) {

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		var loadingMessage = $("#loading-status");
		var emptyMessage   = $("#nothing-status");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");

		if(category === undefined) {
			category = this.CurrentCategory;
		} else {
			this.CurrentCategory = category;
		}

		if(query === undefined) {
			query = this.CurrentQuery;
		} else {
			this.CurrentQuery = query;
		}

		if(page === undefined) {
			page = 1;
		}

		var feedscontainer = $("div[data-loadtype]");

		feedscontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		$.get("/api/stuff?showcreatoronly", {c: category, p : page, q: query}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Creations.CurrentPage = data['page'];
			var current_page = ANORRL.Creations.CurrentPage;
			var total_pages = data['total_pages'];

			if(assets.length == 0) {
				if(pagercontainer.css("display") == "block") {
					pagercontainer.css("display", "none");
				}
				loadingMessage.css("display", "none");
				emptyMessage.css("display", "block");
				
			} else {
				loadingMessage.css("display", "none");
				if(pagercontainer.css("display") == "none") {
					pagercontainer.css("display", "block");
				}

				
				for (var key in assets) {

					var asset = assets[key];
					var template = $($("div[template]").clone().prop('outerHTML'));

					for(var item in asset) {
						var element = template.find("#"+item);
						var value = asset[item];
						if(typeof(element.attr("href")) != "undefined") {
							element.attr("href", value);
						} else if(typeof(element.attr("src")) != "undefined") {
							element.attr("src", value);
						} else {
							element.html(value);
						}

					}
					
					template.removeAttr("template");

					feedscontainer.append(template);
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

				pagercontainer.find("input").val(current_page);
				pagercontainer.find("#page-counter").html(total_pages);
			}

			ANORRL.Creations.CurrentlyLoadingCrapBruh = false;
		});
	}
}

$(function(){
	if($("div[data-loadtype]").length != 0) {
		if($("#pager").length == 0) {
			var pager = $("<div></div>");
			pager.attr("id", "pager");

			pager.append("<hr>");

			var backbtn = $("<a></a>");
			backbtn.attr("href", "#");
			backbtn.attr("id", "back-pager");
			backbtn.on("click", ANORRL.Creations.PrevPage);
			backbtn.html("&lt;&lt; back")

			pager.append(backbtn);
			// lazy
			pager.append($('<span>&nbsp;</span><input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">0</span><span>&nbsp;</span>'));

			var nextbtn = $("<a></a>");
			nextbtn.attr("href", "#");
			nextbtn.attr("id", "next-pager");
			nextbtn.on("click", ANORRL.Creations.NextPage);
			nextbtn.html("next &gt;&gt;");

			pager.append(nextbtn);

			$("div[data-loadtype]").parent().append(pager);

			$("#pager").find("input").on("change", function() {
				ANORRL.Creations.GrabAssets(ANORRL.Creations.CurrentCategory, Number($(this).val()));
			});
		}

		$("#search-box").on("keypress", function(e) {
			if(e.keyCode == 13) {
				ANORRL.Creations.Submit();
			}
		});

		ANORRL.Creations.GrabAssets($("div[data-loadtype]").attr("data-loadtype"));
	} else {
		alert("no feckin container to load m8")
	}
});