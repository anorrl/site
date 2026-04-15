<!DOCTYPE html>
<html>
	<head>
		<title>Toolbox</title>
		<link rel="stylesheet" href="/public/css/toolbox.css"/>
		<script type="text/javascript" src="/public/js/core/jquery.js"></script>
		<script type="text/javascript" src="/public/js/core/jquery-migrate.js"></script>
		<script type="text/javascript" src="/public/js/toolbox.js"></script>
	</head>
	<body class="Page" style="margin: 0;">
		<input name="__RequestVerificationToken" type="hidden" value="6_eZHOjUPq8Jhw66Ug0so8DxlG33_rZY0TrLaXEc7aMbOKqRbphTsdZWYh_pBl5ud60toqWtjSAZmQHQU93ZLxukFYLaUIRjFnWCQD57CiwhlECKHNRU2ejI5FDEhDWcZ1Ru3g2"/>
		<div id="NewToolboxContainer" data-isuserauthenticated="<?= !SESSION ? "false" : "true" ?>" data-isdecalcreationenabled="true" data-requesturl="http://<?= CONFIG->domain ?>/asset/" data-isrecentlyinsertedassetenabled="false">
			<div id="ToolboxControls">
				<div id="SetTabs">
					<div id="Inventory" class="Tabs">Inventory</div>
					<div id="Search" class="Tabs">Search</div>
				</div>
				<div id="ToolboxSelector">
					<span class="toolboxDisplayText">Display: </span>
					<select id="ddlSets" class="Toolboxes"></select>
					<div id="SearchList" class="SetList SetOptions hidden" style="float: left; min-width: 58px; ">
						<a href="#" id="activeOption" class="btn-dropdown" data-value="FreeModels">Models</a>
						<span class="dropdownImg"></span>
						<div class="clear"></div>
						<div class="SetListDropDown">
							<div class="SetListDropDownList invisible">
								<div id="SearchMenu" class="menu invisible">
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<div id="ToolboxSearch" class="hidden">
						<input type="text" id="tbSearch" title="Search" class="Search"/>
						<div id="Button" class="ButtonText"></div>
					</div>
				</div>
			</div>
			<div id="ToolBoxScrollWrapper">
				<div id="Filters" class="searchFilter hidden">
					<span class="filterText">Sort by: </span>
					<select name="SortList" id="SortList" class="Toolboxes" style="float:none;min-width: 103px">
						<option value="Relevance">Relevance</option>
						<option value="MostTaken">Most Taken</option>
						<option value="Favorites">Favorites</option>
						<option value="Updated">Updated</option>
					</select>
				</div>
				<div id="ResultStatus" class="hidden"></div>
				<div id="Navigation" class="Navigation hidden">
				</div>
				<div id="ToolboxItems">
				</div>
				<div id="ShowMore" class="Navigation hidden" style="clear:both;">
					<div id="showMoreNext">
						<a id="showMoreButton" class="btn-control btn-control-small" style="cursor:pointer;margin-left: 2px;">Show More</a>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			if (typeof ClientToolbox === "undefined") {
				ClientToolbox = {};
			}

			ClientToolbox.Resources = {
				//<sl:translate>
				models: "Models",
				recentModels: "Recent models",
				recentDecals: "Recent decals",
				myModels: "My Models",
				myDecals: "My Decals",
				decals: "Decals",
				mySets: "My Sets",
				mySubscribedSets: "My Subscribed Sets",
				robloxSets: "ANORRL Sets",
				noSets: "No sets are available",
				setsError: "An error occured while retrieving sets",
				results: "Results",
				loading: "Loading...",
				noResults: "No results found.",
				error: "Error Occurred.",
				errorData: "An error occured while retrieving this data",
				insertError: "Could not insert the requested item",
				dragError: "Sorry Could not drag the requested item",
				noVotesYet: "No votes yet",
				endorsedAsset: "A high-quality item",
				//</sl:translate>
				endorsedIcon: "/public/images/ide/endorsed.png"
			};
		</script>
	</body>
</html>