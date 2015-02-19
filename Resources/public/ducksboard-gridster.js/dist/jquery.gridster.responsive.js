
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
            min_size: [2, 2],
            stop: function (event, ui, widget) {
                var positions = JSON.stringify(this.serialize());
                localStorage.setItem(page, positions);
            }
        },
        serialize_params: function ($w, wgd)
        {
            return {id: $w.attr('id-block'), col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y};
        },
        draggable:
            {
                //                    handle: '.panel-heading, .panel-handel',
                handle: '.widget-header, .widget-title',
                stop: function (event, ui,$widget) {
                    var _positions = this.serialize();
                    $.each(_positions, function (i, value) {
                        _state = $('#' + value.id).attr('data-state');
                        if (_state == 'min') {
                            value.size_y = $('#' + value.id).attr('data-sizey-old')
                            _positions[i] = value;
                        }

                    });
                    var positions = JSON.stringify(_positions);
                    localStorage.setItem(page, positions);
                    var dataSerialize;
                    if(initPostSerialize){
                        dataSerialize = gridster.serialize_changed();
                    }else{
                        dataSerialize = gridster.serialize($widget);
                        initPostSerialize = true;
                    }
                    var urlUpdatePosition = listWidgets.attr('url-update-widget');
                    $.ajax({
                        type: 'POST',
                        url: urlUpdatePosition,
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

    /* load states (after colors) */
    if (localdata_states) {
        $.each(localdata_states, function (i, value) {
            if (value) {
                console.log(value);
                if (value.state == 'closed') {
                    $(".gridster > ul").data('gridster').remove_widget($('#' + value.panel));
                } else if (value.state == false)
                    _state_minimize(value.panel);
            }
        });
    }

    /* register the minimize button */
    $('.widget-box').on('hide.ace.widget', function (e) {
        var panel = $(this).parent().attr("id");
        var widget = $('#'+panel);
        var dataSerialize = gridster.serialize(widget);
        _state_minimize(panel);
        var url = $(this).find('.collapse').attr('minimize');
        $.ajax({
            type: 'POST',
            url: url,
            data: { data: dataSerialize }
        });
    });
    /* register the maximize button */
    $('.widget-box').on('show.ace.widget', function (e) {
        var panel = $(this).parent().attr("id");
        _state_maxamize(panel);
        
        var url = $(this).find('.collapse').attr('maximize');
        $.ajax(url);
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
    /* register the close button */
    
    $('.widget-box').on('reload.ace.widget', function(e) {
        //this = the widget-box
        var widgetBox = $(this);
        var url = widgetBox.find('.box-reload').attr('path');
        $.ajax(url).done(function(a){
            widgetBox.find('.widget-main').html(a);
            widgetBox.trigger("reloaded.ace.widget");
        });
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
        var _oldsize = parseInt($('#' + panel).attr('data-sizey-old'));
        $('#' + panel + '').attr('data-sizey', _oldsize);
        $(".gridster > ul").data('gridster').resize_widget($('#' + panel), $('#' + panel).attr('data-sizex'), _oldsize);
    }

    function _state_minimize(panel) {
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
