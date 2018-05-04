(function () {
    var Components = {};

    Components.parcelPointLinks = {
        trigger: '.bw-select-parcel',
        mapContainer: null,
        map: null,
        infoWindow: null,
        markers: [],
        bounds: null,

        init: function () {
            var self = this;
            self.on("body", "click", self.trigger, function() {
                self.mapContainer = document.querySelector('#bw-map');
                if (!self.mapContainer) {
                    self.initMap();
                }

                var options = {
                    zoom: 11,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                self.map = new google.maps.Map(document.getElementById("bw-map-canvas"), options);
                self.infoWindow = new google.maps.InfoWindow();
                self.bounds = new google.maps.LatLngBounds();
                google.maps.event.trigger(self.map, 'resize');

                self.on("body", "click", ".bw-parcel-point-button", function() {
                    self.selectPoint(this.getAttribute("data-code"), this.getAttribute("data-name"), this.getAttribute("data-operator"))
                        .then(function(name) {
                            var target = document.querySelector(".bw-parcel-client");
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


                google.maps.event.addListener(self.map, "click", function() {
                    self.infoWindow.close();
                });

                self.openMap();
                self.getPoints();
            });
        },

        initMap: function() {
            var self = this;
            var mapClose = document.createElement("div");
            mapClose.setAttribute("class", "bw-close");
            mapClose.setAttribute("title", translations.text.closeMap);
            mapClose.addEventListener("click", function() {
                self.closeMap()
            });

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
        },

        openMap: function() {
            this.mapContainer.classList.add("bw-modal-show");
            var offset = window.pageYOffset + (window.innerHeight - this.mapContainer.offsetHeight)/2;
            if (offset < window.pageYOffset) {
                offset = window.pageYOffset;
            }
            this.mapContainer.style.top = offset + 'px';
        },

        closeMap: function() {
            this.mapContainer.classList.remove("bw-modal-show");
            this.infoWindow.close();
            this.clearMarkers();
        },

        initSelectedParcelPoint: function() {
            var selectPointLink = document.querySelector(".bw-select-parcel");
            var selectParcelPointContent = document.createElement("span");
            selectParcelPointContent.setAttribute("class", "bw-parcel-client");
            var selectParcelPoint = document.createElement("span");
            selectParcelPoint.innerHTML = translations.text.selectedParcelPoint + " ";
            selectParcelPoint.appendChild(selectParcelPointContent);
            selectPointLink.parentNode.insertBefore(selectParcelPoint, selectPointLink.nextSibling);
            selectPointLink.parentNode.insertBefore(document.createElement("br"), selectPointLink.nextSibling);
        },

        getPoints: function() {
            var self = this;

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
            var self = this;
            return new Promise(function(resolve, reject) {
                var carrier = self.getSelectedCarrier();
                if (!carrier) {
                    reject(translations.error.carrierNotFound);
                }
                var httpRequest = new XMLHttpRequest();
                httpRequest.onreadystatechange = function(data) {
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
                httpRequest.send("action=get_points&carrier=" + encodeURIComponent(carrier) + "&security=" + encodeURIComponent(ajaxNonce));
            });
        },

        addParcelPointMarkers: function(parcelPoints) {
            var self = this;
            for (var i = 0; i < parcelPoints.length; i++) {
                parcelPoints[i].index = i;
                self.addParcelPointMarker(parcelPoints[i]);
            }
        },

        addParcelPointMarker: function(point) {
            var info ="<div class='bw-marker-popup'><b>"+point.name+'</b><br/>'+
                '<a href="#" class="bw-parcel-point-button" data-code="'+point.code+'" data-name="'+point.name+'" data-operator="'+point.operator+'"><b>'+translations.text.chooseParcelPoint+'</b></a><br/>' +
                point.address+', '+point.zipcode+' '+point.city+'<br/>'+"<b>" + translations.text.openingHours +
                "</b><br/>"+'<div class="bw-parcel-point-schedule">';

            for (var i = 0, l = point.schedule.length; i < l; i++) {
                var day = point.schedule[i];

                if ((typeof(day.firstPeriodOpeningTime)==="undefined") || (typeof(day.firstPeriodClosingTime)==="undefined") ||
                    (typeof(day.secondPeriodOpeningTime)==="undefined") || (typeof(day.secondPeriodClosingTime)==="undefined")) {
                    continue;
                }

                var am = day.firstPeriodOpeningTime !== null && day.firstPeriodClosingTime !== null;
                var pm = day.secondPeriodOpeningTime !== null && day.secondPeriodClosingTime !== null;
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

            var bwMarker = {
                url: imgDir + "markers/"+(point.index + 1)+".png",
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(13, 37),
                scaledSize: new google.maps.Size(26, 37)
            };

            var latLng = new google.maps.LatLng(parseFloat(point.latitude),parseFloat(point.longitude));

            var marker = new google.maps.Marker({
                map: this.map,
                position: latLng,
                icon: bwMarker,
                title: point.name
            });
            this.markers.push(marker);
            marker.set("content", info);
            this.bounds.extend(marker.getPosition());

            this.addMarkerEvent(marker, point.code);
        },

        addMarkerEvent: function(marker, code) {
            var self = this;
            google.maps.event.addListener(marker, "click", function() {
                self.infoWindow.close();
                self.infoWindow.setContent(this.get("content"));
                self.infoWindow.open(self.map,this);
            });
            self.on("body", "click", ".bw-show-info-" + code, function(){
                self.infoWindow.close();
                self.infoWindow.setContent(marker.get("content"));
                self.infoWindow.open(self.map,marker);
            });
        },

        formatHours: function(time) {
            var explode = time.split(':');
            if (explode.length == 3) {
                time = explode[0]+':'+explode[1];
            }
            return time;
        },

        getRecipient: function() {
            return new Promise(function(resolve, reject) {
                var recipientAddressRequest = new XMLHttpRequest();
                recipientAddressRequest.onreadystatechange = function(data) {
                    if (recipientAddressRequest.readyState === 4) {
                        if (recipientAddressRequest.response.success === false) {
                            reject(recipientAddressRequest.response.data.message);
                        } else {
                            var geocoderRequest = new XMLHttpRequest();
                            geocoderRequest.onreadystatechange = function(data) {
                                if (geocoderRequest.readyState === 4) {
                                    if (geocoderRequest.response.status === "OK") {
                                        resolve(geocoderRequest.response.results[0]);
                                    } else {
                                        switch (geocoderRequest.response.status) {
                                            case "OVER_QUERY_LIMIT":
                                                reject(translations.error.googleQuotaExceeded);
                                                break;

                                            default:
                                                reject(translations.error.addressNotFound);
                                                break;
                                        }
                                    }
                                }
                            };
                            geocoderRequest.open("GET", "https://maps.googleapis.com/maps/api/geocode/json?address=" + encodeURIComponent(recipientAddressRequest.response) + "&key=" + googleKey);
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
                recipientAddressRequest.send("action=get_recipient_address&security=" + encodeURIComponent(ajaxNonce));
            });
        },

        addRecipientMarker: function(point) {
            var self = this;

            var bwMarkerRecipient = {
                url: imgDir + "marker-recipient.png",
                anchor: new google.maps.Point(13, 37),
                scaledSize: new google.maps.Size(26, 37)
            };

            var latLng = new google.maps.LatLng(parseFloat(point.geometry.location.lat),parseFloat(point.geometry.location.lng));

            var marker = new google.maps.Marker({
                map: this.map,
                position: latLng,
                icon: bwMarkerRecipient,
                title: translations.text.yourAddress
            });
            self.markers.push(marker);
            self.bounds.extend(marker.getPosition());

            self.map.setCenter(marker.getPosition());
            google.maps.event.addDomListener(window, "resize", function() {
                self.map.setCenter(marker.getPosition());
            });
        },

        setMapBounds: function() {
            if(this.map.getZoom() > 15){
                this.map.setZoom(15);
            }
            this.map.fitBounds(this.bounds);
            google.maps.event.trigger(this.map, "resize");
        },

        fillParcelPointPanel: function(parcelPoints) {
            var html = '';
            html += '<table><tbody>';
            for (var i = 0; i < parcelPoints.length; i++) {
                var point = parcelPoints[i];
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
            var self = this;
            return new Promise(function(resolve, reject) {
                var carrier = self.getSelectedCarrier();
                if (!carrier) {
                    reject(translations.error.carrierNotFound);
                }
                var setPointRequest = new XMLHttpRequest();
                setPointRequest.onreadystatechange = function(data) {
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
                    + "&name=" + encodeURIComponent(name) + "&operator=" + encodeURIComponent(operator) + "&security=" + encodeURIComponent(ajaxNonce));
            });
        },

        clearMarkers: function() {
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
        },

        getSelectedCarrier: function() {
            var carrier;
            if (jQuery('input[type="hidden"].shipping_method').length === 1) {
                carrier = jQuery('input[type="hidden"].shipping_method').attr('value');
            } else {
                carrier = jQuery('input.shipping_method:checked').attr('value');
            }
            return carrier;
        },

        showError: function(error) {
            this.closeMap();
            alert(error);
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
            Components.parcelPointLinks.init();
        }
    );

})();
