
function fnCreateGridster(page, colors, states, titles) {

    /* load saved position and sizes */
    if (localdata_position) {
        $.each(localdata_position, function (i, value) {
            $('#' + value.id).attr({"data-col": value.col, "data-row": value.row, "data-sizex": value.size_x, "data-sizey": value.size_y});
        });
    }

    /* load titles */
    if (localdata_titles) {
        $.each(localdata_titles, function (i, value) {
            if (value) {
                if (value.title)
                    $('#' + value.panel + ' .panel-title-text').html(value.title);
            }
        });
    }

    var minCols = 1;
    /* force 1 column on mobile screen sizes */
    if ($(window).width() <= 480 || $(window).width() == 640) {
        var cols = 1;
        var offset = 40;
    } else {
        var cols = 12;
        var offset = 40;
        minCols = 12;
    }


    /* get the default size for the ratio */
    var base_size = ($(window).width() / cols) - offset;
    var widget_base_x = base_size;
    var widget_base_y = 60;
//    console.log(base_size);
    // Calculate a height for each of the blocks.
    $(".gridster .widget-box").each(function (i, value) {
        var height = $(this).actual('height');
        var units = Math.ceil(height / (widget_base_y + 5));
        $(this).parent().attr('data-sizey', units);
    });
    var initPostSerialize = false;
    /* start gridster */
    var listWidgets =$(".gridster > ul");
    var gridster = listWidgets.gridster({
        extra_cols: 1,
        autogrow_cols: true,
        min_cols: minCols,
        max_cols: cols,
        widget_margins: [5, 5],
        widget_base_dimensions: [base_size, widget_base_y],
        resize: {
            enabled: true,
            min_size: [1, 2],
            stop: function (event, ui, widget) {
                
                var positions = JSON.stringify(this.serialize());
                localStorage.setItem(page, positions);
                var urlUpdateWidget = listWidgets.attr('url-update-widget');
                $.ajax({
                    type: 'POST',
                    url: urlUpdateWidget,
                    data: { data: this.serialize(widget) }
                });
            }
        },
        serialize_params: function ($w, wgd)
        {
            return {id: $w.attr('id-block'), col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y, widget_color: $w.attr('widget-color')};
        },
        draggable:
            {
                //                    handle: '.panel-heading, .panel-handel',
                handle: '.widget-header, .widget-title',
                stop: function (event, ui,$widget) {
                    var dataSerialize;
                    if(initPostSerialize){
                        dataSerialize = gridster.serialize_changed();
                    }else{
                        dataSerialize = gridster.serialize($widget);
                        initPostSerialize = true;
                    }
                    var urlUpdateWidget = listWidgets.attr('url-update-widget');
                    $.ajax({
                        type: 'POST',
                        url: urlUpdateWidget,
                        data: { data: dataSerialize }
                    });
                }
            }
    }).data('gridster');


    /* load title colors */
    if (localdata_colors) {
        $.each(localdata_colors, function (i, value) {
            if (value)
                $('#' + value.panel + ' .panel-heading, #' + value.panel + ' .btn-colorselector').css("background-color", value.color);
        });
    }

    /* register the minimize button */
    $('.widget-box').on('hide.ace.widget', function (e) {
        var panel = $(this).parent().attr("id");
        var widget = $('#'+panel);
        if(widget.attr('data-state') == 'max'){
            var dataSerialize = gridster.serialize(widget);
            _state_minimize(panel);
            var url = $(this).find('.box-collapse').attr('minimize');
            $.ajax({
                type: 'POST',
                url: url,
                data: { data: dataSerialize }
            });
        }
    });
    /* register the maximize button */
    $('.widget-box').on('fullscreen.ace.widget', function (e) {
        var panel = $(this).parent();
        if(panel.hasClass("fullscreen")){
            panel.removeClass("fullscreen");
            panel.addClass("gs-w");
        }else{
            panel.removeClass("gs-w");
            panel.addClass("fullscreen");
        }
        var $this = $(this);
        if(panel.css('position') === 'fixed'){ 
            $this.animate({width: $this.attr('data-width'),height:$this.attr('data-height')},300);
        } else {
            $this.attr({
                'data-width': $this.width(),
                'data-height': $this.height(),
            });
            $this.animate({
                margin: '0',
                top: '0',
                left: '0',
                bottom: '0',
                right: '0',
                width: '100%', 
                height : '100%'
            },300);
        }
    });
    
    var saveData = function(widget){
        var urlUpdateWidget = listWidgets.attr('url-update-widget');
        var dataSerialize = gridster.serialize(widget);
        $.ajax({
            type: 'POST',
            url: urlUpdateWidget,
            data: { data: dataSerialize }
        });
    };
    var reloadWidget = function(widgetBox,callback){
        var url = widgetBox.find('.box-reload').attr('path');
        $.ajax(url).done(function(a){
            widgetBox.find('.widget-main').html(a);
            widgetBox.trigger("reloaded.ace.widget");
            if(callback){
                callback(widgetBox);
            }
        });
    };
    var callReloadAjax = true;
    /* register the maximize button */
    $('.widget-box').on('show.ace.widget', function (e) {
        var widget = $(this);
        var panel = widget.parent();
        if(panel.attr('data-state') == 'min'){
            var dataLoad = widget.attr('data-load');
            if(dataLoad == 'false'){
                e.preventDefault();
                callReloadAjax = false;
                widget.find('.box-reload').click();
                reloadWidget(widget,function(){
                    widget.attr('data-load','true');
                    widget.find('.box-collapse').click();
                });
                return;
            }
            _state_maxamize(panel.attr('id'));
            var url = $(this).find('.box-collapse').attr('maximize');
            $.ajax(url);
        }
    });
    
    /* register the close button */
    $('.widget-box').on('close.ace.widget', function(e) {
        //this = the widget-box
        var widgetBox = $(this);
        gridster.remove_widget( widgetBox.parent(),function(a){
            var url = widgetBox.find('.box-close').attr('path');
            $.ajax(url);
        });
   });
   
    /* register the reload button */
    $('.widget-box').on('reload.ace.widget', function(e) {
        if(callReloadAjax == true){
            //this = the widget-box
            var widgetBox = $(this);
            reloadWidget(widgetBox);
        }
        callReloadAjax = true;
   });
   
   $(document).on('click','.dropdown-colorpicker',(function(e){
       if($(this).hasClass('open')){
           $(this).removeClass('open');
       }else{
           $(this).addClass('open');
       }
       
    }));
     $('.simple-colorpicker-1').ace_colorpicker({pull_right:true}).on('change', function(){
            var color_class = $(this).find('option:selected').data('class');
            var widgetBox = $(this).closest('.widget-box');
            var new_class = 'widget-box';
            if(widgetBox.hasClass('collapsed')){
                new_class += ' collapsed ';
            }
            var class_color = '';
            if(color_class != 'default'){
                class_color = ' widget-color-'+color_class;
                new_class += class_color;
            }
            widgetBox.attr('class', new_class);
            $(this).closest('.widget-container-col').attr('widget-color',class_color);
            saveData($(this).closest('.widget-container-col'));
    });
    /* register the maximize button */
    $(document).on("click", ".panel-max", function (e) {
        e.preventDefault();
        var panel = $(this).attr("data-id");
        if ($(this).hasClass('glyphicon-resize-small')) {

            $('.main-nav').show();
            $('#' + panel).find('.hide-full').show();
            $('#' + panel + ' .gs-resize-handle').hide();
            $('#' + panel).css({'position': 'absolute', 'top': $('#' + panel).attr('data-top'), 'left': $('#' + panel).attr('data-left'), 'width': $('#' + panel).attr('data-width'), 'height': $('#' + panel).attr('data-height'), 'z-index': '0'});
            $(this).removeClass('glyphicon-resize-small').addClass('glyphicon-resize-full');

        } else {
            $('.main-nav').hide();
            var _position = $('#' + panel).position();
            $('#' + panel).attr({
                'data-width': $('#' + panel).width(),
                'data-height': $('#' + panel).height(),
                'data-left': _position.left,
                'data-top': _position.top
            });
            $('#' + panel).css({'position': 'fixed', 'top': '0', 'left': '0', 'width': '100%', 'height': '100%', 'z-index': '9999'});

            $(this).removeClass('glyphicon-resize-full').addClass('glyphicon-resize-small');
            $('#' + panel + ' .gs-resize-handle').show();
            $('#' + panel).find('.hide-full').hide();
        }
    });
    

    /* helpers */
    function _state_maxamize(panel) {
        $('#'+panel +'').attr('data-state', 'max');
        var _oldsize = parseInt($('#' + panel).attr('data-sizey-old'));
        $('#' + panel + '').attr('data-sizey', _oldsize);
        $(".gridster > ul").data('gridster').resize_widget($('#' + panel), $('#' + panel).attr('data-sizex'), _oldsize);
    }

    function _state_minimize(panel) {
        $('#'+panel +'').attr('data-state', 'min');
        $('#' + panel).attr('data-sizey-old', $('#' + panel).attr('data-sizey'));
        $(".gridster > ul").data('gridster').resize_widget($('#' + panel), $('#' + panel).attr('data-sizex'), 1);
        $('#' + panel).attr('data-sizey', '1');
    }

    function _resize_gridster() {
        gridster.resize_widget_dimensions({
            widget_base_dimensions: [(((base_size * ($(window).width() / base_size)) / cols) - offset), widget_base_y],
            widget_margins: [5, 5],
        });
    }

    /* we're ready for the show */
    $(window).on('resize load', _resize_gridster);

    /* give it a bit to fully load then fade in */
    setTimeout(function () {
        $('.gridster').fadeIn('fast');
    }, 400);
}
