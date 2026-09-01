if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.Versions = {
	CurrentAssetID: -1,
	CurrentPage: 1,
	CurrentlyLoadingCrapBruh: false,
	NextPage: function() {
		this.GrabVersions(this.CurrentPage + 1);
	},
	PrevPage: function() {
		this.GrabVersions(this.CurrentPage - 1);
	},
	GrabVersions: function(page) {
		if(this.CurrentAssetID == -1)
			return;

		if(this.CurrentlyLoadingCrapBruh) {
			return;
		} else {
			this.CurrentlyLoadingCrapBruh = true;
		}

		if(page === undefined) {
			page = 1;
		}

		var versions_container = $("#versions-container");

		versions_container.find("tbody").children().each(function() {
			console.log($(this))
			if(typeof($(this).attr("header")) == "undefined") {
				$(this).remove();
			}

		});

		var pagercontainer = $("#pager");
		
		var backPager = pagercontainer.find("#back-pager");
		var nextPager = pagercontainer.find("#next-pager");

		$.get("/asset/"+this.CurrentAssetID+"/versions", {p: page}, function(data) {

			if(!data['success']) {
				ANORRL.MessageBox.Show(2, data['reason']);
				window.location.reload();
				return;
			}
			
			var versions = data['versions'];
			ANORRL.Versions.CurrentPage = data['page'];
			var current_page = ANORRL.Versions.CurrentPage;
			var total_pages = data['total_pages'];

			if(versions.length == 0) {
				if(pagercontainer.css("display") == "block") {
					pagercontainer.css("display", "none");
				}
			} else {
				if(pagercontainer.css("display") == "none") {
					pagercontainer.css("display", "block");
				}

				
				for (var key in versions) {

					var version = versions[key];
					var template = $("<tr></tr>");
					var version_elem = $('<td align="center"><a href>[ download ]</a>&nbsp;<a href="#version">[ make_current ]</a></td>');
					if(version["current"])
						version_elem = $('<td align="center"><a href="/asset/?id='+ANORRL.Versions.CurrentAssetID+'&version='+version["sub_id"]+'">[ download ]</a>&nbsp;<b style="display: inline-block;width: 87px;">>> current! <<</b></td>');
					else {
						var select_link = $(version_elem.find("a")[1]);
						var download_link = $(version_elem.find("a")[0]);
						download_link.attr("title", "click to download this version");
						download_link.attr("target", "__blank");
						download_link.attr("href", "/asset/?id="+ANORRL.Versions.CurrentAssetID+"&version="+version['sub_id']);

						select_link.data("versionid", version['id']);
						select_link.attr("title", "click to make this the current version");

						select_link.on("click", function() {
							var vid = $(this).data("versionid");
							$.post("/asset/"+ANORRL.Versions.CurrentAssetID+"/setversion/"+vid, function(data) {
								if(!data['success']) {
									ANORRL.MessageBox.Show(2, data['reason']);
								}
								ANORRL.Versions.GrabVersions();
							})
						});
					}
						

					template.append(version_elem);
					
					var version_id_elem = $('<td align="center"></td>');
					var version_date_elem = $('<td align="center"></td>');
					
					version_id_elem.html(version['sub_id']);
					version_date_elem.html(version['date']);
					template.append(version_id_elem);
					template.append(version_date_elem);

					versions_container.append(template);
				}

				if(current_page == 1) {
					backPager.css("display", "none");
				} else {
					backPager.css("display", "inline");
				}

				if(current_page == total_pages) {
					nextPager.css("display", "none");
				} else {
					nextPager.css("display", "inline");
				}

				pagercontainer.find("input[type='text']").val(current_page);
				pagercontainer.find("#page-counter").html(total_pages);
			}

			ANORRL.Versions.CurrentlyLoadingCrapBruh = false;
		});
	}
}

$(function(){
	ANORRL.Versions.GrabVersions();
});