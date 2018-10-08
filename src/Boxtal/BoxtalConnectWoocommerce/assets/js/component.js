(function () {
	var Components = {};

	Components.modals = {
		trigger: '.bw-modal-trigger',

		init: function () {
			var triggers = document.querySelectorAll(this.trigger);
			if (triggers.length) {

				for (var i = 0, len = triggers.length; i < len; i++) {
					var targetSelector = triggers[i].getAttribute("bw-modal-target");
					var target = document.querySelector("#" + targetSelector);
					document.body.appendChild(target);

					triggers[i].addEventListener(
						"click", function() {
							var targetContent = document.querySelector("#" + targetSelector + " .bw-modal-content");
							targetContent.classList.add("bw-modal-show");
							document.getElementById("bw-modal-backdrop").classList.add("bw-modal-show");
						}
					);
				}

				// add backdrop
				var backdrop = document.createElement("div");
				backdrop.setAttribute("id", "bw-modal-backdrop");

				backdrop.addEventListener(
					"click", function() {
						var targets = document.querySelectorAll(".bw-modal-content");
						for (var j = 0, len2 = targets.length; j < len2; j++) {
							targets[j].classList.remove("bw-modal-show");
						}
						this.classList.remove("bw-modal-show");
					}
				);
				document.body.appendChild(backdrop);
			}
		}
	};

	document.addEventListener(
		"DOMContentLoaded", function() {
			Components.modals.init();
		}
	);

})();
