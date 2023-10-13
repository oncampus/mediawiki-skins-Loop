$(document).ready(function () {
  var html = $("html");
  var body = $("body");
  var mobileNavBtn = $("#toggle-mobile-menu-btn");
  var navMenu = $("#sidebar-wrapper");
  var mobileSearchBtn = $("#toggle-mobile-search-btn");
  var mobileSearchBar = $("#mobile-searchbar");
  var pageNavBtn = $("#dropdownMenuButton");
  var userNavBtn = $("#user-menu-dropdown");
  var tocNav = $("#toc-nav");
  var tocSpecialNav = $("#toc-specialpages");

  /**
   * Set position of footer for short pages to bottom of the window.
   */

  $("#page-wrapper").css(
    "min-height",
    $(window).height() - $("#footer").height()
  );
  $("footer").animate({ opacity: "1" }, 200);

  /**
   * Accessibility workaround for page menu.
   * Simulates states to make the tab key trigger working on buttons
   */
  pageNavBtn.add(userNavBtn).keydown(function (e) {
    if (e.keyCode === 9) {
      //tab key
      if (!$(this).parent(".dropdown").hasClass("show")) {
        //.tab('show') not working as intended so manually:
        $(this).next(".dropdown-menu").addClass("show");
        $(this).parent(".dropdown").addClass("show");
        $(this).attr("aria-expanded", "true");
      } else {
        $(this).next(".dropdown-menu").removeClass("show");
        $(this).parent(".dropdown").removeClass("show");
        $(this).attr("aria-expanded", "false");
      }
    }
  });
  // if focus is lost after last item
  pageNavBtn
    .add(userNavBtn)
    .next(".dropdown-menu")
    .children()
    .last()
    .focusout(function () {
      $(this).parent(".dropdown-menu").removeClass("show");
      $(this).parent(".dropdown").removeClass("show");
      $(this).attr("aria-expanded", "false");
    });

  /**
   * Toggle visibility of mobile toc menu.
   */

  // user-menu-dropdown

  mobileNavBtn.click(function () {
    navMenu.toggleClass("mobile-sidebar");
    navMenu.toggleClass("d-none");
    navMenu.toggleClass("d-block");
    navMenu.toggleClass("d-sm-none");
    navMenu.toggleClass("d-sm-block");
    navMenu.toggleClass("d-md-none");
    navMenu.toggleClass("d-md-block");
  });
  mobileSearchBtn.click(function () {
    mobileSearchBar.toggleClass("d-none");
    mobileSearchBar.toggleClass("d-block");
    mobileSearchBar.find("input").focus();
  });

  // TOC navigation function
  $(".toc-caret").click(function () {
    $(this).parent().toggleClass("openNode");
    $(this).toggleClass("openCaret");
  });

  // Page audio button
  $("#t2s-button").click(function () {
    $service_url = $("#loopexportrequestlink").attr("href");

    $(this).removeClass("ic-audio").addClass("rotating ic-buffering");
    $("#audio-wrapper")
      .removeClass("col-1")
      .addClass("col-12 col-sm-5 col-lg-4");
    $("#breadcrumb-area")
      .removeClass("col-11")
      .addClass("col-12 col-sm-7 col-lg-8");

    $.ajax({
      url: $service_url,
      cache: false,
      dataType: "html",
    })
      .success(function (data) {
        //console.log(data)
        $("#t2s-audio source").attr("src", data);
        $("#t2s-button").hide();
        const player = new Plyr("#t2s-audio", {
          volume: 1,
          autoplay: false,
			invertTime: true,
          muted: false,
          /*iconUrl:
            mw.config.get("stylepath") +
            "/Loop/node_modules/plyr/dist/plyr.svg", // use svg icons from server, not from cdn
          */
          controls: [
            "play", // Play/pause playback
            "progress", // The progress bar and scrubber for playback and buffering
            "current-time", // The current time of playback
            "mute", // Toggle mute
            "captions", // Toggle captions
            "settings", // Settings menu
            /* optional settings to add */
            //'play-large', // The large play button in the center
            //'restart', // Restart playback
            //'rewind', // Rewind by the seek time (default 10 seconds)
            //'fast-forward', // Fast forward by the seek time (default 10 seconds)
            //'duration', // The full duration of the media
            "volume", // Volume control
            //'pip', // Picture-in-picture (currently Safari only)
            //'airplay', // Airplay (currently Safari only)
            //'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
            //'fullscreen', // Toggle fullscreen
          ],
        });
      })
      .fail(function (xhr, textStatus, errorThrown) {
        //console.log(textStatus + " : " + errorThrown );
      });
  });

  // Page button tooltips
  $(".page-symbol").tooltip({ boundary: "window" });

  // Page button tooltips
  $(".loopeditmode-hint").tooltip({ boundary: "window" });

  // Jump to top button
  $("#page-topjump").click(function () {
    $("html, body").animate({ scrollTop: 0 }, "fast");
  });

  $(window).on("resize", function () {
    resizeTables(false);
  });
  $(document).ready(resizeTables(true));

  var available_width;
  var table_width;
  var table_height;
  var table_ratio;

  function resizeTables(repeat) {
    available_width = $("#mw-content-text").width();
    $(".wikitable").each(function () {
      table_width = $(this).width();
      table_height = $(this).height();
      table_ratio = available_width / table_width;

      if (available_width < table_width && table_ratio < 1) {
        if (!$(this).parent().hasClass("viewport")) {
          $(this).wrap("<div class='viewport'></div>");
        }
        $(this)
          .parent()
          .css({
            transform: "scale(" + table_ratio + ")",
            height: table_height * table_ratio,
          });
      } else {
        $(this).parent(".viewport").css({
          transform: "none",
          height: "auto",
        });
      }
      $(this).show();
    });
    if (repeat == true) {
      resizeTables(false);
    }
  }

  $(".loopzoom").each(function () {
    var zoom_id = $(this).attr("id");
    var html = $(this).get(0).innerHTML;

    $("." + zoom_id + "-modal .modal-content").append(html);
  });

  /* Swipe Functionality - Adapted code, original by Philipp Guttmann */
  var SwipeArea = document.querySelector("body");
  var SwipeWidth = window.innerWidth / 2;
  var SwipeWidthMax = 320;

  if (SwipeWidth > SwipeWidthMax) {
    SwipeWidth = SwipeWidthMax;
  }

  var SwipeStartX = null;
  var SwipeEndX = null;
  var SwipeLengthX = null;

  var SwipeStartY = null;
  var SwipeEndY = null;
  var SwipeLengthY = null;

  SwipeArea.addEventListener("touchstart", SwipeStart, false);
  SwipeArea.addEventListener("touchend", SwipeEnd, false);

  function SwipeStart(evt) {
    SwipeStartX = evt.changedTouches[0].clientX;
    SwipeStartY = evt.changedTouches[0].clientY;
  }

  function SwipeEnd(evt) {
    SwipeEndX = evt.changedTouches[0].clientX;
    SwipeEndY = evt.changedTouches[0].clientY;
    SwipeLengthX = SwipeStartX - SwipeEndX;
    SwipeLengthY = SwipeStartY - SwipeEndY;

    if (Math.abs(SwipeLengthX) > Math.abs(SwipeLengthY)) {
      if (SwipeLengthX > SwipeWidth && mw.config.exists("jsSwipeNext")) {
        window.location.href = mw.config.get("jsSwipeNext");
      } else if (
        SwipeLengthX < -SwipeWidth &&
        mw.config.exists("jsSwipePrev")
      ) {
        window.location.href = mw.config.get("jsSwipePrev");
      }
    }
  }

  /* Featherlight */
  $(".responsive-image").each(function (index) {
    if (
      !$(this).hasClass("image-editmode") &&
      !$(this).parent().parent().hasClass("modal-content")
    ) {
      let url = $(this).attr("src");
      //downsized images receive a featherlight box for the original pic
      if (url.indexOf("/thumb/") >= 0) {
        url = url.substr(0, url.lastIndexOf("/")).replace("/thumb/", "/");
      }

      $(this).wrap('<a href="' + url + '"></a>');
      $(this).featherlight(url);
    }
  });

  $('.loop_consent_agree').click(function() {
    if (!document.cookie.match(/^(.*;)?\s*LoopConsent\s*=\s*[^;]+(.*)?$/)) {
      let date = new Date();
      date.setTime(date.getTime() + 24 * 60 * 60 * 1000 * 365);
      document.cookie =
        "LoopConsent=true; expires=" + date.toUTCString() + "; path=/";
      window.location.search = "consent=true";
    }
  });

  function calcAspectRatio() {
    $(".loop_consent").each(function () {
      let width = $(this).width();
      $(this).height(Math.round((width / 16) * 9));
    });
  }

  $(".loop_consent").each(function () {
    calcAspectRatio();
  });

  $(window).resize(function () {
    calcAspectRatio();
  });

  /**
   * @source https://www.mediawiki.org/wiki/Snippets/Open_specific_links_in_new_window
   * @version 2018-09-15 edited 2020-12-03
   */
  $(function () {
    $("#mw-content-text").on("click", ".newtab > a", function () {
      var otherWindow = window.open();
      otherWindow.opener = null;
      otherWindow.location = this;
      return false;
    });
  });
});
