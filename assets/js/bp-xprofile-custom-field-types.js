(function ($) {
    'use strict';
    // colors
    if (!Modernizr.inputtypes.color) {
        // No html5 field colorpicker => Calling jscolor.
        $('.bpxcftr-color').addClass('color');
    }

    $('#profile-edit-form').attr('enctype', 'multipart/form-data');
    $('#signup-form').attr('enctype', 'multipart/form-data');
    $('#your-profile').attr('enctype', 'multipart/form-data');

    // Slider.
    $('input.bpxcftr-slider').on('input', function () {
        $('#output-' + $(this).attr('id')).html($(this).val());
    });

    $('.bpxcftr-remove-tag').on('click', function () {

        var $this = $(this);

        $.post(
            ajaxurl,
            {
                action: 'bpxcftr_remove_user_tag',
                nonce: $this.data('nonce'),
                field_id: $this.data('fieldId'),
                tag: $this.data('tag')
            },
            function (resp) {
                if (resp.success) {
                    $this.remove();
                }
            }
        );

    });

    var bpx_leaflet_template = function(str, data) {
        return str.replace(/\{ *([\w_]+) *\}/g, function (str, key) {
          var value = data[key];
    
          if (value === undefined) {
            value = '';
          } else if (typeof value === 'function') {
            value = value(data);
          }    
          return value.trim();
        });
    }; 
    
    var bpx_leaflet_setLocation = function(r,map,marker,$label,$hidden) {
        if (r) {
            var tpl = bpx_leaflet_template("{building} {road} {house_number}, {postcode} {city} {town} {village}, {county}, {country}", r.properties.address);
            tpl = tpl.replace(/\s{2,}/g, " ").replace(/\s,/g,',').replace(/,{2,}/g,',').trim().replace(/^,/g,'').trim();
            var value = JSON.stringify(Object.assign({},r.center,{name : tpl}));
            if (marker) {                 
                  marker
                    .setLatLng(r.center)
                    .setPopupContent(r.html)
                    .openPopup();
            } else {
                marker = L.marker(r.center)
                .bindPopup(r.html)
                .addTo(map)
                .openPopup();                
            }                
            $label.text( tpl );
            $hidden.val(value);
        }
        return marker;
    };

    //Leaflet
    $('.bpx-leaflet-map').each(function () {
        var $divmap = $(this);
        var $label =  $(this).closest("fieldset").find("label");
        var $hidden =  $(this).closest("fieldset").find("input[type=hidden]");
        var $buttonRemove =  $(this).closest("fieldset").find("svg");
        var map;
        var marker;
        var lat = $divmap.data("latitude");
        var lng = $divmap.data("longitude");
        var zoom =  $divmap.data("zoom");
        var value = null;

       
        if ($hidden.val()!==null) {
            // Set the map lat,Lng with the value
            try {
                value = JSON.parse($hidden.val()); 
                if (typeof value.lat !=='undefined' && typeof value.lng !=="undefined") {
                    lat = value.lat;
                    lng = value.lng;            
                }
            }
            catch (e) {
                // Keep default value
            }        
        }  
        map = L.map($divmap[0].id).setView([lat, lng ], zoom);        
        if (value!=null && typeof value.name !=='undefined') {
            marker = new L.marker(value).bindPopup(value.name).addTo(map).openPopup();
        }
        //var geocoder = L.Control.Geocoder.nominatim({htmlTemplate : hmtlTemplateGeocoder});
        var geocoder = L.Control.Geocoder.nominatim();
        var control = L.Control.geocoder({            
            placeholder: 'Search here...',
            geocoder: geocoder
        }).addTo(map);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a>'    
        }).addTo(map);
        // Click on map
        map.on('click', function(e) {
            geocoder.reverse(e.latlng, map.options.crs.scale(map.getZoom()), function(results) {
              var r = results[0];
              marker = bpx_leaflet_setLocation(r,map,marker,$label,$hidden);
            });
        });
        // Search location found
        control.on('markgeocode',function(r) {
            marker = bpx_leaflet_setLocation(r.geocode,map,marker,$label,$hidden);            
        })
        // Remove adresse
        $buttonRemove.on('click',function() {
            $label.text("");
            $hidden.val("");
            if (marker) {
                map.removeLayer(marker);
            }
            marker = null;
        })
    });

})(jQuery);