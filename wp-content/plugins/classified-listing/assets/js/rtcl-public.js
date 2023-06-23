(function ($) {
  "use strict";

  /* global rtcl */
  // Single listing Comment form
  $("body")
  // Star ratings for comments
  .on("init", "#rating", function () {
    $(".single-rtcl_listing #rating").hide().before('<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>');
  }).on("click", "#respond p.stars a", function () {
    var $star = $(this),
      $rating = $star.closest("#respond").find("#rating"),
      ratingWrap = $rating.parent(".form-group"),
      $container = $star.closest(".stars");
    $rating.val($star.text());
    $star.siblings("a").removeClass("active");
    $star.addClass("active");
    $container.addClass("selected");
    ratingWrap.removeClass("has-danger");
    ratingWrap.find(".with-errors").remove();
    return false;
  }).on("change", ".rtcl-ordering select.orderby", function () {
    $(this).closest("form").submit();
  })
  // single page animate scroll
  .on("click", ".rtcl-animate", function (e) {
    e.preventDefault();
    var position = $($(this).attr("href")).offset();
    $("html,body").stop().animate({
      scrollTop: position.top - 120
    }, 500);
  }).on("input", ".rtcl-password", function () {
    var pass_input = $(this),
      pass = pass_input.val(),
      element_wrap = pass_input.parent(),
      pass_status_wrap = element_wrap.find(".rtcl-pass-strength-result"),
      strength;
    if (!pass_status_wrap.length) {
      pass_status_wrap = $('<div class="rtcl-pass-strength-result" />');
      element_wrap.append(pass_status_wrap);
    }
    pass_status_wrap.removeClass("short bad good strong empty");
    if (!pass || "" === pass.trim() || pass.trim().length < rtcl_validator.pw_min_length) {
      pass_status_wrap.addClass("empty").html("&nbsp;");
      return;
    }
    strength = rtclCheckPasswordStrength(pass);
    switch (strength) {
      case -1:
        pass_status_wrap.addClass("bad").html(rtcl_validator.pwsL10n.unknown);
        break;
      case 1:
      case 2:
        pass_status_wrap.addClass("bad").html(rtcl_validator.pwsL10n.bad);
        break;
      case 3:
      case 4:
        pass_status_wrap.addClass("good").html(rtcl_validator.pwsL10n.good);
        break;
      case 5:
      case 6:
        pass_status_wrap.addClass("strong").html(rtcl_validator.pwsL10n.strong);
        break;
      // case 5:
      //     pass_status_wrap.addClass('short').html(rtcl_validator.pwsL10n.mismatch);
      // break;
      default:
        pass_status_wrap.addClass("short").html(rtcl_validator.pwsL10n["short"]);
    }
  }).on("input focusout", "#rtcl-reg-confirm-password", function () {
    var $confirm_input = $(this);

    /*const promise = new Promise((resolve, reject) => {
        let valid = $confirm_input.attr('aria-invalid') !== undefined && $confirm_input.attr('aria-invalid') != 'true';
        return resolve(valid);
    });
     Promise.all([promise]).then((result) => {
        const $element_wrap = $confirm_input.closest('.confirm-password-wrap');
        const $checkmark = $element_wrap.find('.rtcl-checkmark');
        $checkmark.toggle($confirm_input.val().length && result);
    }).catch((error) => console.log(error));*/

    setTimeout(function () {
      var valid = $confirm_input.attr('aria-invalid') !== undefined && $confirm_input.attr('aria-invalid') != 'true';
      var $element_wrap = $confirm_input.closest('.confirm-password-wrap');
      var $checkmark = $element_wrap.find('.rtcl-checkmark');
      $checkmark.toggle($confirm_input.val().length > 0 && valid);
    }, 100);
  }).on('click', '.rtcl-renew-btn', function (e) {
    e.preventDefault();
    var $self = $(this);
    var listingId = $self.data('id') || 0;
    if (!listingId) {
      toastr.error(rtcl_store.lng.error);
      return false;
    }
    var parentWrap = $self.parents('.rtcl-listing-item');
    if (confirm(rtcl.confirm_text)) {
      $.ajax({
        url: rtcl.ajaxurl,
        type: "POST",
        data: {
          listingId: listingId,
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce,
          action: 'rtcl_ajax_renew_listing'
        },
        beforeSend: function beforeSend() {
          parentWrap.rtclBlock();
        },
        success: function success(res) {
          if (res.success) {
            $self.slideUp();
            parentWrap.find('.rtcl-status-wrap .rtcl-status').html(res.data.status);
            parentWrap.find('.rtcl-expire-wrap .rtcl-expire').html(res.data.expire_at);
            toastr.success(res.data.message);
          } else {
            toastr.error(res.data);
          }
          parentWrap.rtclUnblock();
        },
        error: function error(e) {
          parentWrap.rtclUnblock();
          toastr.error('Server Error.');
        }
      });
    }
    return false;
  });

  // Init Tabs and Star Ratings
  $("#rating").trigger("init");
  $(document).on("click", "#rtcl-resend-verify-link", function (e) {
    e.preventDefault();
    if (confirm(rtcl.re_send_confirm_text)) {
      var login = $(this).data("login"),
        parent = $(this).parent();
      $.ajax({
        url: rtcl.ajaxurl,
        data: {
          action: "rtcl_resend_verify",
          user_login: login,
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce
        },
        type: "POST",
        dataType: "JSON",
        beforeSend: function beforeSend() {
          parent.rtclBlock();
        },
        success: function success(response) {
          parent.rtclUnblock();
          alert(response.data.message);
        },
        error: function error(e) {
          parent.rtclUnblock();
          alert("Server Error!!!");
        }
      });
    }
    return false;
  });
  window.rtcl_make_checkout_request = function (form, callback) {
    var $form = $(form),
      $submitBtn = $("button[type=submit]", $form),
      msgHolder = $("<div class='alert rtcl-response'></div>"),
      data = $form.serialize();
    $.ajax({
      url: rtcl.ajaxurl,
      data: data,
      type: "POST",
      dataType: "JSON",
      beforeSend: function beforeSend() {
        $submitBtn.prop("disabled", true);
        $form.find(".alert.rtcl-response").remove();
        $form.rtclBlock();
      },
      success: function success(response) {
        $submitBtn.prop("disabled", false);
        $form.rtclUnblock();
        var msg = "";
        if (response.success) {
          if (response.success_message.length) {
            response.success_message.map(function (message) {
              msg += "<p>" + message + "</p>";
            });
          }
          if (msg) {
            msgHolder.removeClass("alert-danger").addClass("alert-success").html(msg).appendTo($form);
          }
        } else {
          if (response.error_message.length) {
            response.error_message.map(function (message) {
              msg += "<p>" + message + "</p>";
            });
          }
          if (msg) {
            msgHolder.removeClass("alert-success").addClass("alert-danger").html(msg).appendTo($form);
          }
        }
        if (typeof callback === "function") {
          callback(response);
        } else {
          setTimeout(function () {
            if (response.redirect_url) {
              window.location = response.redirect_url;
            }
          }, 600);
        }
      },
      error: function error(e) {
        $submitBtn.prop("disabled", false);
        $form.rtclUnblock();
        if (typeof callback === "function") {
          callback(e);
        }
      }
    });
  };
  window.rtcl_on_recaptcha_load = function () {
    if (rtcl.recaptcha && rtcl.recaptcha.v === 2) {
      rtcl.recaptcha.response = {};
      var args = {
        sitekey: rtcl.recaptcha.site_key
      };
      // Add reCAPTCHA in login form
      var $loginForms = $("form.rtcl-login-form, form#rtcl-login-form");
      if ($loginForms.length && $.inArray("login", rtcl.recaptcha.on) !== -1) {
        $loginForms.each(function (index, form) {
          var $form = $(form);
          if (!$form.data("reCaptchaId")) {
            if ($form.find("#rtcl-login-g-recaptcha").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find("#rtcl-login-g-recaptcha")[0], args));
            } else if ($form.find(".rtcl-g-recaptcha-wrap").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find(".rtcl-g-recaptcha-wrap")[0], args));
            }
          }
        });
      }

      // Add reCAPTCHA in registration form
      var $regForms = $("form#rtcl-register-form, form.rtcl-register-form");
      if ($regForms.length && $.inArray("registration", rtcl.recaptcha.on) !== -1) {
        $regForms.each(function (index, form) {
          var $form = $(form);
          if (!$form.data("reCaptchaId")) {
            if ($form.find("#rtcl-registration-g-recaptcha").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find("#rtcl-registration-g-recaptcha")[0], args));
            } else if ($form.find(".rtcl-g-recaptcha-wrap").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find(".rtcl-g-recaptcha-wrap")[0], args));
            }
          }
        });
      }

      // Add reCAPTCHA in listing form
      var $submitForm = $("form#rtcl-post-form");
      if ($submitForm.length && $.inArray("listing", rtcl.recaptcha.on) !== -1) {
        if (!$submitForm.data("reCaptchaId")) {
          if ($submitForm.find("#rtcl-listing-g-recaptcha").length) {
            $submitForm.data("reCaptchaId", grecaptcha.render($submitForm.find("#rtcl-listing-g-recaptcha")[0], args));
          } else if ($submitForm.find(".rtcl-g-recaptcha-wrap").length) {
            $submitForm.data("reCaptchaId", grecaptcha.render($submitForm.find(".rtcl-g-recaptcha-wrap")[0], args));
          }
        }
      }

      // Add reCAPTCHA in contact form
      var $contactForms = $("form.rtcl-contact-form, form#rtcl-contact-form");
      if ($contactForms.length && $.inArray("contact", rtcl.recaptcha.on) !== -1) {
        $contactForms.each(function (index, form) {
          var $form = $(form);
          if (!$form.data("reCaptchaId")) {
            if ($form.find("#rtcl-contact-g-recaptcha").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find("#rtcl-contact-g-recaptcha")[0], args));
            } else if ($form.find(".rtcl-g-recaptcha-wrap").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find(".rtcl-g-recaptcha-wrap")[0], args));
            }
          }
        });
      }
      // Add reCAPTCHA in report abuse form
      var $reportForms = $("form.rtcl-report-abuse-form, form#rtcl-report-abuse-form");
      if ($reportForms.length && $.inArray("report_abuse", rtcl.recaptcha.on) !== -1) {
        $reportForms.each(function (index, form) {
          var $form = $(form);
          if (!$form.data("reCaptchaId")) {
            if ($form.find("#rtcl-report-abuse-g-recaptcha").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find("#rtcl-report-abuse-g-recaptcha")[0], args));
            } else if ($form.find(".rtcl-g-recaptcha-wrap").length) {
              $form.data("reCaptchaId", grecaptcha.render($form.find(".rtcl-g-recaptcha-wrap")[0], args));
            }
          }
        });
      }
      $(document).trigger("rtcl_recaptcha_loaded");
    }
  };
  function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
      sURLVariables = sPageURL.split("&"),
      sParameterName,
      i;
    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split("=");
      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : sParameterName[1];
      }
    }
  }
  function equalHeight() {
    $(".rtcl-equal-height").each(function () {
      var $equalItemWrap = $(this),
        equalItems = $equalItemWrap.find(".equal-item");
      equalItems.height("auto");
      if ($(window).width() > 767) {
        var maxH = 0;
        equalItems.each(function () {
          var itemH = $(this).outerHeight();
          if (itemH > maxH) {
            maxH = itemH;
          }
        });
        equalItems.height(maxH + "px");
      } else {
        equalItems.height("auto");
      }
    });
  }

  // On load function
  $(function () {
    /*if ( $('body.rtcl-archive-no-sidebar, body.rtcl-single-no-sidebar').find('.rtcl-wrapper #primary').next('div').length ) {
        $('.rtcl-wrapper #primary').css('width', '66.6666666667%');
    }*/

    $('#rtcl-reg-confirm-password').on("cut copy paste", function (e) {
      e.preventDefault();
    });
    $(".rtcl-delete-listing").on("click", function (e) {
      e.preventDefault();
      if (confirm(rtcl.confirm_text)) {
        var _self = $(this),
          wrapper = _self.parents(".rtcl-listing-item"),
          data = {
            action: "rtcl_delete_listing",
            post_id: parseInt(_self.attr("data-id"), 10),
            __rtcl_wpnonce: rtcl.__rtcl_wpnonce
          };
        if (data.post_id) {
          $.ajax({
            url: rtcl.ajaxurl,
            data: data,
            type: "POST",
            beforeSend: function beforeSend() {
              wrapper.rtclBlock();
            },
            success: function success(data) {
              wrapper.rtclUnblock();
              if (data.success) {
                wrapper.animate({
                  height: 0,
                  opacity: 0
                }, "slow", function () {
                  $(this).remove();
                });
              }
            },
            error: function error() {
              wrapper.rtclUnblock();
            }
          });
        }
      }
      return false;
    });
    $(".rtcl-delete-favourite-listing").on("click", function (e) {
      e.preventDefault();
      if (confirm(rtcl.confirm_text)) {
        var _target = this,
          _self = $(_target),
          data = {
            action: "rtcl_public_add_remove_favorites",
            post_id: parseInt(_self.attr("data-id"), 10),
            __rtcl_wpnonce: rtcl.__rtcl_wpnonce
          };
        if (data.post_id) {
          $.ajax({
            url: rtcl.ajaxurl,
            data: data,
            type: "POST",
            beforeSend: function beforeSend() {
              $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(_self);
            },
            success: function success(res) {
              res.target = _target;
              _self.next(".rtcl-icon-spinner").remove();
              if (res.success) {
                _self.parents(".rtcl-listing-item").animate({
                  height: 0,
                  opacity: 0
                }, "slow", function () {
                  $(this).remove();
                });
              }
              $(document).trigger("rtcl.favorite", res);
            },
            error: function error(e) {
              $(document).trigger("rtcl.favorite.error", {
                action: "remove",
                post_id: data.post_id,
                target: _target
              });
              _self.next(".rtcl-icon-spinner").remove();
            }
          });
        }
      }
      return false;
    });
    $("#rtcl-checkout-form").on("click", 'input[name="pricing_id"]', function (e) {
      if ($(this).val() == 0) {
        $("#rtcl-payment-methods, #rtcl-checkout-submit-btn").slideUp(250);
      } else {
        $("#rtcl-payment-methods, #rtcl-checkout-submit-btn").slideDown(250);
      }
    }).on("change", 'input[name="payment_method"]', function (e) {
      var target_payment_box = $("div.payment_box.payment_method_" + $(this).val());
      if ($(this).is(":checked") && !target_payment_box.is(":visible")) {
        $("#rtcl-checkout-form div.payment_box").filter(":visible").slideUp(250);
        if ($(this).is(":checked")) {
          target_payment_box.slideDown(250);
        }
      }
    });

    // Profile picture upload
    $(".rtcl-media-upload-pp .rtcl-media-action").on("click", "span.add", function () {
      var addBtn = $(this);
      var ppFile = $("<input type='file' style='position:absolute;left:-9999px' />");
      $("body").append(ppFile);
      if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        ppFile.trigger("change");
      } else {
        ppFile.trigger("click");
      }
      ppFile.on("change", function () {
        var fileItem = $(this);
        var pp_wrap = addBtn.parents(".rtcl-media-upload-pp");
        var pp_thumb_holder = $(".rtcl-media-item", pp_wrap);
        var form = new FormData();
        var pp = fileItem[0].files[0];
        var allowed_image_types = rtcl.image_allowed_type.map(function (type) {
          return "image/" + type;
        });
        var max_image_size = parseInt(rtcl.max_image_size);
        if ($.inArray(pp.type, allowed_image_types) !== -1) {
          if (pp.size <= max_image_size) {
            form.append("pp", pp);
            form.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
            form.append("action", "rtcl_ajax_user_profile_picture_upload");
            $.ajax({
              url: rtcl.ajaxurl,
              data: form,
              cache: false,
              contentType: false,
              processData: false,
              type: "POST",
              beforeSend: function beforeSend() {
                pp_wrap.rtclBlock();
              },
              success: function success(response) {
                pp_wrap.rtclUnblock();
                if (!response.error) {
                  pp_wrap.removeClass("no-media").addClass("has-media").parents(".rtcl-profile-picture-wrap").find(".rtcl-gravatar-wrap").hide();
                  pp_thumb_holder.html("<img class='rtcl-thumbnail' src='" + response.data.src + "'/>");
                }
              },
              error: function error(jqXhr, json, errorThrown) {
                pp_wrap.rtclUnblock();
                console.log("error");
              }
            });
          } else {
            alert(rtcl.error_image_size);
          }
        } else {
          alert(rtcl.error_image_extension);
        }
      });
    }).on("click", "span.remove", function () {
      var self = $(this);
      var pp_wrap = self.parents(".rtcl-media-upload-pp");
      var media_holder = $(".rtcl-media-item", pp_wrap);
      if (confirm(rtcl.confirm_text)) {
        $.ajax({
          url: rtcl.ajaxurl,
          data: {
            action: "rtcl_ajax_user_profile_picture_delete",
            __rtcl_wpnonce: rtcl.__rtcl_wpnonce
          },
          type: "POST",
          beforeSend: function beforeSend() {
            pp_wrap.rtclBlock();
          },
          success: function success(response) {
            pp_wrap.rtclUnblock();
            if (!response.error) {
              pp_wrap.removeClass("has-media").addClass("no-media").parents(".rtcl-profile-picture-wrap").find(".rtcl-gravatar-wrap").show();
              media_holder.html("");
            }
          },
          error: function error(jqXhr, json, errorThrown) {
            pp_wrap.rtclUnblock();
            console.log("error");
          }
        });
      }
    });

    // Toggle password fields in user account form
    $("#rtcl-change-password").on("change", function () {
      var $checked = $(this).is(":checked");
      if ($checked) {
        $(".rtcl-password-fields").show().find('input[type="password"]').attr("disabled", false);
      } else {
        $(".rtcl-password-fields").hide().find('input[type="password"]').attr("disabled", "disabled");
      }
    }).trigger("change");

    // Report abuse [on modal closed]
    $("#rtcl-report-abuse-modal").on("hidden.bs.modal", function (e) {
      $("#rtcl-report-abuse-message").val("");
      $("#rtcl-report-abuse-message-display").html("");
      $(this).find(".modal-dialog").removeClass("modal-vertical-centered");
    }).on("shown.bs.modal", function () {
      $(this).find(".modal-dialog").addClass("modal-vertical-centered");
    });

    // Alert users to login (only if applicable)
    $(".rtcl-require-login").on("click", function (e) {
      e.preventDefault();
      alert(rtcl.user_login_alert_message);
    });

    // Contact do email
    $(".rtcl-do-email").on("click", "a", function (e) {
      e.preventDefault();
      var _self = $(this),
        wrap = _self.parents(".rtcl-do-email");
      $("#rtcl-contact-form", wrap).slideToggle("slow");
      return false;
    });

    // Add or Remove from favourites
    $(document).on("click", "a.rtcl-favourites", function (e) {
      e.preventDefault();
      var _target = this,
        _self = $(_target),
        data = {
          action: "rtcl_public_add_remove_favorites",
          post_id: parseInt(_self.attr("data-id"), 10),
          __rtcl_wpnonce: rtcl.__rtcl_wpnonce
        };
      if (data.post_id) {
        $.ajax({
          url: rtcl.ajaxurl,
          data: data,
          type: "POST",
          beforeSend: function beforeSend() {
            $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(_self);
          },
          success: function success(res) {
            res.target = _target;
            _self.next(".rtcl-icon-spinner").remove();
            if (res.success) {
              _self.replaceWith(res.html);
            }
            $(document).trigger("rtcl.favorite", res);
          },
          error: function error(e) {
            $(document).trigger("rtcl.favorite.error", {
              action: "remove",
              post_id: data.post_id,
              target: _target
            });
            _self.next(".rtcl-icon-spinner").remove();
          }
        });
      }
    });

    /**
     * Slider Class.
     */
    var RtclSlider = function RtclSlider($slider) {
      this.$slider = $slider;
      this.slider = this.$slider.get(0);
      this.swiperSlider = this.slider.swiper || null;
      this.defaultOptions = {
        breakpointsInverse: true,
        observer: true,
        navigation: {
          nextEl: this.$slider.find(".swiper-button-next").get(0),
          prevEl: this.$slider.find(".swiper-button-prev").get(0)
        }
      };
      this.slider_enabled = "function" === typeof Swiper;
      this.options = Object.assign({}, this.defaultOptions, this.$slider.data("options") || {});
      this.initSlider = function () {
        if (!this.slider_enabled) {
          return;
        }
        if (this.options.rtl) {
          this.$slider.attr("dir", "rtl");
        }
        if (this.swiperSlider) {
          this.swiperSlider.parents = this.options;
          this.swiperSlider.update();
        } else {
          this.swiperSlider = new Swiper(this.$slider.get(0), this.options);
        }
      };
      this.imagesLoaded = function () {
        var that = this;
        if (!$.isFunction($.fn.imagesLoaded) || $.fn.imagesLoaded.done) {
          this.$slider.trigger("rtcl_slider_loading", this);
          this.$slider.trigger("rtcl_slider_loaded", this);
          return;
        }
        this.$slider.imagesLoaded().progress(function (instance, image) {
          that.$slider.trigger("rtcl_slider_loading", [that]);
        }).done(function (instance) {
          that.$slider.trigger("rtcl_slider_loaded", [that]);
        });
      };
      this.start = function () {
        var that = this;
        this.$slider.on("rtcl_slider_loaded", this.init.bind(this));
        setTimeout(function () {
          that.imagesLoaded();
        }, 1);
      };
      this.init = function () {
        this.initSlider();
      };
      this.start();
    };
    $.fn.rtcl_slider = function () {
      new RtclSlider(this);
      return this;
    };
    $(".rtcl-carousel-slider").each(function () {
      $(this).rtcl_slider();
    });

    // Populate child terms dropdown
    $(".rtcl-terms").on("change", "select", function (e) {
      e.preventDefault();
      var $this = $(this),
        taxonomy = $this.data("taxonomy"),
        parent = $this.data("parent"),
        value = $this.val(),
        slug = $this.find(":selected").attr("data-slug") || "",
        classes = $this.attr("class"),
        termHolder = $this.closest(".rtcl-terms").find("input.rtcl-term-hidden"),
        termValueHolder = $this.closest(".rtcl-terms").find("input.rtcl-term-hidden-value");
      termHolder.val(value).attr("data-slug", slug);
      termValueHolder.val(slug);
      $this.parent().find("div:first").remove();
      if (parent != value) {
        $this.parent().append('<div class="rtcl-spinner"><span class="rtcl-icon-spinner animate-spin"></span></div>');
        var data = {
          action: "rtcl_child_dropdown_terms",
          taxonomy: taxonomy,
          parent: value,
          "class": classes
        };
        $.post(rtcl.ajaxurl, data, function (response) {
          $this.parent().find("div:first").remove();
          $this.parent().append(response);
        });
      }
    });
    var listObj = {
      active: null,
      target: null,
      loc: {
        items: [],
        selected: null,
        parents: [],
        text: rtcl.location_text
      },
      cat: {
        items: [],
        selected: null,
        parents: [],
        text: rtcl.category_text
      }
    };
    $(".rtcl-widget-search-form .rtcl-search-input-category").on("click", function () {
      listObj.active = "cat";
      listObj.target = $(this);
      var modal = new RtclModal({
        footer: false,
        wrapClass: "no-heading"
      });
      if (!listObj.cat.items.length) {
        $.ajax({
          url: rtcl.ajaxurl,
          type: "POST",
          data: {
            action: "rtcl_get_all_cat_list_for_modal"
          },
          beforeSend: function beforeSend() {
            modal.addModal().addLoading();
          },
          success: function success(response) {
            modal.removeLoading();
            if (response.success) {
              listObj.cat.items = response.categories;
              listObj.cat.selected = null;
              listObj.cat.parent = null;
              modal.content(generate_list());
            }
          },
          error: function error(e) {
            modal.removeLoading();
            modal.content(rtcl_validator.server_error);
          }
        });
      } else {
        modal.addModal();
        modal.content(generate_list());
      }
    });
    $(".rtcl-widget-search-form .rtcl-search-input-location").on("click", function () {
      listObj.active = "loc";
      listObj.target = $(this);
      var modal = new RtclModal({
        footer: false,
        wrapClass: "no-heading"
      });
      if (!listObj.loc.items.length) {
        $.ajax({
          url: rtcl.ajaxurl,
          type: "POST",
          data: {
            action: "rtcl_get_all_location_list_for_modal"
          },
          beforeSend: function beforeSend() {
            modal.addModal().addLoading();
          },
          success: function success(response) {
            modal.removeLoading();
            if (response.success) {
              listObj.loc.items = response.locations;
              listObj.loc.selected = null;
              listObj.loc.parent = null;
              modal.content(generate_list());
            } else {
              modal.content(rtcl_validator.server_error);
            }
          },
          error: function error(e) {
            modal.removeLoading();
            modal.content(rtcl_validator.server_error);
          }
        });
      } else {
        modal.addModal();
        modal.content(generate_list());
      }
    });
    var autocomplete_item = $(".rtcl-widget-search-form .rtcl-autocomplete");
    if ($.fn.autocomplete && autocomplete_item.length) {
      autocomplete_item.autocomplete({
        minChars: 2,
        search: function search(event, ui) {
          if (!$(event.target).parent().find(".rtcl-icon-spinner").length) {
            $("<span class='rtcl-icon-spinner animate-spin'></span>").insertAfter(event.target);
          }
        },
        response: function response(event, ui) {
          $(event.target).parent().find(".rtcl-icon-spinner").remove();
        },
        source: function source(req, response) {
          req.location_slug = rtcl.rtcl_location || "";
          req.category_slug = rtcl.rtcl_category || "";
          req.type = $(this.element).data("type") || "listing";
          req.action = "rtcl_inline_search_autocomplete";
          $.ajax({
            dataType: "json",
            type: "POST",
            url: rtcl.ajaxurl,
            data: req,
            success: response
          });
        },
        select: function select(event, ui) {
          var _self = $(event.target);
          _self.next("input").val(ui.item.target);
        }
      }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li />").data("item.autocomplete", item).append(item.label).appendTo(ul);
      };
    }
    $(".rtcl-ajax-load").each(function () {
      var _self = $(this),
        settings = _self.data("settings") || {};
      settings.action = "rtcl_ajax_taxonomy_filter_get_sub_level_html";
      settings.__rtcl_wpnonce = rtcl.__rtcl_wpnonce;
      $.ajax({
        url: rtcl.ajaxurl,
        type: "POST",
        dataType: "json",
        data: settings,
        beforeSend: function beforeSend() {
          _self.rtclBlock();
        },
        success: function success(response) {
          _self.html(response.data).rtclUnblock();
        },
        complete: function complete() {
          _self.rtclUnblock();
        },
        error: function error(request, status, _error) {
          _self.rtclUnblock();
          if (status === 500) {
            console.error("Error while adding comment");
          } else if (status === "timeout") {
            console.error("Error: Server doesn't respond.");
          } else {
            // process WordPress errors
            var wpErrorHtml = request.responseText.split("<p>"),
              wpErrorStr = wpErrorHtml[1].split("</p>");
            console.error(wpErrorStr[0]);
          }
        }
      });
    });
    function findSelectedItemFromListByIds(ids, list) {
      function findSelectedItem(id) {
        if (selectedItem.sub) {
          selectedItem = selectedItem.sub;
        }
        return selectedItem.find(function (item) {
          return id === item.id;
        });
      }
      var selectedItem = list;
      if (ids.length) {
        for (var i = 0; i < ids.length; i++) {
          selectedItem = findSelectedItem(ids[i], selectedItem);
        }
      }
      return selectedItem;
    }
    function generate_list() {
      var type = listObj.active,
        items = listObj[type].items,
        ul = get_list(items);
      var container = $('<div class="rtcl-ui-select-list-wrap"><h4>' + listObj[type].text + '</h4><div class="rtcl-select-action"></div><div class="rtcl-ui-select-list"></div></div>');
      container.find(".rtcl-ui-select-list").append(ul);
      return container;
    }
    function get_list(items) {
      var ul = $("<ul />");
      items.forEach(function (item) {
        var a = $('<a href="javascript:;" />'),
          li = $("<li />");
        if (item.hasOwnProperty("sub")) {
          li.addClass("has-sub");
        }
        if (item.hasOwnProperty("icon")) {
          a.html(item.icon);
        }
        a.append(item.name);
        a.attr("data-item", JSON.stringify(get_safe_term_item(item)));
        li.append(a);
        ul.append(li);
      });
      return ul;
    }
    function get_safe_term_item(item) {
      var safe_item = Object.assign({
        icon: "",
        sub: ""
      }, item);
      delete safe_item["icon"];
      delete safe_item["sub"];
      return safe_item;
    }
    $(document).on("click", ".rtcl-ui-select-list li.has-sub a", function (e) {
      e.preventDefault();
      var type = listObj.active,
        items = listObj[type].items,
        _self = $(this),
        _item = _self.data("item"),
        list = [],
        wrap = _self.parents(".rtcl-ui-select-list-wrap"),
        list_wrap = $(".rtcl-ui-select-list", wrap),
        action = $(".rtcl-select-action", wrap),
        title = $("h4", wrap),
        ul = _self.parents("ul"),
        selectedItemId = parseInt(_item.id, 10),
        selectedItem;
      if (listObj[type].selected) {
        selectedItem = listObj[type].selected.sub.find(function (item) {
          return item.id === selectedItemId;
        });
        listObj[type].parent = listObj[type].selected.id;
      } else {
        selectedItem = items.find(function (item) {
          return item.id === selectedItemId;
        });
      }
      listObj[type].selected = selectedItem;
      if (selectedItem.parent) {
        listObj[type].parents.push(selectedItem.parent);
      }
      if (selectedItem.hasOwnProperty("sub") && selectedItem.sub.length) {
        ul.remove();
        list_wrap.html(get_list(selectedItem.sub));
        var a = $('<a href="javascript:;" />');
        a.append(selectedItem.name);
        a.attr("data-item", JSON.stringify(get_safe_term_item(selectedItem)));
        if (title.find("span").length) {
          title.find("span").html(a);
        } else {
          var wrapItem = $('<span class="rtcl-icon-angle-right rtcl-selected-term-item" />').append(a);
          title.append(wrapItem);
        }
        action.html("<div class='go-back'>" + rtcl.go_back + "</div>");
      }
    }).on("click", ".rtcl-select-action .go-back", function (e) {
      e.preventDefault();
      var type = listObj.active,
        _self = $(this),
        wrap = _self.parents(".rtcl-ui-select-list-wrap"),
        list_wrap = $(".rtcl-ui-select-list", wrap),
        title = $("h4", wrap),
        action = $(".rtcl-select-action", wrap),
        list,
        selectedItem,
        level = 0;
      if (listObj[type].parents.length) {
        selectedItem = findSelectedItemFromListByIds(listObj[type].parents, listObj[type].items);
        list = selectedItem.sub;
        listObj[type].parents.pop();
        listObj[type].selected = selectedItem;
        level = 1;
      } else {
        listObj[type].selected = null;
        list = listObj[type].items;
      }
      list_wrap.html("");
      list_wrap.append(get_list(list));
      if (level) {
        var a = $('<a href="javascript:;" />');
        a.append(selectedItem.name);
        a.attr("data-item", JSON.stringify(get_safe_term_item(selectedItem)));
        if (title.find("span").length) {
          title.find("span").html(a);
        } else {
          var wrapItem = $('<span class="rtcl-icon-angle-right rtcl-selected-term-item" />').append(a);
          title.append(wrapItem);
        }
      } else {
        title.find("span").remove();
        action.find(".go-back").remove();
      }
    }).on("click", ".rtcl-ui-select-list li:not(.has-sub) a, .rtcl-selected-term-item a", function (e) {
      e.preventDefault();
      var _self = $(this),
        _item = _self.data("item") || null;
      if (_item && listObj.target.length) {
        listObj.target.find(".search-input-label").text(_item.name);
        listObj.target.find("input.rtcl-term-field").val(_item.slug);
        $("body > .rtcl-ui-modal").remove(); // TODO need to make this dynamic
        $("body").removeClass("rtcl-modal-open");
        listObj.target.closest("form").submit();
      }
      return false;
    }).on("click", ".ul-list-group.is-parent > ul > li > a", function (e) {
      e.preventDefault();
      var self = $(this),
        li = self.parent("li"),
        parent = li.parent("ul"),
        target = $(".col-md-6.sub-wrapper"),
        wrap = $("<li />"),
        list = li.find(".ul-list-group.is-sub").clone() || "",
        a_clone = self.clone(),
        a = wrap.append(a_clone);
      list.find("ul").prepend(a);
      target.addClass("is-active");
      target.html(list);
      parent.find("> li").removeClass("is-active");
      li.addClass("is-active");
      return false;
    }).on("click", ".rtcl-filter-form .filter-list .is-parent.has-sub .arrow", function (e) {
      e.preventDefault();
      var self = $(this),
        li = self.closest("li"),
        parent = self.closest(".ui-accordion-content"),
        is_ajax_load = parent.hasClass("rtcl-ajax-load"),
        settings = parent.data("settings") || {},
        target = li.find("> ul.sub-list");
      if (li.hasClass("is-open")) {
        target.slideUp(function () {
          li.removeClass("is-open");
        });
      } else {
        if (is_ajax_load && settings.taxonomy && li.hasClass("has-sub") && !li.hasClass("is-loaded")) {
          if (!parent.hasClass("rtcl-loading")) {
            settings.parent = li.data("id") || -1;
            settings.action = "rtcl_ajax_taxonomy_filter_get_sub_level_html";
            $.ajax({
              url: rtcl.ajaxurl,
              type: "POST",
              dataType: "json",
              data: settings,
              beforeSend: function beforeSend() {
                parent.rtclBlock();
              },
              success: function success(response) {
                li.append(response.data);
                parent.rtclUnblock();
                target.slideDown();
                li.addClass("is-open is-loaded");
              },
              complete: function complete() {
                parent.rtclUnblock();
              },
              error: function error(request, status, _error2) {
                parent.rtclUnblock();
                if (status === 500) {
                  console.error("Error while adding comment");
                } else if (status === "timeout") {
                  console.error("Error: Server doesn't respond.");
                } else {
                  // process WordPress errors
                  var wpErrorHtml = request.responseText.split("<p>"),
                    wpErrorStr = wpErrorHtml[1].split("</p>");
                  console.error(wpErrorStr[0]);
                }
              }
            });
          }
        } else {
          target.slideDown();
          li.addClass("is-open");
        }
      }
    }).on("click", "ul.filter-list.is-collapsed li.is-opener, ul.sub-list.is-collapsed li.is-opener, ul.ui-link-tree.is-collapsed li.is-opener", function () {
      $(this).parent("ul").removeClass("is-collapsed").addClass("is-open");
    });
    $(".rtcl-filter-form .ui-accordion-item").on("click", ".ui-accordion-title", function () {
      var self = $(this),
        holder = self.parents(".ui-accordion-item"),
        target = $(".ui-accordion-content", holder);
      if (holder.hasClass("is-open")) {
        target.slideUp(function () {
          holder.removeClass("is-open");
        });
      } else {
        target.slideDown();
        holder.addClass("is-open");
      }
    });
    $(".rtcl-filter-form").on("click", ".filter-submit-trigger", function (e) {
      var r,
        i,
        self = $(this);
      if (!self.is(":checkbox")) {
        e.preventDefault();
        r = self.siblings("input");
        i = r.prop("checked");
        r.prop("checked", !i);
      }
      if (self.is(":radio") || !self.is(":radio") && self.siblings("input").is(":radio")) {
        self.closest("form").submit();
      }
    });

    /* REVEAL PHONE */
    $(".reveal-phone").on("click", function (e) {
      var $this = $(this),
        isMobile = $this.hasClass("rtcl-mobile");
      if (!$this.hasClass("revealed")) {
        e.preventDefault();
        var options = $this.data("options") || {};
        var $numbers = $this.find(".numbers");
        var aPhone = "";
        var wPhone = "";
        if (options.safe_phone && options.phone_hidden) {
          var purePhone = options.safe_phone.replace(rtcl.phone_number_placeholder, options.phone_hidden);
          aPhone = $('<a href="#" />').attr("href", "tel:" + purePhone).text(purePhone);
          $this.attr("data-tel", "tel:" + purePhone);
        }
        if (options.safe_whatsapp_number && options.whatsapp_hidden) {
          var pureWPhone = options.safe_whatsapp_number.replace(rtcl.phone_number_placeholder, options.whatsapp_hidden);
          wPhone = $('<a class="revealed-whatsapp-number" href="#" />').attr("href", "https://wa.me/" + pureWPhone.replace(/\D/g, "").replace(/^0+/, "") + "/?text=" + rtcl.wa_message).html('<i class="rtcl-icon rtcl-icon-whatsapp"></i>').append(pureWPhone);
        }
        $numbers.html(aPhone).append(wPhone);
        $this.addClass("revealed");
      } else {
        if (isMobile) {
          var tel = $this.attr("data-tel");
          if (tel) {
            window.location = tel;
          }
        }
      }
    });
    var option = getUrlParameter("option") || "",
      gateway = getUrlParameter("gateway") || "";
    if (option) {
      $("input[name='pricing_id'][value='" + option + "']").prop("checked", true);
    } else {
      $("input[name='pricing_id'][value='0']").prop("checked", true);
    }
    if (gateway) {
      $("label[for='gateway-" + gateway + "']").trigger("click");
    }
    rtclInitDateField();
  });
  if ($.fn.validate) {
    $("#rtcl-lost-password-form, #rtcl-password-reset-form").each(function () {
      $(this).validate();
    });

    // Check out validation
    $("#rtcl-checkout-form").validate({
      submitHandler: function submitHandler(form) {
        $(document.body).trigger("rtcl_before_checkout_request", [form]);
        rtcl_make_checkout_request(form);
        return false;
      }
    });

    //Login form
    $("form#rtcl-login-form, form.rtcl-login-form").each(function () {
      $(this).validate({
        submitHandler: function submitHandler(form) {
          var $form = $(form);
          // recaptcha v2
          if (rtcl.recaptcha && typeof grecaptcha !== "undefined" && rtcl.recaptcha.on && $.inArray("login", rtcl.recaptcha.on) !== -1) {
            if (rtcl.recaptcha.v === 2 && $form.data("reCaptchaId") !== undefined) {
              var response = grecaptcha.getResponse($form.data("reCaptchaId"));
              var $captcha_msg = $form.find("#rtcl-login-g-recaptcha-message");
              $captcha_msg.html("");
              if (0 === response.length) {
                $captcha_msg.addClass("text-danger").html(rtcl.recaptcha.msg.invalid);
                grecaptcha.reset($form.data("reCaptchaId"));
                return false;
              }
              if ($form.hasClass("rtcl-ajax-login")) {
                submit_form_data_ajax();
                return false;
              }
              return true;
            } else if (rtcl.recaptcha.v === 3) {
              grecaptcha.ready(function () {
                $form.rtclBlock();
                grecaptcha.execute(rtcl.recaptcha.site_key, {
                  action: "login"
                }).then(function (token) {
                  if ($form.hasClass("rtcl-ajax-login")) {
                    submit_form_data_ajax(token);
                    return false;
                  } else {
                    $form.append('<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="' + token + '" />');
                    $form.append('<input type="hidden" name="rtcl-login" value="login" />');
                    $form.off("submit").trigger("submit");
                    return true;
                  }
                });
              });
              return false;
            }
          }
          if ($form.hasClass("rtcl-ajax-login")) {
            submit_form_data_ajax();
            return false;
          } else {
            return true;
          }
          function submit_form_data_ajax(token) {
            var fromData = new FormData(form);
            var temp_user = fromData.get("username").trim();
            var temp_pass = fromData.get("password");
            fromData["delete"]("username");
            fromData["delete"]("password");
            fromData.set("username", rtclCipher(rtcl.__rtcl_wpnonce)(temp_user));
            fromData.set("password", rtclCipher(rtcl.__rtcl_wpnonce)(temp_pass));
            if (token) {
              fromData.set("g-recaptcha-response", token);
            }
            fromData.append("action", "rtcl_login_request");
            fromData.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
            $.ajax({
              url: rtcl.ajaxurl,
              type: "POST",
              dataType: "json",
              cache: false,
              processData: false,
              contentType: false,
              data: fromData,
              beforeSend: function beforeSend() {
                $form.find(".rtcl-error").remove();
                $form.rtclBlock();
              },
              success: function success(res) {
                if (res.success) {
                  toastr.success(res.data.message);
                  $form.append('<div class="rtcl-error alert alert-success" role="alert"><p>' + res.data.message + "</p></div>");
                  $form[0].reset();
                  window.location.reload(true);
                } else {
                  $form.rtclUnblock();
                  toastr.error(res.data);
                  $form.append('<div class="rtcl-error alert alert-danger" role="alert"><p>' + res.data + "</p></div>");
                }
              },
              error: function error() {
                $form.rtclUnblock().append('<div class="rtcl-error alert alert-danger" role="alert"><p>' + rtcl_validator.messages.server_error + "</p></div>");
                toastr.error(rtcl_validator.messages.server_error);
              }
            });
          }
        }
      });
    });

    // Validate registration form
    $("form#rtcl-register-form, form.rtcl-register-form").each(function () {
      $(this).validate({
        submitHandler: function submitHandler(form) {
          var $form = $(form);
          if (rtcl.recaptcha && typeof grecaptcha !== "undefined" && rtcl.recaptcha.on && $.inArray("listing", rtcl.recaptcha.on) !== -1) {
            if (rtcl.recaptcha.v === 2 && $form.data("reCaptchaId") !== undefined) {
              var response = grecaptcha.getResponse($form.data("reCaptchaId"));
              var $captcha_msg = $("#rtcl-registration-g-recaptcha-message");
              $captcha_msg.html("");
              if (0 === response.length) {
                $captcha_msg.addClass("text-danger").html(rtcl.recaptcha.msg.invalid);
                grecaptcha.reset($form.data("reCaptchaId"));
                return false;
              }
              if ($form.hasClass("rtcl-ajax-registration")) {
                submit_form_data_ajax();
                return false;
              }
              return true;
            } else if (rtcl.recaptcha.v === 3) {
              grecaptcha.ready(function () {
                $form.rtclBlock();
                grecaptcha.execute(rtcl.recaptcha.site_key, {
                  action: "registration"
                }).then(function (token) {
                  if ($form.hasClass("rtcl-ajax-registration")) {
                    submit_form_data_ajax(token);
                    return false;
                  } else {
                    $form.append('<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" value="' + token + '" />');
                    $form.append('<input type="hidden" name="rtcl-register" value="register" />');
                    $form.off("submit").trigger("submit");
                    return true;
                  }
                });
              });
              return false;
            }
          }
          if ($form.hasClass("rtcl-ajax-registration")) {
            submit_form_data_ajax();
            return false;
          } else {
            return true;
          }
          function submit_form_data_ajax(recaptcha_token) {
            var fromData = new FormData(form);
            if (recaptcha_token) {
              fromData.append("g-recaptcha-response", recaptcha_token);
            }
            fromData.append("action", "rtcl_registration_request");
            fromData.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
            $.ajax({
              url: rtcl.ajaxurl,
              type: "POST",
              dataType: "json",
              cache: false,
              processData: false,
              contentType: false,
              data: fromData,
              beforeSend: function beforeSend() {
                $form.find(".rtcl-error").remove();
                $form.rtclBlock();
              },
              success: function success(res) {
                $form.rtclUnblock();
                if (res.success) {
                  $form.append('<div class="rtcl-error alert alert-success" role="alert"><p>' + res.data.message + "</p></div>");
                  $form[0].reset();
                  if (res.data.redirect_url && res.data.redirect_utl !== window.location.href) {
                    window.location = res.data.redirect_url + "?t=" + new Date().getTime();
                  }
                } else {
                  $form.append('<div class="rtcl-error alert alert-danger" role="alert"><p>' + res.data + "</p></div>");
                }
              },
              error: function error() {
                $form.rtclUnblock().append('<div class="rtcl-error alert alert-danger" role="alert"><p>' + rtcl_validator.messages.server_error + "</p></div>");
              }
            });
          }
        }
        /*messages: {
            pass2: {
                equalTo: 'ggjggjj'
            }
        }*/
      });
    });

    // Validate report abuse form
    $("form.rtcl-report-abuse-form, form#rtcl-report-abuse-form").each(function () {
      $(this).validate({
        submitHandler: function submitHandler(form) {
          var $form = $(form);
          if (rtcl.recaptcha && typeof grecaptcha !== "undefined" && rtcl.recaptcha.on && $.inArray("report_abuse", rtcl.recaptcha.on) !== -1) {
            if (rtcl.recaptcha.v === 2 && $form.data("reCaptchaId") !== undefined) {
              var response = grecaptcha.getResponse($form.data("reCaptchaId"));
              var $captcha_msg = $form.find("#rtcl-report-abuse-message-display");
              $captcha_msg.html("");
              if (0 === response.length) {
                $captcha_msg.removeClass("text-success").addClass("text-danger").html(rtcl.recaptcha.msg.invalid);
                grecaptcha.reset(rtcl.recaptcha.response["report_abuse"]);
                return false;
              }
              submit_form_data_ajax(response);
              return false;
            } else if (rtcl.recaptcha.v === 3) {
              grecaptcha.ready(function () {
                grecaptcha.execute(rtcl.recaptcha.site_key, {
                  action: "reportAbuse"
                }).then(function (token) {
                  submit_form_data_ajax(token);
                });
              });
              return false;
            }
          }
          submit_form_data_ajax();
          return false;
          function submit_form_data_ajax(reCaptchaToken) {
            //Post via AJAX
            var fromData = new FormData(form);
            fromData.append("action", "rtcl_public_report_abuse");
            fromData.append("post_id", rtcl.post_id || 0);
            fromData.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
            if (reCaptchaToken) {
              fromData.append("g-recaptcha-response", reCaptchaToken);
            }
            var targetBtn = $form.find(".btn.btn-primary");
            $.ajax({
              url: rtcl.ajaxurl,
              data: fromData,
              dataType: "json",
              cache: false,
              processData: false,
              contentType: false,
              type: "POST",
              beforeSend: function beforeSend() {
                $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(targetBtn);
              },
              success: function success(response) {
                targetBtn.next(".rtcl-icon-spinner").remove();
                if (response.success) {
                  form.reset();
                  $form.find("#rtcl-report-abuse-message-display").removeClass("text-danger").addClass("text-success").html(response.data.message);
                  setTimeout(function () {
                    $form.parents("#rtcl-report-abuse-modal").modal("hide");
                  }, 1500);
                } else {
                  $form.find("#rtcl-report-abuse-message-display").removeClass("text-success").addClass("text-danger").html(response.data.error);
                }
                if (rtcl.recaptcha && rtcl.recaptcha.v === 2 && $form.data("reCaptchaId") !== undefined) {
                  grecaptcha.reset($form.data("reCaptchaId"));
                }
              },
              error: function error(e) {
                $("#rtcl-report-abuse-message-display").removeClass("text-success").addClass("text-danger").html(e);
                targetBtn.next(".rtcl-icon-spinner").remove();
              }
            });
          }
        }
      });
    });

    // Validate Listing Contact form
    $("form.rtcl-contact-form, form#rtcl-contact-form").each(function () {
      $(this).validate({
        submitHandler: function submitHandler(form) {
          var $form = $(form);
          var $captcha_msg = $form.find("#rtcl-contact-message-display");
          var recaptchaId = $form.data("reCaptchaId");
          if (rtcl.recaptcha && typeof grecaptcha !== "undefined" && rtcl.recaptcha.on && $.inArray("contact", rtcl.recaptcha.on) !== -1) {
            if (rtcl.recaptcha.v === 2 && recaptchaId !== undefined) {
              var response = grecaptcha.getResponse(recaptchaId);
              $captcha_msg.html("");
              if (0 === response.length) {
                $captcha_msg.removeClass("text-success").addClass("text-danger").html(rtcl.recaptcha.msg.invalid);
                grecaptcha.reset(recaptchaId);
                return false;
              }
              submit_form_data_ajax(response);
              return false;
            } else if (rtcl.recaptcha.v === 3) {
              grecaptcha.ready(function () {
                $form.rtclBlock();
                grecaptcha.execute(rtcl.recaptcha.site_key, {
                  action: "contact"
                }).then(function (token) {
                  $form.rtclUnblock();
                  submit_form_data_ajax(token);
                });
              });
              return false;
            }
          }
          submit_form_data_ajax();
          return false;
          function submit_form_data_ajax(reCaptchaToken) {
            // Post via AJAX
            var fromData = new FormData(form);
            if (reCaptchaToken) {
              fromData.append("g-recaptcha-response", reCaptchaToken);
            }
            fromData.append("action", "rtcl_public_send_contact_email");
            fromData.append("post_id", rtcl.post_id || 0);
            fromData.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
            $.ajax({
              url: rtcl.ajaxurl,
              type: "POST",
              dataType: "json",
              cache: false,
              processData: false,
              contentType: false,
              data: fromData,
              beforeSend: function beforeSend() {
                $form.rtclBlock();
                $captcha_msg.removeClass("d-block").html("");
                $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter($form.find(".btn"));
              },
              success: function success(response) {
                $form.rtclUnblock();
                $form.find(".btn").next(".rtcl-icon-spinner").remove();
                $captcha_msg.addClass("d-block");
                if (response.success) {
                  form.reset();
                  $captcha_msg.removeClass("text-danger").addClass("d-block text-success").html(response.data.message);
                  if ($form.parent().data("hide") !== 0) {
                    setTimeout(function () {
                      $form.slideUp();
                    }, 800);
                  }
                } else {
                  $captcha_msg.removeClass("text-success").addClass("d-block text-danger").html(response.data.error);
                }
                if (rtcl.recaptcha && rtcl.recaptcha.v === 2 && recaptchaId !== undefined) {
                  grecaptcha.reset(recaptchaId);
                }
              },
              error: function error(e) {
                $form.rtclUnblock();
                $captcha_msg.removeClass("text-success").addClass("d-block text-danger").html(e);
                $form.find(".btn").next(".rtcl-icon-spinner").remove();
              }
            });
          }
        }
      });
    });

    // User account form
    $("#rtcl-user-account").validate({
      submitHandler: function submitHandler(form) {
        var $form = $(form),
          targetBtn = $form.find("input[type=submit]"),
          responseHolder = $form.find(".rtcl-response"),
          msgHolder = $("<div class='alert'></div>"),
          fromData = new FormData(form);
        fromData.append("action", "rtcl_update_user_account");
        fromData.append("__rtcl_wpnonce", rtcl.__rtcl_wpnonce);
        $.ajax({
          url: rtcl.ajaxurl,
          data: fromData,
          dataType: "json",
          cache: false,
          processData: false,
          contentType: false,
          type: "POST",
          beforeSend: function beforeSend() {
            $form.addClass("rtcl-loading");
            targetBtn.prop("disabled", true);
            responseHolder.html("");
            $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(targetBtn);
          },
          success: function success(response) {
            targetBtn.prop("disabled", false).next(".rtcl-icon-spinner").remove();
            $form.removeClass("rtcl-loading");
            if (!response.error) {
              $form.find("input[name=pass1]").val("");
              $form.find("input[name=pass2]").val("");
              msgHolder.removeClass("alert-danger").addClass("alert-success").html(response.message).appendTo(responseHolder);
              setTimeout(function () {
                responseHolder.html("");
              }, 1000);
            } else {
              msgHolder.removeClass("alert-success").addClass("alert-danger").html(response.message).appendTo(responseHolder);
            }
          },
          error: function error(e) {
            msgHolder.removeClass("alert-success").addClass("alert-danger").html(e.responseText).appendTo(responseHolder);
            targetBtn.prop("disabled", false).next(".rtcl-icon-spinner").remove();
            $form.removeClass("rtcl-loading");
          }
        });
      }
    });
  }
  window.rtclInitDateField = function () {
    if ($.fn.daterangepicker) {
      $(".rtcl-date").each(function () {
        var input = $(this);
        var options = input.data("options") || {};
        options = rtclFilter.apply('dateRangePickerOptions', options);
        if (Array.isArray(options.invalidDateList) && options.invalidDateList.length) {
          options.isInvalidDate = function (param) {
            return options.invalidDateList.includes(param.format(options.locale.format));
          };
        }
        $(this).daterangepicker(options);
        if (options.autoUpdateInput === false) {
          input.on("apply.daterangepicker", function (ev, picker) {
            if (picker.singleDatePicker) {
              $(this).val(picker.startDate.format(picker.locale.format));
            } else {
              $(this).val(picker.startDate.format(picker.locale.format) + picker.locale.separator + picker.endDate.format(picker.locale.format));
            }
          });
          input.on("cancel.daterangepicker", function (ev, picker) {
            $(this).val("");
          });
        }
      });
    }
  };

  /* Listing - Reveal Phone */
  // On load function
  $(function () {
    $(".rtcl-phone-reveal").on("click", function () {
      if ($(this).hasClass("revealed")) {
        var $link;
        $link = $(this).attr('href');
        if ($link) {
          window.location.href = $link;
        }
      }
      if ($(this).hasClass("not-revealed")) {
        $(this).removeClass("not-revealed").addClass("revealed");
        var phone = $(this).data("phone");
        $(this).find("span").text(phone);
      }
      return false;
    });

    // User page ad listing infinity scroll
    var user_ads_wrapper = $(".rtcl-user-ad-listing-wrapper"),
      pagination;
    if (user_ads_wrapper.length) {
      var wrapper = $(".rtcl-listing-wrapper", user_ads_wrapper);
      pagination = wrapper.data("pagination") || {};
      pagination.disable = false;
      pagination.loading = false;
      $(window).on("scroll load", function () {
        infinite_scroll(wrapper);
      });
    }
    function infinite_scroll(wrapper) {
      var ajaxVisible = user_ads_wrapper.offset().top + user_ads_wrapper.outerHeight(true),
        ajaxScrollTop = $(window).scrollTop() + $(window).height();
      if (ajaxVisible <= ajaxScrollTop && ajaxVisible + $(window).height() > ajaxScrollTop) {
        if (pagination.max_num_pages > pagination.current_page && !pagination.loading && !pagination.disable) {
          var data = {
            action: "rtcl_user_ad_load_more",
            current_page: pagination.current_page,
            max_num_pages: pagination.max_num_pages,
            found_posts: pagination.found_posts,
            posts_per_page: pagination.posts_per_page,
            user_id: rtcl.user_id
          };
          $.ajax({
            url: rtcl.ajaxurl,
            data: data,
            type: "POST",
            beforeSend: function beforeSend() {
              pagination.loading = true;
              $('<span class="rtcl-icon-spinner animate-spin"></span>').insertAfter(wrapper);
            },
            success: function success(response) {
              wrapper.next(".rtcl-icon-spinner").remove();
              pagination.loading = false;
              pagination.current_page = response.current_page;
              if (pagination.max_num_pages === response.current_page) {
                pagination.disable = true;
              }
              if (response.complete && response.html) {
                wrapper.append(response.html);
              }
            },
            error: function error(e) {
              pagination.loading = false;
              wrapper.next(".rtcl-icon-spinner").remove();
            }
          });
        }
      }
    }
  });

  // Window load and resize function
  $(window).on("resize load", equalHeight).on("load", function () {
    $(".rtcl-range-slider-input").on("input", function () {
      var field_wrap = $(this).parent();
      field_wrap.find("span.rtcl-range-value").text(this.value);
    });
  });

  //Favourite Icon Update
  //=========================
  $(document).on("rtcl.favorite", function (e, data) {
    var $favCount = $(".rt-el-header-favourite-count").first();
    var $favCountAll = $(".rt-el-header-favourite-count");
    var favCountVal = parseInt($favCount.text(), 10);
    favCountVal = isNaN(favCountVal) ? 0 : favCountVal;
    if ("added" === data.action) {
      favCountVal++;
      $favCountAll.text(favCountVal);
    } else if ("removed" === data.action) {
      favCountVal--;
      $favCountAll.text(favCountVal);
    }
  });
  //End Favourite Icon Update
  //Compare icon update
  //====================
  $(document).on("rtcl.compare.added", function (e, data) {
    $(".rtcl-el-compare-count").text(data.current_listings);
  });
  $(document).on("rtcl.compare.removed", function (e, data) {
    $(".rtcl-el-compare-count").text(data.current_listings);
  });
  $(document).on("click", ".rtcl-compare-btn-clear", function () {
    $(".rtcl-el-compare-count").text("0");
  });

  // Builder Content visible. Elementor Builder Jumping issue fixed
  $(window).on('load', function () {
    $('.builder-content').removeClass('content-invisible');
  });

  //End Compare icon update
})(jQuery);
