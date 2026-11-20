/* global flatsome_infinite_scroll, Packery, ga */

;(function ($) {
  $.fn.silverInfiniteScroll = function () {
    return this.each(function () {
      var isBlog = flatsome_infinite_scroll.context === 'blog'
      var $container, paginationNext, append, hideNav, viewMoreButton

      if (isBlog) {
        // Blog archives: posts are either articles right in #post-list
        // (Normal / Inline layouts) or the columns of a blog_posts row
        // (List / 2 / 3 columns, item styles).
        var $list = jQuery('#post-list')
        var $row = $list.children('.row').filter(function () {
          return jQuery(this).children('.post-item').length > 0
        }).last()
        $container = $row.length ? $row : $list
        if ($container.hasClass('slider')) return
        paginationNext = '#post-list .nav-pagination li a.next'
        append = $row.length ? '#post-list > .row > .post-item' : '#post-list > article'
        hideNav = '#post-list .page-numbers.nav-pagination'
        viewMoreButton = jQuery('button.view-more-button.posts-archive')
        // New articles go at the end of #post-list: keep the button below them.
        $list.after(viewMoreButton.closest('.view-more-wrap'))
      } else {
        $container = jQuery('.shop-container .products')
        paginationNext = '.woocommerce-pagination li a.next'
        append = '.shop-container .product'
        hideNav = '.archive .woocommerce-pagination'
        viewMoreButton = jQuery('button.view-more-button.products-archive')
      }

      if ($container.length === 0 ||
        jQuery(paginationNext).length === 0 ||
        $container.hasClass('ux-infinite-scroll-js-attached')) {
        return
      }

      var byButton = flatsome_infinite_scroll.type === 'button'
      var isMasonry = isBlog
        ? $container.is('[data-packery-options], .has-packery')
        : flatsome_infinite_scroll.list_style === 'masonry'
      // Set packery instance as outlayer when masonry is set.
      var outlayer = isMasonry && window.Packery ? Packery.data($container[0]) : false

      var $infiScrollContainer = $container.infiniteScroll({
        path: paginationNext,
        append: append,
        checkLastPage: true,
        status: '.page-load-status',
        hideNav: hideNav,
        button: '.view-more-button',
        history: flatsome_infinite_scroll.history,
        historyTitle: true,
        debug: false,
        outlayer: outlayer,
        scrollThreshold: parseInt(flatsome_infinite_scroll.scroll_threshold)
      })

      if (byButton) {
        viewMoreButton.removeClass('hidden')
        $infiScrollContainer.infiniteScroll('option', {
          scrollThreshold: false,
          loadOnScroll: false
        })
      }

      $infiScrollContainer.on('load.infiniteScroll', function (event, response, path) {
        silverInfiniteScroll.attachBehaviors(response)
      })

      $infiScrollContainer.on('request.infiniteScroll', function (event, path) {
        if (byButton) viewMoreButton.addClass('loading').attr('aria-busy', 'true')
      })

      // No next page left: no button.
      $infiScrollContainer.on('last.infiniteScroll', function () {
        if (byButton) viewMoreButton.closest('.view-more-wrap').remove()
      })

      $infiScrollContainer.on('append.infiniteScroll', function (event, response, path, items) {
        jQuery(document).trigger('silver-infiniteScroll-append', [response, path, items])
        if (byButton) viewMoreButton.removeClass('loading').removeAttr('aria-busy')

        // Fix Safari bug
        jQuery(items).find('img').each(function (index, img) {
          img.outerHTML = img.outerHTML
        })

        // Load fragments and init_handling_after_ajax for new items.
        jQuery(document).trigger('yith_wcwl_reload_fragments')
        jQuery(document).trigger('silver-equalize-box')

        Silver.attach('lazy-load-images', $container)
        silverInfiniteScroll.animateNewItems(items)

        if (isMasonry && $infiScrollContainer.packery) {
          setTimeout(function () {
            $infiScrollContainer.imagesLoaded(function () {
              $infiScrollContainer.packery('layout')
            })
          }, 500)
        }

        if (window.ga && ga.loaded && typeof ga === 'function') {
          var link = document.createElement('a')
          link.href = path
          ga('set', 'page', link.pathname)
          ga('send', 'pageview')
        }
      })

      var silverInfiniteScroll = {
        attachBehaviors: function (response) {
          Silver.attach('quick-view', response)
          Silver.attach('tooltips', response)
          Silver.attach('add-qty', response)
          Silver.attach('wishlist', response)
        },
        animateNewItems: function (items) {
          if (isMasonry) return
          jQuery(items).hide().fadeIn(parseInt(flatsome_infinite_scroll.fade_in_duration))
        }
      }

      // Initialize completed.
      $container.addClass('ux-infinite-scroll-js-attached')
    })
  }

  // Doc ready.
  $(function () {
    $(document.body).silverInfiniteScroll()

    $(document).on('facetwp-loaded yith-wcan-ajax-filtered experimental-silver-pjax-request-done', function () {
      $(document.body).silverInfiniteScroll()
    })
  })
})(jQuery)
