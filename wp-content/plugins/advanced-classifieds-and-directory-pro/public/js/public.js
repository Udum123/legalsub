(function( $ ) {
	'use strict';	
	
	/**
     * Initialize the range slider.
     *
	 * @since 2.0.0
	 */
	function acadp_initialize_range_slider( $el ) {
		var $bubble = $el.find( '.acadp-range-value' );
		var $range  = $el.find( '.acadp-range-input' );

		var min = parseInt( $range.prop( 'min' ) );
		var max = parseInt( $range.prop( 'max' ) );

		$range.on( 'input', function() {
			var value = Number( ( $range.val() - min ) * 100 / ( max - min ) );
			var position = 10 - ( value * 0.2 );

			$bubble.html( '<span>' + $range.val() + '</span>' ).css( 'left', 'calc(' + value + '% + (' + position + 'px))' );
		}).trigger( 'input' );
	}

	/**
     * Initialize the date/time picker.
     *
	 * @since 1.8.6
	 */
	function acadp_initialize_datetime_picker( $el ) {
		if ( $.fn.flatpickr ) {
			flatpickr.l10ns.default.rangeSeparator = ' ' + acadp.search_form_daterange_separator + ' ';

			var config = {
				allowInput: true
			};

			if ( $el.hasClass( 'acadp-has-daterange' ) ) {
				config.mode = 'range';
			}

			if ( $el.hasClass( 'acadp-datetime-picker' ) ) {
				config.enableTime    = true;				
				config.enableSeconds = true;
				config.time_24hr     = true;
			}

			$el.flatpickr( config );
		}
	}

	/**
     * Initialize the Video.
     *
	 * @since 1.9.0
	 */
	 function acadp_initialize_video() {
		$( '.acadp-video' ).each(function() {
			$( this ).attr( 'src', $( this ).data( 'src' ) );
		});
	}

	/**
     * Initialize the Map.
     *
	 * @since 1.9.0
	 */
	function acadp_initialize_map() {
		$( '.acadp-map:not(.acadp-map-loaded)' ).each(function() {		
			if ( 'osm' == acadp.map_service ) {
				acadp_osm_render_map( $( this ) );
			} else {
				acadp_google_render_map( $( this ) );
			}
		});
	}

	/**
     * [Map: OpenStreetMap] Render a Map onto the selected jQuery element.
     *
	 * @since 1.8.0
	 */
	function acadp_osm_render_map( $el ) {
		$el.addClass( 'acadp-map-loaded' );

		// Vars
		var $markers = $el.find( '.marker' );
		var type = $el.data( 'type' );
		var lat = 0;
		var lng = 0;
		var popup_content = '';

		if ( $markers.length > 0 ) {
			var $marker = $markers.eq(0);

			lat = $marker.data( 'latitude' );
			lng = $marker.data( 'longitude' );
			popup_content = $marker.html();
		}	

		// Set a custom image path
		L.Icon.Default.prototype.options.imagePath = acadp.plugin_url + 'vendor/leaflet/images/';

		// Creating map options
		var map_options = {
			center: [ lat, lng ],
			zoom: acadp.zoom_level
		}

		// Creating a map object        	
		var map = new L.map( $el[0], map_options );	

		// Creating a Layer object
		var layer = new L.TileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		});

		// Adding layer to the map
		map.addLayer( layer );

		if ( 'markerclusterer' == type ) {
			// Creating Marker Options
			var marker_options = {
				clickable: true,
				draggable: false
			}

			// Creating Markers		
			var markers = L.markerClusterGroup();

			$markers.each(function() {	
				var lat = $( this ).data( 'latitude' );
				var lng = $( this ).data( 'longitude' );

				// Creating a Marker
				var marker = L.marker( [ lat, lng ], marker_options );

				// Adding popup to the marker
				var content = $( this ).html();
				if ( content ) {
					marker.bindPopup( content, { maxHeight: 200 } );
				}

				markers.addLayer( marker );
			});

			map.addLayer( markers );

			// Try HTML5 geolocation
			if ( acadp.snap_to_user_location && navigator.geolocation ) {
				navigator.geolocation.getCurrentPosition(function( position ) {
				  map.panTo( new L.LatLng( position.coords.latitude, position.coords.longitude ) );
				}, function() {
					// Browser doesn't support Geolocation
					map.fitBounds(markers.getBounds(), {
						padding: [50, 50]
					});
				});
			} else {
				map.fitBounds(markers.getBounds(), {
					padding: [50, 50]
				});	
			}
		} else {
			// Creating Marker Options
			var marker_options = {
				clickable: true,
				draggable: ( 'form' == type ? true : false )
			}

			// Creating a Marker
			var marker = L.marker( [ lat, lng ], marker_options );

			// Adding popup to the marker
			if ( popup_content ) {
				marker.bindPopup( popup_content, { maxHeight: 200 } );
			}

			// Adding marker to the map
			marker.addTo( map );

			// Is the map editable?
			if ( 'form' == type ) {				
				// Update latitude and longitude values in the form when marker is moved
				marker.on( 'dragend', function( event ) {
					var position = event.target.getLatLng();

					map.panTo( new L.LatLng( position.lat, position.lng ) );
					acadp_update_latlng( position.lat, position.lng );
				});

				// Update map when contact details fields are updated in the custom post type "acadp_listings"
				$ ( '#acadp-contact-details' ).on( 'blur', '.acadp-map-field', function() {
					var query = [];					

					var locations = [];

					var def_location = $( '#acadp-default-location' ).val();
					if ( def_location ) {
						locations.push( def_location );
					}

					$( 'select', '#acadp-contact-details' ).each(function() {
						var _default  = $( this ).find( 'option:first' ).text();
						var _selected = $( this ).find( 'option:selected' ).text();
						if ( _selected != _default ) locations.push( _selected );
					});

					if ( locations.length > 0 ) {
						locations.reverse();
						query.push( locations.join() );
					}				

					var zipcode = $( '#acadp-zipcode' ).val();
					if ( zipcode ) {
						query.push( zipcode );
					}

					if ( 0 == query.length ) {
						var address = $( '#acadp-address' ).val();
						if ( address ) {
							address = address.replace( /(?:\r\n|\r|\n)/g, ',' );
							address = address.replace( ',,', ',' );
							address = address.replace( ', ', ',' );

							query.push( address );
						}
					}

					query = query.filter( function( v ) { return v !== '' } );
					query = query.join();
					
					$.get( 'https://nominatim.openstreetmap.org/search.php?q=' + encodeURIComponent( query ) +'&polygon_geojson=1&format=jsonv2', function( response ) {
						if ( response.length > 0 ) {
							var latlng = new L.LatLng( response[0].lat, response[0].lon );

							marker.setLatLng( latlng );
							map.panTo( latlng );
							acadp_update_latlng( response[0].lat, response[0].lon );
						}
					}, 'json');
				});

				if ( acadp_is_empty( $( '#acadp-latitude' ).val() ) ) {
					$( '#acadp-address' ).trigger( 'blur' );
				}
			}
		}	
	};

	/**
     *  [Map: Google] Render a Google Map onto the selected jQuery element.
     *
	 *  @since 1.0.0
	 */
	function acadp_google_render_map( $el ) {		
		$el.addClass( 'acadp-map-loaded' );

		// var
		var $markers = $el.find( '.marker' );

		// vars
		var args = {
			zoom: parseInt( acadp.zoom_level ),
			center: new google.maps.LatLng( 0, 0 ),
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			zoomControl: true,
			scrollwheel: false
		};

		// create map	        	
		var map = new google.maps.Map( $el[0], args );

		// add a markers reference
		map.markers = [];
		
		// set map type
		map.type = $el.data( 'type' );
	
		// add markers
		$markers.each(function() {							   
			acadp_google_add_marker( $( this ), map );			
		});

		// center map
		if ( map.type == 'markerclusterer' ) {
			// Try HTML5 geolocation
			if ( acadp.snap_to_user_location && navigator.geolocation ) {
				navigator.geolocation.getCurrentPosition(function( position ) {
				  var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				  };

				  map.setCenter( pos );
				}, function() {
					acadp_google_center_map( map );
				});
			} else {
				// Browser doesn't support Geolocation
				acadp_google_center_map( map );
			}
		} else {
			acadp_google_center_map( map );
		}
		
		// update map when contact details fields are updated in the custom post type 'acadp_listings'
		if ( 'form' == map.type ) {			
			var geoCoder = new google.maps.Geocoder();		
			
			$( '#acadp-contact-details' ).on( 'blur', '.acadp-map-field', function() {
				var query = [];
				
				var address = $( '#acadp-address' ).val();
				if ( address ) {
					address = address.replace( /(?:\r\n|\r|\n)/g, ',' );
					address = address.replace( ',,', ',' );
					address = address.replace( ', ', ',' );

					query.push( address );
				}

				var locations = [];

				var def_location = $( '#acadp-default-location' ).val();
				if ( def_location ) {
					locations.push( def_location );
				}

				$( 'select', '#acadp-contact-details' ).each(function() {
					var _default  = $( this ).find( 'option:first' ).text();
					var _selected = $( this ).find( 'option:selected' ).text();
					if ( _selected != _default ) locations.push( _selected );
				});

				if ( locations.length > 0 ) {
					locations.reverse();
					query.push( locations.join() );
				}				

				var zipcode = $( '#acadp-zipcode' ).val();
				if ( zipcode ) {
					query.push( zipcode );
				}

				query = query.filter( function( v ) { return v !== '' } );
				query = query.join();
			
				geoCoder.geocode({ 'address': query }, function( results, status ) {															
   					if ( status == google.maps.GeocoderStatus.OK ) {						
						var point = results[0].geometry.location;									
						map.markers[0].setPosition( point );
						acadp_google_center_map( map );
						acadp_update_latlng( point.lat(), point.lng() );						
    				};				
   				});
			});
				
			if ( acadp_is_empty( $( '#acadp-latitude' ).val() ) ) {
				$( '#acadp-address' ).trigger( 'blur' );
			}						
		} else if ( map.type == 'markerclusterer' ) {			
			var markerCluster = new MarkerClusterer( map, map.markers, { imagePath: acadp.plugin_url + 'vendor/markerclusterer/images/m' } );			
		};
	};	
	
	/**
	 *  [Map: Google] Add a marker to the selected Map.
	 *
	 *  @since 1.0.0
	 */
	function acadp_google_add_marker( $marker, map ) {
		// var
		var latlng = new google.maps.LatLng( $marker.data( 'latitude' ), $marker.data( 'longitude' ) );

		// check to see if any of the existing markers match the latlng of the new marker
		if ( map.markers.length ) {
    		for ( var i = 0; i < map.markers.length; i++ ) {
        		var existing_marker = map.markers[i];
        		var pos = existing_marker.getPosition();

        		// if a marker already exists in the same position as this marker
        		if ( latlng.equals( pos ) ) {
            		// update the position of the coincident marker by applying a small multipler to its coordinates
            		var latitude  = latlng.lat() + ( Math.random() - .5 ) / 1500; // * (Math.random() * (max - min) + min);
            		var longitude = latlng.lng() + ( Math.random() - .5 ) / 1500; // * (Math.random() * (max - min) + min);
            		latlng = new google.maps.LatLng( latitude, longitude );
        		}
    		}
		}
		
		// create marker
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			draggable: ( 'form' == map.type ) ? true : false
		});

		// add to array
		map.markers.push( marker );
	
		// if marker contains HTML, add it to an infoWindow
		if ( $marker.html() ) {
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content: $marker.html()
			});

			// show info window when marker is clicked
			google.maps.event.addListener( marker, 'click', function() {	
				infowindow.open( map, marker );
			});
		};
		
		// update latitude and longitude values in the form when marker is moved
		if ( 'form' == map.type ) {
			google.maps.event.addListener( marker, 'dragend', function() {																  
  				var point = marker.getPosition();
				map.panTo( point );
				acadp_update_latlng( point.lat(), point.lng() );			
			});	
		};
	};	

	/**
	 *  [Map: Google] Center the map, showing all markers attached to this map.
     *
	 *  @since 1.0.0
	 */
	function acadp_google_center_map( map ) {
		// vars
		var bounds = new google.maps.LatLngBounds();

		// loop through all markers and create bounds
		$.each( map.markers, function( i, marker ) {
			var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
			bounds.extend( latlng );
		});

		// only 1 marker?
		if ( 1 == map.markers.length ) {			
			// set center of map
	    	map.setCenter( bounds.getCenter() );
	    	map.setZoom( parseInt( acadp.zoom_level ) );			
		} else {			
			// fit to bounds
			map.fitBounds( bounds );			
		};
	};
	
	/**
	 *  Set the latitude and longitude values from the address.
     *
	 *  @since 1.0.0
	 */
	function acadp_update_latlng( lat, lng ) {		
		$( '#acadp-latitude' ).val( lat );
		$( '#acadp-longitude' ).val( lng );			
	};
	
	/**
	 *  Make images inside the listing form sortable.
     *
	 *  @since 1.0.0
	 */
	function acadp_sort_images() {		
		if( $.fn.sortable ) {						
			var $sortable_element = $( '#acadp-images tbody' );
			
			if ( $sortable_element.hasClass( 'ui-sortable' ) ) {
				$sortable_element.sortable( 'destroy' );
			};
			
			$sortable_element.sortable({
				handle: '.acadp-handle'
			});
			
			$sortable_element.disableSelection();
		};		
	}
	
	/**
	 *  Check if the user have permission to upload image.
     *
	 *  @since  1.0.0
	 *  @return bool  True if can upload image, false if not.
	 */
	function acadp_can_upload_image() {		
		var limit = acadp_images_limit();
		var uploaded = acadp_images_uploaded_count();	
		
		if ( ( limit > 0 && uploaded >= limit ) || $( '#acadp-progress-image-upload' ).hasClass( 'uploading' ) ) {
			return false;
		}
		
		return true;		
	}
	
	/**
	 *  Get the maximum number of images the user can upload in the current listing.
     *
	 *  @since  1.5.8
	 *  @return int   Number of images.
	 */
	function acadp_images_limit() {		
		var limit = $( '#acadp-upload-image' ).attr( 'data-limit' );

		if ( typeof limit !== typeof undefined && limit !== false ) {
  			limit = parseInt( limit );
		} else {
			limit = parseInt( acadp.maximum_images_per_listing );
		}
		
		return limit;		
	}
	
	/**
	 *  Get the number of images user had uploaded for the current listing.
     *
	 *  @since  1.5.8
	 *  @return int   Number of images.
	 */
	function acadp_images_uploaded_count() {
		return $( '.acadp-image-field' ).length;		
	}
	
	/**
	 *  Enable or disable image upload
     *
	 *  @since 1.0.0
	 */
	function acadp_enable_disable_image_upload() {		
		if ( acadp_can_upload_image() ) {			
			$( '#acadp-upload-image' ).removeAttr( 'disabled' );			
		} else {			
			$( '#acadp-upload-image' ).attr( 'disabled', 'disabled' );			
		};		
	}

	/**
 	 * Check if value is empty.
 	 *
 	 * @since 1.8.0
 	 */
	 function acadp_is_empty( value ) {		
		if ( '' == value || 0 == value || null == value ) {
			return true;
		}

		return false;
	}
	
	/**
	 * Called when the page has loaded.
	 *
	 * @since 1.0.0
	 */
	$(function() {	
		// Common: Initialize the range slider	
		$( '.acadp-range-slider' ).each(function() {
			acadp_initialize_range_slider( $( this ) );
		});
		
		// Common: Initialize the date/time picker	
		$( '.acadp-date-picker, .acadp-datetime-picker' ).each(function() {
			acadp_initialize_datetime_picker( $( this ) );
		});

		// Common: Show GDPR Consent
		if ( acadp.show_cookie_consent ) {
			$( '.acadp-privacy-consent-button' ).on( 'click', function() {
				$( this ).html( '...' );
				
				var data = {
					'action': 'acadp_set_cookie',
					'security': acadp.ajax_nonce
				};
	
				$.post( 
					acadp.ajax_url, 
					data, 
					function( response ) {
						if ( response.success ) {
							acadp.show_cookie_consent = false;

							$( '.acadp-privacy-wrapper' ).remove();

							acadp_initialize_video();
							acadp_initialize_map();
						}
					}
				);
			});
		}

		// load custom fields of the selected category in the search form
		$( 'body' ).on( 'change', '.acadp-category-search', function() {	
			var $form = $( this ).closest( 'form' );	
			var $search_elem = $form.find( ".acadp-custom-fields-search" );							
			
			if ( $search_elem.length ) {
				var fields = {};

				// Build fields input from cache
				var cached = $search_elem.attr( 'data-cache' );

				if ( cached ) {
					cached = JSON.parse( cached );
				}

				for ( var key in cached ) {
					if ( cached.hasOwnProperty( key ) ) {
						fields[ key ] = cached[ key ];
					}
				}
			
				// Build fields input from current visible form fields
				var current = $form.serializeArray();

				$.each( current, function() {
					if ( this.name.indexOf( 'cf' ) !== -1 ) {
						fields[ this.name ] = this.value;
					}
				} );

				// Cache the new fields data 
				$search_elem.attr( 'data-cache', JSON.stringify( fields ) );

				// Build cached_meta input from the fields object
				var cached_meta = [];

				for ( var key in fields ) {
					if ( fields.hasOwnProperty( key ) ) {
						cached_meta.push( encodeURIComponent( key ) + '=' + encodeURIComponent( fields[ key ] ) );
					}
				}

				cached_meta = cached_meta.join( '&' );

				$search_elem.html( '<div class="acadp-spinner"></div>' );
				
				var data = {
					'action': 'acadp_custom_fields_search',
					'term_id': $( this ).val(),
					'style': $search_elem.data( 'style' ),
					'cached_meta': cached_meta,
					'security': acadp.ajax_nonce
				};
				
				$.post( acadp.ajax_url, data, function(response) {
					$search_elem.html( response );

					$search_elem.find( '.acadp-date-picker' ).each(function() {
						acadp_initialize_datetime_picker( $( this ) );
					});
				});			
			};			
		});
		
		// add "required" attribute to the category field in the listing form [fallback for versions prior to 1.5.5]
		$( '#acadp_category' ).attr( 'required', 'required' );
		
		// load custom fields of the selected category in the custom post type "acadp_listings"
		$( 'body' ).on( 'change', '.acadp-category-listing', function() {
			var fields = {};

			// Build fields input from cache
			var cached = $( '#acadp-custom-fields-listings' ).attr( 'data-cache' );

			if ( cached ) {
				cached = JSON.parse( cached );
			}

			for ( var key in cached ) {
				if ( cached.hasOwnProperty( key ) ) {
					fields[ key ] = cached[ key ];
				}
			}
		
			// Build fields input from current visible form fields
			var current = $( this ).closest( 'form' ).serializeArray();

			$.each( current, function() {
				if ( this.name.indexOf( 'acadp_fields' ) !== -1 ) {
					fields[ this.name ] = this.value;
				}
			} );

			// Cache the new fields data 
			$( '#acadp-custom-fields-listings' ).attr( 'data-cache', JSON.stringify( fields ) );

			// Build cached_meta input from the fields object
			var cached_meta = [];

			for ( var key in fields ) {
				if ( fields.hasOwnProperty( key ) ) {
					cached_meta.push( encodeURIComponent( key ) + '=' + encodeURIComponent( fields[ key ] ) );
				}
			}

			cached_meta = cached_meta.join( '&' );

			$( '.acadp-listing-form-submit-btn' ).prop( 'disabled', true );
			$( '#acadp-custom-fields-listings' ).html( '<div class="acadp-spinner"></div>' );
			
			var data = {
				'action': 'acadp_public_custom_fields_listings',
				'post_id': $( '#acadp-custom-fields-listings' ).data( 'post_id' ),
				'terms': $( this ).val(),
				'cached_meta': cached_meta,
				'security': acadp.ajax_nonce
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$( '#acadp-custom-fields-listings' ).html( response );

				$( '.acadp-date-picker, .acadp-datetime-picker', '#acadp-custom-fields-listings' ).each(function() {
					acadp_initialize_datetime_picker( $( this ) );
				});
				
				$( '.acadp-listing-form-submit-btn' ).prop( 'disabled', false );
			});			
		});	
		
		// slick slider
		if ( $.fn.slick ) {			
			var $carousel = $( '.acadp-slider-for' ).slick({
				rtl: ( parseInt( acadp.is_rtl ) ? true : false ),
  				asNavFor: '.acadp-slider-nav',
				arrows: false,
  				fade: true,
				slidesToShow: 1,
  				slidesToScroll: 1,
				adaptiveHeight: true
			});

			if ( $.fn.magnificPopup ) { // magnific popup
				$carousel.magnificPopup({
					type: 'image',
					delegate: 'div:not(.slick-cloned) img',
					gallery: {
						enabled: true
					},
					callbacks: {
						elementParse: function( item ) {
							item.src = item.el.attr( 'src' );
						},
						open: function() {
							var current = $carousel.slick( 'slickCurrentSlide' );
							$carousel.magnificPopup( 'goTo', current );
						},
						beforeClose: function() {
							$carousel.slick( 'slickGoTo', parseInt( this.index ) );
						}
					}
				});
			};
		
			$( '.acadp-slider-nav' ).slick({
				rtl: ( parseInt( acadp.is_rtl ) ? true : false ),
				asNavFor: '.acadp-slider-for',
				nextArrow: '<div class="acadp-slider-next"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></div>',
				prevArrow: '<div class="acadp-slider-prev"><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></div>',
  				focusOnSelect: true,
				slidesToShow: 5,
				slidesToScroll: 1,
				infinite: false,
				responsive: [
					{
					  breakpoint: 1024,
					  settings: {
						slidesToShow: 3,
						slidesToScroll: 1,
					  }
					},
					{
					  breakpoint: 600,
					  settings: {
						slidesToShow: 2,
						slidesToScroll: 1
					  }
					}
				]
			});		
		};

		// magnific popup
		if ( $.fn.magnificPopup ) {		
			$( '.acadp-image-popup' ).magnificPopup({
				type: 'image'
			});
		};
		
		// render map/video in the custom post type "acadp_listings"
		if ( ! acadp.show_cookie_consent ) {
			acadp_initialize_video();
			acadp_initialize_map();			
		}
		
		// display the media uploader when "Upload Image" button clicked in the custom post type "acadp_listings"		
		$( '#acadp-upload-image' ).on( 'click', function( e ) { 
            e.preventDefault();
			
			if ( acadp_can_upload_image() ) {
				$( '#acadp-upload-image-hidden' ).trigger('click');
			}; 
        });
		
		// upload image 
		$( "#acadp-upload-image-hidden" ).change( function() {			
			var selected = $( this )[0].files.length;
			if ( ! selected ) return false;			
			
			var limit = acadp_images_limit();
			var uploaded = acadp_images_uploaded_count();
			var remaining = limit - uploaded;
			if ( limit > 0 && selected > remaining ) {
				alert( acadp.upload_limit_alert_message.replace( /%d/gi, remaining ) );
				return false;
			};
		
			if ( acadp.is_image_required > 0 ) {
				$( '#acadp-images-panel .panel-heading span, #acadp-images-panel .help-block span' ).removeClass( 'text-danger' );
			}
			$( '#acadp-progress-image-upload' ).addClass( 'uploading' ).html( '<div class="acadp-spinner"></div>' );
			
			acadp_enable_disable_image_upload();
						
			var options = {
				dataType: 'json',
				url: acadp.ajax_url,
        		success: function( json, statusText, xhr, $form ) {
					// do extra stuff after submit
					$( '#acadp-progress-image-upload' ).removeClass( 'uploading' ).html( '' );
					
					$.each( json, function( key, value ) {							
						if ( ! value['error'] ) {
							var html = '<tr class="acadp-image-row">' + 
								'<td class="acadp-handle"><span class="glyphicon glyphicon-th-large"></span></td>' +          	
								'<td class="acadp-image">' + 
									'<img src="' + value['url'] + '" alt="" />' + 
									'<input type="hidden" class="acadp-image-field" name="images[]" value="' + value['id'] + '" />' + 
								'</td>' + 
								'<td>' + 
									'<span class="acadp-image-url">' + value['url'].split(/[\\/]/).pop() + '</span><br />' + 
									'<a href="javascript:;" class="acadp-delete-image" data-attachment_id="' + value['id'] + '">' + acadp.delete_label + '</a>' + 
								'</td>' +                 
							'</tr>';					
							$( '#acadp-images' ).append( html );
						};						
					});

					acadp_sort_images();
					acadp_enable_disable_image_upload();
				},
				error: function( data ) {
					$( '#acadp-progress-image-upload' ).removeClass( 'uploading' ).html( '' );
					acadp_enable_disable_image_upload();
				}
    		}; 

    		// submit form using 'ajaxSubmit' 
    		$('#acadp-form-upload').ajaxSubmit( options );										 
		});	

		// make the isting images sortable in the custom post type "acadp_listings"
		acadp_sort_images();
		
		// Delete the selected image when "Delete Permanently" button clicked in the custom post type "acadp_listings"	
		$( '#acadp-images' ).on( 'click', 'a.acadp-delete-image', function( e ) {														 
            e.preventDefault();
								
			var $this = $( this );
			
			var data = {
				'action': 'acadp_public_delete_attachment_listings',
				'attachment_id': $this.data('attachment_id'),
				'security': acadp.ajax_nonce
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$this.closest( 'tr' ).remove();
				$( '#acadp-upload-image-hidden' ).val( '' );
				acadp_enable_disable_image_upload();
			});			
		});
		
		// Toggle password fields in user account form
		$( '#acadp-change-password', '#acadp-user-account' ).on( 'change', function() {			
			var $checked = $( this ).is( ":checked" );
			
			if ( $checked ) {
				$( '.acadp-password-fields', '#acadp-user-account' ).show().find( 'input[type="password"]' ).attr( "disabled", false );				
			} else {
				$( '.acadp-password-fields', '#acadp-user-account' ).hide().find( 'input[type="password"]' ).attr( "disabled", "disabled" );
			};			
		}).trigger( 'change' );
			
		// Validate ACADP forms
		if ( $.fn.validator ) {			
			// Validate login, forgot password, password reset, user account forms
			var acadp_login_submitted = false;
			
			$ ( '#acadp-login-form, #acadp-forgot-password-form, #acadp-password-reset-form, #acadp-user-account' ).validator({
				disable: false
			}).on( 'submit', function( e ) {				
				if ( acadp_login_submitted ) {
					return false;
				}

				acadp_login_submitted = true;
					
				// Check for errors				
				if ( e.isDefaultPrevented() ) {				 	
					acadp_login_submitted = false; // Re-enable the submit event
				};			 
			});
			
			// Validate registration form
			var acadp_register_submitted = false;
			
			$( '#acadp-register-form' ).validator({
				disable: false
			}).on( 'submit', function( e ) {				
				if ( acadp_register_submitted ) {
					return false;
				}

				acadp_register_submitted = true;
					
				// Check for errors
				var error = 1;
				
				if ( ! e.isDefaultPrevented() ) {				 
				 	error = 0;
					
			 		if ( acadp.recaptcha_registration > 0 ) {				 
			 			var response = grecaptcha.getResponse( acadp.recaptchas['registration'] );
				
						if ( 0 == response.length ) {
							$( '#acadp-registration-g-recaptcha-message' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['registration'] );
					
							error = 1;
						};			
					};			
				};
				
				if ( error ) {					
					acadp_register_submitted = false; // Re-enable the submit event					
					return false;					
				};			 
			});
			
			// Validate listing form
			var acadp_listing_submitted = false;
			
			$( '#acadp-post-form' ).validator({
				'custom': {
					cb_required: function( $el ) {
						var class_name = $el.data( 'cb_required' );
						return $( "input." + class_name + ":checked" ).length > 0 ? true : false;
					}
				},
				errors: {
      				cb_required: "You must select atleast one option."
   				},
				disable: false
			}).on( 'submit', function( e ) {				
				if ( acadp_listing_submitted ) {
					return false;
				}

				acadp_listing_submitted = true;
					
				// Check for errors
				var error = 1;
				
				if ( ! e.isDefaultPrevented() ) {					
					error = 0;

					if ( acadp.is_image_required > 0 ) {
						var uploaded = acadp_images_uploaded_count();
						
						if ( uploaded == 0 ) {
							$( '#acadp-images-panel .panel-heading span, #acadp-images-panel .help-block span' ).addClass( 'text-danger' );
							error = 1;
						}
					}
				 
			 		if ( acadp.recaptcha_listing > 0 ) {				 
			 			var response = grecaptcha.getResponse( acadp.recaptchas['listing'] );
				
						if ( 0 == response.length ) {
							$( '#acadp-listing-g-recaptcha-message' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['listing'] );
					
							error = 1;
						};			
					};			
				};
				
				if ( error ) {					
					$( "#acadp-post-errors" ).show();
					
					$( 'html, body' ).animate({
        				scrollTop: $( "#acadp-post-form" ).offset().top - 50
    				}, 500 );
					
					acadp_listing_submitted = false; // Re-enable the submit event
					
					return false;					
				} else {					
					$( "#acadp-post-errors" ).hide();					
				};			 
			});
			
			// Validate report abuse form
			var acadp_report_abuse_submitted = false;
			
			$( '#acadp-report-abuse-form' ).validator({
				disable: false
			}).on( 'submit', function( e ) {									
				if ( acadp_report_abuse_submitted ) {
					return false;
				}

				acadp_report_abuse_submitted = true;
				
				// Check for errors
				if ( ! e.isDefaultPrevented() ) {			 
			 		e.preventDefault();
					
			 		var response = '';
					
			 		if ( acadp.recaptcha_report_abuse > 0 ) {				 
			 			response = grecaptcha.getResponse( acadp.recaptchas['report_abuse'] );
				
						if ( 0 == response.length ) {
							$( '#acadp-report-abuse-message-display' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['report_abuse'] );
				
							acadp_report_abuse_submitted = false; // Re-enable the submit event							
							return false;
						};			
					};
			 
			 		// Post via AJAX			 
			 		var data = {
						'action': 'acadp_public_report_abuse',
						'post_id': $( '#acadp-post-id' ).val(),
						'message': $( '#acadp-report-abuse-message' ).val(),
						'g-recaptcha-response': response,
						'security': acadp.ajax_nonce
					};
			
					$.post( acadp.ajax_url, data, function( response ) {
						if ( 1 == response.error ) {
							$( '#acadp-report-abuse-message-display' ).addClass('text-danger').html( response.message );
						} else {
							$( '#acadp-report-abuse-message' ).val('');
							$( '#acadp-report-abuse-message-display' ).addClass('text-success').html( response.message );
						};
				
						if ( acadp.recaptcha_report_abuse > 0 ) {
							grecaptcha.reset( acadp.recaptchas['report_abuse'] );
						};
						
						acadp_report_abuse_submitted = false; // Re-enable the submit event
					}, 'json' );			
				};																		  
			});
			
			// Validate contact form
			var acadp_contact_submitted = false;
			
			$( '#acadp-contact-form' ).validator({
				disable: false
			}).on( 'submit', function( e ) {							
				if ( acadp_contact_submitted ) return false;
				
				// Check for errors
				if ( ! e.isDefaultPrevented() ) {			 
			 		e.preventDefault();
					
					acadp_contact_submitted = true;
			 		var response = '';
					
			 		if ( acadp.recaptcha_contact > 0 ) {				 
			 			response = grecaptcha.getResponse( acadp.recaptchas['contact'] );
				
						if ( 0 == response.length ) {
							$( '#acadp-contact-message-display' ).addClass( 'text-danger' ).html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['contact'] );
				
							acadp_contact_submitted = false; // Re-enable the submit event						
							return false;
						};				
					};
					
					$( '#acadp-contact-message-display' ).append( '<div class="acadp-spinner"></div>' );
					
			 		// Post via AJAX
			 		var data = {
						'action': 'acadp_public_send_contact_email',
						'post_id': $( '#acadp-post-id' ).val(),
						'name': $( '#acadp-contact-name' ).val(),
						'email': $( '#acadp-contact-email' ).val(),
						'message': $( '#acadp-contact-message' ).val(),
						'g-recaptcha-response': response,
						'security': acadp.ajax_nonce
					};

					if ( $( '#acadp-contact-phone' ).length > 0 ) {
						data.phone = $( '#acadp-contact-phone' ).val();
					}

					if ( $( '#acadp-contact-send-copy' ).length > 0 ) {
						data.send_copy = $( '#acadp-contact-send-copy' ).is( ':checked' ) ? 1 : 0;
					}
			
					$.post( acadp.ajax_url, data, function( response ) {
						if ( 1 == response.error ) {
							$( '#acadp-contact-message-display' ).addClass( 'text-danger' ).html( response.message );
						} else {
							$( '#acadp-contact-message' ).val('');
							$( '#acadp-contact-message-display' ).addClass( 'text-success' ).html( response.message );
						};
				
						if ( acadp.recaptcha_contact > 0 ) {
							grecaptcha.reset( acadp.recaptchas['contact'] );
						};
						
						acadp_contact_submitted = false; // Re-enable the submit event
					}, 'json' );					
				} else {
					acadp_contact_submitted = false;
				};				
			});		
		};
		
		// Report abuse [on modal closed]
		$( '#acadp-report-abuse-modal' ).on( 'hidden.bs.modal', function( e ) {																	   
			$( '#acadp-report-abuse-message' ).val( '' );
			$( '#acadp-report-abuse-message-display' ).html( '' );			
		});
		
		// Contact form [on modal closed]
		$( '#acadp-contact-modal' ).on( 'hidden.bs.modal', function( e ) {																  
			$( '#acadp-contact-message' ).val( '' );
			$( '#acadp-contact-message-display' ).html( '' );			
		});
		
		// Add or Remove from favourites
		$( '#acadp-favourites' ).on( 'click', 'a.acadp-favourites', function( e ) {													   
			 e.preventDefault();
			 
			 var $this = $( this );
			 
			 var data = {
				'action': 'acadp_public_add_remove_favorites',
				'post_id': $this.data('post_id'),
				'security': acadp.ajax_nonce
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$( '#acadp-favourites' ).html( response );
			});																		   
		});
		
		// Alert users to login (only if applicable)
		$( '.acadp-require-login' ).on( 'click', function( e ) {														  
			e.preventDefault();			 
			alert( acadp.user_login_alert_message );			 
		});
		
		// Calculate and update total amount in the checkout form
		$( '.acadp-checkout-fee-field' ).on( 'change', function() {	
			var total_amount = 0,
			    fee_fields   = 0;
				
			$( "#acadp-checkout-form-data input[type='checkbox']:checked, #acadp-checkout-form-data input[type='radio']:checked" ).each(function() {
				total_amount += parseFloat( $( this ).data('price') );
				++fee_fields;
			});
			
			$( '#acadp-checkout-total-amount' ).html( '<div class="acadp-spinner"></div>' );
			
			if ( 0 == fee_fields ) {
				$( '#acadp-checkout-total-amount' ).html( '0.00' );
				$( '#acadp-payment-gateways, #acadp-cc-form, #acadp-checkout-submit-btn' ).hide();
				return;
			};
			
			var data = {
				'action': 'acadp_checkout_format_total_amount',
				'amount': total_amount,
				'security': acadp.ajax_nonce
			};
			
			$.post( acadp.ajax_url, data, function( response ) {												   
				$( '#acadp-checkout-total-amount' ).html( response );
				
				var amount = parseFloat( $( '#acadp-checkout-total-amount' ).html() );
				
				if ( amount > 0 ) {
					$( '#acadp-payment-gateways, #acadp-cc-form' ).show();
					$( '#acadp-checkout-submit-btn' ).val( acadp.proceed_to_payment_btn_label ).show();
				} else {
					$( '#acadp-payment-gateways, #acadp-cc-form' ).hide();
					$( '#acadp-checkout-submit-btn' ).val( acadp.finish_submission_btn_label ).show();
				}				
			});			
		}).trigger( 'change' );
		
		// Validate checkout form
		var acadp_checkout_submitted = false;
		
		$( '#acadp-checkout-form' ).on( 'submit', function() {					
			if ( acadp_checkout_submitted ) {
				return false;
			}

			acadp_checkout_submitted = true;			
		});
		
		// Populate ACADP child terms dropdown
		$( '.acadp-terms' ).on( 'change', 'select', function( e ) {								
			e.preventDefault();
			 
			var $this    = $( this );
			var taxonomy = $this.data( 'taxonomy' );
			var parent   = $this.data( 'parent' );
			var value    = $this.val();
			var classes  = $this.attr( 'class' );
			
			$this.closest( '.acadp-terms' ).find( 'input.acadp-term-hidden' ).val( value );
			$this.parent().find( 'div:first' ).remove();
			
			if ( value && parent != value ) {
				$this.parent().append( '<div class="acadp-spinner"></div>' );
				
				var data = {
					'action': 'acadp_public_dropdown_terms',
					'taxonomy': taxonomy,
					'parent': value,
					'class': classes,
					'security': acadp.ajax_nonce
				};
				
				$.post( acadp.ajax_url, data, function( response ) {
					$this.parent().find( 'div:first' ).remove();
					$this.parent().append( response );
				});
			};			
		});

		// Show phone number
		$( '.acadp-show-phone-number' ).on( 'click', function() {
			$( this ).hide();
			$( '.acadp-phone-number' ).show();
		});

		// Gutenberg: Refresh Map.
		if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp['hooks'] ) {
			var acadp_block_interval;
			var acadp_block_interval_retry_count;

			wp.hooks.addFilter( 'acadp_block_listings_init', 'acadp/listings', function( attributes ) {
				if ( 'map' === attributes.view ) {
					if ( acadp_block_interval_retry_count > 0 ) {
						clearInterval( acadp_block_interval );
					}				
					acadp_block_interval_retry_count = 0;

					acadp_block_interval = setInterval(
						function() {
							acadp_block_interval_retry_count++;

							if ( $( '.acadp-map:not(.acadp-map-loaded)' ).length > 0 || acadp_block_interval_retry_count >= 10 ) {
								clearInterval( acadp_block_interval );
								acadp_block_interval_retry_count = 0;

								acadp_initialize_map();
							}
						}, 
						1000
					);
				}
			});
		}

		// WhatsApp Share
		$( '.acadp-social-whatsapp' ).on( 'click', function() {
			if ( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
				$( this ).removeAttr( 'href' );
				var article = jQuery( this ).attr( 'data-text' );
				var weburl = jQuery( this ).attr( 'data-link' );
				var whatsapp_message = encodeURIComponent( article ) + ' - ' + encodeURIComponent( weburl );
				var whatsapp_url = 'whatsapp://send?text=' + whatsapp_message;
				window.location.href= whatsapp_url;
			}
		});	
	});
})( jQuery );

