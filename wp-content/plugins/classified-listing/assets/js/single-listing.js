(function ($) {
  "user strict";

  /**
   * Listing gallery class.
   */
  var RtclListingGallery = function RtclListingGallery($slider_wrapper, args) {
    var _this$sliderThumbs;
    this.$sliderWrapper = $slider_wrapper;
    this.$slider = $(".rtcl-slider", this.$sliderWrapper);
    this.$sliderThumbs = $(".rtcl-slider-nav", this.$sliderWrapper);
    if (!this.$slider.length) {
      return;
    }
    this.slider = this.$slider.get(0);
    this.swiperSlider = this.slider.swiper || null;
    this.sliderThumbs = this.$sliderThumbs.get(0);
    this.swiperThumbsSlider = (this === null || this === void 0 ? void 0 : (_this$sliderThumbs = this.sliderThumbs) === null || _this$sliderThumbs === void 0 ? void 0 : _this$sliderThumbs.swiper) || null;
    this.$slider_images = $(".rtcl-slider-item", this.$slider);
    this.settings = Object.assign({}, rtcl_single_listing_localized_params || {}, this.$sliderWrapper.data("options") || {});
    this.args = args || {};
    this.options = Object.assign({}, this.args, this.settings.slider_options);
    //if rtl value was not passed and html is in rtl..enable it by default.
    if (this.options.rtl && $("html").attr("dir") === "rtl") {
      this.options.rtl = true;
    }

    // Pick functionality to initialize...
    this.slider_enabled = "function" === typeof Swiper && this.settings.slider_enabled;
    this.zoom_enabled = $.isFunction($.fn.zoom) && this.settings.zoom_enabled;
    this.photoswipe_enabled = typeof PhotoSwipe !== "undefined" && this.settings.photoswipe_enabled;

    // ...also taking args into account.
    if (args) {
      this.slider_enabled = false === args.slider_enabled ? false : this.slider_enabled;
      this.zoom_enabled = false === args.zoom_enabled ? false : this.zoom_enabled;
      this.photoswipe_enabled = false === args.photoswipe_enabled ? false : this.photoswipe_enabled;
    }
    if (1 === this.$slider_images.length) {
      this.slider_enabled = false;
    }
    this.initSlider = function () {
      if (!this.slider_enabled) {
        return;
      }
      var $slider = this.$slider;
      var $sliderThumbs = this.$sliderThumbs;
      if (this.options.rtl) {
        $slider.attr("dir", "rtl");
        $sliderThumbs.attr("dir", "rtl");
      }
      var that = this;
      var swiperThumbsSlider;
      if (this.swiperThumbsSlider) {
        swiperThumbsSlider = this.swiperThumbsSlider;
        this.swiperThumbsSlider.update();
      } else {
        swiperThumbsSlider = new Swiper(this.sliderThumbs, {
          watchSlidesVisibility: true,
          spaceBetween: 5,
          navigation: {
            nextEl: $sliderThumbs.find(".swiper-button-next").get(0),
            prevEl: $sliderThumbs.find(".swiper-button-prev").get(0)
          },
          breakpoints: {
            0: {
              slidesPerView: 3
            },
            576: {
              slidesPerView: 4
            },
            768: {
              slidesPerView: 5
            }
          }
        });
        this.swiperThumbsSlider = swiperThumbsSlider;
      }
      var swiperSlider;
      var swiperSliderDefaultParams = {
        navigation: {
          nextEl: $slider.find(".swiper-button-next").get(0),
          prevEl: $slider.find(".swiper-button-prev").get(0)
        },
        on: {
          init: function init(e) {
            if (e.slides[e.activeIndex].querySelector("iframe")) {
              e.el.classList.add("active-video-slider");
            }
          }
        }
      };
      if (this.$sliderThumbs.length) {
        swiperSliderDefaultParams.thumbs = {
          swiper: swiperThumbsSlider
        };
      }
      var swiperSliderParams = Object.assign({}, swiperSliderDefaultParams, this.options);
      if (this.swiperSlider) {
        swiperSlider = this.swiperSlider;
        this.swiperSlider.parents = swiperSliderParams;
        this.swiperSlider.update();
      } else {
        swiperSlider = new Swiper(this.slider, swiperSliderParams);
        this.swiperSlider = swiperSlider;
      }
      swiperSlider.on("slideChange", function (e) {
        that.initZoomForTarget(swiperSlider.activeIndex);
        swiperSlider.slides.forEach(function (slide, index) {
          if (index !== swiperSlider.activeIndex) {
            var $iframes = $(slide).find("iframe");
            if ($iframes.length) {
              $iframes.each(function () {
                var src = $(this).attr("src");
                $(this).attr("src", src);
              });
            }
          }
        });
        if (e.slides[e.activeIndex].querySelector("iframe")) {
          e.el.classList.add("active-video-slider");
        } else {
          e.el.classList.remove("active-video-slider");
        }
      });
    };
    this.imagesLoaded = function () {
      var that = this;
      if ($.fn.imagesLoaded.done) {
        this.$sliderWrapper.trigger("rtcl_gallery_loading", this);
        this.$sliderWrapper.trigger("rtcl_gallery_loaded", this);
        return;
      }
      this.$sliderWrapper.imagesLoaded().progress(function (instance, image) {
        that.$sliderWrapper.trigger("rtcl_gallery_loading", [that]);
      }).done(function (instance) {
        that.$sliderWrapper.trigger("rtcl_gallery_loaded", [that]);
      });
    };
    this.initZoom = function () {
      if (!this.zoom_enabled) {
        return;
      }
      this.initZoomForTarget(0);
    };
    this.initZoomForTarget = function (sliderIndex) {
      if (!this.zoom_enabled) {
        return;
      }
      var galleryWidth = this.$slider.width(),
        zoomEnabled = false,
        zoomTarget = this.$slider_images.eq(sliderIndex);
      $(zoomTarget).each(function (index, element) {
        var image = $(element).find("img");
        if (parseInt(image.data("large_image_width")) > galleryWidth) {
          zoomEnabled = true;
          return false;
        }
      });

      // But only zoom if the img is larger than its container.
      if (zoomEnabled) {
        var zoom_options = $.extend({
          touch: false
        }, this.settings.zoom_options);
        if ("ontouchstart" in document.documentElement) {
          zoom_options.on = "click";
        }
        zoomTarget.trigger("zoom.destroy");
        zoomTarget.zoom(zoom_options);
        this.$sliderWrapper.on("rtcl_gallery_init_zoom", this.initZoom);
      }
    };
    this.initPhotoswipe = function () {
      if (!this.photoswipe_enabled) {
        return;
      }
      this.$slider.prepend('<a href="#" class="rtcl-listing-gallery__trigger"><i class="rtcl-icon-search"></i></i> </a>');
      this.$slider.on("click", ".rtcl-listing-gallery__trigger", this.openPhotoswipe.bind(this));
    };
    this.getGalleryItems = function () {
      var $slides = this.$slider_images,
        items = [];
      if ($slides.length > 0) {
        $slides.each(function (i, el) {
          var img = $(el).find("img");
          if (img.length) {
            var large_image_src = img.attr("data-large_image"),
              large_image_w = img.attr("data-large_image_width"),
              large_image_h = img.attr("data-large_image_height"),
              item = {
                src: large_image_src,
                w: large_image_w,
                h: large_image_h,
                title: img.attr("data-caption") ? img.attr("data-caption") : img.attr("title")
              };
            items.push(item);
          }
        });
      }
      return items;
    };
    this.openPhotoswipe = function (e) {
      e.preventDefault();
      var pswpElement = $(".pswp")[0],
        items = this.getGalleryItems(),
        eventTarget = $(e.target),
        clicked;
      if (eventTarget.is(".rtcl-listing-gallery__trigger") || eventTarget.is(".rtcl-listing-gallery__trigger img")) {
        clicked = this.$slider.find(".swiper-slide.swiper-slide-active");
      } else {
        clicked = eventTarget.closest(".rtcl-slider-item");
      }
      var options = $.extend({
        index: $(clicked).index()
      }, this.settings.photoswipe_options);

      // Initializes and opens PhotoSwipe.
      var photoswipe = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
      photoswipe.init();
    };
    this.start = function () {
      var that = this;
      this.$sliderWrapper.on("rtcl_gallery_loaded", this.init.bind(this));
      setTimeout(function () {
        that.imagesLoaded();
      }, 1);
    };
    this.init = function () {
      this.initSlider();
      this.initZoom();
      this.initPhotoswipe();
    };
    this.start();
  };
  $.fn.rtcl_listing_gallery = function (args) {
    new RtclListingGallery(this, args);
    return this;
  };
  $(".rtcl-slider-wrapper").each(function () {
    $(this).rtcl_listing_gallery();
  });
})(jQuery);
