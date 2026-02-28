(function($) {
  'use strict';
  $(function() {
    var body = $('body');
    var contentWrapper = $('.content-wrapper');
    var scroller = $('.container-scroller');
    var footer = $('.footer');
    var sidebar = $('.sidebar');

    //Add active class to nav-link based on url dynamically
    //Active class can be hard coded directly in html file also as required

    function normalizePath(pathname) {
      if (!pathname) return '/';
      var normalized = pathname.replace(/\/+$/, '');
      return normalized === '' ? '/' : normalized;
    }

    function isSamePath(href) {
      if (!href || href === '#') {
        return false;
      }

      try {
        var parsed = new URL(href, window.location.origin);
        return normalizePath(parsed.pathname) === currentPath;
      } catch (e) {
        return false;
      }
    }

    function addActiveClass(element) {
      var href = element.attr('href');

      if (!isSamePath(href)) {
        return;
      }

      element.parents('.nav-item').last().addClass('active');
      if (element.parents('.sub-menu').length) {
        element.closest('.collapse').addClass('show');
        element.addClass('active');
      }
      if (element.parents('.submenu-item').length) {
        element.addClass('active');
      }
    }

    var currentPath = normalizePath(window.location.pathname);
    $('.nav li a', sidebar).each(function() {
      var $this = $(this);
      addActiveClass($this);
    })

    $('.horizontal-menu .nav li a').each(function() {
      var $this = $(this);
      addActiveClass($this);
    })

    $(".aside-toggler").on("click", function () {
      $(".mail-sidebar,.chat-list-wrapper").toggleClass("menu-open");
    });


    //Change sidebar and content-wrapper height
    applyStyles();

    function applyStyles() {
      //Applying perfect scrollbar
      if (!body.hasClass("rtl")) {
        if ($('.settings-panel .tab-content .tab-pane.scroll-wrapper').length) {
          const settingsPanelScroll = new PerfectScrollbar('.settings-panel .tab-content .tab-pane.scroll-wrapper');
        }
        if ($('.chats').length) {
          const chatsScroll = new PerfectScrollbar('.chats');
        }
        if (body.hasClass("sidebar-fixed")) {
          var fixedSidebarScroll = new PerfectScrollbar('#sidebar .nav');
        }
      }
    }

    $('[data-toggle="minimize"]').on("click", function() {
      if ((body.hasClass('sidebar-toggle-display')) || (body.hasClass('sidebar-absolute'))) {
        body.toggleClass('sidebar-hidden');
      } else {
        body.toggleClass('sidebar-icon-only');
      }
    });

    //checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');

    //fullscreen
    $("#fullscreen-button").on("click", function toggleFullScreen() {
      if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
          document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
          document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (document.documentElement.msRequestFullscreen) {
          document.documentElement.msRequestFullscreen();
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
    })
    // proBanner: apenas aplica se o elemento existir no DOM
    var proBannerEl = document.querySelector('#proBanner');
    var navbarEl    = document.querySelector('.navbar');
    var pageWrapper = document.querySelector('.page-body-wrapper');

    if (proBannerEl) {
      if ($.cookie('connectplus-free-banner') != "true") {
        proBannerEl.classList.add('d-flex');
        if (navbarEl) navbarEl.classList.remove('fixed-top');
      } else {
        proBannerEl.classList.add('d-none');
        if (navbarEl) navbarEl.classList.add('fixed-top');
      }

      if (navbarEl && $(navbarEl).hasClass('fixed-top')) {
        if (pageWrapper) pageWrapper.classList.remove('pt-0');
        navbarEl.classList.remove('pt-5');
      } else if (navbarEl) {
        if (pageWrapper) pageWrapper.classList.add('pt-0');
        navbarEl.classList.add('pt-5');
        navbarEl.classList.add('mt-3');
      }

      var bannerClose = document.querySelector('#bannerClose');
      if (bannerClose) {
        bannerClose.addEventListener('click', function() {
          proBannerEl.classList.add('d-none');
          proBannerEl.classList.remove('d-flex');
          if (navbarEl) {
            navbarEl.classList.remove('pt-5');
            navbarEl.classList.add('fixed-top');
            navbarEl.classList.remove('mt-3');
          }
          if (pageWrapper) pageWrapper.classList.add('proBanner-padding-top');
          var date = new Date();
          date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
          $.cookie('connectplus-free-banner', "true", { expires: date });
        });
      }
    }
  });
})(jQuery);