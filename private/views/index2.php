<!DOCTYPE html>
<html>
	<head>
		<title>Welcome to ANORRL!</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		
		<script src="/public/js/core/jquery.js"></script>
		<script src="/public/js/main.js?t=1776250887"></script>
		<script src="/public/wimpy/wimpy.js"></script>
		
		<style>
			@font-face {
				font-family: 'punk';
				src: url('/public/css/Punktype.ttf');
			}

			@font-face {
				font-family: 'road';
				src: url('/public/css/TransportMedium.ttf');
			}

			html {
				background: #123f83;
				font-family: Arial,Helvetica,sans-serif;
				color: #eee;
				font-size: 12px;
			}

			#Footer {
				line-height: 1.428;
				width: 100%;
				margin: 0 auto;
				padding: 10px 0;
				text-align: center;
				background: linear-gradient(to bottom,rgb(121, 12, 148) 0,rgb(226, 181, 255) 100%);
			}

			#FooterContainer {
				width: 970px;
				margin: 0 auto;
			}

			#Body {
				background: url("/public/images/bkg.png?v=1") top center repeat-x #32043f;
				min-height: calc(100vh - 200px); /* testing */
				animation: bgScroll 50s linear infinite;
			}

			@keyframes bgScroll {
				0% {
					background-position : 0px 0px;
				}
				100% {
					background-position : -100% 0px;
				}
			}

			@keyframes bgScroll2 {
				0% {
					background-position : 0px 0px;
				}
				100% {
					background-position : 100% 0px;
				}
			}

			#Header {
				display:block;
				width:100%;
				height: 30px;
				background-image:url("/public/images/header/navbar3.jpg");
				animation: bgScroll2 50s linear infinite;
				background-size: contain;
				border-bottom: 5px solid black;
				text-align: center;
			}

			#Header #Links {
				position:relative;
				font-size: 16px;
				padding-top: 5px;
			}

			#Header #Links a {
				color: white;
				padding: 1px 1.5em;
				background: rgb(0,0,0);
				border: 2px solid black;
				font-family: punk;
			}

			#Header #Links a:hover {
				text-decoration: underline;
				background: #222;
			}

			#Header #Logo a {
				width: 35px;
				height: 30px;
				display: inline-block;
			}

			#Header #Logo a img {
				height: 30px;
				background: linear-gradient(90deg,rgb(191, 181, 0) 0%, rgba(173, 139, 15, 0.76) 50%, rgba(0, 0, 0, 0) 100%);
				background-blend-mode: overlay;
				padding-left: 5px;
			}

			#Header #container {
				width: 970px;
				margin: 0 auto;
			}

			body {
				margin: 0;
			}
		</style>
	</head>
	<body>
		<div id="Header">
			<div id="Logo" style="float: left;">
				<a href="/">
					<img src="/public/images/header/logo2.png">
				</a>
			</div>
			<div id="container">
				<div id="Links">
					<a href="/users/0/profile">Profile</a>
					<a href="/games">Games</a>
					<a href="/catalog">Catalog</a>
					<a href="/vandals">Vandals</a>
				</div>
			</div>
			
		</div>
		<div id="Body">
			<div id="BodyContainer">
				<div>TEST</div>
			</div>
		</div>
		<div id="Footer" style="position:relative;">
			<div id="FooterContainer">
				<p id="Legalese">
					<img src="/public/images/hankdanceloop.gif" style="height: 60px;float:left;margin-top:-8px;">
					<span style="margin-top: 20px;display: block;">
						ANORRL (ANother Old Roblox Revival Lol) is made for a friends-only userbase<br>and thus we won't be accepting anyone that we don't know directly.
						<br><b>Made by grace with love &lt;3</b> | <a href="/info/credits">Contributors!</a> | <b>Lucky Number: 37452</b>
					</span>
					<br style="display:block;clear:both;">
				</p>
			</div>
		</div>
	</body>
</html>
