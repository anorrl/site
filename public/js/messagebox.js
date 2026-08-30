if(typeof(ANORRL) == "undefined") {
	ANORRL = {}
}

ANORRL.MessageBox = {
	Show: function(type, reason) {
		$.post("/api/messagebox", {'ANORRL$MessageBox$Type': type, 'ANORRL$MessageBox$Contents': reason}, function(data) {
			$(data).modal({showClose: false});
			if(type == 2) {
				new Audio("/public/sounds/windows/stop.wav").play();
			}
			else {
				new Audio("/public/sounds/windows/ding.wav").play();
			}
			$("input[type='file']").val("");
		});
	},
};

ANORRL.MessageBox.Type = {
	INFO: 0,
	WARNING: 1,
	ERROR: 2
};