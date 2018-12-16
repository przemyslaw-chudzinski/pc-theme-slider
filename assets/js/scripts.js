(function ($) {

    window.PC_THEME_SLIDER = {

        sortable_config: {
            placeholder: 'pc-theme-slider-sortable-highlight',
            update: (e, ui) => this.PC_THEME_SLIDER._sortableUpdateHandler(e , ui),
            items: '.pc-theme-slider-sortable-item',
            axis: 'y'
        },

        init(){
            this.$openMediaBtn = $('#pc-theme-slider-open-media-btn');
            this.$imageInput = $('#pc-theme-slider-slide-input');
            this.$clearMediaBtn = $('#pc-theme-slider-clear-media-btn');
            this.$previewContainer = $('#pc-theme-slider-slide-preview-container');
            this.$changeStatusBtn = $('.pc-theme-slider-change-status-btn');
            this.$sortableWrapper = $('#pc-theme-slider-sortable-wrapper');
            this.$tableLoader = $('.pc-theme-slider-table-loader');
            this.$msgAjaxWrapper = $('#pc-theme-slider-ajax-msg');
            this.$msgAjaxWrapper.hide();
            this.$tableLoader.hide();
            this.mediaFrame = null;
            this._createMediaFrame();
            this.$sortableWrapper.sortable(this.sortable_config);
            this._assignEvents();
        },

        _assignEvents(){
            this.$openMediaBtn.on('click', this._clickMediaBtnHandler.bind(this));
            if (this.mediaFrame) {
                this.mediaFrame.on('select', this._mediaFrameOnSelectHandler.bind(this));
            }
            this.$clearMediaBtn.on('click', this._clickClearMediaBtnHandler.bind(this));
            this.$changeStatusBtn.on('click', this._changeStatusHandler.bind(this));
        },

        _createMediaFrame(){
            if(!wp.media) {
                return;
            }
            this.mediaFrame = wp.media({
                title: 'Wybierz obrazek',
                button: {
                    text: 'Wstaw zdjęcie'
                },
                multiple: false
            });
        },

        _clickMediaBtnHandler(e){
            e.preventDefault();
            if(this.mediaFrame) {
                this.mediaFrame.open();
            }
        },

        _mediaFrameOnSelectHandler(){
            const image = this.mediaFrame.state().get('selection').toJSON();
            const value = image[0].url;
            this.$imageInput.val(value);
            this.$previewContainer.empty();
            const img = image[0];
            const $item = $('<a class="pc-theme-slider-preview-item" href="#"><img src="'+ img.sizes.thumbnail.url +'" ></a>');
            this.$previewContainer.append($item);
            this.$openMediaBtn.hide();
            this.$clearMediaBtn.show();
        },

        _clickClearMediaBtnHandler(e){
            e.preventDefault();
            this.$imageInput.val('');
            this.$previewContainer.empty();
            this.$clearMediaBtn.hide();
            this.$openMediaBtn.show();
        },

        _changeStatusHandler(e){
            const $target = $(e.target);
            const slideId = parseInt($target.data('id'));
            this._showTableLoader();
            $.ajax({
                method: "POST",
                url: ajaxurl,
                data: {action: 'change_slide_status', id: slideId},
                success: (response) => this._changeStatusHandlerSuccess(response, $target),
                error: (error) => this._changeStatusHandlerError(error, $target)
            });
        },

        _changeStatusHandlerSuccess(response, $button){
            response = JSON.parse(response);
            if (response.error) {
                return;
            }
            if (response.prev_state === 0) {
                $button.addClass('button-primary');
                $button.removeClass('button-secondary');
                $button.text('Aktywny');
            } else {
                $button.addClass('button-secondary');
                $button.removeClass('button-primary');
                $button.text('Nieaktywny');
            }
            this._hideTableLoader();
            this._showMsgAjaxWrapper(response.message);
        },

        _changeStatusHandlerError(error, $button){
            console.log(error);
            $button.text('Wystąpił błąd');
            $button.removeClass('button-primary');
            $button.removeClass('button-secondary');
            $button.addClass('button-error');
        },

        _sortableUpdateHandler(e, ui){
            this._showTableLoader();
            const idsArr = this.$sortableWrapper.sortable('toArray').map(id => parseInt(id));
            $.ajax({
                method: 'post',
                url: ajaxurl,
                data: {
                    action: 'change_slides_order',
                    order: idsArr
                },
                success: (response) => this._sortableUpdateHandlerSuccess(response),
                error: (error) => this._sortableUpdateHandlerError(error)
            });
        },

        _sortableUpdateHandlerSuccess(response){
            response = JSON.parse(response);
            console.log(response);
            this._showMsgAjaxWrapper(response.message);
            this._hideTableLoader();
        },

        _sortableUpdateHandlerError(error){
            error = JSON.parse(error);
            this._hideTableLoader();
        },

        _showTableLoader(){
            this.$tableLoader.show();
        },

        _hideTableLoader(){
            this.$tableLoader.hide();
        },

        _hideMsgAjaxWrapper(){
            this.$msgAjaxWrapper.hide();
            this.$msgAjaxWrapper.removeClass('updated');
            this.$msgAjaxWrapper.removeClass('error');
        },

        _showMsgAjaxWrapper(msg = '', status = 'updated'){
            this.$msgAjaxWrapper.removeClass('updated');
            this.$msgAjaxWrapper.removeClass('error');
            this.$msgAjaxWrapper.find('p').text(msg);
            this.$msgAjaxWrapper.show();
            this.$msgAjaxWrapper.addClass(status);
        }


    };


    PC_THEME_SLIDER.init();

})(jQuery);