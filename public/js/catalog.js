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
		this.GrabAssets(this.CurrentFilter, this.CurrentCategory, 1, $("#search-box[name=query]").val());
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

		var loadingMessage = $("#loading-status");
		var emptyMessage   = $("#nothing-status");

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

		var items_container = $("#catalog-container");

		items_container.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		var currentFilterElement = $("li[data-category="+category+"]");
		var spanFilter = currentFilterElement.find("span");

		$("li[data-category]").each(function() {
			$(this).removeAttr("selected");
			$(this).find("img").remove();
		});

		currentFilterElement.attr("selected", "");
		$("#category-name").html(spanFilter.html());
		spanFilter.prepend('<img src="/public/images/icons/selection.png">');

		$("li[data-filter]").each(function() {
			$(this).removeAttr("selected");
			$(this).find("img").remove();
		});

		currentFilterElement = $("li[data-filter="+filter+"]");
		spanFilter = currentFilterElement.find("span");

		currentFilterElement.attr("selected", "");
		$("#filter-name").html(spanFilter.html());
		spanFilter.prepend('<img src="/public/images/icons/selection.png">');

		$("li[data-category="+category+"]").attr("selected", "");
		$("li[data-filter="+filter+"]").attr("selected", "");
		
		$.get("/api/catalog", {f: filter, c: category, q: query, p : page}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Catalog.CurrentPage = data['page'];
			var current_page = ANORRL.Catalog.CurrentPage;
			var total_pages = data['total_pages'];

			items_container.attr("hidden", true);

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

					var template = $($(".asset[template]").clone().prop('outerHTML'));
					template.removeAttr("template");
					
					template.find("#Pricing").attr("oneprice", "true");
					template.find("#Pricing").children().each(function() {
						$(this).remove();
					});
					
					if(asset['onsale']) {
						//template.find("#Pricing").append($("<span id=\"FreeTag\">Sold: "+ salecount +"</span>"));
					} else {
						//template.find("#Pricing").append($("<span id=\"NotOnSaleTag\">Not on sale</span>"))
					}

					var one_favourite = asset['favourites'] == 1;
					var one_sale = asset['sales'] == 1;


					template.attr("title", asset['name'] + " by " + asset['creator']['name']);
					template.find("img").attr("src", asset['thumbnail']);
					template.find("#name").html(asset['name']);
					template.find(" > a").attr("href", asset['url']);
					template.find("#creator > a").html(asset['creator']['name']);
					template.find("#creator > a").attr("href", "/users/"+asset['creator']['id']+"/profile");
					template.find("#sales #count").html(asset['sales'] +(one_sale ? " time": " times"));
					template.find("#favourites #count").html(asset['favourites'] +(one_favourite ? " time": " times"));
					template.find("#price").html("Free");

					items_container.append(template);
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

			ANORRL.Catalog.CurrentlyLoadingCrapBruh = false;
		});
	}
}

$(function(){

	$("li[data-category]").on("click",function() {
		ANORRL.Catalog.GrabAssets(ANORRL.Catalog.CurrentFilter, $(this).attr("data-category"), ANORRL.Catalog.CurrentPage, "");
	});

	$("li[data-filter]").on("click",function() {
		ANORRL.Catalog.GrabAssets($(this).attr("data-filter"), ANORRL.Catalog.CurrentCategory, ANORRL.Catalog.CurrentPage, "");
	});
	
	ANORRL.Catalog.GrabAssets();

	$("#search-box").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Catalog.Submit();
		}
	});

	$("#pager").find("input").on("change", function() {
		ANORRL.Catalog.GrabAssets(ANORRL.Catalog.CurrentFilter, ANORRL.Catalog.CurrentCategory, Number($(this).val()));
	});
});