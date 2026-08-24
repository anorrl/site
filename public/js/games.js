ANORRL = {};

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Games = {
	CurrentFilter: 9,
	CurrentPage: 1,
	CurrentQuery: "",
	MobileEnabled: false,
	LoadNoQueryGames: function(page) {
		if(page === undefined) {
			page = 1;
		}

		this.LoadGames("", page, this.CurrentFilter);
	},
	Submit: function() {
		this.LoadGames($("#search-box[name=query]").val(), 1, this.CurrentFilter);
	},
	NextPage: function() {
		this.LoadGames(this.CurrentQuery, this.CurrentPage + 1, this.CurrentFilter);
	},
	PrevPage: function() {
		this.LoadGames(this.CurrentQuery, this.CurrentPage - 1, this.CurrentFilter);
	},
	LoadGames: function(query, page, filter) {

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
		if(page === undefined) {
			page = this.CurrentPage;
		} else {
			this.CurrentPage = page;
		}

		var loadingMessage = $("#loading-status");
		var emptyMessage   = $("#nothing-status");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");

		var gamescontainer = $("#games-container");

		gamescontainer.children().each(function() {
			$(this).remove();
		});
		
		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		$("li[data-filter]").each(function() {
			$(this).removeAttr("selected");
			$(this).find("img").remove();
		});

		var currentFilterElement = $("li[data-filter="+filter+"]");
		var spanFilter = currentFilterElement.find("span");

		currentFilterElement.attr("selected", "");
		$("#filter-name").html(spanFilter.html());
		spanFilter.prepend('<img src="/public/images/icons/selection.png">');

		var original = 1; //$("#ANORRL_Games_OriginalGamesInput").is(":checked") ? 1 : 0;

		$.get("/api/games", {f: filter, q: query, p : page, o: original}, function(data) {

			var games = data['games'];
			ANORRL.Games.CurrentPage = data['page'];
			var current_page = ANORRL.Games.CurrentPage;
			var total_pages = data['total_pages'];

			gamescontainer.attr("hidden", true);

			if(games.length == 0) {
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

				var index = 0;
				
				for (var key in games) {
					var asset = games[key];
					var template = $($(".game[template]").clone().prop('outerHTML'));
					template.removeAttr("template");

					/*template.find("a").on("click", function(ev) {
						ev.stopPropagation(); // overrides container click so only this action is performed
						window.location.href = $(this).attr("href");
					});*/
					
					if(ANORRL.Games.MobileEnabled) {
						template.on("click", function() {
							window.location.href = "/games/start?placeid="+$(this).data("placeid"); 
						});
					}

					/*if(asset['original'] && !original) {
						template.find("#OriginalArea").css("display", "block");
					}
					
					template.find("#FavouritesArea > span").html(asset['favouritescount']);

					template.data("placeid", asset['id']);*/

					var one_play = asset['visits'] == 1;
					var one_player = asset['activeplayers'] == 1;


					template.attr("title", asset['name'] + " by " + asset['creator']['name']);

					template.find("img").attr("data-src", asset['thumbnail']);
					template.find("img").lazy();
					template.find("#name").html(asset['name']);
					template.find(" > a").attr("href", asset['url']);
					template.find("#creator > a").html(asset['creator']['name']);
					template.find("#creator > a").attr("href", "/users/"+asset['creator']['id']+"/profile");
					template.find("#visit-count").html("played " + asset['visits'] +(one_play ? " time": " times"));
					template.find("#currently-playing").html(asset['activeplayers'] + " player" + (one_player ? "":"s") + " online");

					gamescontainer.append(template);

					// implement details
					gamescontainer.removeAttr("hidden");
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

				ANORRL.Games.CurrentPage = current_page;
				pagercontainer.find("input").val(current_page);
				pagercontainer.find("#page-counter").html(total_pages);
			}

			//ANORRL.Stuff.CurrentlyLoadingCrapBruh = false;
		}, null, "gzip");
	}
};

$(function() {
	ANORRL.Games.LoadNoQueryGames();

	$("#ANORRL_Games_OriginalGamesInput").on("click", function() {
		ANORRL.Games.Submit();
	})

	$("li[data-filter]").on("click",function() {
		ANORRL.Games.LoadGames(ANORRL.Games.CurrentQuery, ANORRL.Games.CurrentPage, $(this).data("filter"));
	});

	$("#search-box").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Games.Submit();
		}
	});

	$("#pager").find("input").on("change", function() {
		ANORRL.Games.LoadGames(ANORRL.Games.CurrentQuery, Number($(this).val()));
	});
})
