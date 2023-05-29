(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function($) {
    // Extends plugins for adding CKFinder 3.
    //  - plugin is external module for customizing.
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'CKFinder': function(context) {
            var self = this;

            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;

            // add CKFinder button
            context.memo('button.CKFinder', function() {
                // create button
                var button = ui.button({
                    contents: '<i class="fas fa-image"></i>'
                    , tooltip: 'Chọn ảnh'
                    , click: function() {
                        CKFinder.modal({
                            //   Full location of the plugins file.
                            //   plugins: [
                            //  'https://example.com/ckfinder/plugins/StatusBarInfo/StatusBarInfo.js',
                            //  'https://example.com/ckfinder/plugins/ImageInfo/ImageInfo.js',
                            //  'https://example.com/ckfinder/plugins/CustomButton/CustomButton.js'
                            //  ],
                            chooseFiles: true,
                            //displayFoldersPanel: false,
                            //startupPath: 'Images:/' + YourFolder + '/',
                            //rememberLastFolder: false,
                            onInit: function(finder) {
                                finder.on('files:choose', function(evt) {
                                    var file = evt.data.files.first();
                                    var url = file.getUrl();
                                    //url = location.origin + url; // full url
                                    context.invoke('editor.insertImage', url);
                                });
                                finder.on('file:choose:resizedImage', function(evt) {
                                    var url_resizedUrl = evt.data.resizedUrl;
                                    //url_resizedUrl = location.origin + url_resizedUrl; // full url
                                    context.invoke('editor.insertImage', url_resizedUrl);
                                });
                            }
                        });
                    }
                , });

                // create jQuery object from button instance.
                var $CKFinder = button.render();
                return $CKFinder;
            });
        }
    , });
}));
