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
		<a title="Player" id="Player" href="/"><img src="/images/avatar.png"></a>
		
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
			} else if(!data.startsWith("anorrl-")) {
				alert(data);
				return;
			}
			window.open(data, "_self");
		});
	},

	EditPlace: function(placeId) {
		$.post("/api/ticketer", {editID: placeId, }, function(data) {
			if(data == "") {
				alert("You need to be logged in to play!");
				return;
			} else if(!data.startsWith("anorrl-")) {
				alert(data);
				return;
			}
			window.open(data, "_self");
		});
	},
	
	CreateServerElement: function(placeID, serverId, currentPlayersCount, maxPlayersCount) {
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
				} else if(!data.startsWith("anorrl-")) {
					alert(data);
					return;
				}
				window.open(data, "_self");
			});
		});

		joinArea.append(joinButton);
		joinBox.append(joinArea);
		
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

		$.get("/api/gameservers", {placeId: placeid}, function(data) {
			
			var servers = data;

			if(servers.length == 0) {
				$("#NoGamesWarning").css("display", "block");
			} else {

				for (var key in servers) {
					console.log(servers[key]);

					var server = servers[key];
					var players = server['players'];

					var playerCount = players.length;
					var maxPlayerCount = server['maxplayercount'];

					var template = ANORRL.PlaceLauncher.CreateServerElement(placeid, server['id'], playerCount, maxPlayerCount);
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
