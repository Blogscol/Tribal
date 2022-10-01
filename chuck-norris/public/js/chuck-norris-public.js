(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	//Add favorite
	jQuery(document).on('click', '.add', e => {
		e.preventDefault();

		let options = {
			theme: "sk-rect",
			message: 'Cargando, espere un momento',
			textColor: "white"
		};

		HoldOn.open(options);

		let button = jQuery(e.target);

		jQuery.ajax({
			type: 'POST',
			url: chuck_norris_ajax_vars.ajax_url,
			data: {
				action: 'save_favorite',
				id: button.data("id"),
				icon_url: button.data("icon_url"),
				url: button.data("url"),
				value: button.data("value"),
			},
			success: function (response) {
				HoldOn.close();

				let data = null;
				try {
					data = JSON.parse(response);
				} catch (e) {}

				if (data != null)
				{
					if(data.type == "success")
					{
						swal(data.titulo, data.mensaje, data.type);
					}
					else
					{
						swal(data.titulo, data.mensaje, data.type);
					}
				}
				else
				{
					swal("Ha habido algún problema.", '', "warning");
				}
			},
			error: function (response) {
				HoldOn.close();
				swal("Ha habido algún problema.", '', "warning");
			},
			fail: function (response) {
				HoldOn.close();
				swal("Ha habido algún problema.", '', "warning");
			}
		});
	});

	//Remove favorite
	jQuery(document).on('click', '.remove', e => {
		e.preventDefault();

		let options = {
			theme: "sk-rect",
			message: 'Cargando, espere un momento',
			textColor: "white"
		};

		HoldOn.open(options);

		let button = jQuery(e.target);

		jQuery.ajax({
			type: 'POST',
			url: chuck_norris_ajax_vars.ajax_url,
			data: {
				action: 'remove_favorite',
				id: button.data("id"),
			},
			success: function (response) {
				HoldOn.close();

				let data = null;
				try {
					data = JSON.parse(response);
				} catch (e) {}

				if (data != null)
				{
					if(data.type == "success")
					{
						swal(data.titulo, data.mensaje, data.type);

						jQuery('#' + button.data("id")).css("display", "none");
					}
					else
					{
						swal(data.titulo, data.mensaje, data.type);
					}
				}
				else
				{
					swal("Ha habido algún problema.", '', "warning");
				}
			},
			error: function (response) {
				HoldOn.close();
				swal("Ha habido algún problema.", '', "warning");
			},
			fail: function (response) {
				HoldOn.close();
				swal("Ha habido algún problema.", '', "warning");
			}
		});
	});
})( jQuery );
