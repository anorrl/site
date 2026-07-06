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

function getKeyByValue(object, value) {
  return Object.keys(object).find(key => object[key] === value);
}

const regex = /[^A-Za-z0-9 ]/g;
const brickcolors = {
	"#F2F3F3": 1,
	"#A1A5A2": 2,
	"#F9E999": 3,
	"#D7C59A": 5,
	"#C2DAB8": 6,
	"#E8BAC8": 9,
	"#80BBDB": 11,
	"#CB8442": 12,
	"#CC8E69": 18,
	"#C4281C": 21,
	"#C470A0": 22,
	"#0D69AC": 23,
	"#F5CD30": 24,
	"#624732": 25,
	"#1B2A35": 26,
	"#6D6E6C": 27,
	"#287F47": 28,
	"#A1C48C": 29,
	"#F3CF9B": 36,
	"#4B974B": 37,
	"#A05F35": 38,
	"#C1CADE": 39,
	"#CD544B": 41,
	"#C1DFF0": 42,
	"#7BB6E8": 43,
	"#F7F18D": 44,
	"#B4D2E4": 45,
	"#D9856C": 47,
	"#84B68D": 48,
	"#F8F184": 49,
	"#ECE8DE": 50,
	"#EEC4B6": 100,
	"#DA867A": 101,
	"#6E99CA": 102,
	"#C7C1B7": 103,
	"#6B327C": 104,
	"#E29B40": 105,
	"#DA8541": 106,
	"#008F9C": 107,
	"#685C43": 108,
	"#435493": 110,
	"#BFB7B1": 111,
	"#6874AC": 112,
	"#E5ADC8": 113,
	"#C7D23C": 115,
	"#55A5AF": 116,
	"#B7D7D5": 118,
	"#A4BD47": 119,
	"#D9E4A7": 120,
	"#E7AC58": 121,
	"#D36F4C": 123,
	"#923978": 124,
	"#EAB892": 125,
	"#A5A5CB": 126,
	"#DCBC81": 127,
	"#AE7A59": 128,
	"#9CA3A8": 131,
	"#D5733D": 133,
	"#D8DD56": 134,
	"#74869D": 135,
	"#877C90": 136,
	"#E09864": 137,
	"#958A73": 138,
	"#203A56": 140,
	"#27462D": 141,
	"#CFE2F7": 143,
	"#7988A1": 145,
	"#958EA3": 146,
	"#938767": 147,
	"#575857": 148,
	"#161D32": 149,
	"#ABADAC": 150,
	"#789082": 151,
	"#957977": 153,
	"#7B2E2F": 154,
	"#FFF67B": 157,
	"#E1A4C2": 158,
	"#756C62": 168,
	"#97695B": 176,
	"#B48455": 178,
	"#898788": 179,
	"#D7A94B": 180,
	"#F9D62E": 190,
	"#E8AB2D": 191,
	"#694028": 192,
	"#CF6024": 193,
	"#A3A2A5": 194,
	"#4667A4": 195,
	"#23478B": 196,
	"#8E4285": 198,
	"#635F62": 199,
	"#828A5D": 200,
	"#E5E4DF": 208,
	"#B08E44": 209,
	"#709578": 210,
	"#79B5B5": 211,
	"#9FC3E9": 212,
	"#6C81B7": 213,
	"#7C5C46": 217,
	"#96709F": 218,
	"#6B629B": 219,
	"#A7A9CE": 220,
	"#CD6298": 221,
	"#E4ADC8": 222,
	"#DC9095": 223,
	"#F0D5A0": 224,
	"#EBB87F": 225,
	"#FDEA8D": 226,
	"#7DBBDD": 232,
	"#342B75": 268,
	"#A75E9B": 321,
	"#EFB838": 333,
	"#F8F8F8": 1001,
	"#CDCDCD": 1002,
	"#111111": 1003,
	"#FF0000": 1004,
	"#FFB000": 1005,
	"#B480FF": 1006,
	"#A34B4B": 1007,
	"#C1BE42": 1008,
	"#FFFF00": 1009,
	"#0000FF": 1010,
	"#002060": 1011,
	"#2154B9": 1012,
	"#04AFEC": 1013,
	"#AA5500": 1014,
	"#AA00AA": 1015,
	"#FF66CC": 1016,
	"#FFAF00": 1017,
	"#12EED4": 1018,
	"#00FFFF": 1019,
	"#00FF00": 1020,
	"#3A7D15": 1021,
	"#7F8E64": 1022,
	"#8C5B9F": 1023,
	"#AFDDFF": 1024,
	"#FFC9C9": 1025,
	"#B1A7FF": 1026,
	"#9FF3E9": 1027,
	"#CCFFCC": 1028,
	"#FFFFCC": 1029,
	"#FFCC99": 1030,
	"#6225D1": 1031,
	"#FF00BF": 1032,
};

