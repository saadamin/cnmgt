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
    jQuery(function() {
		jQuery(".delete_button").click(function(){
			if (confirm("Are you sure you want to delete?")){
				jQuery('form#delete_person'+jQuery(this).attr('person_id')).submit();
			}
		});
		});
		jQuery(document).ready(function () {
		// Setup - add a text input to each footer cell
		jQuery('#people tfoot th').each(function () {
			var title = jQuery(this).text();
			if(title != 'Action'){
				jQuery(this).html('<input type="text" placeholder="Search ' + title + '" />');
			}
		});
	 
		// DataTable
		var table = jQuery('#people').DataTable({
			initComplete: function () {
				// Apply the search
				this.api()
					.columns()
					.every(function () {
						var that = this;
						jQuery('input', this.footer()).on('keyup change clear', function () {
							if (that.search() !== this.value) {
								that.search(this.value).draw();
							}
						});
					});
			},
		});
	});
})( jQuery );
