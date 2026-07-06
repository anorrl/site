;// bundle: page___627eead65af35eae6a5f7f6716ba14c4_m
;// files: extensions/string.js, ClientToolbox.js

;// extensions/string.js
$.extend(String.prototype,function(){function n(){return this.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#39;")}return{escapeHTML:n}}());

;// ClientToolbox.js

ClientToolbox = {
	TotalNumAssetsInSet: 0
}
function url_query(n) {
	n = n.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]").toLowerCase();
	var i = "[\\?&]" + n + "=([^&#]*)",
		r = new RegExp(i),
		t = r.exec(window.location.href.toLowerCase());
	return t !== null ? t[1] : !1;
}
function insertContent(n) {
	try {
		ClientToolbox.IsRecentlyInsertedAssetEnabled &&
			ClientToolbox.IsUserAuthenticated &&
			UpdateRecentlyInsertedAsset(n),
			window.external.Insert(ClientToolbox.RequestURL + "?id=" + n);
	} catch (t) {
		alert(ClientToolbox.Resources.insertError);
	}
}
function dragRBX(n, t) {
	try {
		ClientToolbox.IsRecentlyInsertedAssetEnabled &&
			ClientToolbox.IsUserAuthenticated &&
			(UpdateRecentlyInsertedAsset(n), t && makeAssetVoteable(n)),
			window.external.StartDrag(ClientToolbox.RequestURL + "?id=" + n);
	} catch (i) {
		alert(ClientToolbox.Resources.dragError);
	}
}
function handleVote(n, t) {
	var f = $("#span_setitem_" + n),
		i,
		r,
		u;
	f.length !== 0 &&
		((i = $(f).find(".voting")), i.length !== 0) &&
		((r = t ? $(i).find(".upvote") : $(i).find(".downvote")), r.length !== 0) &&
		((u = $(r).attr("class").indexOf("selected") !== -1),
		u
			? $.ajax({
				  url: "/voting/studio/unvote",
				  data: addAntiForgeryToken({ assetId: n }),
				  type: "POST",
				  dataType: "json",
				  success: function () {
					  $(i).find(".upvote").removeClass("selected"),
						  $(i).find(".downvote").removeClass("selected"),
						  $(i).removeClass("has-voted");
				  },
			  })
			: $.ajax({
				  url: "/voting/studio/vote",
				  data: addAntiForgeryToken({ assetId: n, vote: t }),
				  type: "POST",
				  dataType: "json",
				  success: function () {
					  t
						  ? ($(i).find(".upvote").addClass("selected"), $(i).find(".downvote").removeClass("selected"))
						  : ($(i).find(".upvote").removeClass("selected"), $(i).find(".downvote").addClass("selected")),
						  $(i).addClass("has-voted");
				  },
			  }));
}
function handleEnterKey(n) {
	var t;
	return (t = window.event ? window.event.keyCode : n.which), t != 13;
}
function UpdateRecentlyInsertedAsset(n) {
	$.post("/IDE/Toolbox/InsertAsset", { assetId: n });
}
function makeAssetVoteable(n) {
	var r = $("#span_setitem_" + n),
		i,
		t;
	r.length > 0 &&
		((i = $(r).children(".voting")),
		i.length > 0 &&
			((t = ""),
			(t += '<div class="voting users-vote">'),
			(t += '  <div class="upvote tiny" onclick="handleVote(' + n + ', true)"></div>'),
			(t += '  <div class="downvote tiny" onclick="handleVote(' + n + ', false)"></div>'),
			(t += "</div>"),
			$(i).empty(),
			$(i).html(t)));
}
(ClientToolbox = {}),
	(ClientToolbox.IsUserAuthenticated = !1),
	(ClientToolbox.IsDecalCreationEnabled = !1),
	(ClientToolbox.IsRecentlyInsertedAssetEnabled = !1),
	(ClientToolbox.NumberOfItemsPerPage = 26),
	(ClientToolbox.TotalNumberOfItemsInCurrentSet = 0),
	(ClientToolbox.CurrentPage = 0),
	ClientToolbox.LastSearchedSetId,
	ClientToolbox.SearchString,
	(ClientToolbox.MySetsHasData = !0),
	ClientToolbox.ResizeHelper,
	(ClientToolbox.SearchTouch = !1),
	ClientToolbox.CurrentTab,
	(ClientToolbox.OnPageLoad = function () {
		var t = url_query("searchset"),
			i,
			n;
		if (t) {
			$("#tbSearch").val(decodeURIComponent(t)), ClientToolbox.ChangeTab("Search");
			return;
		}
		if (((i = url_query("search")), i)) {
			ClientToolbox.ChangeTab("Search");
			return;
		}
		(n = url_query("selectedset")),
			n && ((n = decodeURIComponent(n)), (ClientToolbox.SelectedSet = n)),
			ClientToolbox.ChangeTab("Inventory");
	}),
	(ClientToolbox.ChangeTab = function (n) {
		var i, t;
		((ClientToolbox.CurrentTab = n), $("#" + n).hasClass("Selected")) ||
			($(".Tabs").removeClass("Selected"),
			$("#" + n).addClass("Selected"),
			$("#ShowMore").hide(),
			n == "Inventory"
				? ($("#activeOption").text(ClientToolbox.Resources.models),
				  $("#tbSearch").val(""),
				  $("#tbSearch").blur(),
				  $("#ToolboxSearch").hide(),
				  $("#SearchList").hide(),
				  $("#Filters").hide(),
				  $(".toolboxDisplayText").show(),
				  $("#ddlSets").empty(),
				  $("#ddlSets").show(),
				  $("#ddlSets").css({ "min-width": "135px" }),
				  $("#ddlSets").empty(),
				  ClientToolbox.IsUserAuthenticated &&
					  ((i = []),
					  ClientToolbox.IsRecentlyInsertedAssetEnabled &&
						  (i.push({ ID: "RecentModels", Name: ClientToolbox.Resources.recentModels }),
						  i.push({ ID: "RecentDecals", Name: ClientToolbox.Resources.recentDecals })),
					  i.push({ ID: "getmymodels", Name: ClientToolbox.Resources.myModels }),
					  ClientToolbox.IsDecalCreationEnabled &&
						  i.push({ ID: "getmydecals", Name: ClientToolbox.Resources.myDecals }),
					  ClientToolbox.PopulateDropDownList(i, !1),
					  ClientToolbox.UpdateOptGroup(!0),
					  ClientToolbox.CallHandler("getsets"),
					  ClientToolbox.CallHandler("getsubscribedsets")),
				  ClientToolbox.UpdateOptGroup(!1),
				  ClientToolbox.CallHandler("getrobloxsets", function () {
					  ClientToolbox.FetchData(!1);
				  }))
				: n == "Search" &&
				  ($("#ToolboxSearch").show(),
				  $("#SearchList").show(),
				  $("#Filters").show(),
				  $("#ddlSets").hide(),
				  $(".toolboxDisplayText").hide(),
				  (t = []),
				  t.push(
					  { ID: "FreeModels", Name: ClientToolbox.Resources.models },
					  { ID: "FreeDecals", Name: ClientToolbox.Resources.decals }
				  ),
				  ClientToolbox.IsUserAuthenticated
					  ? (t.push({ ID: "getmymodels", Name: ClientToolbox.Resources.myModels }),
						ClientToolbox.IsDecalCreationEnabled &&
							t.push({ ID: "getmydecals", Name: ClientToolbox.Resources.myDecals }),
						ClientToolbox.PopulateSearchDropdownList(t, !0))
					  : ClientToolbox.PopulateSearchDropdownList(t, !1),
				  $("#tbSearch").focus(),
				  ClientToolbox.FetchData(!1)));
	}),
	(ClientToolbox.FetchData = function (n) {
		var r, t, i;
		ClientToolbox.CurrentTab == "Inventory"
			? ((r = $("#ddlSets option:selected").attr("value")), (t = ""), (i = "MostFavorited"))
			: ClientToolbox.CurrentTab == "Search" &&
			  ((t = $("#tbSearch").val()),
			  t != $("#tbSearch")[0].title || ClientToolbox.SearchTouch || (t = ""),
			  (r = $(".SetList #activeOption").attr("data-value")),
			  (i = $("#SortList").val())),
			ClientToolbox.PopulateAssets(r, t, i, n);
	}),
	(ClientToolbox.UpdateOptGroup = function (n) {
		var t, i, r;
		(t = n
			? [ClientToolbox.Resources.mySets, ClientToolbox.Resources.mySubscribedSets]
			: [ClientToolbox.Resources.robloxSets]),
			(i = "");
		for (r in t) i += "<optgroup label='" + t[r] + "'></optgroup>";
		$(i).appendTo("#ddlSets");
	}),
	(ClientToolbox.PopulateDropDownList = function (n, t, i) {
		for (var f = "", u, e, r = 0; r < n.length; r++)
			(u = n[r].Name.escapeHTML()),
				u.length > 13 && (u = u.substr(0, 10) + "..."),
				(f += "<option value='" + n[r].ID + "'>" + u + "</option>");
		t
			? ($("#ddlSets").empty(), $("#ddlSets").html(f))
			: i != undefined
			  ? $(f).appendTo($('#ddlSets optgroup[label="' + i + '"]'))
			  : $(f).appendTo("#ddlSets"),
			ClientToolbox.SelectedSet &&
				((e = $("#ddlSets").children("[value='" + ClientToolbox.SelectedSet + "']")[0]),
				e && ($(e).parent().val(ClientToolbox.SelectedSet), (ClientToolbox.SelectedSet = "")));
	}),
	(ClientToolbox.PopulateSearchDropdownList = function (n, t) {
		for (var r = "", i = 0; i < n.length; i++)
			r += "<a href='#' data-value='" + n[i].ID + "' class='searchListOption'>" + n[i].Name.escapeHTML() + "</a>";
		$("#SearchMenu").html(r),
			t &&
				($("#SearchList").css({ "min-width": "76px" }),
				$("#activeOption").css({ width: "53px" }),
				$("#SearchMenu").css({ width: "76px" }),
				$(".searchListOption").css({ width: "59px" })),
			$("#activeOption").attr("data-value", n[0].ID),
			$(".searchListOption").click(function () {
				$("#activeOption").text($(this).text()),
					$("#activeOption").attr("data-value", $(this).attr("data-value")),
					$("#ToolboxItems").html(""),
					ClientToolbox.FetchData(!1);
			});
	}),
	(ClientToolbox.DropDownListChanged = function () {
		$("#ToolboxItems").html(""), ClientToolbox.FetchData();
	}),
	(ClientToolbox.CallHandler = function (n, t) {
		$.ajax({
			url: "/Sets/SetHandler.ashx",
			data: { maxsets: 10, rqtype: n },
			type: "GET",
			dataType: "json",
			success: function (i) {
				i.length < 1
					? (n == "getsets" && (ClientToolbox.MySetsHasData = !1),
					  n != "getsubscribedsets" ||
						  ClientToolbox.MySetsHasData ||
						  $("#ToolboxItems").html(ClientToolbox.Resources.noSets))
					: n == "getsets"
					  ? ClientToolbox.PopulateDropDownList(i, !1, ClientToolbox.Resources.mySets)
					  : n == "getsubscribedsets"
						? ClientToolbox.PopulateDropDownList(i, !1, ClientToolbox.Resources.mySubscribedSets)
						: ClientToolbox.PopulateDropDownList(i, !1, ClientToolbox.Resources.robloxSets),
					typeof t == "function" && t();
			},
			error: function () {
				$("#ToolboxItems").html(ClientToolbox.Resources.setsError);
			},
		});
	}),
	(ClientToolbox.GetSetInfo = function (n) {
		if (($("#ResultStatus").hide(), n == "getmydecals" || n == "getmymodels")) {
			n == "getmydecals" && ClientToolbox.IsUserAuthenticated
				? (n = 6)
				: n == "getmymodels" && ClientToolbox.IsUserAuthenticated && (n = 7);
			var t =
				"/Sets/SetHandler.ashx?rqtype=getothersetinfo&settype=" +
				n +
				"&query=" +
				(typeof ClientToolbox.SearchString == "undefined" ? "" : ClientToolbox.SearchString);
			/*$.getJSON(t, function (n) {
				n.Error && n.Error.substr(0, 6) == "ERROR_"
					? $("#ResultStatus").hide()
					: ((ClientToolbox.TotalNumberOfItemsInCurrentSet = n[0].TotalNumAssetsInSet),
					  ClientToolbox.UpdatePaging());
			});*/
		} else
			n == "RecentModels" || n == "RecentDecals"
				? ((ClientToolbox.TotalNumberOfItemsInCurrentSet = 20), ClientToolbox.UpdatePaging())
				: n == "FreeDecals" || n == "FreeModels"
				  ? $.getJSON(
						"/IDE/Toolbox/GetTotalNumberOfResults?keyword=" +
							(typeof ClientToolbox.SearchString == "undefined" ? "" : ClientToolbox.SearchString) +
							"&type=" +
							n +
							"&sortBy=" +
							$("#SortList").val(),
						function (n) {
							n.Error && n.Error.substr(0, 6) == "ERROR_"
								? $("#ResultStatus").hide()
								: ((ClientToolbox.TotalNumberOfItemsInCurrentSet = n[0].TotalNumberOfResults),
								  ClientToolbox.UpdatePaging());
						}
					)
					: false;
				  /*: $.getJSON("/Sets/SetHandler.ashx?rqtype=getsetinfo&setid=" + n, function (n) {
						n.Error && n.Error.substr(0, 6) == "ERROR_"
							? $("#ResultStatus").hide()
							: ((ClientToolbox.TotalNumberOfItemsInCurrentSet = n[2].TotalNumAssetsInSet),
							  ClientToolbox.UpdatePaging());
					})*/;
	}),
	(ClientToolbox.UpdatePaging = function () {
		var n, t;
		ClientToolbox.CurrentTab == "Search" &&
			((n = typeof ClientToolbox.SearchString == "undefined" ? "" : ClientToolbox.SearchString),
			(n = n.escapeHTML()),
			(t = "<b>" + ClientToolbox.TotalNumberOfItemsInCurrentSet + "</b> " + ClientToolbox.Resources.results),
			n != "" && (t += ' for "' + n + '"'),
			$("#ResultStatus").html(t),
			$("#ResultStatus").show()),
			ClientToolbox.TotalNumberOfItemsInCurrentSet > ClientToolbox.NumberOfItemsPerPage
				? (ClientToolbox.CurrentPage > 0 ? $("#Previous").show() : $("#Previous").hide(),
				  (ClientToolbox.CurrentPage + 1) * ClientToolbox.NumberOfItemsPerPage <
				  ClientToolbox.TotalNumberOfItemsInCurrentSet
					  ? ($("#Next").show(), $("#ShowMore").show())
					  : ($("#Next").hide(), $("#ShowMore").hide()))
				: $("#ShowMore").hide(),
			ClientToolbox.ResizeHelper();
	}),
	(ClientToolbox.Paging = function (n) {
		var i =
				(ClientToolbox.CurrentPage + 1) * ClientToolbox.NumberOfItemsPerPage <
				ClientToolbox.TotalNumberOfItemsInCurrentSet,
			t = ClientToolbox.CurrentPage > 0;
		if (n == "previous" && t) ClientToolbox.CurrentPage--;
		else if (n == "next" && i) ClientToolbox.CurrentPage++;
		else return !1;
		ClientToolbox.UpdatePaging(),
			window.setTimeout(function () {
				$("#ToolboxItems").is(":empty") &&
					$("#ToolboxItems").append(
						"<div style='text-align: center;margin-top: 25px;'><img src='/public/images/ProgressIndicator4.gif' alt='" +
							ClientToolbox.Resources.loading +
							"' /></div>"
					);
			}, 500),
			ClientToolbox.FetchData(!0);
	}),
	(ClientToolbox.ResetPagingForNewSearch = function () {
		(ClientToolbox.CurrentPage = 0),
			(ClientToolbox.TotalNumberOfItemsInCurrentSet = 0),
			ClientToolbox.GetSetInfo(ClientToolbox.LastSearchedSetId);
	}),
	(ClientToolbox.PopulateAssets = function (n, t, i, r) {
		var f, u;
		(typeof i == "undefined" || i == "") && (i = "MostTaken"),
			(f = !1),
			ClientToolbox.LastSearchedSetId != n && ((ClientToolbox.LastSearchedSetId = n), (f = !0)),
			ClientToolbox.SearchString != t && ((ClientToolbox.SearchString = t), (f = !0)),
			f && ClientToolbox.ResetPagingForNewSearch(),
			n == "RecentModels" || n == "RecentDecals"
				? $.ajax({
					  url: "/IDE/Toolbox/GetRecentlyInsertedAssets",
					  type: "GET",
					  data: { type: n },
					  dataType: "json",
					  cache: !1,
					  success: function (n) {
						  var i, t, u;
						  if (n.length == 0) $("#ToolboxItems").html(ClientToolbox.Resources.noResults);
						  else if (n.length > 0 && n[0].error) $("#ToolboxItems").text(ClientToolbox.Resources.error);
						  else {
							  for (i = "", t = 0; t < n.length; t++)
								  (u = n[t]), (i += ClientToolbox.GenerateOtherSetItemHtml(u));
							  r ? $("#ToolboxItems").append(i) : $("#ToolboxItems").html(i);
						  }
					  },
					  error: function () {
						  $("#ToolboxItems").html(ClientToolbox.Resources.errorData);
					  },
				  })
				: n == "FreeModels" || n == "FreeDecals"
				  ? $.ajax({
						url: "/IDE/Toolbox/Search",
						type: "GET",
						data: {
							keyword: typeof ClientToolbox.SearchString == "undefined" ? "" : ClientToolbox.SearchString,
							type: n,
							sortBy: i,
							startOffset: ClientToolbox.CurrentPage * ClientToolbox.NumberOfItemsPerPage,
							resultSize: ClientToolbox.NumberOfItemsPerPage,
						},
						dataType: "json",
						cache: !1,
						success: function (n) {
							var i, t, u;
							if (n.length == 0) $("#ToolboxItems").html(" ");
							else if (n.length > 0 && n[0].error) $("#ToolboxItems").text(ClientToolbox.Resources.error);
							else {
								for (i = "", t = 0; t < n.length; t++)
									(u = n[t]), (i += ClientToolbox.GenerateOtherSetItemHtml(u));
								r ? $("#ToolboxItems").append(i) : $("#ToolboxItems").html(i);
							}
						},
						error: function () {
							$("#ToolboxItems").html(ClientToolbox.Resources.errorData);
						},
					})
				  : ((u = {
						startRowIndex: ClientToolbox.CurrentPage * ClientToolbox.NumberOfItemsPerPage,
						maximumRows: ClientToolbox.NumberOfItemsPerPage,
					}),
					(u.query = typeof ClientToolbox.SearchString == "undefined" ? "" : ClientToolbox.SearchString),
					isNaN(n) || n == 0 ? (u.rqtype = n) : ((u.rqtype = "getsetitems"), (u.setID = n)),
					$.ajax({
						url: "/Sets/SetHandler.ashx",
						type: "GET",
						data: u,
						dataType: "json",
						success: function (t) {
							var i, f, o, u, e;
							if (t.length == 0) $("#ToolboxItems").html(ClientToolbox.Resources.noResults);
							else if (!(t.length > 0) || !t[0].error) {
								if (
									((i = ""),
									n == "getmydecals" || n == "getmymodels" || n == "FreeDecals" || n == "FreeModels")
								)
									for (f = 0; f < t.length; f++)
										(o = t[f]), (i += ClientToolbox.GenerateOtherSetItemHtml(o));
								else
									for (u = 0; u < t.length; u++)
										(e = t[u]), (i += ClientToolbox.GenerateAssetSetItemHtml(e));
								r ? $("#ToolboxItems").append(i) : $("#ToolboxItems").html(i);
							}
						},
						error: function () {
							$("#ToolboxItems").html(ClientToolbox.Resources.errorData);
						},
					})),
			window.setTimeout(function () {
				$("#ToolboxItems").is(":empty") &&
					$("#ToolboxItems").append(
						"<div style='text-align: center;margin-top: 25px;'><img src='/public/images/ProgressIndicator4.gif' alt='" +
							ClientToolbox.Resources.loading +
							"' /></div>"
					);
			}, 500);
	}),
	(ClientToolbox.GenerateAssetSetItemHtml = function (n) {
		var i = ClientToolbox.GenerateHtml(n),
			t = "span_setitem_" + n.AssetID;
		return ClientToolbox.GetThumbnail(n.AssetVersionID, t, 0, !0), i;
	}),
	(ClientToolbox.GenerateOtherSetItemHtml = function (n) {
		var i = ClientToolbox.GenerateHtml(n),
			t = "span_setitem_" + n.ID;
		return ClientToolbox.GetThumbnail(n.ID, t, 0, !1), i;
	}),
	(ClientToolbox.GenerateHtml = function (n) {
		var f = n.AssetID || n.ID,
			t = "",
			o = "span_setitem_" + f,
			e = n.Name.escapeHTML(),
			i = typeof n.VoteableAfterUse != "undefined" && n.VoteableAfterUse == !0,
			s = n.IsVoteable || i ? "WithVoting" : "WithoutVoting",
			r,
			u;
		if (
			((t += "<div class='ToolboxItem " + s + "' id='" + o + "'ondragstart='dragRBX(" + f + ", " + i + ")'>"),
			(t += "   <a href='javascript:insertContent(" + f + ")' class='itemLink' title='" + e + "'>"),
			(t +=
				"	   <img alt='" +
				e +
				"' id='img_" +
				o +
				"' src='/public/images/unavailable.jpg' border='0px' style='height:62px;width:60px;' onerror='return Roblox.Controls.Image.OnError(this)'/>"),
			n.IsEndorsed &&
				(t +=
					"<img class='endorsed-icon' src='" +
					ClientToolbox.Resources.endorsedIcon +
					"' title='" +
					ClientToolbox.Resources.endorsedAsset +
					"' />"),
			(t += "   </a>"),
			n.IsVoteable)
		) {
			var c = n.UserVotedUp || n.UserVotedDown ? "has-voted" : "",
				h = n.UserVotedUp ? "selected" : "",
				l = n.UserVotedDown ? "selected" : "";
			(t += '<div class="voting users-vote ' + c + '">'),
				(t += '  <div class="upvote tiny ' + h + '" onclick="handleVote(' + n.ID + ', true)"></div>'),
				(t += '  <div class="downvote tiny ' + l + '" onclick="handleVote(' + n.ID + ', false)"></div>'),
				(t += "</div>");
		} else
			i &&
				((r = n.Upvotes + n.Downvotes),
				(u = r > 0 ? Math.round((n.Upvotes * 100) / r) : null),
				(t += '<div class="voting">'),
				u !== null
					? ((t +=
						  '<div class="votes upvotes-percentage">' +
						  u +
						  '% <span class="upvote tiny unclickable-thumbs-up"></span></div>'),
					  (t += '<div class="votes totalVotes">(' + n.TotalVotesAsString + ")</div>"))
					: (t += '<div class="votes noVotes">' + ClientToolbox.Resources.noVotesYet + "</div>"),
				(t += "</div>"));
		return (t += "</div>");
	}),
	(ClientToolbox.GetThumbnail = function (n, t, i, r) {
		(url = "/Thumbs/RawAsset.ashx?Width=60&Height=62&ImageFormat=png"),
			(url += r ? "&AssetVersionID=" : "&AssetID="),
			(url += n),
			$.get(url, function (u) {
				if (u !== null) {
					if (u == "PENDING") {
						i < 4 &&
							window.setTimeout(function () {
								ClientToolbox.GetThumbnail(n, t, i + 1, r);
							}, 3e3);
						return;
					}
					console.log(u);
					$("#img_" + t).attr("src", u);
				}
			});
	});
var addAntiForgeryToken = function (n) {
	return (n.__RequestVerificationToken = $("input[name=__RequestVerificationToken]").val()), n;
};
$(function () {
	function r() {
		var n = $(window).height();
		$("#ToolBoxScrollWrapper").height(n - 61);
	}
	(ClientToolbox.IsUserAuthenticated = $("#NewToolboxContainer").data("isuserauthenticated")),
		(ClientToolbox.IsDecalCreationEnabled = $("#NewToolboxContainer").data("isdecalcreationenabled")),
		(ClientToolbox.RequestURL = $("#NewToolboxContainer").data("requesturl")),
		(ClientToolbox.IsRecentlyInsertedAssetEnabled = $("#NewToolboxContainer").data(
			"isrecentlyinsertedassetenabled"
		)),
		$("#tbSearch").focus(function () {
			$("#tbSearch").val() == $("#tbSearch")[0].title &&
				($("#tbSearch").removeClass("greyText"), $("#tbSearch").val(""), (ClientToolbox.SearchTouch = !0));
		}),
		$("#tbSearch").blur(function () {
			$("#tbSearch").val() == "" &&
				($("#tbSearch").addClass("greyText"),
				$("#tbSearch").val($("#tbSearch")[0].title),
				(ClientToolbox.SearchTouch = !1));
		}),
		$("#tbSearch").keyup(function (n) {
			if (n.keyCode == 13) return $("#Button").click(), !1;
		}),
		$(".Search").blur(),
		$("#SortList").change(function () {
			ClientToolbox.FetchData(!1);
		}),
		(ClientToolbox.ResizeHelper = r),
		$(window).resize(function () {
			r();
		});
	var n = !1,
		u = $(".SetOptions .SetListDropDownList"),
		i = $(".SetOptions .SetListDropDownList .menu"),
		t = $(".SetOptions  .btn-dropdown,.SetOptions .btn-dropdown-active");
	$(".SetOptions .btn-dropdown").click(function (r) {
		return (
			(n = !n),
			r.preventDefault(),
			u.toggleClass("invisible"),
			i.toggleClass("invisible"),
			t.toggleClass("btn-dropdown"),
			t.toggleClass("btn-dropdown-active"),
			r.stopPropagation(),
			!1
		);
	}),
		$(document).click(function () {
			n &&
				(u.toggleClass("invisible"),
				i.toggleClass("invisible"),
				t.toggleClass("btn-dropdown"),
				t.toggleClass("btn-dropdown-active"),
				(n = !1));
		}),
		$("#showMoreNext").click(function () {
			ClientToolbox.Paging("next");
		}),
		$("#Button").click(function () {
			ClientToolbox.FetchData(!1);
		}),
		$("#tbSearch").keypress(function () {
			handleEnterKey(event);
		}),
		$("#ddlSets").change(function () {
			ClientToolbox.DropDownListChanged();
		}),
		$(".Tabs").click(function () {
			ClientToolbox.ChangeTab($(this).attr("id"));
		});
	try {
		ClientToolbox.OnPageLoad();
	} catch (f) {}
});
