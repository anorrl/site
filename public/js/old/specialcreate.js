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

var categoryFileTypes = {
	21:"image/*",
}

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Create = {
	CurrentPlace: -1,
	StudioMode: true,
	CurrentPage: 1,
	CurrentCategory: 21,
	CurrentlyLoadingCrapBruh: false,
	AdvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage + 1);
	},
	DeadvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage - 1);
	},
	GrabAssets: function(category, page) {
		if(this.CurrentPlace < 1) {
			return;
		}

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
		if(page === undefined) {
			page = this.CurrentPage;
		}

		var feedscontainer = $("#AssetsContainer > table");
		feedscontainer.attr("hidden", "true");

		feedscontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#AssetsContainer #Paginator");
		
		var backPager = pagercontainer.find("#PrevPager");
		var nextPager = pagercontainer.find("#NextPager");

		$("li[data_category]").each(function() {
			$(this).removeAttr("selected");
		});

		$("li[data_category="+category+"]").attr("selected", "");
		/*if(!Number(category)) {
			ChangeUrl("", "/create/"+category);
		} else {
			ChangeUrl("", "/create/"+$("li[data_category="+category+"]").find("a").html().toLowerCase().replaceAll("-", "").replaceAll(" ", ""));
		}*/
		
		$("#files").attr("accept", categoryFileTypes[category]);

		var warning = $("#InfoWarning");

		if(category == 10 || category == 9) {
			warning.css("display", "block");
		} else {
			warning.css("display", "none");
		}

		$.get("/api/placestuff", {c: category, p : page, i: this.CurrentPlace}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Create.CurrentPage = data['page'];
			var current_page = ANORRL.Create.CurrentPage;
			var total_pages = data['total_pages'];

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
					

					template.find("#NameAndThumbs > img").attr("src", asset['thumbnail']);

					template.find("#NameAndThumbs > span").html(asset['name']);
					
					if(ANORRL.Create.StudioMode) {
						template.find("#NameAndThumbs").attr("href", "/edit?id="+asset['id']);
					} else {
						template.find("#NameAndThumbs").attr("href", asset['url']);
					}

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

			ANORRL.Create.CurrentlyLoadingCrapBruh = false;
		});
	}
}

function ChangeUrl(title, url) {
    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: title, Url: url };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        window.location.href = url;
    }
}

$(function(){
	$("li[data_category]").on("click",function() {
		ANORRL.Create.GrabAssets($(this).attr("data_category"));
	});

	var url = window.location.href;
	url = url.replace(window.location.origin, "").replace("/create/", "").split("/")[1].replace("/", "");

	$("#files").change(function() {
		filename = this.files[0].name;
		$("#filename").html(filename);
	});

	var categories = {
		"badge": 21
	};

	ANORRL.Create.CurrentPlace = $("#StuffContainer").attr("data-placeid");
	ANORRL.Create.StudioMode = $("#StuffContainer").attr("data-studio");
	ANORRL.Create.GrabAssets(categories[url]);
});

