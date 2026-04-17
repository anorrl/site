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
	1:"image/*",
	11:"image/*",
	18:"image/*",
	2: "image/*",
	12:"image/*",
	3: ".mp3,.ogg,.wav",
	13:"image/*",
	10:".rbxm,.rbxmx",
	4:"*",
	5:".txt,.lua",
	8: ".rbxm,.rbxmx",
	24: ".rbxm,.rbxmx",
}

var versionshit = [
	1, 3, 13
];

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Create = {
	CurrentPage: 1,
	CurrentCategory: 11,
	CurrentlyLoadingCrapBruh: false,
	AdvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage + 1);
	},
	DeadvancePager: function() {
		this.GrabAssets(this.CurrentCategory, this.CurrentPage - 1);
	},
	GrabAssets: function(category, page) {

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

		if(category != 8) {
			$("#HatUploadRules").css("display", "none");
		} else {
			$("#HatUploadRules").css("display", "block");
		}
		
		if(category != 19) {
			$("#GearUploadRules").css("display", "none");
		} else {
			$("#GearUploadRules").css("display", "block");
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
		if(!Number(category)) {
			ChangeUrl("", "/create/"+category);
		} else {
			ChangeUrl("", "/create/"+$("li[data_category="+category+"]").find("a").html().toLowerCase().replaceAll("-", "").replaceAll(" ", ""));
		}
		

		var categorylabel = $("li[data_category="+category+"]").find("a").html();
		if(categorylabel.endsWith("s") && categorylabel != "Pants" && categorylabel != "Meshes") {
			categorylabel = categorylabel.substring(0, categorylabel.length-1);
		}

		if(categorylabel.endsWith("es")) {
			categorylabel = categorylabel.substring(0, categorylabel.length-2);
		}

		$("#TypaLabel").html(categorylabel);
		if(categorylabel == "Pants" || categorylabel == "Shirt") {
			var template_name = categorylabel+"Template";
			var template_image_path = "/public/images/"+template_name+".png";
			
			var template_window = $(".Window#ShirtPantsTemplate");
			var template_link = template_window.find("#Contents a");
			var template_image = template_link.find("img");
			
			template_link.attr("download", template_name+".png");
			template_link.attr("href", template_image_path);
			template_image.attr("src", template_image_path);

			template_window.find("#Name > #Title").html(categorylabel+" Template");
			template_window.css("display", "block");
		} else {
			$(".Window#ShirtPantsTemplate").css("display", "none");
		}

		if(categorylabel == "Body Type") {
			$("#bodytyperow").removeAttr("style");
		} else {
			$("#bodytyperow").css("display", "none");
		}
		
		$("#files").attr("accept", categoryFileTypes[category]);

		var warning = $("#InfoWarning");

		if(category == 10 || category == 9) {
			warning.css("display", "block");
		} else {
			warning.css("display", "none");
		}

		$.get("/api/stuff", {c: category, p : page, showcreatoronly: true}, function(data) {
			
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

					template.find("#Pricing").remove();
					

					template.find("#NameAndThumbs > img").attr("src", asset['thumbnail']);

					template.find("#NameAndThumbs > span").html(asset['name']);
					template.find("#NameAndThumbs").attr("href", "/"+asset['name'].replaceAll(regex,"").trim().replaceAll(" ", "-").toLowerCase()+"-item?id="+asset['id']);

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

	var url = window.location.pathname;
	url = url.replaceAll("/create/", "").replaceAll("/","");

	$("#files").change(function() {
		filename = this.files[0].name;
		$("#filename").html(filename);
	});

	// TODO: Move this out of here this is such a disaster waiting to come
	var categories = {
		"hats": 8,
		"faces": 18,
		"shirts": 11,
		"tshirts": 2,
		"pants": 12,

		"audio": 3,
		"decals": 13,
		"models": 10,
		
		"gears": 19,
		"images": 1,
		"packages": 32,
		"meshes": 4,
		"lua": 5,
		"animations": 24,
		"body": "body"
	}

	ANORRL.Create.GrabAssets(categories[url]);
});

