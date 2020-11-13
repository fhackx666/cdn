( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		// Check if Div is Empty
		function isEmpty( el ) {
		    return ! $.trim( el.html() );
		}

		if ( ! window.hasOwnProperty( 'yz_disable_niceselect' ) && ( $( '.youzer select:not([multiple="multiple"])' ).get( 0 ) || $( '.logy select' ).get( 0 ) ) ) {
    		$( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/jquery.nice-select.min.js' } ).appendTo( 'head' );
			$( '.youzer select:not([multiple="multiple"])' ).niceSelect();
			$( '.logy select' ).not( '[multiple="multiple"]' ).niceSelect();
		}

		// Textarea Auto Height.
		if ( $( '.youzer textarea' ).get( 0 ) ) {
    		$( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/autosize.min.js' } ).appendTo( 'head' );
			yz_autosize( $( '.youzer textarea' ) );
		}

    	$( '.yzw-form-show-all' ).on( 'click', function( e ) {
    		$( '.yzw-form-show-all' ).fadeOut( function() {
    			$( '.yz-wall-opts-item' ).fadeIn();
    			$( this ).remove();
    		});
		});

		// Delete Empty Notices.
		$( '.widget_bp_core_sitewide_messages' ).each( function() {
	        if ( isEmpty( $( this ).find( '.bp-site-wide-message' ) ) ) {
	          $( this ).remove();
	        }
	    });

		// Delete Empty Actions.
		$( '.youzer .group-members-list .action' ).each( function() {
	        if ( isEmpty( $( this ) ) ) {
	          $( this ).remove();
	        }
	    });

		// Delete Empty Sub Navigations.
		$( '#subnav ul' ).each( function() {
	        if ( isEmpty( $( this ) ) ) {
	          $( this ).parent().remove();
	        }
	    });

		// Delete Empty Search
        if ( isEmpty( $( '.yz-group-manage-members-search' ) ) ) {
          $( '.yz-group-manage-members-search' ).remove();
        }

		// Close SiteWide Notice.
		$( '#close-notice' ).on( 'click', function( e ) {
			$( this ).closest( '#sitewide-notice' ).fadeOut();
		});

		/**
		 * Display Activity tools.
		 */
		$( document ).on( 'click',  '.yz-item .yz-show-item-tools', function ( e ) {

			// Load Reviews Script.
    		if ( $( this ).parent().hasClass( 'yz-review-item' ) ) {
    			$.yz_load_reviews_script();
    		}

			// Switch Current Icon.
			$( this ).toggleClass( 'yz-close-item-tools' );

			// Show / Hide Tools.
			$( this ).closest( '.yz-item' ).find( '.yz-item-tools' ).fadeToggle();

		});

		/**
		 * Get Url Variable.
		 */
		$.yz_get_var_in_url = function( url, name ) {
			var urla = url.split( "?" );
			var qvars = urla[1].split( "&" );//so we hav an arry of name=val,name=val
			for ( var i = 0; i < qvars.length; i++ ) {
				var qv = qvars[i].split( "=" );
				if ( qv[0] == name )
					return qv[1];
			}
			return '';
		}

		// Change Fields Privacy.
	    $( '.field-visibility-settings .radio input[type=radio]' ).change( function() {
	    	var new_privacy = $( this ).parent().find( '.field-visibility-text' ).text();
	    	$( this ).closest('.field-visibility-settings')
	    	.prev( '.field-visibility-settings-toggle' )
	    	.find( '.current-visibility-level' )
	    	.text( new_privacy );
	    });

		// Append Dialog.
		$( 'body' ).append( '<div class="youzer-dialog"></div>' );

	    /**
	     * Dialog Message.
	     */
	    $.yz_DialogMsg = function ( type, msg ) {

	     	var dialogHeader, dialogTitle, dialogButton, confirmation_btn;

	     	// Get Dialog Title.
			if ( type == 'error' ) {
	     		dialogTitle = '<div class="youzer-dialog-title">' + Youzer.ops + '</div>';
	     	} else if ( type == 'success' ) {
	     		dialogTitle = '<div class="youzer-dialog-title">' + Youzer.done + '</div>';
	     	} else if ( type == 'info' ) {
	     		dialogTitle = '';
	     	}

	     	// Get Dialog Button.
			if ( type == 'error' ) {
	     		dialogButton = Youzer.gotit;
	     	} else if ( type == 'success' ) {
	     		dialogButton = Youzer.thanks;
	     	} else if ( type == 'info' ) {
	     		dialogButton = Youzer.cancel;
	     	}

	     	// Get Header Icon.
	     	if ( type == 'error' ) {
	     		dialogHeader = '<i class="fas fa-exclamation-triangle"></i>';
	     	} else if ( type == 'info' ) {
	     		dialogHeader = '<i class="fas fa-info-circle"></i>';
	     	} else if ( type == 'success' ) {
	     		dialogHeader = '<i class="fas fa-check"></i>';
	     	}

	     	// Get Confirmation Button
	     	if ( type == 'info' ) {
	     		confirmation_btn = '<li><a class="yz-confirm-dialog">' + Youzer.confirm + '</a></li>';
	     	} else {
	     		confirmation_btn = '';
	     	}

	     	var dialog =
	     	'<div class="yz-' + type + '-dialog">' +
	            '<div class="youzer-dialog-container">' +
	                '<div class="yz-dialog-header">' + dialogHeader + '</div>' +
	                '<div class="youzer-dialog-msg">' +
	                    '<div class="youzer-dialog-desc">' + dialogTitle + '<div class="yz-dialog-msg-content">' + msg + '</div>' + '</div>' +
	               	'</div>' +
	                '<ul class="yz-dialog-buttons">' +
	                	confirmation_btn +
	                	'<li><a class="yz-close-dialog">' + dialogButton + '</a></li>' +
	                '</ul>'+
	            '</div>' +
	        '</div>';

	     	$( '.youzer-dialog' ).empty().append( dialog );
	        $( '.youzer-dialog' ).addClass( 'yz-is-visible' );

	    }

	    // Close Dialog
	    $( '.youzer-dialog' ).on( 'click', function( e ) {
	        if ( $( e.target ).is( '.yz-close-dialog' ) || $( e.target ).is( '.youzer-dialog' ) ) {
	            e.preventDefault();
	            $( this ).removeClass( 'yz-is-visible' );
	        }
	    });

	    // Close Modal
	    $( '.youzer-modal' ).on( 'click', function( e ) {
	        if ( $( e.target ).is( '.yz-close-dialog' ) || $( e.target ).is( '.youzer-modal' ) ) {
	            e.preventDefault();
	            $( this ).removeClass( 'yz-is-visible' );
	        }
	    });

	    // Close Dialog if you user Clicked Cancel
	    $( 'body' ).on( 'click', '.yz-close-dialog', function( e ) {
	        e.preventDefault();
	        $( '.youzer-dialog,.youzer-modal' ).removeClass( 'yz-is-visible' );
	    });

	    // Add Close Button to Login Popup.
	    $( '.yz-popup-login .logy-form-header' )
	    .append( '<i class="fas fa-times yz-close-login"></i>' );

	    // Display Login Popup.
	    $( 'a[data-show-youzer-login="true"],.yz-show-youzer-login-popup a' ).on( 'click', function( e ) {

	    	if ( Youzer.login_popup == 'off' ) {
	    		return;
	    	}

	        e.preventDefault();
	        $( '.yz-popup-login' ).addClass( 'yz-is-visible' );
	    });

	    // Close Login Popup.
	    $( '.yz-popup-login' ).on( 'click', function( e ) {
	        if ( $( e.target ).is( '.yz-close-login' ) || $( e.target ).is( '.yz-popup-login' ) ) {
	            e.preventDefault();
	            $( this ).removeClass( 'yz-is-visible' );
	        }
	    });

	    // Close Dialog if you user Clicked Cancel
	    $( '.yz-close-login' ).on( 'click', function( e ) {
	        e.preventDefault();
	        $( '.yz-popup-login' ).removeClass( 'yz-is-visible' );
	    });

	    // Ajax Login.
	    $( '.logy-login-form' ).on( 'submit', function( e ) {

	    	if ( Youzer.ajax_enabled == 'off' ) {
	    		return;
	    	}

	    	// Add Authenticating Class.
	    	$( this ).addClass( 'yz-authenticating' );

	    	// Init Vars.
	    	var yz_login_form = $( this ), yz_btn_txt, yz_btn_icon, yz_submit_btn;

	    	// Get Current Button Text & Icon.
	    	yz_submit_btn = $( this ).find( 'button[type="submit"]' );
	    	yz_btn_txt  = yz_submit_btn.find( '.logy-button-title' ).text();
	    	yz_btn_icon = yz_submit_btn.find( '.logy-button-icon i' ).attr( 'class' );

	    	// Display "Authenticating..." Messages.
	    	yz_submit_btn.find( '.logy-button-title' ).text( Youzer.authenticating );
	    	yz_submit_btn.find( '.logy-button-icon i' ).attr( 'class', 'fas fa-spinner fa-spin' );

	    	// Get Current Button Icon
	    	var yz_login_data = {
                'action': 'yz_ajax_login',
                'username': $( this ).find( 'input[name="log"]' ).val(),
                'password': $( this ).find( 'input[name="pwd"]' ).val(),
                'remember': $( this ).find( 'input[name="rememberme"]' ).val(),
                'redirect_to': $( this ).find( 'input[name="yz_redirect_to"]' ).val(),
                'security': $( this ).find( 'input[name="yz_ajax_login_nonce"]' ).val(),
	        };

	        $.ajax({
	            type: 'POST',
	            dataType: 'json',
	            url: ajaxurl,
	            data: yz_login_data,
	            success: function( response ) {

	                if ( response.loggedin == true ) {
	                	// Change Login Button Title.
	    				yz_submit_btn.find( '.logy-button-title' ).text( response.message );
	    				yz_submit_btn.find( '.logy-button-icon i' ).attr( 'class', 'fas fa-check' );
		         		// Redirect.
	                    document.location.href = response.redirect_url;
	                } else {

		            	// Add Authenticating Class.
		    			yz_login_form.removeClass( 'yz-authenticating' );

	                	// Clear Inputs Depending on the errors ..
	                	if ( response.error_code && 'incorrect_password' == response.error_code ) {
	                		// Clear Password Field.
	                		yz_login_form.find( 'input[name="pwd"]' ).val( '' );
	                	} else {
	                		// If Username invalid Clear Inputs.
	                		yz_login_form.find( 'input[name="log"],input[name="pwd"]' ).val( '' );
	                	}
	                	// Change Login Button Title & Icon.
	    				yz_submit_btn.find( '.logy-button-title' ).text( yz_btn_txt );
	    				yz_submit_btn.find( '.logy-button-icon i' ).attr( 'class', yz_btn_icon );
		            	// Show Error Message.
		            	$.yz_DialogMsg( 'error', response.message );
	                }
	            }
        	});

	        e.preventDefault();

	    });

		// Responsive Navbar Menu
		$( '.yz-responsive-menu' ).click( function( e ) {
	        e.preventDefault();
			// Hide Account Settings Menu to avoid any Conflect.
			if (  $( '.yz-settings-area' ).hasClass( 'open-settings-menu' ) ) {
				$( '.yz-settings-area' ).toggleClass( 'open-settings-menu'  );
				$( '.yz-settings-area .yz-settings-menu' ).fadeOut();
			}
			// Show / Hide Navigation Menu
			$( this ).toggleClass( 'is-active' );
	        $( '.yz-profile-navmenu' ).fadeToggle( 600 );
		});

		/**
		 * # Hide Modal if user clicked Close Button or Icon
		 */
		$( document ).on( 'click', '.yz-wall-modal-close' , function( e ) {

			e.preventDefault();

			// Hide Form.
			$( '.yz-wall-modal' ).removeClass( 'yz-wall-modal-show' );
	        $( '.yz-wall-modal-overlay' ).fadeOut( 600 );

			setTimeout(function(){
			   // wait for card1 flip to finish and then flip 2
			   $( '.yz-wall-modal' ).remove();
			}, 500);

		});

		/**
		 * # Check is String is Json Code.
		 */
		$.yz_isJSON = function ( str ) {

		    if ( typeof( str ) !== 'string' ) {
		        return false;
		    }

		    try {
		        JSON.parse( str );
		        return true;
		    } catch ( e ) {
		        return false;
		    }
		}

		/**
		 * Woocommerce Add to cart with ajax.
		 */
		$( document ).on( 'click', '.yz-addtocart-ajax', function (e) {

		    e.preventDefault();

		    var $thisbutton = $(this),
		    	button_icon_class = $thisbutton.find( '.yz-btn-icon i' ).attr( 'class' ),
	            $form = $thisbutton.closest('form.cart'),
	            variation_id = $form.find('input[name=variation_id]').val() || 0;

		    var data = {
		        action: 'woocommerce_ajax_add_to_cart',
		        product_id: $thisbutton.data( 'yz-product-id' ),
		        product_sku: '',
		        quantity: 1,
		        variation_id: variation_id,
		    };

		    $( document.body ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

		    $.ajax({
		        type: 'post',
		        url: Youzer.ajax_url,
		        data: data,
		        beforeSend: function (response) {

		            $thisbutton.removeClass('added').addClass('loading');
		        },
		        complete: function (response) {

					// Show Check .
		            $thisbutton.addClass( 'added' ).removeClass('loading');
		            $thisbutton.find( '.yz-btn-icon i' ).attr( 'class', 'fas fa-check' );

					setTimeout( function() {
						// Change Button Icon.
			            $thisbutton.find( '.yz-btn-icon i' ).attr( 'class', button_icon_class ).hide().fadeIn();
					}, 1000 );
		        },
		        success: function (response) {

		            if (response.error & response.product_url) {
		                window.location = response.product_url;
		                return;
		            } else {
		                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
		            }
		        },
		    });

		    return false;
		});

		/**
		 * # Hide Modal if user clicked Close Button or Icon
		 */
		$( document ).on( 'click', '.yz-modal-close, .yz-modal-close-icon' , function( e ) {

			e.preventDefault();

			// Hide Black Overlay
			$( '.yz-modal-overlay' ).fadeOut( 500 );

			// Get Data.
			var modal = $( this ).closest( '.yz-modal' ).fadeOut( 300, function() {
				$( this ).remove();
			});

		});

		// Hide Modal If User Clicked Escape Button
		$( document ).keyup( function( e ) {
			if ( $( '.yz-modal-show' )[0] ) {
			    if ( e.keyCode === 27 ) {
				    $( '.yz-modal-close' ).trigger( 'click' );
			    }
			}
		});

		// # Hide Modal if User Clicked Outside
		$( document ).mouseup( function( e ) {
		    if ( $( '.yz-modal-overlay' ).is( e.target ) && $( '.yz-modal-show' )[0] ) {
				$( '.yz-modal-close' ).trigger( 'click' );
		    }
		});

        // Overrding Append Function.
        var yz_origAppend = $.fn.append;
		$.fn.append = function () {
			return yz_origAppend.apply( this, arguments ).trigger( 'append' );
		};

		$( '<div class="yz-mobile-nav yz-inline-mobile-nav">'+
			'<div class="yz-mobile-nav-item yz-show-tab-menu"><div class="yz-mobile-nav-container"><i class="fas fa-bars"></i><a>' + Youzer.menu_title + '</a></div></div>' + '</div>'
		).insertBefore( $( '.yz-profile div.item-list-tabs,.yz-group div.item-list-tabs' )  );

		var yz_resizeTimer;

		$( window ).on( 'resize', function ( e ) {

			// Init Vars.
			var window_changed;

		    clearTimeout( yz_resizeTimer );

		    yz_resizeTimer = setTimeout( function () {

		    	if ( $.browser.mobile ) {
			    	window_changed = $( window ).width() != app.size.window_width;
				} else {
					window_changed = true;
				}

	    		if ( window_changed ) {
			        if ( $( window ).width() > 768 ) {
			        	$( '.item-list-tabs, .item-list-tabs ul, #yz-directory-search-box, #members-order-select,#groups-order-select,.yz-profile-navmenu' ).fadeIn().removeAttr("style");;
			        }
		 		}
			}, 250 );
		});

		// Display Search Box.
    	$( '.yz-show-tab-menu' ).on( 'click', function( e ) {
    		e.preventDefault();
    		$( '#subnav.item-list-tabs' ).fadeToggle();
		});

		// Display Search Box.
    	$( '.yz-tool-btn' ).on( 'click', function( e ) {

    		e.preventDefault();

    		if ( $( this ).hasClass( 'yz-verify-btn' ) && ! $( 'body' ).hasClass( 'yz-verify-script-loaded' ) ) {
    			$( 'body' ).addClass( 'yz-verify-script-loaded' );
    			$( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/yz-verify-user.min.js' } ).appendTo( 'head' );
    			$( this ).trigger( 'click' );
    		}

    		// Load Reviews Script.
    		if ( $( this ).hasClass( 'yz-review-btn' ) ) {
    			$.yz_load_reviews_script( $( this ) );
    		}

		});

    	/**
    	 * Load Reviews Script
    	 */
    	$.yz_load_reviews_script = function( button ) {

    		if ( ! $( 'body' ).hasClass( 'yz-review-script-loaded' ) ) {
    			$( 'body' ).addClass( 'yz-review-script-loaded' );
    			$( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/yz-reviews.min.js' } ).appendTo( 'head' );
    			if ( button ) {
    				button.trigger( 'click' );
    			}
    		}

    	}

		// Display Menu Box.
    	$( document ).on( 'click', '.youzer a[data-lightbox]', function( e ) {

    		if ( window.hasOwnProperty( 'yz_disable_lightbox' ) ) {
    			e.preventDefault();
    			return;
			}

    		e.preventDefault();

    		if ( ! $( 'body' ).hasClass( 'yz-lightbox-script-loaded' ) ) {
    			$( 'body' ).addClass( 'yz-lightbox-script-loaded' );
		        $( '<script/>', { rel: 'text/javascript', src: Youzer.assets + 'js/lightbox.min.js' } ).appendTo( 'head' );
	    		$( '<link/>', { rel: 'stylesheet', href: Youzer.assets + 'css/lightbox.min.css' } ).appendTo( 'head' );
    			$( this ).trigger( 'click' );
    		}

		});

	});

})( jQuery );