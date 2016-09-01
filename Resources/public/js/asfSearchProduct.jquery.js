

// jQuery Artscore Studio Framework Search Product Plugin for Symfony2
// A jQuery Plugin used in Symfony2 project for interactive search product field
//
// version 0.1, 2015-09-21
// by Nicolas Claverie <info@artscore-studio.fr>
(function($){
    $.asfSearchProduct = function(element, options) {
        
        // Plugin's default options
        // Private property accesible only from inside the plugin
        var defaults = {
            productRequestRoute: 'asf_product_product_ajax_request',
            productSuggestRoute: 'asf_product_product_suggest_ajax_request',
            productCreateRoute: 'asf_product_product_create_ajax_request',
            modalId: '#search-product-modal'
        }
        
        // Reference to the current instance of the object
        , plugin = this;
        
        // Variable for the merging of defaults options and user-provider options
        plugin.settings = {};
        
        var $element = $(element), // Reference to the jQuery version of DOM element
            element = element,     // Reference to the actual DOM element
            $modal;                // Plugin modal
        
        // Constructor method called at the creation of the object
        plugin.init = function() {
            
            // Merge defaults options and user-provider options
            plugin.settings = $.extend({}, defaults, options);
            
            // Create modal
            plugin.createModal();
            
            // Autocompletion
            plugin.autocomplete.init();
            
            console.debug($.fn.select2.defaults);
        }
        
        // Autocomplete field
        // ======================================================================
        plugin.autocomplete = {
        	init: function(){
                $element.select2({
                    ajax: {
                        url: Routing.generate(plugin.settings.productRequestRoute),
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: plugin.autocomplete.processResult,
                        cache: true
                    },
                    tags: true,
                    minimumInputLength: 3,
                    escapeMarkup: plugin.autocomplete.escapeMarkup,
                    createTag: plugin.autocomplete.newTag,
                    templateResult: plugin.autocomplete.templateResult,
                    templateSelection: plugin.autocomplete.templateSelection
                });
            },
            escapeMarkup: function(markup) {
            	return markup;
            },
            processResult: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) > data.total_count
                    }
                };
            },
            newTag: function(params) {
                return {
                    id: params.term,
                    name: params.term,
                    newOption: true
                }
            },
            templateSelection: function(data) {
                return data.name || data.text;
            },
            templateResult: function(response) {
                if (response.loading) return response.text;
                var $markup = $('<span></span>');
                $markup.text(response.name);
                if ( response.newOption ) {
                    $markup.prepend('<span class="label label-info">new</span> ');
                }
                return $markup;
            }
        }

        // Check the search against the db
        // ======================================================================
        plugin.onChange = function(event, ui) {
            if ( ui.item == null && $element.val() != '' ) {
                $modal.find('.modal-title').text(Translator.trans('asf.product.title.suggested_products'));
                $modal.modal('show');
                $.ajax({
                    url: Routing.generate(plugin.settings.productSuggestRoute, {term: $element.val()}),
                    success: plugin.suggestedProductWindow
                });
            }
        }

        plugin.suggestedProductWindow = function(response){
            $modal.find('.modal-body').html(response);
            $modal.find('.btn-submit').on('click', function(){
                var classnames = $(this).attr('class'), $self = $(this);
                if ( classnames.indexOf('btn-waiting') != -1 ) {
                    return;
                }
                
                $(this).addClass('btn-waiting').text(Translator.trans('asf.product.msg.loading_please_wait');
                
                var db_product = $modal.find('input[name=selected_product]:checked'), product_name;
                if ( db_product.length > 1 ) {
                    $modal.find('.modal-body').prepend('<div class="alert alert-danger">'+Translator.trans('asf.product.msg.just_one_product')+'</div>');
                    $(this).removeClass('btn-waiting').text(Translator.trans('asf.product.btn.save'));
                    $element.removeClass('data-found');
                } else if (db_product.length == 1) {
                    $modal.find('.modal-body .alert').remove();
                    product_name = $(db_product).val();
                    $element.val(product_name);
                    $element.addClass('data-found');
                    $(this).removeClass('btn-waiting').text(Translator.trans('asf.product.btn.save'));
                    $modal.modal('hide');
                } else if ( db_product.length == 0 ) {
                    var datas = {
                        productName: $('input[name*="product-name"]').val(),
                        brandName: $('input[name*="brand-name"]').val(),
                        weight: $('input[name*="weight"]').val(),
                        capacity: $('input[name*="capacity"]').val()
                    }
                    $.ajax({
                        url: Routing.generate(plugin.settings.productCreateRoute, datas),
                        success: function(response) {
                            $.each(response, function(key, value){
                                if ( key == 'name' ) {
                                    $element.val(value);
                                    $element.addClass('data-found');
                                    $self.removeClass('btn-waiting').text(Translator.trans('asf.product.btn.save'));
                                    $modal.modal('hide');
                                } else if ( key == 'error' ) {
                                    $modal.find('.modal-body').prepend('<div class="alert alert-danger">'+response.error+'</div>');
                                    $element.removeClass('data-found');
                                    $self.addClass('btn-waiting').text(Translator.trans('asf.product.msg.loading_please_wait'));
                                }
                            });
                        },
                        dataType: 'json'
                    })
                }
            });
        }
        
        // Twitter Bootstrap Modal 
        // ======================================================================
        plugin.createModal = function() {
            
            if ( $('body').find(plugin.settings.modalId).length == 0 ) {
                $modal = $('<div class="modal fade" id="'+plugin.settings.modalId+'" tabindex="-1" role="dialog" aria-labelledby="Suggested product dialog window"></div>');
                var $modal_diag = $('<div class="modal-dialog modal-lg" role="document"></div>')
                    , $modal_content = $('<div class="modal-content"></div>')
                    
                    , $modal_header = $('<div class="modal-header"></div>')
                    , $header_btn_close = $('<button type="button" class="close" data-dismiss="modal" aria-label="'+Translator.trans('Close', {}, 'asf_product')+'"><span aria-hidden="true">&times;</span></button>')
                    , $modal_title = $('<h4 class="modal-title" id="myModalLabel"></h4>')
                        
                    , $modal_body = $('<div class="modal-body"><div class="text-center">'+Translator.trans('asf.product.msg.loading_please_wait')+'</div></div>')        
                    
                    , $modal_footer = $('<div class="modal-footer">')
                    , $btn_close = $('<button type="button" class="btn btn-default" data-dismiss="modal">'+Translator.trans('asf.product.btn.close')+'</button>')
                    , $btn_save = $('<button type="button" class="btn btn-primary btn-submit">'+Translator.trans('asf.product.btn.save')+'</button>');
                
                $modal_header.append($header_btn_close, $modal_title);
                $modal_footer.append($btn_close, $btn_save);
                
                $modal_content.append($modal_header, $modal_body, $modal_footer).appendTo($modal_diag);
                $modal.append($modal_diag).appendTo('body').on('hidden.bs.modal', function(event){
                    $(this).find('.modal-body').html('<div class="text-center">'+Translator.trans('asf.product.msg.loading_please_wait')+'</div>');
                    $(this).find('.btn-submit').off('click');
                });
            }
            
            return true;
        }
        
        // Fire up the plugin !
        plugin.init();
    }
    
    // Add the plugin to jQuery functions (jQuery.fn object)
    $.fn.asfSearchProduct = function(options) {
        
        // Iteration through the DOM elements we are attaching the plugin
        return this.each(function(){
            
            // If the plugin has not already been attached to the element
            if (undefined == $(this).data('asfSearchProduct')) {
                
                // Create new instance of the plugin with current DOM element and user-provided options
                var plugin = new $.asfSearchProduct(this, options);
                
                // In the jQuery version of the element,
                // store a reference to the plugin object
                // for access to the plugin form outside like 
                // element.data('asfSearchProduct').createBtn(), etc.
                $(this).data('asfSearchProduct', plugin);
            }
        });
    }
    
})(jQuery);

