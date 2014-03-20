/**
 *	Post Fetcher
 *	Requires: jQuery
 */
(function($) {
	function postFetcher( options ) {
		// INIT
		var settings = $.extend({
			postType: 'post',
			filterType: 'category',
			termSlug: '',
			contentContainerSelector: false,
		}, options );

		// EVENTS
		$.post(post_fetcher_data.ajaxurl, {
			dataType: "jsonp",
			action: 'fetch_posts',
			nonce: post_fetcher_data.nonce,
			posttype: settings.postType,
			filtertype: settings.filterType,
			termslug: settings.termSlug
		}, function(response) {
			if ( response.success === true ) {
				settings.contentContainerSelector.empty(); // remove all content from desired content area
				settings.contentContainerSelector.html( response.html ); // insert new content.
			} else {
				
			}
		});
	}
}( jQuery )); // post_fetcher()
