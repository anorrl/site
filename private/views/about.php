<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;
	
    $randomsplash = new FileSplasher("titles/about")->getRandomSplash();

	$page = new Page("About ANORRL");
	$page->loadHeader2();
?>
<style>
	h1 {
		margin: 10px 0px;
	}

	.box > p {
		font-size: 13px;
	}

	.box {
		padding: 10px 20px;
	}
</style>
<h2 class="page-title">.about</h2>
<h3 class="page-slogan"><?= $randomsplash ?></h3>
<div class="box">
	<h1>sooo what is anorrl?</h1>
	<hr>
	<img style="float:right; height:300px;margin-bottom:-10px;" title="kuro (hey thats me!)" src="/public/images/characters/kuro.png">
	<p>
		<b>ANO</b>ther <b>R</b>oblox <b>R</b>etake <b>L</b>OL
		is a project that aims to serve as an alternative to ROBLOX.
		It bases itself off the March 2016 source code (that was leaked around the same time), we do not want profits!
		We just want to have fun, we don't want the restrictions held by the platform at this time.
	</p>
	<p>
		So what does this mean to you? The newcomer coming across this site?
	</p>
	<p>
		Well first off, nothing.
		This project is for friends, and yes we know how absurd it is to have a <b><i>.com</i></b> domain for this but it was SUPER cheap!
	</p>
	<p>
		Second, this project is not your bog standard old roblox shit.
		Yes, the features added to the client may not be crazy or cool but it adds what the developers need for transitioning.
		We will not hold back from transforming the client to be entirely different than what everyone else wants to stick by.
	</p>
	<br style="clear:both">
</div>
<br>
<div class="box">
	<h1>sooo why anorrl over anything else?</h1>
	<b>(yap incoming)</b>
	<img style="float:right; height:500px;margin-right:-20px;" title="em" src="/public/images/characters/em_face.png">
	<p>
		I'm not sure!
		It's your decision to consider this project as anything remotely interesting...

		Again as stated before, we don't want to be like everything else.
		We want to bring a fresh new look on to the clients, and bring something new to the table.
	</p>
	<p>
		I personally find the <b><acronym title="Old Roblox Community">ORC</acronym></b> to be well...
	</p>
	<p>
		First off, ass.
	</p>
	<p>
		Second, I find it lacks anything original. The frontends all look the same, a recolor if you're lucky.
		And I don't want to discredit the effort put into them, I've been there after all. (Unless you use Bubbablox source, in that case fuck you I guess.)
	</p>
	<p>
		However, I find that everything homogenizes in my mind after the 10th revival that uses the same frontend shit.
		The only notable frontend thing I've seen is Hexagon but their clients fall flat. 
	</p>
	<p>
		And don't even get me started on the clients, they NEVER get changed. MAX like the branding of the APPLICATION.
		Yes I've heard of Itteblox but that's one case out of MANY.

		Everything and I mean everything, has the same fucking blue scheme OR red (depending on the era).

		NOTHING CHANGES.
	</p>
	<p>
		And I'm sick of it!
	</p>
	<p>
		So to roll it back around, why ANORRL?
		If you're the type of person that genuinely wants to go out of their way to push themselves to try new things like creating games or items rather than reuploading.
	</p>
	<p>We are open.</p>
	<p>
		If you're the type of person that wants to see innovation, to see what limits the clients can be pushed to and want a new experience along with everything else?
	</p>
	<p>
		HELL!
		If you're the type of person that's bored of seeing the same shit OVER AND OVER and <b><i>HATE</i></b> reuploads with a burning passion????
	</p>
	<p>We are open.</p>
</div>

<?php $page->loadFooter2(); ?>