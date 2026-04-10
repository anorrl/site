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

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Catalog  = {
	CurrentPage: 1,
	CurrentFilter: 1,
	CurrentCategory: 8,
	CurrentQuery: "",
	CurrentlyLoadingCrapBruh: false,
	Submit: function() {
		this.GrabAssets(this.CurrentFilter, this.CurrentCategory, 1, $("#SearchBox[name=query]").val());
	},
	NextPage: function() {
		this.GrabAssets(this.CurrentFilter, this.CurrentCategory, this.CurrentPage + 1);
	},
	PrevPage: function() {
		this.GrabAssets(this.CurrentFilter, this.CurrentCategory, this.CurrentPage - 1);
	},
	GrabAssets: function(filter, category, page, query) {

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
			page = 1;
		}

		if(filter === undefined) {
			filter = this.CurrentFilter;
		} else {
			this.CurrentFilter = filter;
		}

		if(query === undefined) {
			query = this.CurrentQuery;
		} else {
			this.CurrentQuery = query;
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

		$("li[data_filter]").each(function() {
			$(this).removeAttr("selected");
		});

		$("li[data_category="+category+"]").attr("selected", "");
		$("li[data_filter="+filter+"]").attr("selected", "");
		
		$.get("/api/catalog", {f: filter, c: category, q: query, p : page}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Catalog.CurrentPage = data['page'];
			var current_page = ANORRL.Catalog.CurrentPage;
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
					
					/*if(asset['onsale']) {
						var salecount = asset['sales_count']+" times";
						if(asset['sales_count'] == 1) {
							salecount = asset['sales_count']+" time";
						}

						template.find("#Pricing").append($("<span id=\"FreeTag\">Sold: "+ salecount +"</span>"));
					} else {
						template.find("#Pricing").append($("<span id=\"NotOnSaleTag\">Not on sale</span>"))
					}*/

					if(asset['onsale']) {
						if(asset['cost']['cones'] + asset['cost']['lights'] == 0) {
							template.find("#Pricing").attr("oneprice", "true");
							template.find("#Pricing").children().each(function() {
								$(this).remove();
							});
							template.find("#Pricing").append($("<span id=\"FreeTag\">Free</span>"))
						} else {

							if(asset['cost']['cones'] == 0) {
								template.find("#Pricing #Cones").remove();
							} else {
								template.find("#Pricing #Cones #Costing").html(asset['cost']['cones']);
							}

							if(asset['cost']['lights'] == 0) {
								template.find("#Pricing #Lights").remove();
							} else {
								template.find("#Pricing #Lights #Costing").html(asset['cost']['lights']);
							}


							if(asset['cost']['lights'] != 0 && asset['cost']['cones'] != 0) {
								template.find("#Pricing").removeAttr("oneprice");
							} else {
								template.find("#Pricing").attr("oneprice", "true");
							}

						}
					} else {
						template.find("#Pricing").attr("oneprice", "true");
						template.find("#Pricing").children().each(function() {
							$(this).remove();
						});
						template.find("#Pricing").append($("<span id=\"NotOnSaleTag\">Not on sale</span>"))
					}


					
					var urlname = asset['name'].replaceAll(regex, "").trim().toLowerCase().replaceAll(" ", "-");
					if(urlname == "") {
						urlname = "unnamed";
					}

					template.find("#NameAndThumbs > img").attr("src", "/thumbs/?id="+asset['id']+"&sxy=130");

					template.find("#NameAndThumbs > span").html(asset['name']);
					template.find("#NameAndThumbs").attr("href", "/"+urlname+"-item?id="+asset['id']);

					template.find("#Creator > span").html(asset['creator']['name']);
					template.find("#Creator").attr("href", "/users/"+asset['creator']['id']+"/profile");

					template.find("#FavouritesArea > span").html(asset['favourites']);

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
				pagercontainer.find("#Counter").html(total_pages);
			}

			ANORRL.Catalog.CurrentlyLoadingCrapBruh = false;
		});
	}
}

$(function(){

	$("li[data_category]").on("click",function() {
		ANORRL.Catalog.GrabAssets(ANORRL.Catalog.CurrentFilter, $(this).attr("data_category"), ANORRL.Catalog.CurrentPage, "");
	});

	$("li[data_filter]").on("click",function() {
		ANORRL.Catalog.GrabAssets($(this).attr("data_filter"), ANORRL.Catalog.CurrentCategory, ANORRL.Catalog.CurrentPage, "");
	});
	
	ANORRL.Catalog.GrabAssets();

	$("#SearchBox").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Catalog.Submit();
		}
	});

	$("#Paginator").find("input").on("change", function() {
		ANORRL.Catalog.GrabAssets(ANORRL.Catalog.CurrentFilter, ANORRL.Catalog.CurrentCategory, Number($(this).val()));
	});
});