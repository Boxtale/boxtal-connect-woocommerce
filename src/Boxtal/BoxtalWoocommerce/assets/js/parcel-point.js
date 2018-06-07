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
                    self.selectPoint(this.getAttribute("data-code"), this.getAttribute("data-name"), this.getAttribute("data-operator"))
                        .then(function(name) {
                            let target = document.querySelector(".bw-parcel-client");
                            if (!target) {
                                self.initSelectedParcelPoint();
                                target = document.querySelector(".bw-parcel-client");
                            }
                            target.innerHTML = name;
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

            self.map = L.map("bw-map-canvas");
            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
                attributionControl: false,
                maxZoom: 18,
                id: 'mapbox.streets',
                accessToken: 'pk.eyJ1IjoiYXJuYXVkZHV0YW50IiwiYSI6ImNqaTQ4a29lZDAwbjYzd3FnY25mN2ZiMngifQ.zSJIBlY4vIBREcS5MJY47w'
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
            const selectPointLink = document.querySelector(".bw-select-parcel");
            const selectParcelPointContent = document.createElement("span");
            selectParcelPointContent.setAttribute("class", "bw-parcel-client");
            const selectParcelPoint = document.createElement("span");
            selectParcelPoint.innerHTML = translations.text.selectedParcelPoint + " ";
            selectParcelPoint.appendChild(selectParcelPointContent);
            selectPointLink.parentNode.insertBefore(selectParcelPoint, selectPointLink.nextSibling);
            selectPointLink.parentNode.insertBefore(document.createElement("br"), selectPointLink.nextSibling);
        },

        getPoints: function() {
            const self = this;

            Promise.all([self.getParcelPoints(), self.getRecipient()]).then(function(valArray) {
                self.addParcelPointMarkers(valArray[0]);
                self.fillParcelPointPanel(valArray[0]);
                self.addRecipientMarker(valArray[1]);
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
            let info ="<div class='bw-marker-popup'><b>"+point.name+'</b><br/>'+
                '<a href="#" class="bw-parcel-point-button" data-code="'+point.code+'" data-name="'+point.name+'" data-operator="'+point.operator+'"><b>'+translations.text.chooseParcelPoint+'</b></a><br/>' +
                point.address+", "+point.zipcode+" "+point.city+"<br/>"+"<b>" + translations.text.openingHours +
                "</b><br/>"+'<div class="bw-parcel-point-schedule">';

            for (let i = 0, l = point.schedule.length; i < l; i++) {
                const day = point.schedule[i];

                if ((typeof(day.firstPeriodOpeningTime)==="undefined") || (typeof(day.firstPeriodClosingTime)==="undefined") ||
                    (typeof(day.secondPeriodOpeningTime)==="undefined") || (typeof(day.secondPeriodClosingTime)==="undefined")) {
                    continue;
                }

                const am = day.firstPeriodOpeningTime !== null && day.firstPeriodClosingTime !== null;
                const pm = day.secondPeriodOpeningTime !== null && day.secondPeriodClosingTime !== null;
                if (am || pm) {
                    info += '<span class="bw-parcel-point-day">'+translations.day[day.weekday]+'</span>';
                    if (am) {
                        info += this.formatHours(day.firstPeriodOpeningTime) +'-'+this.formatHours(day.firstPeriodClosingTime);
                        if (pm) {
                            info += ', '+this.formatHours(day.secondPeriodOpeningTime) +'-'+this.formatHours(day.secondPeriodClosingTime);
                        }
                    } else {
                        info += this.formatHours(day.secondPeriodOpeningTime) +'-'+this.formatHours(day.secondPeriodClosingTime);
                    }
                    info += '<br/>';
                }
            }
            info += '</div>';

            const popup = L.popup()
                .setContent(info);

            const marker = L.marker(
                [parseFloat(point.latitude), parseFloat(point.longitude)],
                {
                    icon: L.icon({
                        iconUrl: imgDir + "markers/"+(point.index + 1)+".png",
                        iconSize: [26, 37],
                        iconAnchor: [13, 37],
                        popupAnchor: [0, -37],
                    }),
                    riseOnHover: true,
                    title: point.name
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

        getRecipient: function() {
            return new Promise(function(resolve, reject) {
                const recipientAddressRequest = new XMLHttpRequest();
                recipientAddressRequest.onreadystatechange = function() {
                    if (recipientAddressRequest.readyState === 4) {
                        if (recipientAddressRequest.response.success === false) {
                            reject(recipientAddressRequest.response.data.message);
                        } else {
                            const geocoderRequest = new XMLHttpRequest();
                            geocoderRequest.onreadystatechange = function() {
                                if (geocoderRequest.readyState === 4) {
                                    if (geocoderRequest.status === 200) {
                                        if (geocoderRequest.response[0]) {
                                            resolve(geocoderRequest.response[0]);
                                        } else {
                                            reject(translations.error.addressNotFound);
                                        }

                                    } else {
                                        reject(translations.error.mapServerError);
                                    }
                                }
                            };
                            geocoderRequest.open("GET", " https://nominatim.openstreetmap.org/search?format=json&" + Object.entries(recipientAddressRequest.response).map(e => e.join('=')).join('&'));
                            geocoderRequest.setRequestHeader(
                                "Content-Type",
                                "application/x-www-form-urlencoded"
                            );
                            geocoderRequest.responseType = "json";
                            geocoderRequest.send(null);
                        }
                    }
                };
                recipientAddressRequest.open("POST", ajaxurl);
                recipientAddressRequest.setRequestHeader(
                    "Content-Type",
                    "application/x-www-form-urlencoded"
                );
                recipientAddressRequest.responseType = "json";
                recipientAddressRequest.send("action=get_recipient_address");
            });
        },

        addRecipientMarker: function(point) {
            const self = this;

            const marker = L.marker(
                [parseFloat(point.lat), parseFloat(point.lon)],
                {
                    icon: L.icon({
                        iconUrl: imgDir + "marker-recipient.png",
                        iconSize: [26, 37],
                        iconAnchor: [13, 37]
                    })
                }
            ).addTo(self.map);

            self.markers.push(marker);

            self.map.setView([parseFloat(point.lat), parseFloat(point.lon)], 11);
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
                html += '<div class="bw-parcel-point-title"><a class="bw-show-info-' + point.code + '">' + point.name + '</a></div><br/>';
                html += point.address + '<br/>';
                html += point.zipcode + ' ' + point.city + '<br/>';
                html += '<a class="bw-parcel-point-button" data-code="'+point.code+'" data-name="'+point.name+'" data-operator="'+point.operator+'"><b>'+translations.text.chooseParcelPoint+'</b></a>';
                html += '</td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            document.querySelector('#bw-pp-container').innerHTML = html;
        },

        selectPoint: function(code, name, operator) {
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
                setPointRequest.send("action=set_point&carrier="+ encodeURIComponent(carrier) +"&code=" + encodeURIComponent(code)
                    + "&name=" + encodeURIComponent(name) + "&operator=" + encodeURIComponent(operator));
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
