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

var allowedcategories = {
	"shirts": 11,
	"tshirts": 2,
	"pants": 12,
	"audio": 3,
	"decals": 13,
	"models": 10,
	"hats": 8,
	"faces": 18,
	"emotes": 61,
	"animations": 24,
}

var shouldnotbeallowedatallcategories = {
	"badges": 21,
	"gamepasses": 34,
	"places": 9,
}

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Stuff  = {
	isAdmin: false,
	CurrentPage: 1,
	CurrentCategory: 8,
	CurrentlyLoadingCrapBruh: false,
	CurrentQuery: "",
	Submit: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage, $("#SearchBox[name=query]").val());
	},
	AdvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage + 1);
	},
	DeadvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage - 1);
	},
	GrabAssets: function(category, page, query) {

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		var loadingMessage = $("#AssetsContainer #StatusText #Loading");
		var emptyMessage = $("#AssetsContainer #StatusText #NoAssets");

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

		var feedscontainer = $("#AssetsContainer > table");

		feedscontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#AssetsContainer #Paginator");
		
		var backPager = pagercontainer.find("#PrevPager");
		var nextPager = pagercontainer.find("#NextPager");

		$("li[data_category]").each(function() {
			$(this).removeAttr("selected");
		});

		var categorylabel = $("li[data_category="+category+"]").find("a").html().toLowerCase().replaceAll("-", "");

		$("li[data_category="+category+"]").attr("selected", "");
		if(shouldnotbeallowedatallcategories[categorylabel] == undefined && (allowedcategories[categorylabel] != undefined || this.isAdmin)) {
			if(categorylabel == "places") {
				$($("#CreateArea").find("a")[0]).attr("href","javascript:alert('Use ANORRL Studio for this!')");
			} else {
				$($("#CreateArea").find("a")[0]).attr("href","/create/"+categorylabel);
			}
			$($("#CreateArea").find("a")[0]).removeAttr("disabled");
			
		} else {
			$($("#CreateArea").find("a")[0]).removeAttr("href");
			$($("#CreateArea").find("a")[0]).attr("disabled", "true");
		}
		
		ANORRL.ChangeUrl("", "/my/stuff#"+categorylabel);

		$.get("/api/stuff", {c: category, p : page, q: query}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Stuff.CurrentPage = data['page'];
			var current_page = ANORRL.Stuff.CurrentPage;
			var total_pages = data['total_pages'];

			feedscontainer.attr("hidden", true);

			if(assets.length == 0) {
				if(pagercontainer.css("display") == "block") {
					pagercontainer.css("display", "none");
				}
				loadingMessage.css("display", "none");
				emptyMessage.css("display", "block");

				emptyMessage.find("#AssetType").html($("li[data_category="+category+"]").find("a").html().toLowerCase());

				
			} else {
				loadingMessage.css("display", "none");
				if(pagercontainer.css("display") == "none") {
					pagercontainer.css("display", "block");
				}

				var index = 0;
				var rowIndex = 0;
				
				for (var key in assets) {
					if(index % 4 == 0 || index == 0) {
						feedscontainer.append($("<tr></tr>"));
						if(index % 4 == 0  && index != 0) {
							rowIndex++;
						}
					} 

					var asset = assets[key];

					var td = $("<td></td>");
					var template = $($(".Asset[template]").clone().prop('outerHTML'));
					td.append(template);
					template.removeAttr("template");
					
					template.find("#Pricing").attr("oneprice", "true");
					template.find("#Pricing").children().each(function() {
						$(this).remove();
					});
					template.find("#Pricing").append($("<span id=\"FreeTag\">Free</span>"))
					
					var urlname = asset['name'].replaceAll(regex, "").trim().toLowerCase().replaceAll(" ", "-");
					if(urlname == "") {
						urlname = "unnamed";
					}

					template.find("#NameAndThumbs > img").attr("src", asset['thumbnail']);

					template.find("#NameAndThumbs > span").html(asset['name']);
					template.find("#NameAndThumbs").attr("href", "/"+urlname+"-item?id="+asset['id']);

					template.find("#Creator > span").html(asset['creator']['name']);
					template.find("#Creator").attr("href", "/users/"+asset['creator']['id']+"/profile");

					// implement details
					feedscontainer.removeAttr("hidden");
					$(feedscontainer.find("tr")[rowIndex]).append(td);

					index++;
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
				pagercontainer.find("#Pages").html(total_pages);
			}

			ANORRL.Stuff.CurrentlyLoadingCrapBruh = false;
		});
	}
}

$(function(){

	$("li[data_category]").on("click",function() {
		ANORRL.Stuff.GrabAssets($(this).attr("data_category"), ANORRL.Stuff.CurrentPage);
	});

	if(window.location.hash != "") {
		var url = window.location.hash;
		url = url.replaceAll("#", "").replaceAll("/","");
		var categories = {
			"hats": 8,
			"faces": 18,
			"shirts": 11,
			"tshirts": 2,
			"pants": 12,

			"audio": 3,
			"decals": 13,
			"models": 10,
			"places": 9,

			"gears": 19,
			"badges": 21,
			"gamepasses": 34,
			"packages": 32,
			"animations": 24,
			"emotes": 61,
		}

		ANORRL.Stuff.GrabAssets(categories[url]);
	} else {
		ANORRL.Stuff.GrabAssets();
	}

	$("#Paginator").find("input").on("change", function() {
		ANORRL.Stuff.GrabAssets(ANORRL.Stuff.CurrentCategory, Number($(this).val()));
	});

	$("#SearchBox").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Stuff.Submit();
		}
	});

	$.get("/api/user?id=0&request=isadmin", function(data) {
		ANORRL.Stuff.isAdmin = data['isadmin'];
	});
});