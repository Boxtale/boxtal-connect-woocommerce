document.addEventListener(
	"DOMContentLoaded", function() {
		document.getElementById("bw-shop-notice-validate").addEventListener(
			'click', function() {
				var httpRequest = new XMLHttpRequest();
				httpRequest.onreadystatechange = function(data) {
					if (httpRequest.readyState === 4) {
						if (httpRequest.status === 200) {
							location.reload();
						} else {
							console.log('Error: ' + httpRequest.status);
						}
					}
				};
				httpRequest.open("POST", ajaxurl);
				httpRequest.setRequestHeader(
					"Content-Type",
					"application/x-www-form-urlencoded"
				);
				httpRequest.responseType = 'json';
				var input = "";
				for (var i = 0; i < 6; i++) {
					input += document.querySelector("input[name=bw-digit-" + i + "]").value;
				}
				httpRequest.send("action=validate_shop_code&input=" + encodeURIComponent(input) + "&security=" + encodeURIComponent(ajax_nonce));
			}
		);
	}
);
