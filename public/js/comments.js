if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Comments  = {
	CurrentPage: 1,
	CurrentlyLoadingCrapBruh: false,
	GetType: function() {
		if(typeof($("body").data("asset")) != "undefined")
			return "asset";
		if(typeof($("body").data("profile")) != "undefined")
			return "users";
	},
	GetID: function() {
		return this.GetType() == "asset" ? Number($("body").data("asset")) : Number($("body").data("profile"));
	},
	GetGrabURL: function() {
		return "/"+this.GetType()+"/"+this.GetID()+"/comments";
	},
	GetPostURL: function() {
		return "/"+this.GetType()+"/"+this.GetID()+"/comment";
	},
	NextPage: function() {
		this.LoadComments(this.CurrentPage + 1);
	},
	PrevPage: function() {
		this.LoadComments(this.CurrentPage - 1);
	},
	LoadComments: function(page) {

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		var loadingMessage = $("#loading-status");
		var emptyMessage   = $("#nothing-status");

		emptyMessage.css("display", "none");
		loadingMessage.css("display", "block");

		if(page === undefined) {
			page = 1;
		}

		var items_container = $("#comments-container");
		var pager_container = $("#comments-pager");

		items_container.children().remove();
		
		$.get(this.GetGrabURL(), {p : page}, function(data) {
			
			var comments = data['comments'];
			ANORRL.Comments.CurrentPage = data['page'];
			var current_page = ANORRL.Comments.CurrentPage;
			var total_pages = data['total_pages'];
			var coms_empty = comments.length == 0;

			loadingMessage.css("display", "none");

			pager_container.css("display", coms_empty ? "none" : "block");

			if(coms_empty) {
				emptyMessage.css("display", "block");
				ANORRL.Comments.CurrentlyLoadingCrapBruh = false;
				return
			}

			pager_container.find("#back-pager").css("display", current_page == 1           ? "none" : "inline");
			pager_container.find("#next-pager").css("display", current_page == total_pages ? "none" : "inline");

			pager_container.find("input").val(current_page);
			pager_container.find("#page-counter").html(total_pages);

			for (var key in comments) {

				var comment = comments[key];

				var template = null;
				if(comment['creator']) {
					template = $($(".comment-right[template]").clone().prop('outerHTML'));
					template.attr("class", "comment");
					template.attr("right", "true");
				} else {
					template = $($(".comment[template]").clone().prop('outerHTML'));
				}
				template.removeAttr("template");

				template.find(".profile-container").attr("title", comment['poster']['name']);
				template.find(".profile-container a").attr("href", comment['poster']['url']);
				template.find(".profile-container img").attr("data-src", comment['poster']['img']);
				template.find(".profile-container img").lazy();
				
				template.find("#details a#name").attr("href", comment['poster']['url']);
				template.find("#details a#name").html(comment['poster']['name']);
				template.find("#details #date").html(comment['date']);

				template.find("#contents").html(comment['contents']);

				items_container.append(template);
			}

			ANORRL.Comments.CurrentlyLoadingCrapBruh = false;
		});
	},
	IsButtonDisabled: function() {
		return typeof($("#comment-post-container").find("button").attr("disabled")) != "undefined"
	},
	IsInputValid: function() {
		var contents = $("#comment-post-container").find("textarea").val().trim();
		return contents.length <= 256 && contents.length >= 4;
	},
	ShowError: function(error) {
		if(!error) {
			$(".comment-error").css("display", "none");
			return;
		}

		$(".comment-error").css("display", "block");
		$(".comment-error").find("span").html(error);
	},
	Submit: function(event) {
		if(ANORRL.Comments.IsButtonDisabled() || !ANORRL.Comments.IsInputValid())
			return;

		ANORRL.Comments.ShowError();

		$.post(ANORRL.Comments.GetPostURL(), {'ANORRL$Comment$Contents': $("#comment-post-container").find("textarea").val()}, function(data) {
			ANORRL.Comments.LoadComments();
			
			if(!data['success']) {
				ANORRL.Comments.ShowError(data['reason']);
			} else {
				document.querySelector("#comment-post-container textarea").value = "";
			}
		});
	}
}

$(function(){
	ANORRL.Comments.LoadComments();

	$("#comment-post-container").find("button").click(ANORRL.Comments.Submit)

	$("#comment-post-container").find("textarea").on("keyup keydown change", function() {
		var contents = $(this).val().trim();
		var disabled = contents.length < $(this).attr("minlength");

		$(this).css("border-color", disabled ? "red" : "")
		$("#comment-post-container").find("button").attr("disabled", disabled);
	})

	$("#comments-pager").find("input").on("change", function() {
		ANORRL.Comments.LoadComments(Number($(this).val()));
	});
});