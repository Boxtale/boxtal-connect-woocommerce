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
            let block = "";
            if (1 === carriers.length) {
                block = "<p>" + translations.order_sent_in_1_shipment + "</p>";
            } else {
                block = "<p>" + translations.order_sent_in_n_shipments.replace('%s', carriers.length) + "</p>";
            }
            for (let i = 0; i < carriers.length; i++) {
                block += this.buildShipmentTracking(carriers[i]);
            }
            target.innerHTML = block;
        },

        buildShipmentTracking: function(shipment) {
            let block = "";
            block += "<h4>" + translations.shipment_ref.replace("%s", "<a href='"+shipment.tracking_url+"' target='_blank'>"+shipment.reference+"</a>") + "</h4>";
            if (0 === shipment.tracking_events.length) {
                block += "<p>" + translations.no_tracking_event_for_shipment + "</p>";
            } else {
                for (let i = 0; i < shipment.tracking_events.length; i++) {
                    const trackingEvent = shipment.tracking_events[i];
                    block += "<p>" + trackingEvent.date + " " + trackingEvent.message + "</p>";
                }
            }
            return block;
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