<h4 class="page-title">.commentary</h4>
<div class="box" style="padding: 10px 20px" id="comment-post-container">
	<h3 class="page-slogan">.post_something_cool!</h3>
	<textarea maxlength="256" minlength="4" class="box input" style="width: 914px" placeholder="hurr durr i love this!!!"></textarea>
	<div class="comment-error">you did something bad: <span></span></div>
	<button class="button" style="margin-top: 5px">submit</button>
</div>
<div style="padding: 5px">
	<div id="statuses">
		<div class="status" id="loading-status">
			<img src="/public/images/ProgressIndicator4White.gif" width="90">
			<br>
			<b>loading comments...</b>
		</div>
		<div class="status" id="nothing-status">
			<img src="/public/images/noassets.png" width="110">
			<br>
			<b>there's no discussion here!</b>
		</div>
	</div>
	<div id="comments-container"></div>
	<div id="comments-pager" style="margin: 0 auto;text-align: center">
		<hr>
		<a href="javascript:ANORRL.Comments.PrevPage()" id="back-pager">&lt;&lt; back</a>
		<input class="box input" type="text" maxlength="3" value="1" style="width: 25px;text-align: center;padding: 2px 4px;"> of <span id="page-counter">1</span>
		<a href="javascript:ANORRL.Comments.NextPage()" id="next-pager">next &gt;&gt;</a>
	</div>
</div>