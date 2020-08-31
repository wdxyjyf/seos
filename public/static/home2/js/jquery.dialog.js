/**
 * @file        鍩轰簬jQuery鐨勫脊绐楃粍浠�
 * @author      榫欐硥 <yangtuan2009@126.com>
 * @version     1.0.0
 */
(function(factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD module
    define(['jquery'], factory);
  } else if (typeof module !== "undefined" && module.exports) {
    // Node/CommonJS
    // Seajs build
    factory(require('jquery'));
  } else {
    // 娴忚鍣ㄥ叏灞€妯″紡
    factory(jQuery);
  }
})(function($) {
  var closeClass = '.j_dialogClose';

  // 榛樿鍙傛暟閰嶇疆
  var dialogDef = {
    id: '', // 濡傛灉椤甸潰涓瓨鍦ㄥ涓牱寮忕殑寮圭獥锛屽彲鐢↖D鍖哄埆鏍峰紡
    title: 'Title',
    isFixed: true,
    hideHeader: false,
    hideClose: false,
    content: null,
    callback: null,
    withNoPadding: false, // 鏄惁涓嶈缃畃adding
    withNoMinWidth: false, // 鏄惁涓嶈缃渶灏忓搴�
    bgHide: true, // 鐐瑰嚮鑳屾櫙鏄惁闅愯棌
    escHide: true // 鎸塃SC鎸夐敭鏄惁闅愯棌
  };

  // 鍏ㄥ眬鍙橀噺
  var dialogConfig = {
    windows: $(window),
    lightbox: '.dialog-background',
    section: '.dialog-section',
    imageTag: '.dialog-imageitem',
    imageData: {},
    width: 0,
    height: 0,
    left: 0,
    top: 0,
    paddingWidth: 0,
    paddingHeight: 0,
    resizeParams: ['.dialog-section']
  };

  // 鐢ㄦ埛浼犻€掕繃鏉ョ殑鍙傛暟闆�
  var dialogOpts = {};

  // 鍩虹瀹炵幇
  var Dialog = {
    /**
     * 鏄剧ずdialog寮圭獥
     * @param  {Object} opts 閰嶇疆閫夐」
     * @return {undefined}
     */
    show: function(opts) {
      dialogOpts = $.extend({}, dialogDef, opts || {});

      if (opts.content) {
        Dialog.dialog(opts);
      } else {
        Dialog.lightbox(opts);
      }

      // 鍒濆鍖栨椂鎵ц鐨勫洖璋冨嚱鏁�
      typeof dialogOpts.onInit === 'function' && dialogOpts.onInit();

      // 鏀瑰彉娴忚鍣ㄥぇ灏忔椂锛屽姩鎬佹敼鍙樺唴瀹规樉绀虹洅瀛愮殑浣嶇疆
      dialogConfig.windows.on('resize', function() {
        Dialog.settings.apply(window, dialogConfig.resizeParams);
      });
    },

    /**
     * 鍏抽棴dialog寮圭獥
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    hide: function(callback) {
      var oLightBox = $(dialogConfig.lightbox);
      var oSection = $(dialogConfig.section);

      if (dialogOpts.isFixed) {
        oSection.animate({
          marginTop: -(dialogConfig.top - 150),
          opacity: 0
        });
      } else {
        oSection.animate({
          top: (dialogConfig.top + 150),
          opacity: 0
        });
      }

      oLightBox.fadeOut(function() {
        oLightBox.remove();
        oSection.remove();
        callback && callback();
      });
    },

    dialog: function(opts) {
      Dialog.install(opts);
      dialogConfig.resizeParams = [dialogConfig.section, true, true];
      Dialog.settings.apply(window, dialogConfig.resizeParams);
    },

    lightbox: function(opts) {
      var clickObj_src = opts.clickObj.attr('data-src') || opts.clickObj.attr('data-image');
      dialogConfig.now = 0;
      Dialog.getImages_src(dialogOpts.imagelist);
      Dialog.loadImage(clickObj_src, true, Dialog.settings);
      Dialog.getNow(clickObj_src);
    },

    install: function(opts) {
      var oBody = $('body');
      var headerHtml = '<div class="dialog-header">' + dialogOpts.title + '</div>';
      var closeHtml = '<div class="dialog-close j_dialogClose"></div>';
      var markId = '';
      var addClass = '';
      var $background = oBody.find(dialogConfig.lightbox);

      if (!dialogOpts.content) {
        var content = '<div class="dialog-imagelist">' + '  <img src="" class="dialog-imageitem" />' + '</div>' + '  ' + '<span class="dialog-btnPrev">&lt;</span>' + '<span class="dialog-btnNext">&gt;</span>';
      } else {
        var content = dialogOpts.content;
      }

      if (dialogOpts.hideHeader) {
        headerHtml = '';
      }

      if (dialogOpts.hideClose) {
        closeHtml = '<div class="dialog-close j_dialogClose" style="display: none;"></div>';
      }

      if (dialogOpts.id) {
        markId = ' id="' + dialogOpts.id + '"';
      }

      var plugs_lightbox = '<div class="dialog-background' + (dialogOpts.bgHide ? ' j_bgHide' : '') + '"></div>';
      var plugs_lightbox_section = '<div class="dialog-section' + (dialogOpts.escHide ? ' j_escHide' : '') + '" ' + markId + '>' + headerHtml + '<div class="dialog-body' + (dialogOpts.withNoPadding ? ' withNoPadding' : '') + (dialogOpts.withNoMinWidth ? ' withNoMinWidth' : '') + '">' + content + '</div>' + closeHtml + '</div>';

      // 濡傛灉涔嬪墠鏈夋墦寮€寮圭獥锛屽厛灏嗗叾鍏抽棴
      if ($background.length) {
        $background.stop().fadeIn();
        oBody.find(dialogConfig.section).remove();
      } else {
        oBody.append(plugs_lightbox)
      }

      oBody.append(plugs_lightbox_section);
      $(dialogConfig.lightbox).fadeIn();
      $(dialogConfig.section).show();

      var iPaddingWidth = $(dialogConfig.section).outerWidth() - $(dialogConfig.section).width();
      var iPaddingHeight = $(dialogConfig.section).outerHeight() - $(dialogConfig.section).height();

      dialogConfig.paddingWidth = iPaddingWidth;
      dialogConfig.paddingHeight = iPaddingHeight;
      dialogOpts.callback && dialogOpts.callback();
    },

    getNow: function(loadImage_src) {
      for (var i = 0, len = dialogConfig.images.length; i < len; i++) {
        if (loadImage_src === dialogConfig.images[i]) {
          dialogConfig.now = i;
        }
      }
    },

    getImages_src: function(images) {
      var images = (typeof images == 'string') ? $(images) : images;
      dialogConfig.images = [];

      for (var i = 0, len = images.length; i < len; i++) {
        var currentImage = images.eq(i);
        var currentImage_src = currentImage.attr('data-src') || currentImage.attr('data-image');
        var currentImage_src = $.trim(currentImage_src);
        if (currentImage_src !== '') {
          dialogConfig.images.push(currentImage_src);
        }
      }
    },

    loadImage: function(loadImage_src, isMove, callback) {
      var image = new Image();
      image.onload = function() {
        if ($('.dialog-section').length === 0) {
          Dialog.install(dialogOpts);
          $('.dialog-btnPrev').on('click', function() {
            Dialog.switchImage(false, false);
          });
          $('.dialog-btnNext').on('click', function() {
            Dialog.switchImage(true, false);
          });
        }

        Dialog.setBtnSate();

        var section = $(dialogConfig.section);
        var imageTag = $(dialogConfig.imageTag);

        dialogConfig.imageData = {
          width: this.width,
          height: this.height,
          src: loadImage_src
        };

        dialogConfig.resizeParams = [section, imageTag, isMove];
        callback && callback.apply(window, dialogConfig.resizeParams);
      }
      image.src = loadImage_src;
    },

    switchImage: function(d, isMove) {
      if (d) {
        dialogConfig.now++;
      } else {
        dialogConfig.now--;
      }

      if (dialogConfig.now < 0) {
        dialogConfig.now = dialogConfig.images.length - 1;
      }

      if (dialogConfig.now > dialogConfig.images.length - 1) {
        dialogConfig.now = 0;
      }

      var loadImage_src = dialogConfig.images[dialogConfig.now];
      Dialog.loadImage(loadImage_src, isMove, Dialog.settings);
    },

    setBtnSate: function() {
      if (dialogConfig.images.length < 2) {
        $('.dialog-btnPrev, .dialog-btnNext').hide();
      }
    },

    // 璁剧疆鍐呭鏄剧ず鐩掑瓙鐨勫ぇ灏忥紝浣嶇疆
    settings: function(section, imageTag, isMove) {
      var section = (typeof section == 'string') ? $(section) : section;
      var winHeight = $(window).height();

      if (!dialogOpts.content) {
        var sectionHeight = 116, // 澶栧洿瀹瑰櫒榛樿鐨勯珮搴︼紝涓轰簡鏂逛究锛岃繖閲屾殏鏃朵娇鐢ㄥ浐瀹氬€硷紝鍚庢湡鏀圭増鍐嶅仛璋冩暣
          configWidth = dialogConfig.imageData.width,
          configHeight = dialogConfig.imageData.height;
        dialogConfig.width = configWidth;
        dialogConfig.height = configHeight;
        if (sectionHeight + dialogConfig.height > winHeight) {
          dialogConfig.height = winHeight - sectionHeight - 50;
          dialogConfig.height = dialogConfig.height < 500 ? 500 : dialogConfig.height;
          dialogConfig.width = Math.round(dialogConfig.width * (dialogConfig.height / configHeight));
        }
      } else {
        section.css({
          left: '0px'
        }); // 鍥哄畾甯冨眬鏃讹紝瀹瑰櫒鐨刲eft搴旇涓�0锛屾牱寮忔枃浠朵笉濂戒慨鏀癸紝鏆傛椂鍦ㄨ繖閲岃皟鏁�
        dialogConfig.width = section.width();
        dialogConfig.height = section.height();
      }

      var outerWidth = dialogConfig.width + dialogConfig.paddingWidth;
      var outerHeight = dialogConfig.height + dialogConfig.paddingHeight + $('.dialog-header').outerHeight();

      if (typeof imageTag === 'object') {
        imageTag.hide().attr('src', dialogConfig.imageData.src).css({
          width: dialogConfig.width,
          height: dialogConfig.height
        }).fadeIn();
      }

      if (dialogOpts.isFixed) {
        dialogConfig.left = Math.floor(outerWidth / 2);
        dialogConfig.top = Math.floor(outerHeight / 2);
        section.css({
          position: 'fixed',
          left: '50%'
        });

        if (isMove) {
          section.css({
            marginLeft: -dialogConfig.left,
            marginTop: -dialogConfig.top
          });
        } else {
          section.animate({
            marginLeft: -dialogConfig.left,
            marginTop: -dialogConfig.top
          }, {
            queue: false
          });
        }
      } else {
        var scrollLeft = dialogConfig.windows.scrollLeft();
        var scrollTop = dialogConfig.windows.scrollTop();
        var windowWidth = $(dialogConfig.lightbox).width();

        dialogConfig.left = Math.floor((windowWidth - outerWidth) / 2) + scrollLeft;
        dialogConfig.top = Math.floor((winHeight - outerHeight) / 2) + scrollTop;
        section.css({
          position: 'absolute',
          marginLeft: 0,
          marginTop: 0
        });

        if (isMove) {
          section.css({
            left: dialogConfig.left,
            top: dialogConfig.top
          });
        } else {
          section.animate({
            left: dialogConfig.left,
            top: dialogConfig.top
          }, {
            queue: false
          });
        }
      }

      if (imageTag) {
        Dialog.move(section, isMove);
      }
    },

    // 鏄剧ず鏃剁殑鍔ㄧ敾鏁堟灉
    move: function(section, isMove) {
      if (dialogOpts.isFixed && isMove) {
        section.css({
          marginTop: -(dialogConfig.top - 150)
        }).animate({
          marginTop: -dialogConfig.top,
          opacity: 1
        }, function() {
          section.css('overflow', 'visible');
        });
      } else if (isMove) {
        section.css({
          top: (dialogConfig.top + 150)
        }).animate({
          top: dialogConfig.top,
          opacity: 1
        }, function() {
          section.css('overflow', 'visible');
        });
      }

      section.animate({
        width: dialogConfig.width
      }, {
        queue: false
      });
    },

    // 鍙栨秷榛樿浜嬩欢
    cancelDefault: function(e) {
      e.preventDefault();
      e.stopPropagation();
    }
  };

  // 寮圭獥绫诲叕鍏卞鐞嗗嚱鏁板皝瑁�
  $.extend({
    /**
     * 鍙戦€佹垚鍔熺殑鎻愮ず妗嗭紙缃《锛�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    sendSuccessToTop: function(msg, duration, callback) {
      var content = '<div class="dialog-success-top">' + '    <i class="i-icon"></i>' + msg + '</div>';

      $('body').append(content);

      var $tipBox = $('.dialog-success-top'),
          width = $tipBox.width();

      $tipBox.css({
        'margin-left': -(width / 2),
        'margin-top': 20,
        'opacity': 0
      });

      $tipBox.animate({
        'opacity': 1,
        'margin-top': 0
      }, 400, function() {
        // 鑷姩闅愯棌
        clearTimeout(window.cc_timerSendSuccessToTop);
        window.cc_timerSendSuccessToTop = setTimeout(function() {
          $tipBox.fadeOut(function() {
            $tipBox.remove();
            typeof callback === 'function' && callback();
          })
        }, duration || 3000);
      });
    },

    /**
     * 鍙戦€佽鍛婄殑鎻愮ず妗嗭紙缃《锛�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    sendWarningToTop: function(msg, duration, callback) {
      var content = '<div class="dialog-warning-top">' + '    <i class="i-icon"></i>' + msg + '</div>';

      $('body').append(content);

      var $tipBox = $('.dialog-warning-top'),
          width = $tipBox.width();

      $tipBox.css({
        'margin-left': -(width / 2),
        'margin-top': 20,
        'opacity': 0
      });

      $tipBox.animate({
        'opacity': 1,
        'margin-top': 0
      }, 400, function() {
        // 鑷姩闅愯棌
        clearTimeout(window.cc_timerSendWarningToTop);
        window.cc_timerSendWarningToTop = setTimeout(function() {
          $tipBox.fadeOut(function() {
            $tipBox.remove();
            typeof callback === 'function' && callback();
          });
        }, duration || 3000);
      });
    },

    /**
     * 鍙戦€佹彁绀轰俊鎭�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @param  {string}   iconStr  icon鍐呭
     * @return {undefined}
     */
    sendMsg: function(msg, duration, callback, iconStr) {
      // 缂虹渷duration鍙傛暟
      if ($.isFunction(duration)) {
        callback = duration;
        duration = undefined;
      }

      var content = '<div class="dialog-msg">' + '    <div class="dialog-msg-text">' + (iconStr || '') + msg + '</div>' + '</div>';

      var _options = {
        id: 'dialogTipBox',
        title: ' ',
        hideHeader: true,
        hideClose: false,
        content: content,
        callback: duration === false ? null : function() {
          // 鑷姩闅愯棌
          clearTimeout(window.timerDialogHide);
          window.timerDialogHide = setTimeout(function() {
            $(closeClass).trigger('click');
          }, duration || 3000);
        },
        onClose: function() {
          typeof callback === 'function' && callback();
        }
      };

      Dialog.show(_options);
    },

    /**
     * 鍙戦€佹垚鍔燂紙寮圭獥锛�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    sendSuccess: function(msg, duration, callback) {
      $.sendMsg(msg, duration, callback, '<i class="i-success"></i>');
    },

    /**
     * 鍙戦€佽鍛婏紙寮圭獥锛�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    sendWarning: function(msg, duration, callback) {
      $.sendMsg(msg, duration, callback, '<i class="i-warning"></i>');
    },

    /**
     * 鍙戦€侀敊璇紙寮圭獥锛�
     * @param  {String}   msg      鎻愮ず璇�
     * @param  {Number}   duration 鎸佺画澶氶暱鏃堕棿鍚庡叧闂�
     * @param  {Function} callback 鍥炶皟鍑芥暟
     * @return {undefined}
     */
    sendError: function(msg, duration, callback) {
      $.sendMsg(msg, duration, callback, '<i class="i-error"></i>');
    },

    /**
     * 鍙戦€佺‘璁ゆ彁绀烘
     * @param  {Object} options 閰嶇疆閫夐」
     * @return {undefined}
     */
    sendConfirm: function(options) {
      // 閰嶇疆閫夐」鍚堝苟
      options = $.extend(true, {
        id: 'dialogConfirmBox',
        title: '鎻愮ず妗�',
        hideHeader: false,
        hideClose: false,
        withCenter: false, // 鏄惁姘村钩灞呬腑
        withIcon: false, // 鏄惁鏄剧ずicon锛屽彲浼犻€抴ithIcon鐨勬浛浠ｇ被鍚�
        autoClose: false, // 鏄惁鑷姩鍏抽棴
        timeout: 3000, // 澶氬皯姣涔嬪悗鑷姩鍏抽棴
        width: null, // 鑷畾涔夊搴�
        noconfirm: false, // 鎻愪氦鎸夐挳鏄惁娣诲姞noconfirm灞炴€�
        msg: '', // 鎻愮ず璇�
        desc: '', // 鎻忚堪鏂囨湰
        content: '', // 鑷畾涔夊唴瀹�
        button: {
          confirm: '纭', // 纭鎸夐挳-鏍囬锛宯ull琛ㄧず涓嶆樉绀猴紝鍙互閫氳繃{text:'鎸夐挳鏂囨湰', href:'鎸夐挳閾炬帴', target:'閾炬帴鎵撳紑鏂瑰紡',behavior:'鏄惁鎵ц琛屼负'}杩涜鑷畾涔夎缃�
          cancel: '鍙栨秷', // 鍙栨秷鎸夐挳-鏍囬锛宯ull琛ㄧず涓嶆樉绀猴紝鍙互閫氳繃{text:'鎸夐挳鏂囨湰', href:'鎸夐挳閾炬帴', target:'閾炬帴鎵撳紑鏂瑰紡',behavior:'鏄惁鎵ц琛屼负'}杩涜鑷畾涔夎缃�
          cancelFirst: false // 鍙栨秷鐙傛槸鍚﹀湪鍓嶉潰
        }
      }, options);

      // 鏄惁鏄剧ず鎸夐挳
      var confirmValue = options.button.confirm,
        cancelValue = options.button.cancel,
        isShowButton = options.button && (confirmValue || cancelValue),
        buttonConfirm = '',
        buttonCancel = '',
        buttonContent = '',
        appendClass = '',
        appendStyle = '';

      if (isShowButton) {
        buttonConfirm = (confirmValue ? '<a href="' + (confirmValue.href || 'javascript:void(0);') + '" target="' + (confirmValue.target || '_self') + '" class="dialog-submit' + (confirmValue.behavior === false ? '' : ' j_dialogConfirm') + '"' + (options.noconfirm ? ' noconfirm="noconfirm"' : '') + '>' + (confirmValue.text || confirmValue) + '</a>' : '');
        buttonCancel = (cancelValue ? '<a href="' + (cancelValue.href || 'javascript:void(0);') + '" target="' + (cancelValue.target || '_self') + '" class="dialog-cancel' + (cancelValue.behavior === false ? '' : ' j_dialogCancel') + '">' + (cancelValue.text || cancelValue) + '</a>' : '');
        buttonContent = '<div class="dialog-buttonBox">' + (options.button.cancelFirst ? buttonCancel + buttonConfirm : buttonConfirm + buttonCancel) + '</div>'
      }

      if (options.withCenter) {
        appendClass += ' withCenter';
      }

      if (options.withIcon) {
        appendClass += ' ' + (typeof options.withIcon === 'string' ? options.withIcon : 'withIcon');
      }

      if (options.width !== null) {
        appendStyle = ' style="width:' + options.width + (typeof options.width === 'string' ? '' : 'px') + ';"';
      }

      // 寮圭獥鍐呭
      var content = '<div class="dialog-confirm' + appendClass + '"' + appendStyle + '>' + (options.msg === '' ? '' : '<div class="dialog-msg">' + options.msg + '</div>') + (options.desc === '' ? '' : '<div class="dialog-desc">' + options.desc + '</div>') + (options.content === '' ? '' : '<div class="dialog-content">' + options.content + '</div>') + (buttonContent) + '</div>';
      options.content = content;

      // 鑷姩闅愯棌閫夐」
      if (options.autoClose) {
        var _callbackCopy = options.callback || $.noop;
        options.callback = function() {
          _callbackCopy();
          // 鑷姩闅愯棌
          clearTimeout(window.timerDialogHide);
          window.timerDialogHide = setTimeout(function() {
            $(closeClass).trigger('click');
          }, options.timeout);
        };
      }

      Dialog.show(options);
    }
  });

  // 鐩稿叧浜嬩欢缁戝畾
  (function() {
    var $doc = $(document);

    // 缁戝畾锛氱敤浜庡叧闂璇濆脊绐�
    $doc.on('click', closeClass, function() {
      var $that = $(this), beforeReturn;

      // 濡傛灉杩斿洖false锛屽垯琛ㄧず涓柇鍏抽棴寮圭獥
      typeof dialogOpts.onBeforeClose === 'function' && (beforeReturn = dialogOpts.onBeforeClose($that));
      if (beforeReturn === false) return;

      clearTimeout(window.timerDialogHide);
      Dialog.hide(function() {
        if (typeof dialogOpts.onClose === 'function') {
          dialogOpts.onClose($that, beforeReturn);
        }
      });
    });

    // 缁戝畾锛氱敤浜庢墽琛屽脊绐楃殑纭鎿嶄綔
    $doc.on('click', '.j_dialogConfirm', function() {
        var $that = $(this), beforeReturn;

      // 濡傛灉鎻愪氦鎸夐挳瀛樺湪noconfirm灞炴€э紝灏嗕笉鎵ц鍝嶅簲
      if ($that.attr('noconfirm') !== undefined) return;

      // 濡傛灉杩斿洖false锛屽垯琛ㄧず涓柇鍏抽棴寮圭獥
      typeof dialogOpts.onBeforeConfirm === 'function' && (beforeReturn = dialogOpts.onBeforeConfirm($that));
      if (beforeReturn === false) return;

      clearTimeout(window.timerDialogHide);
      Dialog.hide(function() {
        if (typeof dialogOpts.onConfirm === 'function') {
          dialogOpts.onConfirm($that, beforeReturn);
        }
      });
    });

    // 缁戝畾锛氱敤浜庢墽琛屽脊绐楃殑鍙栨秷鎿嶄綔
    $doc.on('click', '.j_dialogCancel', function() {
      var $that = $(this), beforeReturn;

      // 濡傛灉杩斿洖false锛屽垯琛ㄧず涓柇鍏抽棴寮圭獥
      typeof dialogOpts.onBeforeCancel === 'function' && (beforeReturn = dialogOpts.onBeforeCancel($that));
      if (beforeReturn === false) return;

      clearTimeout(window.timerDialogHide);
      Dialog.hide(function() {
        if (typeof dialogOpts.onCancel === 'function') {
          dialogOpts.onCancel($that, beforeReturn);
        }
      });
    });

    // 缁戝畾锛氱偣鍑诲脊绐楅伄缃╁眰鍏抽棴寮圭獥
    $doc.on('click', '.j_bgHide', function() {
      $(closeClass).trigger('click');
    });

    // 缁戝畾锛氭寜ESC鎸夐敭鍏抽棴寮圭獥
    $doc.on('keyup', function(ev) {
      if (ev.keyCode == 27 && $('.j_escHide').length) {
        $(closeClass).trigger('click').removeClass('j_dialogClose');
      }
    });
  })();

  // 浣跨敤$.dialog()杩涜璁块棶
  $.dialog = Dialog.show;
  $.dialogClose = Dialog.hide;
});