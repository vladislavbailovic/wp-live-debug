(function( $ ) {
	var safetyPopup            = $( '#safety-popup' ),
		acceptRisk             = $( '#riskaccept' ),
		acceptRiskData         = { 'action': 'wp-live-debug-accept-risk' },
		responseHolder         = $( '#wp-debug-response-holder' ),
		refreshToggle          = $( '#toggle-auto-refresh' ),
		debugArea              = $( '#wp-live-debug-area' ),
		refreshData            = { 'action': 'wp-live-debug-read-log' },
		clearButton            = $( '#wp-live-debug-clear' ),
		clearData              = { 'action': 'wp-live-debug-clear-debug-log' },
		backupButton           = $( '#wp-live-debug-backup' ),
		createBackupData       = { 'action': 'wp-live-debug-create-backup' },
		restoreButton          = $( '#wp-live-debug-restore' ),
		restoreBackupData      = { 'action': 'wp-live-debug-restore-backup' },
		wpDebugToggle          = $( '#toggle-wp-debug' ),
		enableWPDebugData      = { 'action': 'wp-live-debug-enable' },
		disableWPDebugData     = { 'action': 'wp-live-debug-disable' },
		scriptDebugToggle      = $( '#toggle-script-debug' ),
		enableScriptDebugData  = { 'action': 'wp-live-debug-enable-script-debug' },
		disableScriptDebugData = { 'action': 'wp-live-debug-disable-script-debug' },
		savequeriesToggle      = $( '#toggle-savequeries' ),
		enableSavequeriesData  = { 'action': 'wp-live-debug-enable-savequeries' },
		disableSavequeriesData = { 'action': 'wp-live-debug-disable-savequeries' },
		checksumsData          = { 'action': 'wp-live-debug-checksums-check' },
		checksumsDiffButton    = 'button[data-do=wp-live-debug-diff]',
		checksumsResponse      = $( '#checksums-response' ),
		checksumsResponseTitle = $( '#checksums-popup .sui-box-title' ),
		checksumsResponseBody  = $( '#checksums-popup .sui-box-body .diff-holder' ),
		mailCheckForm          = $( '#wp-live-debug-mail-check' ),
		serverInfo             = $( '#server-info' ),
		serverInfodata         = { 'action': 'wp-live-debug-gather-server-info' },
		mysqlInfo              = $( '#mysql-info' ),
		mysqlInfodata          = { 'action': 'wp-live-debug-gather-mysql-info' },
		phpInfo                = $( '#php-info' ),
		phpInfodata            = { 'action': 'wp-live-debug-gather-php-info' },
		constantsInfo          = $( '#constants-info' ),
		constantsInfodata      = { 'action': 'wp-live-debug-gather-constants-info' },
		cronjobInfodata        = { 'action': 'wp-live-debug-gather-cronjob-info' },
		cronjobInfo            = $( '#cronjob-response' ),
		cronjobRunButton       = 'a[data-do=run-job]',
		cronjobRespHolder      = $( '.hookname' ),
		cronjobSuccess         = $( '#job-success' ),
		cronjobError           = $( '#job-error' ),
		sslInfoData            = { 'action': 'wp-live-debug-get-ssl-information' },
		sslResponse            = $( '#ssl-response' ),
		dirSize                = $( '#dir-size' ),
		dirSizeData            = { 'action': 'wp-live-debug-get-dir-size' }
		dirPerm                = $( '#dir-perm' ),
		dirPermData            = { 'action': 'wp-live-debug-get-dir-perm' }
		genInfo                = $( '#gen-info' ),
		genInfoData            = { 'action': 'wp-live-debug-get-gen-info' };

	// Get General Information
	if ( genInfo.length ) {
		$.post( ajaxurl, genInfoData, function( response ) {
			genInfo.html( response.data.message );
		} );
	}
	// Get Dir Size
	if ( dirPerm.length ) {
		$.post( ajaxurl, dirPermData, function( response ) {
			dirPerm.html( response.data.message );
		} );
	}
	// Get Dir Size
	if ( dirSize.length ) {
		$.post( ajaxurl, dirSizeData, function( response ) {
			dirSize.html( response.data.message );
		} );
	}
	// Get SSL Information
	if ( sslResponse.length ) {
		sslResponse.html( '<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>' );
		$.post( ajaxurl, sslInfoData, function( response ) {
			sslResponse.html( response.data.message );
		} );
	}
	// Run Cronjob
	cronjobInfo.on( 'click', cronjobRunButton, function( e ) {
		var hook = $( this ).data( 'hook' ),
			sig = $( this ).data( 'sig' ),
			data;
		e.preventDefault();
		data = {
			'action': 'wp-live-debug-run-cronjob',
			'hook': hook,
			'sig': sig
		};
		cronjobRespHolder.html( hook );
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				cronjobSuccess.show();
			} else {
				cronjobError.show();
			}
		});
	});
	// Get Cronjobs
	if( cronjobInfo.length ) {
		$.post(	ajaxurl, cronjobInfodata, function( response ) {
			cronjobInfo.html( response.data.message );
		});
	}
	// Server Info
	if ( serverInfo.length ) {
		$.post( ajaxurl, serverInfodata, function( response ) {
			serverInfo.html( response.data.message );
		});
	}
	// MySQL Info
	if ( mysqlInfo.length ) {
		$.post( ajaxurl, mysqlInfodata, function( response ) {
			mysqlInfo.html( response.data.message );
		});
	}
	// PHP Info
	if ( phpInfo.length ) {
		$.post( ajaxurl, phpInfodata, function( response ) {
			phpInfo.html( response.data.message );
		});
	}
	// Constants Info
	if ( constantsInfo.length ) {
		$.post( ajaxurl, constantsInfodata, function( response ) {
			constantsInfo.html( response.data.message );
		});
	}
	// Mail Check
	mailCheckForm.submit( function( e ) {
		var email = $( '#wp-live-debug-mail-check #email' ).val(),
			emailSubject = $( '#wp-live-debug-mail-check #email_subject' ).val(),
			emailMessage = $( '#wp-live-debug-mail-check #email_message' ).val(),
			data;
		e.preventDefault();
		$( '#mail-check-box .sui-box-body' ).html('<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>');
		data = {
			'action': 'wp-live-debug-mail',
			'email': email,
			'email_subject': emailSubject,
			'email_message': emailMessage
		};
		$.post( ajaxurl, data, function( response ) {
			$( '#mail-check-box .sui-box-body' ).html( response.data.message );
		});
	});
	// Checksum Ajax
	if ( checksumsResponse.length ) {
		$.post(	ajaxurl, checksumsData, function( response ) {
			checksumsResponse.html( response.data.message );
		});
	}
	// Checksum Diff
	checksumsResponse.on( 'click', checksumsDiffButton, function( e ) {
		var file = $( this ).data( 'file' ),
			data;
		e.preventDefault();
		data = {
			'action': 'wp-live-debug-view-diff',
			'file': file
		};
		$.post( ajaxurl, data, function( response ) {
			const cp = document.getElementById( 'checksums-popup' );
			const checksum = new A11yDialog( cp );
			checksum.show();
			checksumsResponseTitle.html( file );
			checksumsResponseBody.scrollTop( checksumsResponseBody[0] );
			checksumsResponseBody.html( response.data.message );
		});
	});
	// Debug View
	if ( debugArea.length ) {
		// Scroll the textarea to bottom.
		function scrollDebugAreaToBottom() {
			debugArea.scrollTop( debugArea[0].scrollHeight );
		}
		// Make the initial debug.log read.
		$.post( ajaxurl, refreshData, function( response ) {
			debugArea.html( response );
			scrollDebugAreaToBottom();
		} );
		// Enable / Disable Auto Scroll
		setInterval( function() {
			var checked = refreshToggle.is( ':checked');
			if ( checked ) {
				$.post( ajaxurl, refreshData, function( response ) {
					debugArea.html( response );
					scrollDebugAreaToBottom();
				} );
			}
		}, 2000 );
		// Handle the clear button clicks.
		clearButton.on( 'click', function( e ) {
			e.preventDefault();
			$.post( ajaxurl, clearData, function( response ) {
				debugArea.html( response );
				scrollDebugAreaToBottom();
			} );
		} );
		// Create wp-config backup
		backupButton.on( 'click', function( e ) {
			e.preventDefault();
			$.post( ajaxurl, createBackupData, function( response ) {
				if ( response.success ) {
					window.location.href = window.location.href;
				} else {
					responseHolder.html( response.data.message );
				}
			});
		});
		// Restore wp-config backup
		restoreButton.on( 'click', function( e ) {
			e.preventDefault();
			$.post( ajaxurl, restoreBackupData, function( response ) {
				if ( response.success ) {
					window.location.href = window.location.href;
				} else {
					responseHolder.html( response.data.message );
				}
			});
		});
		// Enable / Disable WP DEDUG
		wpDebugToggle.on( 'change', function( e ) {
			e.preventDefault();
			var checked = $(this).is( ':checked');
			if ( checked ) {
				$.post( ajaxurl, enableWPDebugData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			} else if ( ! checked ) {
				$.post( ajaxurl, disableWPDebugData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			}
		});
		// Enable / Disable SCRIPT DEDUG
		scriptDebugToggle.on( 'change', function( e ) {
			e.preventDefault();
			var checked = $(this).is( ':checked');
			if ( checked ) {
				$.post( ajaxurl, enableScriptDebugData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			} else if ( ! checked ) {
				$.post( ajaxurl, disableScriptDebugData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			}
		});
		// Enable / Disable SAVEQUERIES
		savequeriesToggle.on( 'change', function( e ) {
			e.preventDefault();
			var checked = $(this).is( ':checked');
			if ( checked ) {
				$.post( ajaxurl, enableSavequeriesData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			} else if ( ! checked ) {
				$.post( ajaxurl, disableSavequeriesData, function( response ) {
					if ( ! response.success ) {
						responseHolder.html( response.data.message );
					}
				});
			}
		});
	}
	// Safety Dialog
	if ( safetyPopup.length ) {
		const sp = document.getElementById( 'safety-popup' );
		const safety = new A11yDialog( sp );
		setTimeout( function() {
			safety.show();
		}, 300 );
		acceptRisk.on( 'click', function( e ) {
			e.preventDefault();
			safety.hide();
			$.post( ajaxurl, acceptRiskData, function( response ) {
				if ( ! response.success ) {
					responseHolder.html( response.data.message );
				}
			});
		});
	}
} )( jQuery )