/**
 *  load reCAPTCHA explicitly.
 *
 *  @since 1.0.0
 */
var acadp_on_recaptcha_load = function() {
	if ( '' != acadp.recaptcha_site_key ) {		
		// Add reCAPTCHA in registration form
		if ( jQuery( "#acadp-registration-g-recaptcha" ).length ) {			
			if ( acadp.recaptcha_registration > 0 ) {
				acadp.recaptchas['registration'] = grecaptcha.render( 'acadp-registration-g-recaptcha', {
    				'sitekey': acadp.recaptcha_site_key
    			});
				
				jQuery( "#acadp-registration-g-recaptcha" ).addClass( 'acadp-margin-bottom' );
			};			
		} else {			
			acadp.recaptcha_registration = 0;			
		};
		
		// Add reCAPTCHA in listing form
		if ( jQuery( "#acadp-listing-g-recaptcha" ).length ) {			
			if ( acadp.recaptcha_listing > 0 ) {
				acadp.recaptchas['listing'] = grecaptcha.render( 'acadp-listing-g-recaptcha', {
    				'sitekey': acadp.recaptcha_site_key
    			});
				
				jQuery( "#acadp-listing-g-recaptcha" ).addClass( 'acadp-margin-bottom' );
			};			
		} else {			
			acadp.recaptcha_listing = 0;			
		};
		
		// Add reCAPTCHA in contact form
		if ( jQuery( "#acadp-contact-g-recaptcha" ).length ) {			
			if ( acadp.recaptcha_contact > 0 ) {
				acadp.recaptchas['contact'] = grecaptcha.render( 'acadp-contact-g-recaptcha', {
    				'sitekey': acadp.recaptcha_site_key
    			});
			};		
		} else {			
			acadp.recaptcha_contact = 0;			
		};
		
		// Add reCAPTCHA in report abuse form
		if ( jQuery( "#acadp-report-abuse-g-recaptcha" ).length ) {			
			if ( acadp.recaptcha_report_abuse > 0 ) {
				acadp.recaptchas['report_abuse'] = grecaptcha.render( 'acadp-report-abuse-g-recaptcha', {
    				'sitekey': acadp.recaptcha_site_key
    			});
			};		
		} else {			
			acadp.recaptcha_report_abuse = 0;			
		};

		// Custom Event for developers (suggested by Paul for his "Site Reviews" plugin)
		document.dispatchEvent( new CustomEvent( 'acadp_on_recaptcha_load' ) );	
	};	
};
