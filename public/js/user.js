if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.User = {
	/*
		0: send friend req
		1: pending req (sent)
		2: pending req (incom)
		2: friends
	*/
	GetURL: function(endpoint) {
		return "/users/"+$("body").data("profile")+"/"+endpoint;
	},
	UpdateFriendButton: function() {
		$.get(this.GetURL("friend"), function(data) {
			if(!data['success'])
				alert(data['reason']);
			
			$("#friends-count").html(data['count']);
			$("#friends-label").html(data['count'] == 1 ? "Friend" : "Friends");
			ANORRL.User.SetStateOfFriendButton(data['result']);
			
		});
	},
	SetStateOfFriendButton: function(state) {
		var btn = $("#friend-btn");

		switch(state) {
			case 0:
				btn.html("friend");
				break;
			case 1:
				btn.html("cancel");
				break;
			case 2:
				btn.html("accept");
				break;
			case 3:
				btn.html("unfriend");
				break;
		}
	},
	Friend: function() {
		$.post(this.GetURL("friend"), {'ANORRL$Friend$Request': true}, function(data) {
			if(!data['success'])
				alert(data['reason']);

			$("#friends-count").html(data['count']);
			$("#friends-label").html(data['count'] == 1 ? "Friend" : "Friends");
			ANORRL.User.SetStateOfFriendButton(data['result']);
			
		});
	},

	UpdateFollowButton: function() {
		$.get(this.GetURL("follow"), function(data) {
			if(!data['success'])
				alert(data['reason']);
			
			$("#followers-count").html(data['count']);
			$("#followers-label").html(data['count'] == 1 ? "Follower" : "Followers");
			ANORRL.User.SetStateOfFollowButton(data['result']);
			
		});
	},
	SetStateOfFollowButton: function(state) {
		$("#follow-btn").html(state ? "unfollow" : "follow");
	},
	Follow: function() {
		$.post(this.GetURL("follow"), {'ANORRL$Follow$Request': true}, function(data) {
			if(!data['success'])
				alert(data['reason']);

			$("#followers-count").html(data['count']);
			$("#followers-label").html(data['count'] == 1 ? "Follower" : "Followers");
			ANORRL.User.SetStateOfFollowButton(data['result']);
			
		});
	},
	
}

$(function(){
	$("input[type='file'][hidden]").on("change", function() {
		var type = $(this).data("type");
		if(type != "pfp" && type != "banner") {
			alert("Something went wrong!");
			return;
		}

		var reader = new FileReader();

		reader.onload = function (e) {
			$("#crop-modal").attr("type", type);
			$("#crop-modal").modal({showClose: false});
			$('#cropper-img').attr('src', e.target.result).width(500);
			$('#cropper-img').cropper({
				aspectRatio: type == "banner" ? 970 / 220 : 1/1,
				viewMode: 1
			});
			$('#cropper-img').data("cropper").replace(e.target.result);	
		};

		reader.readAsDataURL(this.files[0]);
	})

	$("#crop-modal button[rel='save']").click(function() {
		var type = $('#cropper-img').parent().attr("type");
		if(type != "pfp" && type != "banner") {
			alert("Something went wrong!");
			return;
		}
		$('#cropper-img').data("cropper").getCroppedCanvas().toBlob((blob) => {
			const formData = new FormData();
			formData.append('croppedImage', blob);
			$.ajax('/users/update/' + type, {
				method: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success(data) {
					if(data['success']) {
						var image = $("#cropper-img").cropper("getCroppedCanvas").toDataURL(type == "banner" ?  'image/png' :  'image/jpeg');
						
						if(type == "banner") {
							$("#profile-container").css("background-image", "url("+image+")");
						} else {
							$("#profile-picture img").attr("src", image);
							$(".header-pfp-image").attr("src", image);
						}
					}
					else {
						alert("Something went wrong: " + data['reason']);
					}
				},
				error() {
					alert('Upload error');
				},
			});
			
			
		}, type == "banner" ?  'image/png' :  'image/jpeg');
	})

	$("button[data-method]").click(function() {
		var method = $(this).data("method");

		if(method.startsWith("upload-")) {
			var type = method.replaceAll("upload-", "");
			var file = $("input[type='file'][hidden]");

			file.attr("data-type", type);
			file.trigger("click");
		}
		else if(method.startsWith("remove-")) {
			var type = method.replaceAll("remove-", "");
			$.post("/users/remove/"+type, function() {window.location.reload();})
		}
	})

	$("a[href='open-modal']").click(function() {
		$("#image-modal").modal({showClose: false});
	});

	$("a[href='open-modal']").removeAttr("href");

	$(".button[data-tab]").click(function() {
		var type = $(this).data("tab");
		$("div[data-tab]").hide();
		$("div[data-tab='"+type+"']").show();
		$(".button[data-tab]").removeAttr("selected");
		$(this).attr("selected", "yes");
	})

	function setType(type) {
		if($(".button[data-tab='"+type+"']").length != 0) {
			$(".button[data-tab='"+type+"']").attr("selected", "yes");
			$("div[data-tab='"+type+"']").show();
		}
		else {
			setType("about");
			window.location.hash = "about";
		}
	}

	var hash = window.location.hash;
	if(hash.startsWith("#"))
		hash = hash.substring(1);

	setType(hash);

	$("#open-more-games").click(function() {
		$("#more-games-panel").show();
		$(this).hide();
	})


	ANORRL.User.UpdateFriendButton();
	$("#friend-btn").click(function() {
		ANORRL.User.Friend();
	});

	ANORRL.User.UpdateFollowButton();
	$("#follow-btn").click(function() {
		ANORRL.User.Follow();
	});

	$("#block-btn").click(function() {
		alert("blocking not implemented YET");
	})
});