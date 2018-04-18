document.addEventListener(
	"DOMContentLoaded", function() {
		if (document.getElementById("bw-shop-notice-validate")) {
            document.getElementById("bw-shop-notice-validate").addEventListener(
                "click", function() {
                    var httpRequest = new XMLHttpRequest();
                    httpRequest.onreadystatechange = function(data) {
                        if (httpRequest.readyState === 4) {
                            if (httpRequest.status === 200) {
                                console.log(httpRequest.response.data);
                                location.reload();
                            } else {
                                console.log("Error: " + httpRequest.status);
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

		var hideableNotices = document.querySelectorAll('.bw-hide-notice');
        for (var i = 0, len = hideableNotices.length; i < len; i++) {
            var notice = hideableNotices[i];
            notice.addEventListener(
                "click", function() {
                    var httpRequest = new XMLHttpRequest();
                    httpRequest.onreadystatechange = function(data) {
                        if (httpRequest.readyState === 4) {
                            if (httpRequest.status === 200) {
                                notice.closest(".bw-notice").style.display = 'none';
                            } else {
                                console.log("Error: " + httpRequest.status);
                            }
                        }
                    };
                    httpRequest.open("POST", ajaxurl);
                    httpRequest.setRequestHeader(
                        "Content-Type",
                        "application/x-www-form-urlencoded"
                    );
                    httpRequest.responseType = "json";
                    var noticeId = this.getAttribute("rel");
                    httpRequest.send("action=hide_notice&notice_id=" + encodeURIComponent(noticeId) + "&security=" + encodeURIComponent(ajax_nonce));
                }
            );
        }
	}
);