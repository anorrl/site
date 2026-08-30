if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Creations  = {
	CurrentPage: 1,
	CurrentCategory: 8,
	CurrentlyLoadingCrapBruh: false,
	CurrentQuery: "",
	CurrentLimit: 12,
	Refresh: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage, this.CurrentQuery);
	},
	Submit: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage, $("#search-box").val());
	},
	NextPage: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage + 1);
	},
	PrevPage: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage - 1);
	},
	SetElementStuff: function(element, value) {
		if(typeof(element.attr("href")) != "undefined") {
			element.attr("href", value);
		} else if(typeof(element.attr("src")) != "undefined") {
			if(element.attr("src").length > 0) {
				element.attr("data-src", value);
				element.lazy();
			}
			else {
				element.attr("src", value);
			}
			
		} else if(typeof(element.attr("title")) != "undefined") {
			element.attr("title", value);
		} else {
			element.html(value);
		}

		if(typeof(element.attr("html")) != "undefined") {
			element.html(value);
		}
	},
	HandleWindowClick: function() {
		$(".cog").removeAttr("active");
		$(".cog-dropdown ul").css("display", "none");
	},
	HandleCogClick: function(event) {
		event.stopPropagation();

		var was_active = typeof($(this).attr("active")) == "undefined";

		ANORRL.Creations.HandleWindowClick();

		if(was_active) {
			$(this).attr("active",true);
			$(this).parent().find("ul").css("display", "block");
		}
	},
	HandleDropdownClick: function(event) {
		/* override */
		event.stopPropagation();
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

		this.CurrentLimit = Number(feedscontainer.data("limit"));
		if(this.CurrentLimit < 3 || isNaN(this.CurrentLimit)) {
			this.CurrentLimit = 12;
		}

		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		$.get("/api/creations", {c: category, p : page, q: query, l: this.CurrentLimit}, function(data) {
			
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
					var template = $($("[template]").clone().prop('outerHTML'));

					for(var item in asset) {
						var element = template.find("#"+item);
						var value = asset[item];

						if(typeof(template.attr("id")) != "undefined" && template.attr("id") == item) {
							ANORRL.Creations.SetElementStuff(template, value);
						}

						if(element.length > 1) {
							element.each(function() {
								ANORRL.Creations.SetElementStuff($(this), value);
							})
						} else {
							ANORRL.Creations.SetElementStuff(element, value);
						}
					}

					template.find(".cog").on("click", ANORRL.Creations.HandleCogClick);
					template.find(".cog-dropdown li").on("click", ANORRL.Creations.HandleDropdownClick);
					template.find(".cog-dropdown ul").data("universeid", asset['universe']);
					template.find(".cog-dropdown ul").data("id", asset['id']);

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

		ANORRL.Creations.GrabAssets($("div[data-loadtype]").data("loadtype"));
	} else {
		ANORRL.MessageBox.Show(2, "no feckin container to load m8")
	}

	$(window).click(ANORRL.Creations.HandleWindowClick);
});