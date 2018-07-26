(function () {
    const Components = {};

    Components.parcelPointLinks = {
        trigger: '.bw-select-parcel',
        mapContainer: null,
        map: null,
        markers: [],
        bounds: null,

        init: function () {
            const self = this;
            self.on("body", "click", self.trigger, function() {
                self.mapContainer = document.querySelector('#bw-map');
                if (!self.mapContainer) {
                    self.initMap();
                }
                self.bounds = L.latLngBounds();

                self.on("body", "click", ".bw-parcel-point-button", function() {
                    self.selectPoint(this.getAttribute("data-code"), this.getAttribute("data-label"), this.getAttribute("data-operator"))
                        .then(function(label) {
                            self.initSelectedParcelPoint();
                             const target = document.querySelector(".bw-parcel-name");
                            target.innerHTML = label;
                            self.closeMap();
                        })
                        .catch(function(err) {
                            self.showError(err);
                        });
                });
                self.openMap();
                self.getPoints();
            });
        },

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

            const mapContainer = document.createElement("div");
            mapContainer.setAttribute("id", "bw-map-container");
            mapContainer.appendChild(mapCanvas);

            const mapPPContainer = document.createElement("div");
            mapPPContainer.setAttribute("id", "bw-pp-container");

            const mapInner = document.createElement("div");
            mapInner.setAttribute("id", "bw-map-inner");
            mapInner.appendChild(mapClose);
            mapInner.appendChild(mapContainer);
            mapInner.appendChild(mapPPContainer);

            self.mapContainer = document.createElement("div");
            self.mapContainer.setAttribute("id", "bw-map");
            self.mapContainer.appendChild(mapInner);
            document.body.appendChild(self.mapContainer);

            self.map = L.map('bw-map-canvas', {
                crs: L.CRS.EPSG3857
            });

            L.mapboxGL({
                style: 'i',
                accessToken: 'whatever'
            }).addTo(self.map);
        },

        openMap: function() {
            this.mapContainer.classList.add("bw-modal-show");
            let offset = window.pageYOffset + (window.innerHeight - this.mapContainer.offsetHeight)/2;
            if (offset < window.pageYOffset) {
                offset = window.pageYOffset;
            }
            this.mapContainer.style.top = offset + 'px';
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
                self.addParcelPointMarkers(parcelPointResponse['parcelPoints']);
                self.fillParcelPointPanel(parcelPointResponse['parcelPoints']);
                self.addRecipientMarker(parcelPointResponse['origin']);
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
                        if (httpRequest.response.success === false) {
                            reject(httpRequest.response.data.message);
                        } else {
                            resolve(httpRequest.response);
                        }
                    }
                };
                httpRequest.open("POST", ajaxurl);
                httpRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                httpRequest.responseType = "json";
                httpRequest.send("action=get_points&carrier=" + encodeURIComponent(carrier));
            });
        },

        addParcelPointMarkers: function(parcelPoints) {
            for (let i = 0; i < parcelPoints.length; i++) {
                parcelPoints[i].index = i;
                this.addParcelPointMarker(parcelPoints[i]);
            }
        },

        addParcelPointMarker: function(point) {
            let info ="<div class='bw-marker-popup'><b>"+point.label+'</b><br/>'+
                '<a href="#" class="bw-parcel-point-button" data-code="'+point.code+'" data-label="'+point.label+'" data-operator="'+point.operator+'"><b>'+translations.text.chooseParcelPoint+'</b></a><br/>' +
                point.address.street+", "+point.address.postcode+" "+point.address.city+"<br/>"+"<b>" + translations.text.openingHours +
                "</b><br/>"+'<div class="bw-parcel-point-schedule">';

            for (let i = 0, l = point.schedule.length; i < l; i++) {
                const day = point.schedule[i];

                info += '<span class="bw-parcel-point-day">'+translations.day[day.weekday]+'</span>';

                for (let j = 0, t = day.timePeriods.length; j < t; j++) {
                    const timePeriod = day.timePeriods[j];
                    info += this.formatHours(timePeriod.openingTime) +'-'+this.formatHours(timePeriod.closingTime);
                }
                info += '<br/>';
            }
            info += '</div>';

            const popup = L.popup()
                .setContent(info);

            const marker = L.marker(
                [parseFloat(point.coordinates.latitude), parseFloat(point.coordinates.longitude)],
                {
                    icon: L.icon({
                        iconUrl: imgDir + "markers/"+(point.index + 1)+".png",
                        iconSize: [26, 37],
                        iconAnchor: [13, 37],
                        popupAnchor: [0, -37],
                    }),
                    riseOnHover: true,
                    title: point.label
                }
            ).bindPopup(popup).addTo(this.map);

            this.markers.push(marker);

            this.addRightColMarkerEvent(marker, point.code);

            this.bounds.extend(marker.getLatLng());
        },

        addRightColMarkerEvent: function(marker, code) {
            this.on("body", "click", ".bw-show-info-" + code, function(){
                marker.openPopup();
            });
        },

        formatHours: function(time) {
            const explode = time.split(':');
            if (explode.length == 3) {
                time = explode[0]+':'+explode[1];
            }
            return time;
        },

        addRecipientMarker: function(latlon) {
            const self = this;

            const marker = L.marker(
                [parseFloat(latlon.latitude), parseFloat(latlon.longitude)],
                //[47.38061, 8.54736],
                {
                    icon: L.icon({
                        iconUrl: imgDir + "marker-recipient.png",
                        iconSize: [26, 37],
                        iconAnchor: [13, 37]
                    })
                }
            ).addTo(self.map);

            self.markers.push(marker);

            self.map.setView([parseFloat(latlon.latitude), parseFloat(latlon.longitude)], 11);
            self.bounds.extend(marker.getLatLng());
        },

        setMapBounds: function() {
            if(this.map.getZoom() > 15){
                this.map.setZoom(15);
            }
            this.map.fitBounds(this.bounds);
        },

        fillParcelPointPanel: function(parcelPoints) {
            let html = '';
            html += '<table><tbody>';
            for (let i = 0; i < parcelPoints.length; i++) {
                const point = parcelPoints[i];
                html += '<tr>';
                html += '<td><img src="' + imgDir + 'markers/'+(i+1)+'.png" />';
                html += '<div class="bw-parcel-point-title"><a class="bw-show-info-' + point.code + '">' + point.label + '</a></div><br/>';
                html += point.address.street + '<br/>';
                html += point.address.postcode + ' ' + point.address.city + '<br/>';
                html += '<a class="bw-parcel-point-button" data-code="'+point.code+'" data-label="'+point.label+'" data-operator="'+point.operator+'"><b>'+translations.text.chooseParcelPoint+'</b></a>';
                html += '</td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            document.querySelector('#bw-pp-container').innerHTML = html;
        },

        selectPoint: function(code, label, operator) {
            const self = this;
            return new Promise(function(resolve, reject) {
                const carrier = self.getSelectedCarrier();
                if (!carrier) {
                    reject(translations.error.carrierNotFound);
                }
                const setPointRequest = new XMLHttpRequest();
                setPointRequest.onreadystatechange = function() {
                    if (setPointRequest.readyState === 4) {
                        if (setPointRequest.response.success === false) {
                            reject(setPointRequest.response.data.message);
                        } else {
                            resolve(label);
                        }
                    }
                };
                setPointRequest.open("POST", ajaxurl);
                setPointRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                setPointRequest.responseType = "json";
                setPointRequest.send("action=set_point&carrier="+ encodeURIComponent(carrier) +"&code=" + encodeURIComponent(code)
                    + "&label=" + encodeURIComponent(label) + "&operator=" + encodeURIComponent(operator));
            });
        },

        clearMarkers: function() {
            for (let i = 0; i < this.markers.length; i++) {
                this.markers[i].remove();
            }
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
