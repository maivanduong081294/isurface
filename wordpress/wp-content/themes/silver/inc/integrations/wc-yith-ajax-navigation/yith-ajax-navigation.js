jQuery(function ($) {
  $(document).on('yith-wcan-ajax-filtered', function () {
    var $container = jQuery('.shop-container')

    Silver.attach('lazy-load-images', $container)
    Silver.attach('quick-view', $container)
    Silver.attach('tooltips', $container)
    Silver.attach('add-qty', $container)
    Silver.attach('wishlist', $container)
    Silver.attach('equalize-box', $container)
  })
})
