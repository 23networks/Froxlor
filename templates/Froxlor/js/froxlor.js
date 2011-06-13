$(document).ready(function() {
	// make rel="external" links open in a new window
		$("a[rel='external']").attr('target', '_blank');
		$(".main").css('min-height', $("nav").height() - 34);
		$(".dboarditem:last").css('min-height', $(".dboarditem:first").height());

		// set focus on username-field if on loginpage
		if ($(".loginpage").length != 0) {
			$("#loginname").focus();
		}

		if ($("table.formtable").length != 0) {
			$("table.formtable tr").hover(function() {
				$(this).css("background-color", "#fff");
			}, function() {
				$(this).css("background-color", "#f5f5f5");
			});
		}
		if ($("table.bradiusodd").leingth != 0) {
			$("table.bradiusodd tbody tr").not(':last-child').hover(function() {
				$(this).css("background-color", "#fff");
			}, function() {
				$(this).css("background-color", "#f5f5f5");
			});
			// last row needs border-radius
			$("table.bradiusodd tbody tr:last-child").hover(function() {
				 $(this).children().css("background-color", "#fff");
				 $(this).children(':first-child').css("-webkit-border-bottom-left-radius", "20px");
				 $(this).children(':first-child').css("-moz-border-radius-bottomleft", "20px");
				 $(this).children(':first-child').css("border-bottom-left-radius", "20px");
			},
			function() {
				$(this).children().css("background-color", "#f5f5f5");
			}
			);
		}

	});
