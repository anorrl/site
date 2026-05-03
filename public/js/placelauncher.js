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

/*
<table class="Server">
	<td id="PlayersBox">
		<a title="Player" id="Player" href="/"><img src="/public/images/avatar.png"></a>
		
	</td>
	<td id="JoinBox" width="150">
		<div>
			1 / 4
		</div>
		<div>
			<button>Join Server</button>
		</div>
	</td>
</table>
*/

//

ANORRL.PlaceLauncher  = {
	CurrentlyLoadingCrapBruh: false,
	LetsJoinAndPlay: function(placeId) {
		$.post("/api/ticketer", {placeID: placeId}, function(data) {
			if(data == "") {
				alert("You need to be logged in to play!");
				return;
			} else if(!data.startsWith("anorrl-player")) {
				alert(data);
				return;
			}
			window.open(data, "_self");

			$("#LaunchingGameContainer").css("opacity", 1);
			$("#LaunchingGameContainer").css("pointer-events", "all");

			// im sorry
			window.setTimeout(function() {
				$("#LoadingAreaContainer").css("display", "none");
				$("#DownloadClientContainer").css("display", "inline");

				window.setTimeout(function() {
					$("#LaunchingGameContainer").css("opacity", 0);
					$("#LaunchingGameContainer").css("pointer-events", "none");
					
					window.setTimeout(function() {
						$("#LoadingAreaContainer").css("display", "inline");
						$("#DownloadClientContainer").css("display", "none");
					}, 1000);
				}, 5000);
			}, 2500);
		});

		
	},

	EditPlace: function(placeId) {
		$.post("/api/ticketer", {editID: placeId}, function(data) {
			if(data == "") {
				alert("You need to be logged in to play!");
				return;
			} else if(!data.startsWith("anorrl-studio")) {
				alert(data);
				return;
			}
			window.open(data, "_self");

			$("#LaunchingGameContainer").css("opacity", 1);
			$("#LaunchingGameContainer").css("pointer-events", "all");

			// im sorry
			window.setTimeout(function() {
				$("#LoadingAreaContainer").css("display", "none");
				$("#DownloadStudioContainer").css("display", "inline");

				window.setTimeout(function() {
					$("#LaunchingGameContainer").css("opacity", 0);
					$("#LaunchingGameContainer").css("pointer-events", "none");
					
					window.setTimeout(function() {
						$("#LoadingAreaContainer").css("display", "inline");
						$("#DownloadStudioContainer").css("display", "none");
					}, 1000);
				}, 5000);
			}, 2500);
		});
	},
	
	CreateServerElement: function(placeID, serverId, currentPlayersCount, maxPlayersCount, isOwner) {
		var table = $("<table><tr></tr></table>");
		table.addClass("Server");

		var trRow = table.find("tr");

		var playersBox = $("<td></td>");
		playersBox.attr("id", "PlayersBox");
		playersBox.appendTo(trRow);

		var joinBox = $("<td></td>");
		joinBox.attr("id", "JoinBox");
		joinBox.attr("width", "150");
		
		joinBox.append("<div id=\"PlayerCount\">"+currentPlayersCount+" / "+maxPlayersCount+"</div>");
		
		var joinArea = $("<div id=\"JoinArea\"></div>");

		var joinButton = $("<button>Join Server</button>");

		joinButton.on("click", function() {
			$.post("/api/ticketer", {serverID: serverId}, function(data) {
				if(data == "") {
					alert("You need to be logged in to play!");
					return;
				} else if(!data.startsWith("anorrl-player")) {
					alert(data);
					return;
				}
				window.open(data, "_self");

				$("#LaunchingGameContainer").css("opacity", 1);
				$("#LaunchingGameContainer").css("pointer-events", "all");

				// im sorry
				window.setTimeout(function() {
					$("#LoadingAreaContainer").css("display", "none");
					$("#DownloadClientContainer").css("display", "inline");

					window.setTimeout(function() {
						$("#LaunchingGameContainer").css("opacity", 0);
						$("#LaunchingGameContainer").css("pointer-events", "none");
						
						window.setTimeout(function() {
							$("#LoadingAreaContainer").css("display", "inline");
							$("#DownloadClientContainer").css("display", "none");
						}, 1000);
					}, 5000);
				}, 2500);
			});
		});

		joinArea.append(joinButton);
		joinBox.append(joinArea);

		if(isOwner) {
			var shutdownArea = $("<div id=\"ShutdownArea\"></div>");
			shutdownArea.css("margin-top", "5px");

			var shutdownButton = $("<button>Shutdown</button>");

			shutdownButton.on("click", function() {
				if(window.confirm("Are you sure you want to shutdown this server?")) {
					$.post("/api/gameservers/shutdown", {serverID: serverId}, function(data) {
						ANORRL.PlaceLauncher.GrabGameservers(placeID);
						if(data['error'])
							window.alert("Error: " + data['reason']);
					});
				}
			})

			shutdownArea.append(shutdownButton);
			joinBox.append(shutdownArea);
		}

		joinBox.appendTo(trRow);

		return table;
	},

	GrabGameservers: function(placeid) {

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		var serversContainer = $("#InfoBox #ServersBox");
		//serversContainer.attr("hidden", "true");

		serversContainer.children().each(function() {
			if($(this).attr("id") != "NoGamesWarning") {
				$(this).remove();
			} else {
				$(this).css("display", "none")
			}
		});

		$.get("/api/gameservers/get", {placeId: placeid}, function(data) {
			
			var servers = data["servers"];
			var isOwner = data["is_owner"];

			if(servers.length == 0) {
				$("#NoGamesWarning").css("display", "block");
			} else {

				for (var key in servers) {
					console.log(servers[key]);

					var server = servers[key];
					var players = server['players'];

					var playerCount = players.length;
					var maxPlayerCount = server['maxplayercount'];

					var template = ANORRL.PlaceLauncher.CreateServerElement(placeid, server['id'], playerCount, maxPlayerCount, isOwner);
					for (var pkey in players) {
						var player = players[pkey];
						// what the fuck is wrong with me
						template.find("#PlayersBox").append("<a title=\""+player['name']+"\" id=\"Player\" href=\"/users/"+player['id']+"/profile\"><img src=\"/thumbs/headshot?id="+player['id']+"\"></a>");
					}
					
					serversContainer.append(template);
				}
			}

			ANORRL.PlaceLauncher.CurrentlyLoadingCrapBruh = false;
		});
	}
}
