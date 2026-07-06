if(ANORRL == undefined) {
	ANORRL = {};
}

const regex = /[^A-Za-z0-9 ]/g;

ANORRL.Games = {
	CurrentFilter: 7,
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
		this.LoadGames($("#SearchBox[name=query]").val(), 1, this.CurrentFilter);
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

		var loadingMessage = $("#Games #StatusText #Loading");
		var emptyMessage = $("#Games #StatusText #NoAssets");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");


		var gamescontainer = $("#ContainerThingy");

		gamescontainer.children().each(function() {
			$(this).remove();
		});
		
		var pagercontainer = $("#Games #Paginator");
		
		var backPager = pagercontainer.find("#BackPager");
		var nextPager = pagercontainer.find("#NextPager");

		$("li[data_filter]").each(function() {
			$(this).removeAttr("selected");
		});

		$("li[data_filter="+filter+"]").attr("selected", "");

		var original = $("#ANORRL_Games_OriginalGamesInput").is(":checked") ? 1 : 0;

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
					var template = $($(".Game[template]").clone().prop('outerHTML'));
					template.removeAttr("template");

					template.find("a").on("click", function(ev) {
						ev.stopPropagation(); // overrides container click so only this action is performed
						window.location.href = $(this).attr("href");
					});

					template.on("click", function() {
						if(ANORRL.Games.MobileEnabled) {
							window.location.href = "/games/start?placeid="+$(this).attr("data-placeid"); 
						} else {
							window.location.href = "/game/"+$(this).attr("data-placeid"); 
						}
						
					});

					template.find("#ImageContainer > img").attr("src", asset['thumbnail']);
					if(ANORRL.Games.MobileEnabled) {
						template.find("#GameName").attr("href", "/games/start?placeid="+asset['id']);
					} else {
						template.find("#GameName").attr("href", "/game/"+asset['id']);
					}
					
					template.find("#GameName").html(asset['name']);

					template.find("#GameCreator").html(asset['creator']['name']);
					template.find("#GameCreator").attr("href", "/users/"+asset['creator']['id']+"/profile");
					template.find("#ActivePlayerCount").html(asset['activeplayercount']);
					template.find("#VisitCount").html(asset['visitcount']);

					if(asset['original'] && !original) {
						template.find("#OriginalArea").css("display", "block");
					}
					
					template.find("#FavouritesArea > span").html(asset['favouritescount']);

					if(asset['activeplayercount'] == 1) {
						template.find("#ActivePlayerCountLabel #Plural").remove();
					}

					if(asset['visitcount'] == 1) {
						template.find("#VisitCountLabel #Plural").remove();
					}

					template.attr("data-placeid", asset['id']);

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
				pagercontainer.find("#Counter").html(total_pages);
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

	$("li[data_filter]").on("click",function() {
		ANORRL.Games.LoadGames(ANORRL.Games.CurrentQuery, ANORRL.Games.CurrentPage, $(this).attr("data_filter"));
	});

	$("#SearchBox").on("keypress", function(e) {
		if(e.keyCode == 13) {
			ANORRL.Games.Submit();
		}
	});

	$("#Games #Paginator").find("input").on("change", function() {
		ANORRL.Games.LoadGames(ANORRL.Games.CurrentQuery, Number($(this).val()));
	});
})
