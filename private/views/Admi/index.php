<?php
	$user = SESSION->user;

	if(!$user->isAdmin()) {
		die("Hey... You're not an admin I don't think...");
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" xmlns:fb="http://www.facebook.com/2008/fbml" class="adminStyle" style="">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,requiresActiveX=true">
		<title>ANORRL | Administration</title>
		<link rel="icon" type="image/vnd.microsoft.icon" href="/favicon.ico">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Content-Language" content="en-us">
		<meta name="author" content="ANORRL">

		<script src="js/Microsoft/MicrosoftAjaxTreeView.js" type="text/javascript"></script>
		<script src="js/JsTree/jquery.js" type="text/javascript"></script>
		<script src="js/JsTree/jstree.js" type="text/javascript"></script>
		<link rel="stylesheet" href="CSS/Base/CSS/Admin.css">
		<link rel="stylesheet" href="CSS/Base/CSS/Admin2.css">
		<link rel="stylesheet" href="CSS/Admi3.css">
		<link rel="stylesheet" href="CSS/Base/CSS/jstree.css">
	</head>
	<body class="pageStyle" style="">
		<div id="image-retry-data" data-image-retry-max-times="10" data-image-retry-timer="1500"></div>
		<div id="http-retry-data" data-http-retry-max-timeout="8000" data-http-retry-base-timeout="1000"></div>
		<script type="text/javascript">
			if (top.location != self.location) {
				top.location = self.location.href;
			}
		</script>
		<form name="aspnetForm" method="post" action="/Admi/" id="aspnetForm">
			<div>
				<input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="">
				<input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="">
				<input type="hidden" name="__LASTFOCUS" id="__LASTFOCUS" value="">
				<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="">
			</div>

			<script type="text/javascript">
				//<![CDATA[
				var theForm = document.forms['aspnetForm'];
				if (!theForm) {
					theForm = document.aspnetForm;
				}
				function __doPostBack(eventTarget, eventArgument) {
					if (!theForm.onsubmit || (theForm.onsubmit() != false)) {
						theForm.__EVENTTARGET.value = eventTarget;
						theForm.__EVENTARGUMENT.value = eventArgument;
						theForm.submit();
					}
				}
				//]]>
			</script>
			<script src="js/Microsoft/MicrosoftAjax.js" type="text/javascript"></script>
			<script src="js/Microsoft/MicrosoftAjaxWebForms.js" type="text/javascript"></script>
			<script type="text/javascript">
				//<![CDATA[
				function WebForm_OnSubmit() {
					if (typeof(ValidatorOnSubmit) == "function" && ValidatorOnSubmit() == false) return false;
						return true;
				}
				//]]>
			</script>
			<div>
				<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="38D9001F">
				<input type="hidden" name="__VIEWSTATEENCRYPTED" id="__VIEWSTATEENCRYPTED" value="">
				<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="7NalTW4BTk4skUSx/dH0WRg5S/QEEy9/hIhFPSuw+MiyN0v9vIUHEMecFGjemFq+VikBi5zORaAw84TWAxCbtAkA8ctUj3ZWwK3YSxauoo9MOIazRzyWvj1GwfMVdSjSDKzKNHIDG8yUzEgdt2xIAtpDZ4zwofWImR4zAodmW2XVK5Tt5+G2ZcDqdpCVwDLpVJ9HIS6lj+BJX7Xdvtmstw8TywIFVGrRl+oTmj+sIiY+y9RAX++Nmnab0biL5HR/rG6+yKRPmbN8vUKqIWsMYDrMLAROqVyoaPXx3fP3/hlC7cseLQmWrLddzcobW7k01cjkiqc4TCad8HrZGk018LHjZ6q/vDZKl0JVuUxiL2GarmqdRDg8qDend4v+xjJ30I+0vUxnQYTQ2tmjO5my6qQdJpq7g8FP/8mX1KQSOekUch9sTtioKaWOatNh0s8MxA2eQp+T+t/M9cpcnK94dvvJ9CQDGbkp3BsKIA8fZlw2+sQ3oQ3E0cU+uluhhJhEYwqdXi/Pem8348kmRjpPfKEjQUlFF9Wz/aU5SieObUexvZ0mi05nRQIyJ+tElVDxzlFYgygidhtOpyZ/ir4JSeWaVPQxHycWQBItVR8uFY/pjZ0IS8er9cwSbeEh9pKF3hlBZ4yKG7s3vrJEoiSQwbHQ0CopnfCJZTBbjxVhjyto5VvCm0V6JgWQndDUyz+PV6oog6H8qMmddb3S8d/v73htrhZkyuHYt7tQfZaW9nwus5szn4ozydVoLAvgD3sH9z5bgIfOBjXKprWrkBmA9tV0YaD6CmfJjdqhi4Li0Vd9P9tA88JhGCz4DqHr27pEKgdSeJw/2Rry1rciYmYqwkj3WA80q4wXDESJR5eblmNi9HickRBy3LuxIBSXch7M6t4cgXfkiNJlnVmphPoLWuRTIDVKEIVZ6qJLct5YzICVYUNL">
			</div>
			<script type="text/javascript">
				//<![CDATA[
					Sys.WebForms.PageRequestManager._initialize('ctl00$ScriptManager', 'aspnetForm', [], [], [], 90, 'ctl00');
				//]]>
			</script>
			<div id="Container">
				<div id="sidebar">
					<div style="padding-left: 11px;">
						<div style="padding-right: 11px; padding-top: 11px;">
							<div class="logo_spacer" style="width: auto; height: 50px; padding-right: 4px;">
								<a href="/" style="display: block;margin-left: auto;margin-right: auto;width: 106px;height: 28px;">
									<img width="106px" height="28px" src="/public/images/roblox_logo.png">
								</a>
							</div>
							<div>
								<div>
									<div>
										<a>Configs</a> | <a>Machines</a>: <b>99</b>% of <b>1346</b>
									</div>
									<div>
										<a>Cores</a>: <b>69</b>% in use of <b>6466</b>
									</div>
									<div>
										<b>4529</b> running, <b>0</b> waiting
									</div>
									<div>
										<b>19715</b> <a>players</a> in <b>4721</b> <a href="/Admi/Games/Default.aspx">games</a> (<b>4.7:1</b>)
									</div>
									<div>
										<b>7</b> <a href="/Admi/Thumbs.aspx">thumb requests</a>
									</div>
								</div>
								<div>
									<hr>
									<div>
										<h6><b>11</b> <a href="/Admi/Moderation/Default.aspx">abuse reports</a>, </h6>
										<h6><b>44</b> <a href="/Admi/Moderation/AssetReview.aspx">images</a>, </h6>
										<h6><b>5</b> <a>videos</a>, </h6>
										<h6><b>74</b> <a href="/Admi/Users/ModerateUser.aspx">users</a></h6>
									</div>
									<div>
										<a href="/">ANORRL</a>, <a href="/Admi/Users/Find.aspx">FindUser</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="AdminNavigation">
						<div style="border: dotted 1px grey;">
							<div id="ctl00_cphRoblox_AdminNavigationTree" class="jstree jstree-1 jstree-default" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1_11" aria-busy="false"><ul class="jstree-container-ul jstree-children" role="group"><li role="presentation" aria-selected="false" aria-level="1" aria-labelledby="j1_1_anchor" aria-expanded="true" id="j1_1" class="jstree-node  jstree-open jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="1" aria-expanded="true" id="j1_1_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Admin Dashboard</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_2_anchor" aria-expanded="true" id="j1_2" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Config/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_2_anchor"></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_5_anchor" id="j1_5" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Shoutbox/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" id="j1_5_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Shoutbox</a></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_6_anchor" id="j1_6" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" id="j1_6_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Site-wide alert</a></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_7_anchor" id="j1_7" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Notifications.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" id="j1_7_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Notifications</a></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_8_anchor" id="j1_8" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Chat.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" id="j1_8_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Chat</a></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_9_anchor" aria-expanded="true" id="j1_9" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_9_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Scripts</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_10_anchor" id="j1_10" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/UserScripts/Scripts.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_10_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Review Scripts</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_11_anchor" id="j1_11" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/UserScripts/ReputationSystem.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_11_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Reputation<br>System</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_12_anchor" aria-expanded="true" id="j1_12" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_12_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>People</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_13_anchor" id="j1_13" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Users/Find.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_13_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Find</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_14_anchor" id="j1_14" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Users/UserAdmin.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_14_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>User Admin</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_15_anchor" id="j1_15" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Diagnostics/MachineConfiguration.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_15_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Machine Config</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_16_anchor" id="j1_16" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/AccountUpgrades/BuildersClub.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_16_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Builders Club</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_17_anchor" id="j1_17" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/AccountUpgrades/Referrals.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_17_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Referral Program</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_18_anchor" id="j1_18" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/AccountUpgrades/Payments.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_18_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Find Payments</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_19_anchor" id="j1_19" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_19_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Find Parent</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_20_anchor" id="j1_20" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Users/BlacklistEmail.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_20_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Blacklist Email</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_21_anchor" id="j1_21" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Users/ManageForumModeration.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_21_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Manage Forum Moderation</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_22_anchor" aria-expanded="true" id="j1_22" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_22_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Groups</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_23_anchor" id="j1_23" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Groups/FindGroup.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_23_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Find Group</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_24_anchor" id="j1_24" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_24_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Group Admin</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_25_anchor" id="j1_25" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_25_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Group Building</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_26_anchor" aria-expanded="true" id="j1_26" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_26_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Contests</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_27_anchor" id="j1_27" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_27_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Edits Contests</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_28_anchor" id="j1_28" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_28_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Create New</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_29_anchor" aria-expanded="true" id="j1_29" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_29_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Moderation</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_30_anchor" id="j1_30" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_30_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Image Queue</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_31_anchor" id="j1_31" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_31_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Abuse Queue</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_32_anchor" id="j1_32" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_32_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Abuse Grid</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_33_anchor" id="j1_33" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_33_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>User Queue</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_34_anchor" id="j1_34" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_34_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>IP Addresses</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_35_anchor" id="j1_35" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_35_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Asset Scrub</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_36_anchor" id="j1_36" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_36_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Order History</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_37_anchor" id="j1_37" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_37_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Content Filter</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_38_anchor" id="j1_38" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_38_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Trade/Evaluation</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_39_anchor" id="j1_39" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/ContentFilter/RegularExpressionView.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_39_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Regular Expressions</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_40_anchor" id="j1_40" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Moderation/ModeratorReview.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_40_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Moderator<br>Review</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_41_anchor" id="j1_41" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_41_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Moderator Performance</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_42_anchor" aria-expanded="true" id="j1_42" class="jstree-node  jstree-open"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" aria-expanded="true" id="j1_42_anchor"><i class="jstree-themeicon jstree-bullet-black jstree-themeicon-custom" role="presentation"></i>Games</a><ul role="group" class="jstree-children"><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_43_anchor" id="j1_43" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Admi/Users/FPSSummary.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_43_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>FPS</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_44_anchor" id="j1_44" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_44_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Build Games</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_45_anchor" id="j1_45" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_45_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Broadcast</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_46_anchor" id="j1_46" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_46_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Places</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_47_anchor" id="j1_47" class="jstree-node  jstree-leaf"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_47_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Search</a></li><li role="presentation" aria-selected="false" aria-level="3" aria-labelledby="j1_48_anchor" id="j1_48" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="3" id="j1_48_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>BadgeAssetAward</a></li></ul></li><li role="presentation" aria-selected="false" aria-level="2" aria-labelledby="j1_49_anchor" id="j1_49" class="jstree-node  jstree-leaf jstree-last"><i class="jstree-icon jstree-ocl" role="presentation"></i><a class="jstree-anchor" href="/Default.aspx" tabindex="-1" role="treeitem" aria-selected="false" aria-level="2" id="j1_49_anchor"><i class="jstree-themeicon jstree-bullet-grey jstree-themeicon-custom" role="presentation"></i>Grid</a></li></ul></li></ul></div>
						</div>
					</div>
				</div>
				<div style="margin-left: 196px;">
					<div class="Panel" style="padding-top: 10px;border: none;">
						<div class="adminContent" style="margin-top: 0;padding-top: 1.6em;">
							<p>Thumbnail Request Count: 2</p>
							<p>Total Count: 0</p>
							<p>Thumbnail Blacklist Count: 0</p>
							<div class="spacer"></div>
							<p>Failure Rate: NaN%&nbsp;&nbsp;Timeout Rate: NaN%</p>
							<button>Clear Blacklist</button>

							<!--div>
							<h3>Very Epic Administration Buttons</h3>
							<div class="panel_buttons">
							<div class="button_small_gray">
							<span class="button_small_gray_left"></span><span class="button_small_gray_content">
							Gray
							</span><span class="button_small_gray_right"></span>
							</div>
							<div class="button_blue">
							<span class="button_blue_left"></span><span class="button_blue_content">
							Blue
							</span><span class="button_blue_right"></span>
							</div>
							<div class="button_black">
							<span class="button_black_left"></span><span class="button_black_content">
							Black
							</span><span class="button_black_right"></span>
							</div>
							<div class="button_gray">
							<span class="button_gray_left"></span><span class="button_gray_content">
							Gray
							</span><span class="button_gray_right"></span>
							</div>
							<div class="button_glossy">
							<span class="button_glossy_left"></span><span class="button_glossy_content">
							Glossy
							</span><span class="button_glossy_right"></span>
							</div>
							</div>
							</div-->
						</div>
					</div>
				</div>
			</div>
		</form>	
		<script type="text/javascript">
			var treeview = $('#ctl00_cphRoblox_AdminNavigationTree');
				var options = {
				'core' : {
					'data' : [{"text":"Admin Dashboard","a_attr":{"href":"/Admi/Default.aspx"},"children":[{"text":"Shoutbox","a_attr":{"href":"/Admi/Shoutbox/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Site-wide alert","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Notifications","a_attr":{"href":"/Admi/Notifications.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Chat","a_attr":{"href":"/Admi/Chat.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Scripts","a_attr":{"href":"/Default.aspx"},"children":[{"text":"Review Scripts","a_attr":{"href":"/Admi/UserScripts/Scripts.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Reputation<br/>System","a_attr":{"href":"/Admi/UserScripts/ReputationSystem.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"People","a_attr":{"href":"/Default.aspx"},"children":[{"text":"Find","a_attr":{"href":"/Admi/Users/Find.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"User Admin","a_attr":{"href":"/Admi/Users/UserAdmin.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Machine Config","a_attr":{"href":"/Admi/Diagnostics/MachineConfiguration.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Builders Club","a_attr":{"href":"/Admi/AccountUpgrades/BuildersClub.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Referral Program","a_attr":{"href":"/Admi/AccountUpgrades/Referrals.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Find Payments","a_attr":{"href":"/Admi/AccountUpgrades/Payments.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Find Parent","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Blacklist Email","a_attr":{"href":"/Admi/Users/BlacklistEmail.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Manage Forum Moderation","a_attr":{"href":"/Admi/Users/ManageForumModeration.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"Groups","a_attr":{"href":"/Default.aspx"},"children":[{"text":"Find Group","a_attr":{"href":"/Admi/Groups/FindGroup.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Group Admin","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Group Building","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"Contests","a_attr":{"href":"/Default.aspx"},"children":[{"text":"Edits Contests","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Create New","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"Moderation","a_attr":{"href":"/Default.aspx"},"children":[{"text":"Image Queue","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Abuse Queue","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Abuse Grid","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"User Queue","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"IP Addresses","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Asset Scrub","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Order History","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Content Filter","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Trade/Evaluation","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Regular Expressions","a_attr":{"href":"/Admi/ContentFilter/RegularExpressionView.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Moderator<br/>Review","a_attr":{"href":"/Admi/Moderation/ModeratorReview.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Moderator Performance","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"Games","a_attr":{"href":"/Default.aspx"},"children":[{"text":"FPS","a_attr":{"href":"/Admi/Users/FPSSummary.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Build Games","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Broadcast","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Places","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"Search","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"},{"text":"BadgeAssetAward","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"},{"text":"Grid","a_attr":{"href":"/Default.aspx"},"children":[],"state":{"selected":false},"icon":"jstree-bullet-grey"}],"state":{"selected":false},"icon":"jstree-bullet-black"}],
					'themes' : {
						'icons' : true
					}
				}
			};
			treeview.jstree(options).on('loaded.jstree', function() {
				treeview.jstree('open_all');
			});
			treeview.bind('select_node.jstree', function (e, data) {
				var href = data.node.a_attr.href;
				document.location.href = href;
			});
		</script>
	</body>
</html>