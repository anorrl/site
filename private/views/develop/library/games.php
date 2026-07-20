<style>
	.game {
		*text-align: center;
		*padding: 10px;
		user-select: none;
		width: 100%;
	}

	.game td {
		vertical-align: top;
	}

	.game #url {
		font-size: 14px;
		font-weight: bold;
	}

	.game #url span {
		margin-top: 2px;
		display: block;

		width: 230px;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.game #thumbnail {
		width: 140px;
	}

	.game:hover {
		border: 2px solid var(--border-color);
		padding: 2px;
  		margin: -4px;
		padding-right: 0px;
		background: linear-gradient(180deg,rgb(26, 12, 35) 0%, rgb(73, 34, 101) 100%);
	}

	.game #info {
		display: none;
	}

	.game:hover #info {
		display: block;
	}

	.game #slot span#picture {
		width: 14px;
		height: 14px;
		display: inline-block;
		background-size: 100%;
		margin-left: -5px;
		margin-right: 2px;
	}

	.game #slot[src="inactive"] span#picture {
		background-image: url("/public/images/OnlineStatusIndicator_IsOffline.png");
	}


	
</style>
<script>
	ANORRL.Creations.HandleDropdownClick = function(event) {
		var universe = $(this).parent().attr("data-universeid");
		var assetid = $(this).parent().attr("data-id");
		var action = $(this).attr("data-actionid");

		if(action == 1) {
			window.location.href = "/develop/universes/"+universe+"/configure";
		}
		else if(action == 4) {
			$.post("/api/universes/"+universe+"/shutdown", function(data) {
				if(!data['success'])
					alert(data['reason']);
				else
					alert("success!");
			})
		}
		else if(action == 5) {
			alert("not legible for sex...");
		}
	}
</script>
<!--<div class="game" template>
	<a id="url" href>
		<img id="thumbnail" src>
		<div id="name"></div>
	</a>
</div>-->
<table class="game" id="name" title template>
	<tr>
		<td width="145">
			<a id="url" href>
				<img id="thumbnail" src>
			</a>
		</td>
		<td width="232">
			<a id="url" href><span id="name"></span></a>
			<table style="margin-top:10px;width: 100%">
				<tr>
					<td id="slot" src=""><span id="picture">&nbsp;</span><span id="slot" html></span></td>
					<td>Updated <span id="updated"></span></td>
				</tr>
			</table>
		</td>
		<td width="100"  style="vertical-align: middle;">
			<div>Total Visitors: <span id="visits">0</span></div>
			<div>Last 7 Days: <span id="weekly_visits">0</span></div>
		</td>
		<td style="vertical-align: middle;text-align: right">
			<input type="submit" class="button" value="edit">
		</td>
		<td style="vertical-align: middle;text-align: center;">
			
			<div class="cog-dropdown">
				<button class="button cog" style="padding: 5px 10px" class=""><img src="/public/images/icons/cog.png" ></button>
				<ul>
					<li data-actionid="1"><span>&gt;</span> configure</li>
					<li data-actionid="2"><span>&gt;</span> advertise</li>
					<li data-actionid="3"><span>&gt;</span> create badge</li>
					<li data-actionid="4"><span>&gt;</span> shutdown all servers</li>
					<li data-actionid="5"><span>&gt;</span> sex update</li>
				</ul>
			</div>
		</td>
	</tr>
</table>
<div id="panel">
	<div class="box" style="padding: 15px;">
		<h2>.games</h2>
		<hr>
		<input type="submit" class="button" value="create a place"> <span style="font-family: road; font-size: 14px;">(5 slots left)</span>
	</div>
	<div class="box" style="padding: 15px; margin-top: 10px;">
		<h2>.games <input class="box input" id="search-box" style="padding: 0px 5px;" placeholder="search whatever dude..."></h2>
		<hr>
		<?php generate_statuses() ?>
		<div data-loadtype="98"></div>
	</div>
</div>