(function () {
    var Components = {};

    Components.links = {
        trigger: '.bw-select-parcel',
        mapContainer: null,
        map: null,
        bounds: null,
        infoWindow: null,

        init: function () {
            var self = this;
            self.on("body", "click", self.trigger, function() {
                self.mapContainer = document.querySelector('#bw-map');
                if (!self.mapContainer) {
                    self.initMap();
                }
                self.showMap();
                self.getPoints();
            });
        },

        initMap: function() {
            var self = this;
            var mapClose = document.createElement("div");
            mapClose.setAttribute("class", "bw-close");
            mapClose.setAttribute("title", "Close map");
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

            var options = {
                zoom: 11,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            self.map = new google.maps.Map(document.getElementById("bw-map-canvas"), options);
            self.bounds = new google.maps.LatLngBounds();
            self.infoWindow = new google.maps.InfoWindow();
            google.maps.event.trigger(self.map, 'resize');

            self.on("body", "click", ".bw-parcel-point-button", function() {
                self.selectPoint(this.getAttribute("data-code"), this.getAttribute("data-name"), this.getAttribute("data-operator"));
            });
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
            this.infoWindow.close();
        },

        getPoints: function() {
            var self = this;
            var carrier;
            var radios = document.getElementsByName("shipping_method[0]");
            for(var i = 0; i < radios.length; i++) {
                if(radios[i].checked) {
                    carrier = radios[i].value;
                }
            }
            if (!carrier) {
                this.closeMap();
                alert('unable to find carrier');
                return;
            }
            var httpRequest = new XMLHttpRequest();
            httpRequest.onreadystatechange = function(data) {
                if (httpRequest.readyState === 4) {
                    if (httpRequest.response.success === false) {
                        self.showParcelPointsError(httpRequest.response.data.message);
                    } else {
                        self.showParcelPoints(httpRequest.response);
                    }
                }
            };
            httpRequest.open("POST", ajaxurl);
            httpRequest.setRequestHeader(
                "Content-Type",
                "application/x-www-form-urlencoded"
            );
            httpRequest.responseType = "json";
            httpRequest.send("action=get_points&carrier=" + encodeURIComponent(carrier) + "&security=" + encodeURIComponent(ajax_nonce));
        },

        showParcelPointsError: function(message) {
            alert(translations['Unable to load parcel points:']+' '+ message);
        },

        showParcelPoints: function(parcelPoints) {
            var self = this;
            if (parcelPoints.length === 0) {
                alert(translations.noPP);
                return;
            }

            for (var i = 0; i < parcelPoints.length; i++) {
                parcelPoints[i].index = i;
                self.addMarker(parcelPoints[i]);
            }

            self.map.setCenter(self.bounds.getCenter());
            google.maps.event.addDomListener(window, "resize", function() {
                self.map.setCenter(self.bounds.getCenter());
            });
            self.map.fitBounds(self.bounds);
            if(self.map.getZoom() > 15){
                self.map.setZoom(15);
            }
            google.maps.event.trigger(self.map, "resize");

            google.maps.event.addListener(self.map, "click", function() {
                self.infoWindow.close();
            });

            self.fillParcelPointPanel(parcelPoints);
        },

        addMarker: function(point) {
            var info ="<div class='bw-marker-popup'><b>"+point.name+'</b><br/>'+
                '<a href="#" class="bw-parcel-point-button" data-code="'+point.code+'" data-name="'+point.name+'" data-operator="'+point.operator+'"><b>'+translations.relayName+'</b></a><br/>' +
                point.address+', '+point.zipcode+' '+point.city+'<br/>'+"<b>" + translations['Opening hours'] +
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
                    info += '<span class="bw-parcel-point-day">'+translations['day_'+day.weekday]+'</span>';
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
            marker.set("content", info);
            this.bounds.extend(marker.getPosition());

            this.addMarkerEvent(marker, point.code);
        },

        formatHours: function(time) {
            var explode = time.split(':');
            if (explode.length == 3) {
                time = explode[0]+':'+explode[1];
            }
            return time;
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
                html += '<a class="bw-parcel-point-button" data-code="'+point.code+'" data-name="'+point.name+'" data-operator="'+point.operator+'"><b>'+translations.relayName+'</b></a>';
                html += '</td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            document.querySelector('#bw-pp-container').innerHTML = html;
        },

        selectPoint: function(code, name, operator) {
            var self = this;
            var carrier;
            var radios = document.getElementsByName("shipping_method[0]");
            for(var i = 0; i < radios.length; i++) {
                if(radios[i].checked) {
                    carrier = radios[i].value;
                }
            }
            if (!carrier) {
                self.closeMap();
                alert('unable to find carrier');
                return;
            }
            var httpRequest = new XMLHttpRequest();
            httpRequest.onreadystatechange = function(data) {
                if (httpRequest.readyState === 4) {
                    if (httpRequest.response.success === false) {
                        console.log(httpRequest.response.data.message);
                    } else {
                        var target = document.querySelector(".bw-parcel-client");
                        if (!target) {
                            self.initSelectedParcelPoint();
                            target = document.querySelector(".bw-parcel-client");
                        }
                        target.innerHTML = name;
                        self.closeMap();
                    }
                }
            };
            httpRequest.open("POST", ajaxurl);
            httpRequest.setRequestHeader(
                "Content-Type",
                "application/x-www-form-urlencoded"
            );
            httpRequest.responseType = "json";
            httpRequest.send("action=set_point&carrier="+ encodeURIComponent(carrier) +"&code=" + encodeURIComponent(code)
                + "&name=" + encodeURIComponent(name) + "&operator=" + encodeURIComponent(operator) + "&security=" + encodeURIComponent(ajax_nonce));
        },

        initSelectedParcelPoint: function() {
            var selectPointLink = document.querySelector(".bw-select-parcel");
            var selectParcelPointContent = document.createElement("span");
            selectParcelPointContent.setAttribute("class", "bw-parcel-client");
            var selectParcelPoint = document.createElement("span");
            selectParcelPoint.innerHTML = translations.selected + " ";
            selectParcelPoint.appendChild(selectParcelPointContent);
            selectPointLink.parentNode.insertBefore(selectParcelPoint, selectPointLink.nextSibling);
            selectPointLink.parentNode.insertBefore(document.createElement("br"), selectPointLink.nextSibling);
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
