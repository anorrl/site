if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.User = {

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
});