/* global rtcl_validator*/
(function ($) {
  window.rtclCheckPasswordStrength = function (password) {
    var strength = 0;
    if (password && password.length < parseInt(rtcl_validator.pw_min_length, 10)) {
      strength += 1;
    }
    if (password.length > 7) strength += 1;
    // If password contains both lower and uppercase characters, increase strength value.
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
    // If it has numbers and characters, increase strength value.
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1;
    // If it has one special character, increase strength value.
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    // If it has two special characters, increase strength value.
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    return strength;
  };
  if ($.fn.validate) {
    var stripHtml = function stripHtml(value) {
      // Remove html tags and space chars
      return value.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " ")

      // Remove punctuation
      .replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g, "");
    };
    $.validator.setDefaults({
      rules: {
        seltype: "required"
      },
      errorElement: "div",
      errorClass: "with-errors",
      errorPlacement: function errorPlacement(error, element) {
        error.addClass("help-block").removeClass('error');
        if (element.prop("type") === "checkbox" || element.prop("type") === "radio") {
          error.insertAfter(element.parents(".rtcl-check-list"));
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function highlight(element, errorClass, validClass) {
        $(element).parents(".form-group").addClass("has-error has-danger").removeClass("has-success");
      },
      unhighlight: function unhighlight(element, errorClass, validClass) {
        $(element).parents(".form-group").addClass("has-success").removeClass("has-error has-danger");
      },
      invalidHandler: function invalidHandler(form, validator) {
        if (!validator.numberOfInvalids()) return;
        $('html, body').animate({
          scrollTop: $(validator.errorList[0].element).offset().top - rtcl_validator.scroll_top || 200
        }, 800);
      }
    });
    $.validator.messages = rtcl_validator.messages;
    $.validator.addMethod("extension", function (value, element, param) {
      param = typeof param === "string" ? param.replace(/,/g, '|') : "json";
      return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, rtcl_validator.messages.extension);
    $.validator.addClassRules("rtcl-import-file", {
      required: true,
      extension: "json"
    });
    $.validator.addMethod("rtcl-password", function (value, element) {
      return this.optional(element) || value.length >= rtcl_validator.pw_min_length;
    }, rtcl_validator.messages.password);

    /**
     * Return true if the field value matches the given format RegExp
     *
     * @example $.validator.methods.pattern("AR1004",element,/^AR\d{4}$/)
     * @result true
     *
     * @example $.validator.methods.pattern("BR1004",element,/^AR\d{4}$/)
     * @result false
     *
     * @name $.validator.methods.pattern
     * @type Boolean
     * @cat Plugins/Validate/Methods
     */
    $.validator.addMethod("pattern", function (value, element, param) {
      if (this.optional(element)) {
        return true;
      }
      if (typeof param === "string") {
        param = new RegExp("^(?:" + param + ")$");
      }
      return param.test(value);
    });
    $.validator.addMethod("maxWords", function (value, element, params) {
      return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length <= params;
    });
    $.validator.addMethod("minWords", function (value, element, params) {
      return this.optional(element) || stripHtml(value).match(/\b\w+\b/g).length >= params;
    });
    $.validator.addMethod("rangeWords", function (value, element, params) {
      var valueStripped = stripHtml(value),
        regex = /\b\w+\b/g;
      return this.optional(element) || valueStripped.match(regex).length >= params[0] && valueStripped.match(regex).length <= params[1];
    });
    $.validator.addMethod("alphanumeric", function (value, element) {
      return this.optional(element) || /^\w+$/i.test(value);
    });
    $.validator.addMethod("lettersonly", function (value, element) {
      return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
    });

    // Accept a value from a file input based on a required mimetype
    $.validator.addMethod("accept", function (value, element, param) {
      // Split mime on commas in case we have multiple types we can accept
      var typeParam = typeof param === "string" ? param.replace(/\s/g, "") : "image/*",
        optionalValue = this.optional(element),
        i,
        file,
        regex;

      // Element is optional
      if (optionalValue) {
        return optionalValue;
      }
      if ($(element).attr("type") === "file") {
        // Escape string to be used in the regex
        // see: https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex
        // Escape also "/*" as "/.*" as a wildcard
        typeParam = typeParam.replace(/[\-\[\]\/\{\}\(\)\+\?\.\\\^\$\|]/g, "\\$&").replace(/,/g, "|").replace(/\/\*/g, "/.*");

        // Check if the element has a FileList before checking each file
        if (element.files && element.files.length) {
          regex = new RegExp(".?(" + typeParam + ")$", "i");
          for (i = 0; i < element.files.length; i++) {
            file = element.files[i];

            // Grab the mimetype from the loaded file, verify it matches
            if (!file.type.match(regex)) {
              return false;
            }
          }
        }
      }

      // Either return true because we've validated each file, or because the
      // browser does not support element.files and the FileList feature
      return true;
    });
    $.validator.addMethod("greaterThan", function (value, max, min) {
      return parseInt(value) > parseInt($(min).val());
    });
  }
})(jQuery);
