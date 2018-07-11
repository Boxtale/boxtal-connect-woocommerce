(function () {
    var Components = {};

    Components.tracking = {
        trigger: '.bw-order-tracking',

        init: function() {
            const trackingBlock = document.querySelector(this.trigger);
            const self = this;

            if (trackingBlock) {
                const httpRequest = new XMLHttpRequest();
                httpRequest.onreadystatechange = function(data) {
                    if (httpRequest.readyState === 4) {
                        if (httpRequest.status === 200) {
                            const carriers = httpRequest.response;
                            const loader = trackingBlock.querySelector(".bw-loading");
                            if (loader) {
                                loader.style.display = 'none';
                            }
                            const trackingInfo = trackingBlock.querySelector(".bw-tracking");
                            if (trackingInfo) {
                                if (carriers.length > 0) {
                                    self.updateTrackingBlock(trackingInfo, carriers);
                                }
                                trackingInfo.style.display = 'block';
                            }
                        } else {
                            console.log("Error: " + httpRequest.status);
                        }
                    }
                };
                httpRequest.open("GET", ajaxurl + "?action=get_order_tracking&order_id=" + encodeURIComponent(orderId));
                httpRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                httpRequest.responseType = "json";
                httpRequest.send();
            }
        },

        updateTrackingBlock: function(target, carriers) {
            console.log(carriers);

            let block = "";
            if (1 === carriers.length) {
                block = "<p>" + translations.order_sent_in_1_shipment + "</p>";
            } else {
                block = "<p>" + translations.order_sent_in_n_shipments + "</p>";
            }
            target.innerHTML(block);
        },

        on: function(elSelector, eventName, selector, fn) {
            const element = document.querySelector(elSelector);

            element.addEventListener(eventName, function(event) {
                const possibleTargets = element.querySelectorAll(selector);
                const target = event.target;

                for (let i = 0, l = possibleTargets.length; i < l; i++) {
                    let el = target;
                    const p = possibleTargets[i];

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
            Components.tracking.init();
        }
    );

})();