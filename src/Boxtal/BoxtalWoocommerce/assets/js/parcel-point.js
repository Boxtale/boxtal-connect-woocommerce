(function () {
    var Components = {};

    Components.links = {
        trigger: '.bw-select-parcel',

        init: function () {
            var triggers = document.querySelectorAll(this.trigger);
            var self = this;

            if (triggers.length) {
                var map = document.querySelector('#bw-map');
                if (!map) {
                    self.initMap();
                    map = document.querySelector('#bw-map');
                }

                self.on("body", "click", self.trigger, function() {
                    map.classList.add("bw-modal-show");
                });
            }
        },

        initMap: function() {
            var mapClose = document.createElement("div");
            mapClose.setAttribute("class", "bw-close");
            mapClose.setAttribute("title", "Close map");

            var mapCanvas = document.createElement("div");
            mapCanvas.setAttribute("id", "bw-map-canvas");

            var mapContainer = document.createElement("div");
            mapContainer.setAttribute("id", "bw-map-container");
            mapContainer.appendChild(mapCanvas);

            var mapPPContainer = document.createElement("div");
            mapPPContainer.setAttribute("id", "bw-pp-container");

            var mapInner = document.createElement("div");
            mapInner.setAttribute("id", "bw-map-inner");
            mapInner.appendChild(mapClose);
            mapInner.appendChild(mapContainer);
            mapInner.appendChild(mapPPContainer);

            var mapOuter = document.createElement("div");
            mapOuter.setAttribute("id", "bw-map");
            mapOuter.appendChild(mapInner);
            document.body.appendChild(mapOuter);

            var offset = window.pageYOffset + (window.innerHeight - mapOuter.outerHeight)/2;
            if (offset < window.pageYOffset) {
                offset = window.pageYOffset;
            }
            mapOuter.style.top = offset + 'px';
            console.log(offset);
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
            Components.links.init();
        }
    );

})();