function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

ANORRL.Character  = {
	PlayerRenderUrl: "",
	CurrentPage: 1,
	CurrentCategory: 8,
	CurrentlyLoadingCrapBruh: false,
	CurrentBodyPart: 0,
	AdvancePager: function() {
		this.LoadWardrobe(this.CurrentCategory, this.CurrentPage + 1);
	},
	DeadvancePager: function() {
		this.LoadWardrobe(this.CurrentCategory, this.CurrentPage - 1);
	},
	LoadWardrobe: function(category, page) {

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		var loadingMessage = $("#Wardrobe #AssetsContainer #StatusText #Loading");
		var emptyMessage = $("#Wardrobe #AssetsContainer #StatusText #NoAssets");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");

		if(category === undefined) {
			category = this.CurrentCategory;
			
		} else {
			if(this.CurrentCategory != category) {
				page = 1;
			}
			this.CurrentCategory = category;
		}
		if(page === undefined) {
			page = 1;
		}

		var wardrobecontainer = $("#Wardrobe #AssetsContainer > table");

		wardrobecontainer.children().each(function() {
			$(this).remove();
		});

		var pagercontainer = $("#Wardrobe #AssetsContainer #Paginator");
		
		var backPager = pagercontainer.find("#BackPager");
		var nextPager = pagercontainer.find("#NextPager");

		$("a[data_category]").each(function() {
			$(this).removeAttr("selected");
		});

		var categorylabel = $("a[data_category="+category+"]").html().toLowerCase().replaceAll("-", "");

		$("a[data_category="+category+"]").attr("selected", "");
		
		ANORRL.ChangeUrl("", "/my/character#"+categorylabel);

		$.get("/api/character", {r: "getwardrobe", c: category, p : page}, function(data) {
			
			var assets = data['assets'];
			ANORRL.Character.CurrentPage = data['page'];
			var current_page = ANORRL.Character.CurrentPage;
			var total_pages = data['total_pages'];

			wardrobecontainer.attr("hidden", true);

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

				var index = 0;
				var rowIndex = 0;
				
				for (var key in assets) {
					if(index % 4 == 0 || index == 0) {
						wardrobecontainer.append($("<tr></tr>"));
						if(index % 4 == 0  && index != 0) {
							rowIndex++;
						}
					} 

					var asset = assets[key];

					var td = $("<td></td>");
					var template = $($(".Asset[template]").clone().prop('outerHTML'));
					td.append(template);
					template.removeAttr("template");

					
					var urlname = asset['name'].replaceAll(regex, "").trim().toLowerCase().replaceAll(" ", "-");
					if(urlname == "") {
						urlname = "unnamed";
					}

					template.find("#NameAndThumbs > img").attr("src", asset['thumbnail']);

					template.find("#NameAndThumbs > span").html(asset['name']);
					template.find("#NameAndThumbs").attr("href", "/"+urlname+"-item?id="+asset['id']);

					template.find("#Creator > span").html(asset['creator']['name']);
					template.find("#Creator").attr("href", "/users/"+asset['creator']['id']+"/profile");
					template.attr("data_assetid", asset['id']);

					template.find("#WearButton").on("click", function() {
						ANORRL.Character.WearAsset($(this).parent().attr("data_assetid"));
					})

					// implement details
					wardrobecontainer.removeAttr("hidden");
					$(wardrobecontainer.find("tr")[rowIndex]).append(td);

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
		});
		ANORRL.Character.CurrentlyLoadingCrapBruh = false;
	},
	LoadCurrentlyWearing: function() {

		var loadingMessage = $("#CurrentlyWearing #AssetsContainer #StatusText #Loading");
		var emptyMessage = $("#CurrentlyWearing #AssetsContainer #StatusText #NoAssets");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");
		
		var wearingcontainer = $("#CurrentlyWearing #AssetsContainer > table");

		wearingcontainer.children().each(function() {
			$(this).remove();
		});

		$.get("/api/character", {r: "getwearing"}, function(data) {
			
			var assets = data['assets'];
			wearingcontainer.attr("hidden", true);

			if(assets.length == 0) {
				loadingMessage.css("display", "none");
				emptyMessage.css("display", "block");
			} else {
				loadingMessage.css("display", "none");
				var index = 0;
				var rowIndex = 0;
				
				for (var key in assets) {
					if(index % 4 == 0 || index == 0) {
						wearingcontainer.append($("<tr></tr>"));
						if(index % 4 == 0  && index != 0) {
							rowIndex++;
						}
					} 

					var asset = assets[key];

					var td = $("<td></td>");
					var template = $($(".Asset[template]").clone().prop('outerHTML'));
					td.append(template);
					template.removeAttr("template");

					
					var urlname = asset['name'].replaceAll(regex, "").trim().toLowerCase().replaceAll(" ", "-");
					if(urlname == "") {
						urlname = "unnamed";
					}

					template.find("#NameAndThumbs > img").attr("src", asset['thumbnail']);

					template.find("#NameAndThumbs > span").html(asset['name']);
					template.find("#NameAndThumbs").attr("href", "/"+urlname+"-item?id="+asset['id']);

					template.find("#Creator > span").html(asset['creator']['name']);
					template.find("#Creator").attr("href", "/users/"+asset['creator']['id']+"/profile");
					template.attr("data_assetid", asset['id']);

					template.find("#WearButton").html("[ remove ]");

					template.find("#WearButton").on("click", function() {
						ANORRL.Character.RemoveAsset($(this).parent().attr("data_assetid"));
					})

					// implement details
					wearingcontainer.removeAttr("hidden");
					$(wearingcontainer.find("tr")[rowIndex]).append(td);

					index++;
				}
			}
		});
		ANORRL.Character.CurrentlyLoadingCrapBruh = false;
	},
	WearAsset: function(assetid) {
		$.post("/api/character?r=wear", { assetid: assetid }, function(data) {
			if(!data['error']) {
				ANORRL.Character.LoadWardrobe();
				ANORRL.Character.LoadCurrentlyWearing();
				ANORRL.Character.RenderPlayer();
				// Render
			} else {
				alert("Error: " + data['reason']);
			}
		});
	},
	RemoveAsset: function(assetid) {
		$.post("/api/character?r=remove", { assetid: assetid }, function(data) {
			if(!data['error']) {
				ANORRL.Character.LoadWardrobe();
				ANORRL.Character.LoadCurrentlyWearing();
				ANORRL.Character.RenderPlayer();
				// Render
			} else {
				alert("Error: " + data['reason']);
			}
		});
	},
	GetBodyPartName: function(bodypartid) {
		switch(bodypartid) {
			case "0":
				return "Head";
			case "1":
				return "Torso";
			case "2":
				return "Left Arm";
			case "3":
				return "Right Arm";
			case "4":
				return "Left Leg";
			case "5":
				return "Right Leg";
			default:
				return "&nbsp;";
		}
	},
	LoadBodyColours: function() {
		$.get("/api/character?r=getbodycolours", function(data) {
			var bodycontainer = $("#CharacterContainer #BodyColours #BodyColoursContainer");
			setBackgroundColour(bodycontainer, 0, data, 'head');
			setBackgroundColour(bodycontainer, 1, data, 'torso');
			setBackgroundColour(bodycontainer, 2, data, 'leftarm');
			setBackgroundColour(bodycontainer, 3, data, 'rightarm');
			setBackgroundColour(bodycontainer, 4, data, 'leftleg');
			setBackgroundColour(bodycontainer, 5, data, 'rightleg');
		});
	},
	PrepareColourPicker: function() {
		var brickcolourhexes = Object.keys(brickcolors);
		for(var i = 0; i < brickcolourhexes.length; i++) {
			let hexColour = brickcolourhexes[i];
			let button = $("<button></button>");
			button.css("background-color",hexColour);
			button.on("click", function() {
				$("button[data_bodytype="+self.CurrentBodyPart+"]").css("background-color", hexColour);
				ANORRL.Character.CloseColourPicker(true);
			});
			$("#Colours").append(button);
		}
	},
	OpenColourPicker: function(bodypartid) {
		self.CurrentBodyPart = bodypartid;
		$("#Colours").modal({showClose: false});
		$("#Colours").css("z-index", "999");
		$(".jquery-modal").css("z-index", "998");
		window.setTimeout(function() {
			$("#BodyPartInfo").html(ANORRL.Character.GetBodyPartName(bodypartid));
		}, 1)
		
	},
	GetBodyColourID: function(bodypartid) {
		return brickcolors[rgb2hex($("button[data_bodytype="+bodypartid+"]").css("background-color")).toUpperCase()];
	},
	CloseColourPicker: function(save) {
		$("#Colours").removeClass("current");
		$("#Colours").removeClass("modal");
		$("#Colours").attr("style", "");
		$(".jquery-modal").remove();
		$("#BodyPartInfo").html("&nbsp;");

		if(save) {
			$.post("/api/character?r=setbodycolours", {
				head: ANORRL.Character.GetBodyColourID(0), 
				torso: ANORRL.Character.GetBodyColourID(1), 
				leftarm: ANORRL.Character.GetBodyColourID(2), 
				rightarm: ANORRL.Character.GetBodyColourID(3), 
				leftleg: ANORRL.Character.GetBodyColourID(4), 
				rightleg: ANORRL.Character.GetBodyColourID(5)
			}, function() {
				// success or something
				ANORRL.Character.RenderPlayer();
			});
		}
	},
	IsRendering: false,
	RenderPlayer: function(forcerender) {
		if(ANORRL.Character.IsRendering) {
			return;
		}
		$(".thumbnail-span canvas").remove();
		$(".thumbnail-holder > img").attr("src","/public/images/ProgressIndicator4White.gif");
		if(this.Is3DActive()) {
			$(".thumbnail-holder > img").css("display", "block");
			$(".thumbnail-span").css("display", "none");
		}
		ANORRL.Character.IsRendering = true;


		$.get("/api/character?r="+(forcerender ? "re" : "")+"rendercharacter", function(data) {
			
			if(data['error']) {
				alert(data['reason']);
			}

			//ANORRL.Character.LoadWardrobe();
			//ANORRL.Character.LoadCurrentlyWearing();
			
			if(ANORRL.Character.Is3DActive()) {
				ANORRL.Character.Load3D();
			} else {
				ANORRL.Character.Load2D();
			}
			
	
			$(".thumbnail-holder > img").attr("src",ANORRL.Character.PlayerRenderUrl+"&t="+Date.now());
			ANORRL.Character.IsRendering = false;

			
		});
	},

	Search: function(page) {

    	if(this.CurrentlyLoadingCrapBruh) return;
    	this.CurrentlyLoadingCrapBruh = true;

    	if(page === undefined) page = 1;

    	var query = $("#SearchBox").val().trim();

    	var wardrobecontainer = $("#Wardrobe #AssetsContainer > table");
    	var loadingMessage = $("#Wardrobe #AssetsContainer #StatusText #Loading");
    	var emptyMessage = $("#Wardrobe #AssetsContainer #StatusText #NoAssets");

    	wardrobecontainer.children().each(function() {
        	$(this).remove();
    	});

    	emptyMessage.hide();
    	loadingMessage.show();

    	$.get("/api/character", {
        	r: "search",
        	q: query,
        	c: this.CurrentCategory,
        	p: page
    	}, function(data) {

        	var assets = data['assets'] || [];

        	loadingMessage.hide();

        	if(assets.length === 0) {
            	emptyMessage.show();
            	ANORRL.Character.CurrentlyLoadingCrapBruh = false;
            	return;
        	}

        	wardrobecontainer.removeAttr("hidden");

        	var index = 0;
        	var rowIndex = 0;

        	for (var key in assets) {

            	if(index % 4 === 0) {
                	wardrobecontainer.append($("<tr></tr>"));
                	if(index !== 0) rowIndex++;
            	}

            	var asset = assets[key];

            	var td = $("<td></td>");
            	var template = $($(".Asset[template]").clone().prop('outerHTML'));
            	td.append(template);
            	template.removeAttr("template");

            	var urlname = asset['name']
                	.replaceAll(regex, "")
                	.trim()
                	.toLowerCase()
                	.replaceAll(" ", "-");

            	if(urlname === "") urlname = "unnamed";

            	template.find("#NameAndThumbs > img")
                	.attr("src", asset['thumbnail']);

            	template.find("#NameAndThumbs > span")
                	.html(asset['name']);

            	template.find("#NameAndThumbs")
                	.attr("href", "/"+urlname+"-item?id="+asset['id']);

            	template.find("#Creator > span")
                	.html(asset['creator']['name']);

            	template.find("#Creator")
                	.attr("href", "/users/"+asset['creator']['id']+"/profile");

            	template.attr("data_assetid", asset['id']);

            	template.find("#WearButton").on("click", function() {
                	ANORRL.Character.WearAsset($(this).parent().attr("data_assetid"));
            	});

            	$(wardrobecontainer.find("tr")[rowIndex]).append(td);

            	index++;
        	}

        	ANORRL.Character.CurrentlyLoadingCrapBruh = false;
   		});
	},
	Is3DActive: function() {
		if(!this.Has3DEnabled())
			return false;

		return $("#ThumbnailSwitcher").attr("data-3d") == "true";
	},
	Has3DEnabled: function() {
		return $(".thumbnail-span").length != 0;
	},
	Load3D: function() {
		if(!this.Has3DEnabled())
			return;

		$("#ThumbnailSwitcher").attr("data-3d", true);

		$(".thumbnail-holder > img").css("display", "none");
		$(".thumbnail-span").css("display", "block");

		$(".thumbnail-span").load3DThumbnail("avatar", function(canvas) {
			console.log("3D: complete!");
		}, function() {
			console.log("3D: I dont like you");
			
			ANORRL.Character.Load2D();
		});
	},
	Load2D: function() {
		if(!this.Has3DEnabled())
			return;

		$("#ThumbnailSwitcher").attr("data-3d", false);

		$(".thumbnail-holder > img").css("display", "block");
		$(".thumbnail-span").css("display", "none");

		$(".thumbnail-span canvas").remove();
	}
};

function setBackgroundColour(bodycontainer, bodytype, data, bodycolor) {
	bodycontainer.find("button[data_bodytype=\""+bodytype+"\"]").css("background-color", getKeyByValue(brickcolors, data['colours'][bodycolor]));
}

$(function(){

	ANORRL.Character.PlayerRenderUrl = $(".thumbnail-holder > img").attr("src");
	
	$("a[data_category]").on("click",function() {
		ANORRL.Character.LoadWardrobe($(this).attr("data_category"), ANORRL.Character.CurrentPage);
	});

	if(window.location.hash != "") {
		var url = window.location.hash;
		url = url.replaceAll("#", "").replaceAll("/","");
		var categories = {
			"hats" : 8,
			"faces" : 18,
			"tshirts" : 11,
			"shirts" : 2,
			"pants" : 12,
			"gears" : 19,
			"outfits" : 32,
			"packages" : 32,
			"heads" : 17,
			"torsos" : 27,
			"leftarms" : 29,
			"rightarms": 28,
			"leftlegs" : 30,
			"rightlegs" : 31,
			"emotes" : 61,
		}

		ANORRL.Character.LoadWardrobe(categories[url]);
	} else {
		ANORRL.Character.LoadWardrobe();
	}

	ANORRL.Character.LoadCurrentlyWearing();
	ANORRL.Character.LoadBodyColours();
	ANORRL.Character.PrepareColourPicker();

	$("#CharacterContainer #BodyColours #BodyColoursContainer button[data_bodytype]").hover(function(){
		$("#BodyPartInfo").html(ANORRL.Character.GetBodyPartName($(this).attr("data_bodytype")));
	})

	$("#CharacterContainer #BodyColours #BodyColoursContainer button[data_bodytype]").on("mouseout", function(){
		$("#BodyPartInfo").html("&nbsp;");
	})

	$("#CharacterContainer #BodyColours #BodyColoursContainer button[data_bodytype]").on("click", function(){
		ANORRL.Character.OpenColourPicker($(this).attr("data_bodytype"));
	})

	$("#Paginator").find("input").on("change", function() {
		ANORRL.Character.LoadWardrobe(ANORRL.Character.CurrentCategory, Number($(this).val()));
	});

	if(ANORRL.Character.Has3DEnabled()) {
		$("#ThumbnailSwitcher").on("click", function() {
			if($(this).attr("data-3d") == "true") {
				ANORRL.Character.Load2D();
			} else {
				$(this).attr("data-3d", true);
				ANORRL.Character.Load3D();
			}
		})
	}
});
