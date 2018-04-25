(function () {
    var Components = {};

    Components.links = {
        trigger: '.bw-select-parcel',
        mapContainer: null,

        init: function () {
            var triggers = document.querySelectorAll(this.trigger);
            var self = this;

            if (triggers.length) {
                self.mapContainer = document.querySelector('#bw-map');
                if (!self.mapContainer) {
                    self.initMap();
                }

                self.on("body", "click", self.trigger, function() {
                    self.showMap();
                });
            }
        },

        initMap: function() {
            var self = this;
            var mapClose = document.createElement("div");
            mapClose.setAttribute("class", "bw-close");
            mapClose.setAttribute("title", "Close map");
            /*mapClose.addEventListener("click", function() {
                self.closeMap()
            });*/

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

            self.mapContainer = document.createElement("div");
            self.mapContainer.setAttribute("id", "bw-map");
            self.mapContainer.appendChild(mapInner);
            document.body.appendChild(self.mapContainer);

            /*
            var options = {
                zoom: 11,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById("bw-map-canvas"), options);
            var bounds = new google.maps.LatLngBounds();
            var infowindow = new google.maps.InfoWindow();
            google.maps.event.trigger(map, 'resize');
            */
        },

        showMap: function() {
            this.mapContainer.classList.add("bw-modal-show");
            var offset = window.pageYOffset + (window.innerHeight - this.mapContainer.offsetHeight)/2;
            if (offset < window.pageYOffset) {
                offset = window.pageYOffset;
            }
            this.mapContainer.style.top = offset + 'px';
        },

        closeMap: function() {
            this.mapContainer.classList.remove("bw-modal-show");
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
