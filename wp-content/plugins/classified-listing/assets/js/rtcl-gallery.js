// Init global namespace
var RTCL = RTCL || {};
RTCL.File = {
  alertError: 0,
  Registered: [],
  RemoveItem: function RemoveItem(id) {
    jQuery.each(RTCL.File.Registered, function (i, item) {
      if (typeof item.Item[id] !== "undefined") {
        delete item.Item[id];
        item.SortableUpdate();
        item.CheckFileLimit();
      }
    });
  },
  GetMime: function GetMime(file) {
    var mime = "other";
    if (file === null) {
      return mime;
    }
    if (typeof file.mime_type === "undefined") {
      return mime;
    }
    var types = {
      image: ["image/jpeg", "image/jpe", "image/jpg", "image/png"]
    };
    for (var index in types) {
      if (types[index].indexOf(file.mime_type) !== -1) {
        mime = index;
      }
    }
    return mime;
  },
  GetIcon: function GetIcon(file) {
    if (file === null) {
      return null;
    }
    var m = this.GetMime(file);
    if (m === "image") {
      return "rtcl-icon-file-image";
    }
    if (["application/x-pdf", "application/pdf"].indexOf(file.mime_type)) {
      return "rtcl-icon-file-pdf";
    } else if (["application/zip", "application/octet-stream"].indexOf(file.mime_type)) {
      return "rtcl-icon-file-archive";
    }
    return "rtcl-icon-doc-inv";
  },
  BrowserError: function BrowserError(error) {
    new RTCL.File.Error(error, false);
  }
};
RTCL.File.Error = function (error, overlay) {
  var template = wp.template("wprtcl-browser-error");
  var $ = jQuery;
  var text = "";
  if (typeof error.responseText !== "undefined") {
    text = error.responseText;
  } else if (typeof error.error !== "undefined") {
    text = error.error;
  }
  if (text.length === 0) {
    return;
  }
  var data = {
    error: text,
    overlay: overlay
  };
  var tpl = template(data);
  this.html = $(tpl);
  this.html.find("a.rtcl-button").on("click", jQuery.proxy(this.CloseClicked, this));
  jQuery("body").append(this.html);
};
RTCL.File.Error.prototype.CloseClicked = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.html.remove();
};
RTCL.File.Uploader = function (setup) {
  var $ = jQuery;
  this.PostID = 0;
  if ($(setup.conf.post_id_input).val()) {
    var pId = parseInt($(setup.conf.post_id_input).val(), 10);
    this.PostID = pId || !isNaN(pId) ? pId : 0;
  }
  this.Item = {};
  this.Browser = new RTCL.File.Browser(this);
  this.setup = setup;
  this.ui = $("#" + setup.init.container);
  this.sortable = this.ui.find(".rtcl-gallery-uploads");
  this.engine = new RTCL.File.Uploader.Plupload();
  var $this = this;
  jQuery.each(setup.data, function (index, result) {
    var file = {
      id: "rtcl-file-" + result.attach_id
    };
    $this.FileAdded(null, file);
    $this.FileUploaded(file, result);
    var x = 0;
  });
  this.sortable.sortable({
    update: jQuery.proxy(this.SortableUpdate, this)
  });
  this.Plupload(setup.init);
};
RTCL.File.Uploader.prototype.GetKeys = function () {
  var keys = [];
  keys.fill(0, 0, this.Item.length);
  jQuery.each(this.Item, function (index, item) {
    if (item.result) {
      keys[item.container.index()] = item.result.attach_id;
    }
  });
  return keys;
};
RTCL.File.Uploader.prototype.GetFileCount = function () {
  var count = Object.keys(this.Item).length;
  return count;
};
RTCL.File.Uploader.prototype.CheckFileLimit = function () {
  var max = parseInt(this.setup.init.max_files, 10) || 5;
  var total_image = this.GetFileCount();
  if (max <= total_image) {
    jQuery(".rtcl-gallery").hide();
  } else {
    jQuery(".rtcl-gallery").show();
  }
};
RTCL.File.Uploader.prototype.SortableUpdate = function (e) {
  if (typeof e !== "undefined") {
    this.ui.find(".rtcl-gallery-upload-update.rtcl-spinner").fadeIn();
  }
  jQuery.ajax({
    url: rtcl_gallery_lang.ajaxurl,
    context: this,
    type: "post",
    dataType: "json",
    data: {
      action: "rtcl_gallery_update_order",
      _ajax_nonce: this.setup.init.multipart_params._ajax_nonce,
      post_id: this.PostID,
      ordered_keys: this.GetKeys()
    },
    success: jQuery.proxy(this.SortableUpdateSuccess, this),
    error: jQuery.proxy(this.SortableUpdateError, this)
  });
};
RTCL.File.Uploader.prototype.SortableUpdateSuccess = function (response) {
  if (response.success) {
    this.ui.find(".rtcl-gallery-upload-update.rtcl-spinner").fadeOut();
  } else {
    RTCL.File.BrowserError(response);
  }
};
RTCL.File.Uploader.prototype.SortableUpdateError = function (response) {
  this.ui.find(".rtcl-gallery-upload-update.rtcl-spinner").fadeOut();
  RTCL.File.BrowserError(response);
};
RTCL.File.Uploader.prototype.FileAdded = function (container, file) {
  var c = jQuery("<div></div>").addClass("rtcl-gallery-upload-item").attr("id", file.id);
  var init = {
    _ajax_nonce: this.setup.init.multipart_params._ajax_nonce
  };
  this.Item[file.id] = new RTCL.File.Singular(file, c, init);
  this.Item[file.id].SetBrowser(this.Browser);
  this.Item[file.id].render();
  this.ui.find(".rtcl-gallery-uploads").append(c);
  this.CheckFileLimit();
};
RTCL.File.Uploader.prototype.FileUploaded = function (file, result) {
  this.Item[file.id].setResult(result);
  this.Item[file.id].render();
  if (this.PostID === 0) {
    this.PostID = result.post_id;
  }
  var pId = parseInt(jQuery(this.setup.conf.post_id_input).val(), 10);
  if (!pId || pId === 0 || isNaN(pId)) {
    jQuery(this.setup.conf.post_id_input).val(this.PostID);
  }
  this.CheckFileLimit();
};
RTCL.File.Uploader.prototype.Plupload = function (init) {
  // create the uploader and pass the config from above
  this.uploader = new plupload.Uploader(init);
  // checks if browser supports drag and drop upload, makes some css adjustments if necessary
  this.uploader.bind('Init', jQuery.proxy(this.engine.Init, this));
  this.uploader.init();
  this.uploader.bind("BeforeUpload", jQuery.proxy(this.engine.BeforeUpload, this));
  this.uploader.bind('FilesAdded', jQuery.proxy(this.engine.FilesAdded, this));
  this.uploader.bind('FileUploaded', jQuery.proxy(this.engine.FileUploaded, this));
  this.uploader.bind('Error', jQuery.proxy(this.engine.Error, this));
};
RTCL.File.Uploader.Plupload = function (init) {
  // do nothing ...
};
RTCL.File.Uploader.Plupload.prototype.getUploader = function () {
  return this.uploader;
};
RTCL.File.Uploader.Plupload.prototype.Init = function (up) {
  if (up.features.dragdrop) {
    this.ui.addClass('drag-drop');
    this.ui.find('.rtcl-gallery').bind('dragover.wp-uploader', jQuery.proxy(this.engine.InitDragOver, this));
    this.ui.find('.rtcl-drag-drop-area').bind('dragleave.wp-uploader, drop.wp-uploader', jQuery.proxy(this.engine.InitDragLeave, this));
  } else {
    this.ui.removeClass('drag-drop');
    this.ui.find('.rtcl-drag-drop-area').unbind('.wp-uploader');
  }
};
RTCL.File.Uploader.Plupload.prototype.InitDragOver = function () {
  this.ui.addClass('drag-over');
};
RTCL.File.Uploader.Plupload.prototype.InitDragLeave = function () {
  this.ui.removeClass('drag-over');
};
RTCL.File.Uploader.Plupload.prototype.BeforeUpload = function (up, file) {
  up.settings.multipart_params.post_id = this.PostID;
};
RTCL.File.Uploader.Plupload.prototype.FilesAdded = function (up, files) {
  var max = parseInt(this.setup.init.max_files, 10) || 5,
    total_image = this.GetFileCount(),
    remaining = max - total_image,
    filesToUploaded = files;
  if (remaining) {
    if (files.length > remaining) {
      filesToUploaded = files.slice(0, remaining);
      var remFiles = files.slice(remaining);
      console.log(files);
      console.log(filesToUploaded);
      console.log(remFiles);
      if (remFiles.length) {
        jQuery.each(remFiles, function (index, file) {
          up.removeFile(file);
        });
      }
    }
    jQuery.each(filesToUploaded, jQuery.proxy(this.engine.FileAdded, this), up);
    up.refresh();
    up.start();
  } else {
    console.log(rtcl_gallery_lang.error_image_limit);
    alert(rtcl_gallery_lang.error_image_limit);
  }
  RTCL.File.alertError = 0;
};
RTCL.File.Uploader.Plupload.prototype.FileAdded = function (index, file) {
  var up = this.uploader;
  this.FileAdded(up.settings.container, file);
};
RTCL.File.Uploader.Plupload.prototype.FileUploaded = function (up, file, response) {
  var result = jQuery.parseJSON(response.response);
  if (this.PostID === 0) {
    this.PostID = result.post_id;
  }
  this.FileUploaded(file, result);
  this.SortableUpdate();
};
RTCL.File.Uploader.Plupload.prototype.Error = function (up, args) {
  var message = rtcl_gallery_lang.error_common;
  if (args) {
    switch (args.code) {
      case -600:
        message = rtcl_gallery_lang.error_image_size;
        break;
      case -601:
        message = rtcl_gallery_lang.error_image_extension;
        break;
    }
  }
  if (!RTCL.File.alertError) {
    alert(message);
  }
};
RTCL.File.Singular = function (file, container, init) {
  this.file = file;
  this.container = container;
  this.init = init;
  this.browser = null;
  this.result = null;
  this.spinner = null;
  this.button = {
    edit: null,
    remove: null
  };
};
RTCL.File.Singular.prototype.SetBrowser = function (browser) {
  this.browser = browser;
};
RTCL.File.Singular.prototype.render = function () {
  var template = wp.template("wprtcl-uploaded-file");
  var $ = jQuery;
  var data = {
    file: this.file,
    result: this.result,
    mime: RTCL.File.GetMime(this.result),
    icon: RTCL.File.GetIcon(this.result)
  };
  var tpl = template(data);
  var html = $(tpl);
  this.container.html(html);
  if (this.result) {
    if (typeof this.result.error !== "undefined") {
      this.container.on("click", jQuery.proxy(this.Dispose, this));
    } else {
      this.button.edit = this.container.find(".rtcl-button-edit");
      this.button.remove = this.container.find(".rtcl-button-remove");
      this.spinner = this.container.find(".rtcl-spinner");
      this.spinner.hide();
      this.button.edit.on("click", jQuery.proxy(this.EditClicked, this));
      this.button.remove.on("click", jQuery.proxy(this.RemoveClicked, this));
    }
  }
};
RTCL.File.Singular.prototype.Dispose = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  var fileId = this.file.id;
  this.container.fadeOut("fast", function () {
    jQuery(this).remove();
    RTCL.File.RemoveItem(fileId);
  });
};
RTCL.File.Singular.prototype.EditClicked = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.browser.Open();
  this.browser.Render(this.result);
  this.browser.UpdateNavigation();
};
RTCL.File.Singular.prototype.RemoveClicked = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.spinner.css("display", "block");
  jQuery.ajax({
    url: rtcl_gallery_lang.ajaxurl,
    context: this,
    type: "post",
    dataType: "json",
    data: {
      action: "rtcl_gallery_delete",
      _ajax_nonce: this.init._ajax_nonce,
      post_id: this.result.post_id,
      attach_id: this.result.attach_id
    },
    success: jQuery.proxy(this.RemoveClickedSuccess, this),
    error: jQuery.proxy(this.RemoveClickedError, this)
  });
};
RTCL.File.Singular.prototype.RemoveClickedSuccess = function (response) {
  if (response.result == 1) {
    this.Dispose();
  } else {
    this.spinner.hide();
    new RTCL.File.Error(response, true);
  }
};
RTCL.File.Singular.prototype.RemoveClickedError = function (response) {
  this.spinner.hide();
  new RTCL.File.Error(response, true);
};
RTCL.File.Singular.prototype.setResult = function (result) {
  this.result = result;
};
RTCL.File.Singular.prototype.Uploaded = function () {};
RTCL.File.Browser = function (uploader) {
  this.file = null;
  this.uploader = uploader;
  var template = wp.template("wprtcl-browser");
  var compiled = template({
    modal_id: "xxx"
  });
  var html = jQuery(compiled);
  html.find(".wprtcl-overlay-close").on("click", jQuery.proxy(this.Close, this));
  html.find(".wprtcl-file-pagi-prev").on("click", jQuery.proxy(this.PrevClicked, this));
  html.find(".wprtcl-file-pagi-next").on("click", jQuery.proxy(this.NextClicked, this));
  this.browser = html;
  this.browser.hide();
  jQuery("body").append(this.browser);
};
RTCL.File.Browser.prototype.SetFile = function (file) {
  this.file = file;
};
RTCL.File.Browser.prototype.GetNavigation = function () {
  var keys = this.uploader.GetKeys();
  var index = keys.indexOf(this.file.attach_id);
  var prev_id = false;
  var next_id = false;
  if (index > 0) {
    prev_id = keys[index - 1];
  }
  if (index + 1 < keys.length) {
    next_id = keys[index + 1];
  }
  return {
    prev_id: prev_id,
    next_id: next_id
  };
};
RTCL.File.Browser.prototype.UpdateNavigation = function () {
  var navi = this.GetNavigation();
  if (navi.prev_id) {
    this.browser.find(".wprtcl-file-pagi-prev").removeClass("wprtcl-navi-disabled");
  } else {
    this.browser.find(".wprtcl-file-pagi-prev").addClass("wprtcl-navi-disabled");
  }
  if (navi.next_id) {
    this.browser.find(".wprtcl-file-pagi-next").removeClass("wprtcl-navi-disabled");
  } else {
    this.browser.find(".wprtcl-file-pagi-next").addClass("wprtcl-navi-disabled");
  }
};
RTCL.File.Browser.prototype.Open = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.browser.show();
};
RTCL.File.Browser.prototype.Close = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.browser.hide();
};
RTCL.File.Browser.prototype.NextClicked = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  var navi = this.GetNavigation();
  var next = null;
  if (navi.next_id === false) {
    return;
  }
  jQuery.each(this.uploader.Item, function (i, item) {
    if (item.result.attach_id == navi.next_id) {
      next = i;
    }
  });
  this.Render(this.uploader.Item[next].result);
  this.UpdateNavigation();
};
RTCL.File.Browser.prototype.PrevClicked = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  var navi = this.GetNavigation();
  var prev = null;
  if (navi.prev_id === false) {
    return;
  }
  jQuery.each(this.uploader.Item, function (i, item) {
    if (item.result.attach_id == navi.prev_id) {
      prev = i;
    }
  });
  this.Render(this.uploader.Item[prev].result);
  this.UpdateNavigation();
};
RTCL.File.Browser.prototype.Render = function (result) {
  this.SetFile(result);
  if (!Date.now) {
    var timestamp = new Date().getTime();
  } else {
    var timestamp = Date.now();
  }
  var template = wp.template("wprtcl-browser-attachment-view");
  var mime = RTCL.File.GetMime(result);
  var $ = jQuery;
  var data = {
    mime: mime,
    icon: RTCL.File.GetIcon(this.file),
    file: this.file,
    timestamp: timestamp
  };
  var tpl = template(data);
  var html = $(tpl);
  html.find(".wprtcl-image-sizes").on("change", jQuery.proxy(this.ImageSizeChanged, this));
  html.find(".rtcl-upload-modal-update").on("click", jQuery.proxy(this.UpdateDescription, this));
  this.element = {
    spinner: html.find(".rtcl-spinner"),
    success: html.find(".rtcl-update-description-success"),
    input: {
      featured: html.find("input[name='rtcl_featured']"),
      caption: html.find("input[name='rtcl_caption']"),
      content: html.find("textarea[name='rtcl_content']")
    }
  };
  if (html.find(".wprtcl-attachment-edit-image").length > 0) {
    this.element.input.edit = html.find(".wprtcl-attachment-edit-image");
    this.element.input.edit.on("click", jQuery.proxy(this.EditImage, this));
  }
  if (html.find(".wprtcl-attachment-create-image").length > 0) {
    this.element.input.edit = html.find(".wprtcl-attachment-create-image");
    this.element.input.edit.on("click", jQuery.proxy(this.CreateImage, this));
  }
  this.browser.find(".wprtcl-attachment-details").html(html);
  this.browser.find(".wprtcl-attachment-details").find(".wprtcl-image-sizes").change();
  if (this.imageSize) {
    this.browser.find(".wprtcl-image-sizes option[value='" + this.imageSize + "']").prop("selected", true);
    this.browser.find(".wprtcl-image-sizes").change();
  }
  this.imageSize = null;
};
RTCL.File.Browser.prototype.EditImage = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.imageSize = this.browser.find(".wprtcl-image-sizes option:selected").val();
  this.actionType = "edit";
  this.dim = [this.file.sizes[this.imageSize].width, this.file.sizes[this.imageSize].height];
  this.dimHistory = [];
  this.dimHistory.push(this.dim);
  this.history = [];
  this.ImageLoad();
};
RTCL.File.Browser.prototype.CreateImage = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.imageSize = this.browser.find(".wprtcl-image-sizes option:selected").val();
  this.actionType = "create";
  this.dim = [this.file.sizes.full.width, this.file.sizes.full.height];
  this.dimHistory = [];
  this.dimHistory.push(this.dim);
  this.history = [];
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageLoad = function () {
  this.jcrop = null;
  this.crop = null;
  this.input = {};
  var imageSize = this.imageSize;
  if (this.actionType === "create") {
    imageSize = null;
  }
  var recommended = null;
  if (this.imageSize !== "full") {
    recommended = RTCL_IMAGE_SIZES[this.imageSize];
  }
  var template = wp.template("wprtcl-browser-attachment-image");
  var $ = jQuery;
  var data = {
    file: this.file,
    size: imageSize,
    dim: this.dim,
    recommended: recommended,
    rand: Math.floor(Math.random() * 10000),
    history: JSON.stringify(this.history),
    nonce: this.uploader.setup.init.multipart_params._ajax_nonce
  };
  var tpl = template(data);
  var html = $(tpl);
  html.find(".rtcl-image-action-crop").on("click", jQuery.proxy(this.ImageCrop, this));
  html.find(".rtcl-image-action-rotate-ccw").on("click", jQuery.proxy(this.RotateCCW, this));
  html.find(".rtcl-image-action-rotate-cw").on("click", jQuery.proxy(this.RotateCW, this));
  html.find(".rtcl-image-action-flip-h").on("click", jQuery.proxy(this.ImageFlipH, this));
  html.find(".rtcl-image-action-flip-v").on("click", jQuery.proxy(this.ImageFlipV, this));
  html.find(".rtcl-image-action-undo").on("click", jQuery.proxy(this.ImageUndo, this));
  html.find(".rtcl-image-action-save").on("click", jQuery.proxy(this.ImageSave, this));
  html.find(".rtcl-image-action-cancel").on("click", jQuery.proxy(this.ImageCancel, this));
  html.find(".rtcl-image-action-restore").on("click", jQuery.proxy(this.ImageRestore, this));
  html.find(".rtcl-image-scale-width").on("keyup", jQuery.proxy(this.KeyWidth, this));
  html.find(".rtcl-image-scale-height").on("keyup", jQuery.proxy(this.KeyHeight, this));
  html.find(".rtcl-image-action-scale").on("click", jQuery.proxy(this.ImageScale, this));
  this.spinner = html.find(".wprtcl-image-edit-spinner");
  this.input.width = html.find(".rtcl-image-scale-width");
  this.input.height = html.find(".rtcl-image-scale-height");
  if (this.history.length == 0) {
    html.find(".rtcl-image-action-undo").css("opacity", "0.5");
    html.find(".rtcl-image-action-save").css("opacity", "0.5");
  }
  html.find(".rtcl-image-action-crop").css("opacity", "0.5");
  this.browser.find(".wprtcl-attachment-details").html(html);
  var icrop = this.browser.find("#wprtcl-image-crop");
  icrop.load(jQuery.proxy(this.ImageCropLoaded, this));
  icrop.attr('src', icrop.data("src"));
};
RTCL.File.Browser.prototype.ImageCropLoaded = function (e) {
  var jopt = {
    onSelect: jQuery.proxy(this.CropSelected, this),
    onChange: jQuery.proxy(this.CropSelected, this),
    onRelease: jQuery.proxy(this.CropReleased, this)
  };
  if (this.imageSize == "full") {
    jopt.trueSize = [this.dim[0], this.dim[1]];
  }
  if (this.actionType == "create" && this.imageSize != "full") {
    jopt.trueSize = [this.dim[0], this.dim[1]];
  }
  this.image = this.browser.find("#wprtcl-image-crop");
  this.jcrop = this.image.Jcrop(jopt);
  var s = this.dimHistory[0];
  var d = this.image.parent().width() / this.dim[0];
  if (d > 1) {
    d = 1;
  }
  this.browser.find(".rtcl-image-prop-original-size").text(s[0].toString() + " x " + s[1].toString());
  this.browser.find(".rtcl-image-prop-current-size").text(this.dim[0].toString() + " x " + this.dim[1].toString());
  this.browser.find(".rtcl-image-prop-zoom").text(Math.round(d * 100, 2).toString() + "%");
  this.spinner.hide();
};
RTCL.File.Browser.prototype.ImageSizeChanged = function (e) {
  var s = this.browser.find(".wprtcl-image-sizes option:selected");
  if (s.val() === "video") {
    this.browser.find(".wprtcl-file-browser-image-actions").hide();
    this.browser.find(".wprtcl-file-browser-video-actions").show();
    this.browser.find(".wprtcl-attachment-image").hide();
    this.browser.find(".wprtcl-attachment-video").show();
  } else {
    this.browser.find(".wprtcl-file-browser-image-actions").show();
    this.browser.find(".wprtcl-file-browser-video-actions").hide();
    this.browser.find(".wprtcl-attachment-image").show();
    this.browser.find(".wprtcl-attachment-video").hide();
  }
  if (s.val() === "full") {
    this.browser.find(".wprtcl-attachment-create-image").hide();
  } else {
    this.browser.find(".wprtcl-attachment-create-image").show();
  }
  this.browser.find(".rtcl-image-preview").hide();
  this.browser.find(".rtcl-image-preview.rtcl-image-preview-" + s.val()).fadeIn("fast");
  this.browser.find(".rtcl-icon-size-explain-desc").text(s.data("explain"));
};
RTCL.File.Browser.prototype.CropSelected = function (c) {
  this.browser.find(".wprtcl-attachment-details").find(".rtcl-image-action-crop").css("opacity", "1");
  this.browser.find(".rtcl-image-prop-selection").text(Math.round(c.w) + " x " + Math.round(c.h));
  this.crop = c;
};
RTCL.File.Browser.prototype.CropReleased = function (e) {
  this.browser.find(".wprtcl-attachment-details").find(".rtcl-image-action-crop").css("opacity", "0.5");
  this.crop = null;
};
RTCL.File.Browser.prototype.ImageCrop = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  if (this.crop === null) {
    return;
  }
  var crop = this.crop;
  crop.a = "c";
  crop.w = Math.round(crop.w);
  crop.h = Math.round(crop.h);
  this.dimHistory.push(this.dim);
  this.dim = [crop.w, crop.h];
  this.history.push(crop);
  this.crop = null;
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.RotateCW = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.dimHistory.push(this.dim);
  this.dim = [this.dim[1], this.dim[0]];
  this.history.push({
    a: "ro",
    v: "90"
  });
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.RotateCCW = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.dimHistory.push(this.dim);
  this.dim = [this.dim[1], this.dim[0]];
  this.history.push({
    a: "ro",
    v: "-90"
  });
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageFlipH = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.dimHistory.push(this.dim);
  this.dim = [this.dim[0], this.dim[1]];
  this.history.push({
    a: "f",
    h: true,
    v: false
  });
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageFlipV = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.dimHistory.push(this.dim);
  this.dim = [this.dim[0], this.dim[1]];
  this.history.push({
    a: "f",
    h: false,
    v: true
  });
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.KeyWidth = function (e) {
  // calculate height
  var width = parseInt(this.input.width.val());
  var max_width = parseInt(this.input.width.attr("max"));
  var max_height = parseInt(this.input.height.attr("max"));
  var scale = width * (max_height / max_width);
  this.input.height.val(Math.round(scale).toString());
};
RTCL.File.Browser.prototype.KeyHeight = function (e) {
  // calculate width
  var height = parseInt(this.input.height.val());
  var max_width = parseInt(this.input.width.attr("max"));
  var max_height = parseInt(this.input.height.attr("max"));
  var scale = height * (max_width / max_height);
  this.input.width.val(Math.round(scale).toString());
};
RTCL.File.Browser.prototype.ImageScale = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.dimHistory.push(this.dim);
  this.dim = [this.input.width.val(), this.input.height.val()];
  this.history.push({
    a: "re",
    w: this.input.width.val(),
    h: this.input.height.val()
  });
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageUndo = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  if (this.history.length === 0) {
    return;
  }
  this.dim = this.dimHistory.pop();
  this.history.pop();
  this.spinner.show();
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageSave = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  if (this.history.length === 0) {
    return;
  }
  this.spinner.show();
  var applyAll = 0;
  if (this.imageSize == "full" && this.browser.find(".wprtcl-image-action-apply-to").is(":checked")) {
    applyAll = 1;
  }
  var data = {
    action: "rtcl_gallery_image_save",
    _ajax_nonce: this.uploader.setup.init.multipart_params._ajax_nonce,
    post_id: this.uploader.PostID,
    history: JSON.stringify(this.history),
    size: this.imageSize,
    attach_id: this.file.attach_id,
    action_type: this.actionType,
    apply_to: this.imageSize,
    apply_to_all: applyAll
  };

  //
  jQuery.ajax({
    url: rtcl_gallery_lang.ajaxurl,
    context: this,
    type: "post",
    dataType: "json",
    data: data,
    success: jQuery.proxy(this.ImageSaveSuccess, this),
    error: jQuery.proxy(this.ImageSaveError, this)
  });
};
RTCL.File.Browser.prototype.ImageCancel = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.Render(this.file);
};
RTCL.File.Browser.prototype.ImageRestore = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  var data = {
    action: "rtcl_gallery_image_restore",
    _ajax_nonce: this.uploader.setup.init.multipart_params._ajax_nonce,
    post_id: this.uploader.PostID,
    size: this.imageSize,
    attach_id: this.file.attach_id,
    action_type: this.actionType,
    apply_to: this.imageSize
  };
  jQuery.ajax({
    url: rtcl_gallery_lang.ajaxurl,
    context: this,
    type: "post",
    dataType: "json",
    data: data,
    success: jQuery.proxy(this.ImageRestoreSuccess, this),
    error: jQuery.proxy(this.ImageRestoreError, this)
  });
  this.history = [];
  this.ImageLoad();
};
RTCL.File.Browser.prototype.ImageRestoreSuccess = function (response) {
  this.spinner.hide();
  if (response.result != "1") {
    new RTCL.File.Error(response, false);
    return;
  }
  for (var i in this.uploader.Item) {
    if (this.uploader.Item[i].result.attach_id == this.file.attach_id) {
      this.uploader.Item[i].result = response.file;
      this.file = response.file;
      this.Render(response.file);
    }
  }
};
RTCL.File.Browser.prototype.ImageRestoreError = function (response) {
  this.spinner.hide();
  new RTCL.File.Error(response, false);
};
RTCL.File.Browser.prototype.ImageSaveSuccess = function (response) {
  var br = this;
  console.log(response);
  this.spinner.hide();
  if (response.result != "1") {
    new RTCL.File.Error(response, false);
    return;
  }
  for (var i in RTCL.File.Registered) {
    for (var j in RTCL.File.Registered[i].Item) {
      if (RTCL.File.Registered[i].Item[j].result.attach_id == response.file.attach_id) {
        RTCL.File.Registered[i].Item[j].result = response.file;
        RTCL.File.Registered[i].Item[j].render();
        RTCL.File.Registered[i].Browser.Render(response.file);
      }
    }
  }
};
RTCL.File.Browser.prototype.ImageSaveError = function (response) {
  this.spinner.hide();
  new RTCL.File.Error(response, false);
};
RTCL.File.Browser.prototype.UpdateDescription = function (e) {
  if (typeof e !== "undefined") {
    e.preventDefault();
  }
  this.element.spinner.css("display", "inline-block");
  var featured = this.element.input.featured.prop("checked") ? 1 : 0;
  jQuery.ajax({
    url: rtcl_gallery_lang.ajaxurl,
    context: this,
    type: "post",
    dataType: "json",
    data: {
      action: "rtcl_gallery_update",
      _ajax_nonce: this.uploader.setup.init.multipart_params._ajax_nonce,
      post_id: this.uploader.PostID,
      attach_id: this.file.attach_id,
      caption: this.element.input.caption.val(),
      content: this.element.input.content.val(),
      featured: featured
    },
    success: jQuery.proxy(this.UpdateDescriptionSuccess, this),
    error: RTCL.File.BrowserError
  }); // end jQuery.ajax
};

RTCL.File.Browser.prototype.UpdateDescriptionSuccess = function (r) {
  this.element.spinner.hide();
  if (r.result == 1) {
    this.element.success.fadeIn("fast", function () {
      jQuery(this).delay(500).fadeOut("slow");
    });
    var featured = this.element.input.featured.prop("checked") ? 1 : 0;
    var br = this;
    var response = r;
    jQuery.each(this.uploader.Item, function (index, item) {
      if (item.result.attach_id == br.file.attach_id) {
        item.result = response.file;
        item.render();
        br.file = response.file;
      } else if (featured) {
        item.result.featured = 0;
        item.render();
      }
    });
  } else {
    alert(r.error);
  }
};
RTCL.File.Browser.Other = function () {};
window.RTCL = RTCL;
(function ($) {
  if (typeof RTCL_PLUPLOAD_DATA === "undefined") {
    return;
  }
  $.each(RTCL_PLUPLOAD_DATA, function (index, item) {
    RTCL.File.Registered.push(new RTCL.File.Uploader(item));
  });
})(jQuery);
