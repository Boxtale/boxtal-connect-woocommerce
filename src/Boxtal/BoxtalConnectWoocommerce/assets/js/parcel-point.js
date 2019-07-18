(function () {
    const Components = {};

    Components.parcelPointLinks = {
        trigger: '.bw-select-parcel',
        mapContainer: null,
        map: null,
        markers: [],

        initMap: function() {
            const self = this;
            const mapClose = document.createElement("div");
            mapClose.setAttribute("class", "bw-close");
            mapClose.setAttribute("title", translations.text.closeMap);
            mapClose.addEventListener("click", function() {
                self.closeMap()
            });

            const mapCanvas = document.createElement("div");
            mapCanvas.setAttribute("id", "bw-map-canvas");

            const mapInner = document.createElement("div");
            mapInner.setAttribute("id", "bw-map-container");
            mapInner.appendChild(mapCanvas);

            const mapPPContainer = document.createElement("div");
            mapPPContainer.setAttribute("id", "bw-pp-container");

            const mapOuter = document.createElement("div");
            mapOuter.setAttribute("id", "bw-map-inner");
            mapOuter.appendChild(mapClose);
            mapOuter.appendChild(mapInner);
            mapOuter.appendChild(mapPPContainer);

            self.mapContainer = document.createElement("div");
            self.mapContainer.setAttribute("id", "bw-map");
            self.mapContainer.appendChild(mapOuter);
            document.body.appendChild(self.mapContainer);

            self.map =  new mapboxgl.Map({
                container: 'bw-map-canvas',
                style: mapUrl,
                zoom: 14,
				accessToken: 'whatever'
            });
            self.map.addControl(new mapboxgl.NavigationControl());

            const logoImg = document.createElement("img");
            logoImg.setAttribute("src", mapLogoImageUrl);
            const logoLink = document.createElement("a");
            logoLink.setAttribute("href", mapLogoHrefUrl);
            logoLink.setAttribute("target", "_blank");
            logoLink.appendChild(logoImg);
            const logoContainer = document.createElement("div");
            logoContainer.setAttribute("id", "bw-boxtal-logo");
            logoContainer.appendChild(logoLink);

            const mapTopLeftCorner = document.querySelector(".mapboxgl-ctrl-top-left");
            if (mapTopLeftCorner) {
                mapTopLeftCorner.appendChild(logoContainer);
            }
        },

        init: function () {
            const self = this;
            self.on("body", "click", self.trigger, function() {
                self.mapContainer = document.querySelector("#bw-map");
                if (!self.mapContainer) {
                    self.initMap();
                }
                self.openMap();
                self.getPoints();
            });

            self.on("body", "click", ".bw-parcel-point-button", function() {
                self.selectPoint(this.getAttribute("data-code"),
                                unescape(this.getAttribute("data-name")),
                                this.getAttribute("data-network"),
                                unescape(this.getAttribute("data-street")),
                                unescape(this.getAttribute("data-zipcode")),
                                unescape(this.getAttribute("data-city")),
                                unescape(this.getAttribute("data-country")),
                                unescape(this.getAttribute("data-openinghours")))
                    .then(function(name) {
                        self.initSelectedParcelPoint();
                        const target = document.querySelector(".bw-parcel-name");
                        target.innerHTML = name;
                        self.closeMap();
                    })
                    .catch(function(err) {
                        self.showError(err);
                    });
            });
        },

        openMap: function() {
            this.mapContainer.classList.add("bw-modal-show");
            let offset = window.pageYOffset + (window.innerHeight - this.mapContainer.offsetHeight)/2;
            if (offset < window.pageYOffset) {
                offset = window.pageYOffset;
            }
            this.mapContainer.style.top = offset + 'px';
            this.map.resize();
        },

        closeMap: function() {
            this.mapContainer.classList.remove("bw-modal-show");
            this.clearMarkers();
        },

        initSelectedParcelPoint: function() {
            const selectParcelPoint = document.querySelector(".bw-parcel-client");
            selectParcelPoint.innerHTML = translations.text.selectedParcelPoint + " ";
            const selectParcelPointContent = document.createElement("span");
            selectParcelPointContent.setAttribute("class", "bw-parcel-name");
            selectParcelPoint.appendChild(selectParcelPointContent);
        },

        getPoints: function() {
            const self = this;

            self.getParcelPoints().then(function(parcelPointResponse) {
                self.addParcelPointMarkers(parcelPointResponse['nearbyParcelPoints']);
                self.fillParcelPointPanel(parcelPointResponse['nearbyParcelPoints']);
                self.addRecipientMarker(parcelPointResponse['searchLocation']);
                self.setMapBounds();
            }).catch(function(err) {
                self.showError(err);
            });
        },

        getParcelPoints: function() {
            const self = this;
            return new Promise(function(resolve, reject) {
                const carrier = self.getSelectedCarrier();
                if (!carrier) {
                    reject(translations.error.carrierNotFound);
                }
                const httpRequest = new XMLHttpRequest();
                httpRequest.onreadystatechange = function() {
                    if (httpRequest.readyState === 4) {
						const response = typeof httpRequest.response === 'object' && httpRequest.response !== null ? httpRequest.response : JSON.parse(httpRequest.response);
						if (false === response.success) {
                            reject(response.data.message);
                        } else {
                            resolve(response);
                        }
                    }
                };
                httpRequest.open("POST", ajaxurl);
                httpRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                httpRequest.responseType = "json";
                httpRequest.send("action=bw_get_points&carrier=" + encodeURIComponent(carrier));
            });
        },

        addParcelPointMarkers: function(parcelPoints) {
            for (let i = 0; i < parcelPoints.length; i++) {
                parcelPoints[i].index = i;
                this.addParcelPointMarker(parcelPoints[i]);
            }
        },

        fillSpaces(value, wantedSize) {
            while(value.length < wantedSize) {
                value += ' ';
            }
            return value;
        },

        formatOpeningDays(openingDays) {
            var parsedDays = [];
      
            for (var i = 0; i < openingDays.length; i++) {
                var openingDay = openingDays[i];
      
                if (openingDay.weekday) {
                    var parsedDay = openingDay.weekday[0] + ' ';
                    var openingPeriods = openingDay.openingPeriods;
                    var parsedPeriods = [];
      
                    for (var j = 0; j < openingPeriods.length; j++) {
                        var openingPeriod = openingPeriods[j];
                        var open = openingPeriod.openingTime === undefined ? '' : openingPeriod.openingTime;
                        var close = openingPeriod.closingTime === undefined ? '' : openingPeriod.closingTime;
      
                        if (open !== '' && close !== '') {
                            parsedPeriods.push(open + '-' + close);
                        }
                    }
      
                    while (parsedPeriods.length < 2) {
                      parsedPeriods.push(this.fillSpaces(translations.text.closed, 11));
                    }
      
                    parsedDay += parsedPeriods.join(' ');
      
                    if (i % 2 === 1) {
                      parsedDay = '<span style="background-color: #d8d8d8;">' + parsedDay + '</span>';
                    }
      
                    parsedDays.push(parsedDay);
                }
            }
            
            return '<pre class="bw-parcel-point-schedule">' + parsedDays.join("\n") + '</pre>';
        },

        generateParcelPointTagData: function(parcelpoint) {
            return ' data-code="'    + parcelpoint.code + '" ' +
                    'data-name="'    + escape(parcelpoint.name) + '" ' +
                    'data-network="' + parcelpoint.network + '" ' +
                    'data-zipcode="' + escape(parcelpoint.location.zipCode) + '" ' +
                    'data-country="' + escape(parcelpoint.location.country) + '" ' +
                    'data-city="'    + escape(parcelpoint.location.city) + '" ' +
                    'data-street="'  + escape(parcelpoint.location.street) + '" ' +
                    'data-openinghours="'  + escape(JSON.stringify(parcelpoint.openingDays)) + '" ';
        },

        addParcelPointMarker: function(point) {
            const self = this;
            let info ="<div class='bw-marker-popup'><b>"+point.parcelPoint.name+'</b><br/>'+
                '<a href="#" class="bw-parcel-point-button" ' + this.generateParcelPointTagData(point.parcelPoint) + '><b>'+translations.text.chooseParcelPoint+'</b></a><br/>' +
                point.parcelPoint.location.street+", "+point.parcelPoint.location.zipCode+" "+point.parcelPoint.location.city+"<br/>"+"<b>" + translations.text.openingHours +
                "</b><br/>";

            info += this.formatOpeningDays(point.parcelPoint.openingDays);

            const el = this.getMarkerHtmlElement(point.index + 1);

            const popup = new mapboxgl.Popup({ offset: 25 })
                .setHTML(info);

            const marker = new mapboxgl.Marker({
                element: el,
				anchor: 'bottom'
            })
                .setLngLat(new mapboxgl.LngLat(parseFloat(point.parcelPoint.location.position.longitude), parseFloat(point.parcelPoint.location.position.latitude)))
                .setPopup(popup)
                .addTo(self.map);

            self.markers.push(marker);

            self.addRightColMarkerEvent(marker, point.parcelPoint.code);
        },

        addRightColMarkerEvent: function(marker, code) {
            this.on("body", "click", ".bw-show-info-" + code, function(){
                marker.togglePopup();
            });
        },

        formatHours: function(time) {
            const explode = time.split(':');
            if (3 === explode.length) {
                time = explode[0]+':'+explode[1];
            }
            return time;
        },

        addRecipientMarker: function(location) {
            const self = this;

            const el = document.createElement('div');
            el.className = 'bw-marker-recipient';

            const marker = new mapboxgl.Marker({
                element: el,
				anchor: 'bottom'
            })
                .setLngLat(new mapboxgl.LngLat(parseFloat(location.position.longitude), parseFloat(location.position.latitude)))
                .addTo(self.map);

            self.markers.push(marker);
        },

        setMapBounds: function() {

            let bounds = new mapboxgl.LngLatBounds();

            for (let i = 0; i < this.markers.length; i++) {
                const marker = this.markers[i];
                bounds = bounds.extend(marker.getLngLat());
            }

            this.map.fitBounds(
                bounds,
                {
                    padding: 30,
                    linear: true
                }
            );
        },

        fillParcelPointPanel: function(parcelPoints) {
            let html = '';
            html += '<table><tbody>';
            for (let i = 0; i < parcelPoints.length; i++) {
                const point = parcelPoints[i];
                html += '<tr>';
                html += '<td>' + this.getMarkerHtmlElement(i+1).outerHTML;
                html += '<div class="bw-parcel-point-title"><a class="bw-show-info-' + point.parcelPoint.code + '">' + point.parcelPoint.name + '</a></div><br/>';
                html += point.parcelPoint.location.street + '<br/>';
                html += point.parcelPoint.location.zipCode + ' ' + point.parcelPoint.location.city + '<br/>';
                html += '<a class="bw-parcel-point-button" ' + this.generateParcelPointTagData(point.parcelPoint) + '><b>'+translations.text.chooseParcelPoint+'</b></a>';
                html += '</td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            document.querySelector('#bw-pp-container').innerHTML = html;
        },

        getMarkerHtmlElement: function(index) {
            const el = document.createElement('div');
            el.className = 'bw-marker';
            el.innerHTML = index;
            return el;
        },

        selectPoint: function (code, name, network, address, zipcode, city, country, openingHours) {
            const self = this;
            return new Promise(function(resolve, reject) {
                const carrier = self.getSelectedCarrier();
                if (!carrier) {
                    reject(translations.error.carrierNotFound);
                }
                const setPointRequest = new XMLHttpRequest();
                setPointRequest.onreadystatechange = function() {
                    if (setPointRequest.readyState === 4) {
						const response = typeof setPointRequest.response === 'object' && setPointRequest.response !== null ? setPointRequest.response : JSON.parse(setPointRequest.response);
                        if (false === response.success) {
							reject(response.data.message);
                        } else {
                            resolve(name);
                        }
                    }
                };
                setPointRequest.open("POST", ajaxurl);
                setPointRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                setPointRequest.responseType = "json";
                setPointRequest.send("action=bw_set_point" 
                + "&carrier=" + encodeURIComponent(carrier)
                + "&code=" + encodeURIComponent(code)
                + "&name=" + encodeURIComponent(name)
                + "&address=" + encodeURIComponent(address)
                + "&zipcode=" + encodeURIComponent(zipcode)
                + "&city=" + encodeURIComponent(city)
                + "&country=" + encodeURIComponent(country)
                + "&openingHours=" + encodeURIComponent(openingHours)
                + "&network=" + encodeURIComponent(network));
            });
        },

        clearMarkers: function() {
            for (let i = 0; i < this.markers.length; i++) {
                this.markers[i].remove();
            }
            this.markers = [];
        },

        getSelectedCarrier: function() {
            let carrier;
            const uniqueCarrier = document.querySelector('input[type="hidden"].shipping_method');
            if (uniqueCarrier) {
                carrier = uniqueCarrier.getAttribute('value');
            } else {
                const selectedCarrier = document.querySelector('input.shipping_method:checked');
                carrier = selectedCarrier.getAttribute('value');
            }
            return carrier;
        },

        showError: function(error) {
            this.closeMap();
            alert(error);
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
            Components.parcelPointLinks.init();
        }
    );

})();
