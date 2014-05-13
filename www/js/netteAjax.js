jQuery.extend({
	nette:{
		updateSnippet:function (id, html) {
			$("#" + id).html(html);
		},

		success:function (payload) {
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// snippets
			if (payload.snippets) {
				for (var i in payload.snippets) {
					$("#" + i).html(payload.snippets[i]);
				}
			}
		}
	}
});

jQuery.ajaxSetup({
	success:jQuery.nette.success,
	dataType:"json"
});

$("a.ajax").live("click", function (event) {
	event.preventDefault();
	$.get(this.href);
});

/* AJAXové odeslání formulářů */
$("form.ajax").live("submit", function () {
	$(this).ajaxSubmit();
	return false;
});

$("form.ajax :submit").live("click", function () {
	$(this).ajaxSubmit();
	return false;
});