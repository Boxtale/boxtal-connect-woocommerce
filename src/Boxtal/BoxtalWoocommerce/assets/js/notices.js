(function () {
    var Components = {};

    Components.notices = {
        trigger: '.bw-notice',

        init: function () {
            var triggers = document.querySelectorAll(this.trigger);
            var self = this;

            if (triggers.length) {
                self.on("body", "click", ".bw-hide-notice", function() {
                    var httpRequest = new XMLHttpRequest();
                    var notice = this;
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
                    var noticeId = notice.getAttribute("rel");
                    httpRequest.send("action=hide_notice&notice_id=" + encodeURIComponent(noticeId) + "&security=" + encodeURIComponent(ajax_nonce));
                });

                self.on("body", "click", "#bw-pairing-update-validate", function() {
                    var httpRequest = new XMLHttpRequest();
                    var notice = this;
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
                    let input = "";
                    for (let i = 0; i < 6; i++) {
                        input += document.querySelector("input[name=bw-digit-" + i + "]").value;
                    }
                    httpRequest.send("action=pairing_update_validate&input=" + encodeURIComponent(input) + "&security=" + encodeURIComponent(ajax_nonce));
                });
            }
        },

        on: function(elSelector, eventName, selector, fn) {
            var element = document.querySelector(elSelector);

            element.addEventListener(eventName, function(event) {
                var possibleTargets = element.querySelectorAll(selector);
                var target = event.target;

                for (var i = 0, l = possibleTargets.length; i < l; i++) {
                    var el = target;
                    var p = possibleTargets[i];

                    while(el && el !== element) {
                        if (el === p) {
                            return fn.call(p, event);
                        }

                        el = el.parentNode;
                    }
                }
            });
        }
    };

    document.addEventListener(
        "DOMContentLoaded", function() {
            Components.notices.init();
        }
    );

})();