$(function () {
    var $container = $('.container');
    var $scroll = $container.find('.sections');
    var height = $container.height();
    var len = 4;
    var current = 1;
    // $container.find('.section').css('height', height + 'px');
    $scroll.show();

// page控制器
    var page = {
        isScrolling: false,
        next: function () {
            if ((current + 1) <= len) {
                current += 1;
                page.move(current);
            }
        },

        move: function (index) {
            page.isScrolling = true;
            var di = -(index - 1) * height + 'px';
            page.start = +new Date();
            $scroll.css('transform', 'translateY('+di+')');
            setTimeout(function () {
                page.isScrolling = false;
            }, 1010);
        },

        pre: function () {
            if ((current - 1) > 0) {
                current -= 1;
                page.move(current);
            }
        }
    };

    var mouseWheelHandle = function (event) {
        var deltaY = 0;
        if (page.isScrolling) {
            return false;
        }
        var e = event.originalEvent || event;
        deltaY = e.deltaY;
        if (deltaY > 0) {
            page.next();
        } else if (deltaY < 0) {
            page.pre();
        }
    };

    var eventBind = function () {
        $(document).on('mousewheel', mouseWheelHandle);
    };
    eventBind();

});