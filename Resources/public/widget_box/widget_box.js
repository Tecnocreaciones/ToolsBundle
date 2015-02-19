/* 
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ("undefined" == typeof jQuery)
    throw new Error("Ace's JavaScript requires jQuery");
!function (a) {
    "ace" in window || (window.ace = {}), "helper" in window.ace || (window.ace.helper = {}), "vars" in window.ace || (window.ace.vars = {}), window.ace.vars.icon = " ace-icon ", window.ace.vars[".icon"] = ".ace-icon", ace.vars.touch = "ontouchstart" in document.documentElement, ace.click_event = ace.vars.touch && a.fn.tap ? "tap" : "click";
    var b = navigator.userAgent;
    ace.vars.webkit = !!b.match(/AppleWebKit/i), ace.vars.safari = !!b.match(/Safari/i) && !b.match(/Chrome/i), ace.vars.android = ace.vars.safari && !!b.match(/Android/i), ace.vars.ios_safari = !!b.match(/OS ([4-9])(_\d)+ like Mac OS X/i) && !b.match(/CriOS/i), ace.vars.ie = window.navigator.msPointerEnabled || document.all && document.querySelector, ace.vars.old_ie = document.all && !document.addEventListener, ace.vars.very_old_ie = document.all && !document.querySelector, ace.vars.firefox = "MozAppearance" in document.documentElement.style, ace.vars.non_auto_fixed = ace.vars.android || ace.vars.ios_safari
}(jQuery),
        function (a, b) {
            var c = function (c, d) {
                function e(a) {
                    a.preventDefault(), a.stopPropagation();
                    var b = A.offset(),
                            c = b[o],
                            d = u ? a.pageY : a.pageX;
                    d > c + G ? (G = d - c - F + I, G > H && (G = H)) : (G = d - c - I, 0 > G && (G = 0)), m.update_scroll()
                }

                function f(b) {
                    b.preventDefault(), b.stopPropagation(), bb = ab = u ? b.pageY : b.pageX, Q = !0, a("html").off("mousemove.ace_scroll").on("mousemove.ace_scroll", g), a(R).off("mouseup.ace_scroll").on("mouseup.ace_scroll", h), A.addClass("active"), S && m.$element.trigger("drag.start")
                }

                function g(a) {
                    a.preventDefault(), a.stopPropagation(), bb = u ? a.pageY : a.pageX, bb - ab + G > H ? bb = ab + H - G : 0 > bb - ab + G && (bb = ab - G), G += bb - ab, ab = bb, 0 > G ? G = 0 : G > H && (G = H), m.update_scroll()
                }

                function h(b) {
                    b.preventDefault(), b.stopPropagation(), Q = !1, a("html").off(".ace_scroll"), a(R).off(".ace_scroll"), A.removeClass("active"), S && m.$element.trigger("drag.end"), w && W && !Y && j()
                }

                function i(a) {
                    var b = +new Date;
                    if (Z && b - db > 1e3) {
                        var c = z[t];
                        $ != c && ($ = c, _ = !0, m.reset(!0)), db = b
                    }
                    w && W && (null != cb && (clearTimeout(cb), cb = null), A.addClass("not-idle"), Y || 1 != a || j())
                }

                function j() {
                    null != cb && (clearTimeout(cb), cb = null), cb = setTimeout(function () {
                        cb = null, A.removeClass("not-idle")
                    }, X)
                }

                function k() {
                    A.css("visibility", "hidden").addClass("scroll-hover"), N = u ? parseInt(A.outerWidth()) || 0 : parseInt(A.outerHeight()) || 0, A.css("visibility", "").removeClass("scroll-hover")
                }

                function l() {
                    if (V !== !1) {
                        var a = y.offset(),
                                b = a.left,
                                c = a.top;
                        u ? M || (b += y.outerWidth() - N) : M || (c += y.outerHeight() - N), V === !0 ? A.css({
                            top: parseInt(c),
                            left: parseInt(b)
                        }) : "left" === V ? A.css("left", parseInt(b)) : "top" === V && A.css("top", parseInt(c))
                    }
                }
                var m = this,
                        n = a.extend({}, a.fn.ace_scroll.defaults, d);
                this.size = 0, this.lock = !1, this.lock_anyway = !1, this.$element = a(c), this.element = c;
                var o, p, q, r, s, t, u = !0,
                        v = !1,
                        w = !1,
                        x = !1,
                        y = null,
                        z = null,
                        A = null,
                        B = null,
                        C = null,
                        D = null,
                        E = null,
                        F = 0,
                        G = 0,
                        H = 0,
                        I = 0,
                        J = !0,
                        K = !1,
                        L = "",
                        M = !1,
                        N = 0,
                        O = 1,
                        P = !1,
                        Q = !1,
                        R = "onmouseup" in window ? window : "html",
                        S = n.dragEvent || !1,
                        T = d.scrollEvent || !1,
                        U = n.detached || !1,
                        V = n.updatePos || !1,
                        W = n.hideOnIdle || !1,
                        X = n.hideDelay || 1500,
                        Y = !1,
                        Z = n.observeContent || !1,
                        $ = 0,
                        _ = !0;
                this.create = function (b) {
                    if (!x) {
                        b && (n = a.extend({}, a.fn.ace_scroll.defaults, b)), this.size = parseInt(this.$element.attr("data-size")) || n.size || 200, u = !n.horizontal, o = u ? "top" : "left", p = u ? "height" : "width", q = u ? "maxHeight" : "maxWidth", r = u ? "clientHeight" : "clientWidth", s = u ? "scrollTop" : "scrollLeft", t = u ? "scrollHeight" : "scrollWidth", this.$element.addClass("ace-scroll"), "static" == this.$element.css("position") ? (P = this.element.style.position, this.element.style.position = "relative") : P = !1;
                        var c = null;
                        U ? c = a('<div class="scroll-track scroll-detached"><div class="scroll-bar"></div></div>').appendTo("body") : (this.$element.wrapInner('<div class="scroll-content" />'), this.$element.prepend('<div class="scroll-track"><div class="scroll-bar"></div></div>')), y = this.$element, U || (y = this.$element.find(".scroll-content").eq(0)), u || y.wrapInner("<div />"), z = y.get(0), U ? (A = c, l()) : A = this.$element.find(".scroll-track").eq(0), B = A.find(".scroll-bar").eq(0), C = A.get(0), D = B.get(0), E = D.style, u || A.addClass("scroll-hz"), n.styleClass && (L = n.styleClass, A.addClass(L), M = !!L.match(/scroll\-left|scroll\-top/)), 0 == N && (A.show(), k()), A.hide(), A.on("mousedown", e), B.on("mousedown", f), y.on("scroll", function () {
                            J && (G = parseInt(Math.round(this[s] * O)), E[o] = G + "px"), J = !1, T && this.$element.trigger("scroll", [z])
                        }), n.mouseWheel && (this.lock = n.mouseWheelLock, this.lock_anyway = n.lockAnyway, this.$element.on(a.event.special.mousewheel ? "mousewheel.ace_scroll" : "mousewheel.ace_scroll DOMMouseScroll.ace_scroll", function (b) {
                            if (!v) {
                                if (i(!0), !w)
                                    return !m.lock_anyway;
                                Q && (Q = !1, a("html").off(".ace_scroll"), a(R).off(".ace_scroll"), S && m.$element.trigger("drag.end")), b.deltaY = b.deltaY || 0;
                                var c = b.deltaY > 0 || b.originalEvent.detail < 0 || b.originalEvent.wheelDelta > 0 ? 1 : -1,
                                        d = !1,
                                        e = z[r],
                                        f = z[s];
                                m.lock || (d = -1 == c ? z[t] <= f + e : 0 == f), m.move_bar(!0);
                                var g = parseInt(e / 8);
                                return 80 > g && (g = 80), g > m.size && (g = m.size), g += 1, z[s] = f - c * g, d && !m.lock_anyway
                            }
                        }));
                        var d = ace.vars.touch && "ace_drag" in a.event.special && n.touchDrag;
                        if (d) {
                            var g = "",
                                    h = d ? "ace_drag" : "swipe";
                            this.$element.on(h + ".ace_scroll", function (a) {
                                if (v)
                                    return void(a.retval.cancel = !0);
                                if (i(!0), !w)
                                    return void(a.retval.cancel = this.lock_anyway);
                                if (g = a.direction, u && ("up" == g || "down" == g) || !u && ("left" == g || "right" == g)) {
                                    var b = u ? a.dy : a.dx;
                                    0 != b && (Math.abs(b) > 20 && d && (b = 2 * b), m.move_bar(!0), z[s] = z[s] + b)
                                }
                            })
                        }
                        W && A.addClass("idle-hide"), Z && A.on("mouseenter.ace_scroll", function () {
                            Y = !0, i(!1)
                        }).on("mouseleave.ace_scroll", function () {
                            Y = !1, 0 == Q && j()
                        }), this.$element.on("mouseenter.ace_scroll touchstart.ace_scroll", function () {
                            _ = !0, Z ? i(!0) : n.hoverReset && m.reset(!0), A.addClass("scroll-hover")
                        }).on("mouseleave.ace_scroll touchend.ace_scroll", function () {
                            A.removeClass("scroll-hover")
                        }), u || y.children(0).css(p, this.size), y.css(q, this.size), v = !1, x = !0
                    }
                }, this.is_active = function () {
                    return w
                }, this.is_enabled = function () {
                    return !v
                }, this.move_bar = function (a) {
                    J = a
                }, this.get_track = function () {
                    return C
                }, this.reset = function (a) {
                    if (!v) {
                        x || this.create();
                        var b = this.size;
                        if (!a || _) {
                            if (_ = !1, U) {
                                var c = parseInt(Math.round((parseInt(y.css("border-top-width")) + parseInt(y.css("border-bottom-width"))) / 2.5));
                                b -= c
                            }
                            var d = u ? z[t] : b;
                            if (u && 0 == d || !u && 0 == this.element.scrollWidth)
                                return void A.removeClass("scroll-active");
                            var e = u ? b : z.clientWidth;
                            u || y.children(0).css(p, b), y.css(q, this.size), d > e ? (w = !0, A.css(p, e).show(), O = parseFloat((e / d).toFixed(5)), F = parseInt(Math.round(e * O)), I = parseInt(Math.round(F / 2)), H = e - F, G = parseInt(Math.round(z[s] * O)), E[p] = F + "px", E[o] = G + "px", A.addClass("scroll-active"), 0 == N && k(), K || (n.reset && (z[s] = 0, E[o] = 0), K = !0), U && l()) : (w = !1, A.hide(), A.removeClass("scroll-active"), y.css(q, ""))
                        }
                    }
                }, this.disable = function () {
                    z[s] = 0, E[o] = 0, v = !0, w = !1, A.hide(), this.$element.addClass("scroll-disabled"), A.removeClass("scroll-active"), y.css(q, "")
                }, this.enable = function () {
                    v = !1, this.$element.removeClass("scroll-disabled")
                }, this.destroy = function () {
                    w = !1, v = !1, x = !1, this.$element.removeClass("ace-scroll scroll-disabled scroll-active"), this.$element.off(".ace_scroll"), U || (u || y.find("> div").children().unwrap(), y.children().unwrap(), y.remove()), A.remove(), P !== !1 && (this.element.style.position = P), null != cb && (clearTimeout(cb), cb = null)
                }, this.modify = function (b) {
                    b && (n = a.extend({}, n, b)), this.destroy(), this.create(), _ = !0, this.reset(!0)
                }, this.update = function (c) {
                    c && (n = a.extend({}, n, c)), this.size = c.size || this.size, this.lock = c.mouseWheelLock || this.lock, this.lock_anyway = c.lockAnyway || this.lock_anyway, c.styleClass != b && (L && A.removeClass(L), L = c.styleClass, L && A.addClass(L), M = !!L.match(/scroll\-left|scroll\-top/))
                }, this.start = function () {
                    z[s] = 0
                }, this.end = function () {
                    z[s] = z[t]
                }, this.hide = function () {
                    A.hide()
                }, this.show = function () {
                    A.show()
                }, this.update_scroll = function () {
                    J = !1, E[o] = G + "px", z[s] = parseInt(Math.round(G / O))
                };
                var ab = -1,
                        bb = -1,
                        cb = null,
                        db = 0;
                return this.track_size = function () {
                    return 0 == N && k(), N
                }, this.create(), _ = !0, this.reset(!0), $ = z[t], this
            };
            a.fn.ace_scroll = function (d, e) {
                var f, g = this.each(function () {
                    var b = a(this),
                            g = b.data("ace_scroll"),
                            h = "object" == typeof d && d;
                    g || b.data("ace_scroll", g = new c(this, h)), "string" == typeof d && (f = g[d](e))
                });
                return f === b ? g : f
            }, a.fn.ace_scroll.defaults = {
                size: 200,
                horizontal: !1,
                mouseWheel: !0,
                mouseWheelLock: !1,
                lockAnyway: !1,
                styleClass: !1,
                observeContent: !1,
                hideOnIdle: !1,
                hideDelay: 1500,
                hoverReset: !0,
                reset: !1,
                dragEvent: !1,
                touchDrag: !0,
                touchSwipe: !1,
                scrollEvent: !1,
                detached: !1,
                updatePos: !0
            }
        }(window.jQuery),
        function (a, b) {
            function c(b, c) {
                var d = b.find(".widget-main");
                a(window).off("resize.widget.scroll");
                var e = ace.vars.old_ie || ace.vars.touch;
                if (c) {
                    var f = d.data("ace_scroll");
                    f && d.data("save_scroll", {
                        size: f.size,
                        lock: f.lock,
                        lock_anyway: f.lock_anyway
                    });
                    var g = b.height() - b.find(".widget-header").height() - 10;
                    g = parseInt(g), d.css("min-height", g), e ? (f && d.ace_scroll("disable"), d.css("max-height", g).addClass("overflow-scroll")) : (f ? d.ace_scroll("update", {
                        size: g,
                        mouseWheelLock: !0,
                        lockAnyway: !0
                    }) : d.ace_scroll({
                        size: g,
                        mouseWheelLock: !0,
                        lockAnyway: !0
                    }), d.ace_scroll("enable").ace_scroll("reset")), a(window).on("resize.widget.scroll", function () {
                        var a = b.height() - b.find(".widget-header").height() - 10;
                        a = parseInt(a), d.css("min-height", a), e ? d.css("max-height", a).addClass("overflow-scroll") : d.ace_scroll("update", {
                            size: a
                        }).ace_scroll("reset")
                    })
                } else {
                    d.css("min-height", "");
                    var h = d.data("save_scroll");
                    h && d.ace_scroll("update", {
                        size: h.size,
                        mouseWheelLock: h.lock,
                        lockAnyway: h.lock_anyway
                    }).ace_scroll("enable").ace_scroll("reset"), e ? d.css("max-height", "").removeClass("overflow-scroll") : h || d.ace_scroll("disable")
                }
            }
            var d = function (b) {
                this.$box = a(b);
                this.reload = function () {
                    var a = this.$box,
                            b = !1;
                    "static" == a.css("position") && (b = !0, a.addClass("position-relative")), a.append('<div class="widget-box-overlay"><i class="' + ace.vars.icon + 'loading-icon fa fa-spinner fa-spin fa-2x white"></i></div>'), a.one("reloaded.ace.widget", function () {
                        a.find(".widget-box-overlay").remove(), b && a.removeClass("position-relative")
                    });
                }, this.close = function () {
                    var a = this.$box,
                            b = 300;
                    a.fadeOut(b, function () {
                        a.trigger("closed.ace.widget"), a.remove()
                    })
                }, this.toggle = function (a, b) {
                    var c = this.$box,
                            d = c.find(".widget-body"),
                            e = null,
                            f = "undefined" != typeof a ? a : c.hasClass("collapsed") ? "show" : "hide",
                            g = "show" == f ? "shown" : "hidden";
                    if ("undefined" == typeof b && (b = c.find("> .widget-header a[data-action=collapse]").eq(0), 0 == b.length && (b = null)), b) {
                        e = b.find(ace.vars[".icon"]).eq(0);
                        var h, i = null,
                                j = null;
                        (i = e.attr("data-icon-show")) ? j = e.attr("data-icon-hide") : (h = e.attr("class").match(/fa\-(.*)\-(up|down)/)) && (i = "fa-" + h[1] + "-down", j = "fa-" + h[1] + "-up")
                    }
                    var k = 250,
                            l = 200;
                    "show" == f ? (e && e.removeClass(i).addClass(j), d.hide(), c.removeClass("collapsed"), d.slideDown(k, function () {
                        c.trigger(g + ".ace.widget")
                    })) : (e && e.removeClass(j).addClass(i), d.slideUp(l, function () {
                        c.addClass("collapsed"), c.trigger(g + ".ace.widget")
                    }))
                }, this.hide = function () {
                    this.toggle("hide")
                }, this.show = function () {
                    this.toggle("show")
                }, this.fullscreen = function () {
                    var a = this.$box.find("> .widget-header a[data-action=fullscreen]").find(ace.vars[".icon"]).eq(0),
                            b = null,
                            d = null;
                    (b = a.attr("data-icon1")) ? d = a.attr("data-icon2") : (b = "fa-expand", d = "fa-compress"), this.$box.hasClass("fullscreen") ? (a.addClass(b).removeClass(d), this.$box.removeClass("fullscreen"), c(this.$box, !1)) : (a.removeClass(b).addClass(d), this.$box.addClass("fullscreen"), c(this.$box, !0)), this.$box.trigger("fullscreened.ace.widget")
                }
            };
            a.fn.widget_box = function (c, e) {
                var f, g = this.each(function () {
                    var b = a(this),
                            g = b.data("widget_box"),
                            h = "object" == typeof c && c;
                    g || b.data("widget_box", g = new d(this, h)), "string" == typeof c && (f = g[c](e))
                });
                return f === b ? g : f
            }, a(document).on("click.ace.widget", ".widget-header a[data-action]", function (b) {
                b.preventDefault();
                var c = a(this),
                        e = c.closest(".widget-box");
                if (0 != e.length && !e.hasClass("ui-sortable-helper")) {
                    var f = e.data("widget_box");
                    f || e.data("widget_box", f = new d(e.get(0)));
                    var g = c.data("action");
                    if ("collapse" == g) {
                        var h, i = e.hasClass("collapsed") ? "show" : "hide";
                        if (e.trigger(h = a.Event(i + ".ace.widget")), h.isDefaultPrevented())
                            return;
                        f.toggle(i, c);
                    } else if ("close" == g) {
                        var h;
                        if (e.trigger(h = a.Event("close.ace.widget")), h.isDefaultPrevented())
                            return;
                        f.close()
                    } else if ("reload" == g) {
                        c.blur();
                        var h;
                        if (e.trigger(h = a.Event("reload.ace.widget")), h.isDefaultPrevented())
                            return;
                        f.reload();
                    } else if ("fullscreen" == g) {
                        var h;
                        if (e.trigger(h = a.Event("fullscreen.ace.widget")), h.isDefaultPrevented())
                            return;
                        f.fullscreen()
                    } else
                        "settings" == g && e.trigger("setting.ace.widget")
                }
            })
        }(window.jQuery),
        function (a) {
            a(document).on("reload.ace.widget", ".widget-box", function () {
                var b = a(this);
                setTimeout(function () {
                    b.trigger("reloaded.ace.widget")
                }, parseInt(1e3 * Math.random() + 1e3))
            })
        }(window.jQuery);

//$('.widget-box').on('reload.ace.widget', function (e) {
//    //this = the widget-box
//});
