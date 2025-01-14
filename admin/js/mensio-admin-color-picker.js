
 (function (e, t) {
    function c() {
        var t, n;
        r ? i = "filter" : (t = e('<div id="iris-gradtest" />'), n = "linear-gradient(top,#fff,#000)", e.each(s, function (e, r) {
            t.css("backgroundImage", r + n);
            if (t.css("backgroundImage").match("gradient")) return i = e, !1
        }), i === !1 && (t.css("background", "-webkit-gradient(linear,0% 0%,0% 100%,from(#fff),to(#000))"), t.css("backgroundImage").match("gradient") && (i = "webkit")), t.remove())
    }
    function h(t, n) {
        return t = t === "top" ? "top" : "left", n = e.isArray(n) ? n : Array.prototype.slice.call(arguments, 1), i === "webkit" ? d(t, n) : s[i] + "linear-gradient(" + t + ", " + n.join(", ") + ")"
    }
    function p(t, n) {
        var r, i, s, o, u, a, f, l, c;
        t = t === "top" ? "top" : "left", n = e.isArray(n) ? n : Array.prototype.slice.call(arguments, 1), r = t === "top" ? 0 : 1, i = e(this), s = n.length - 1, o = "filter", u = r === 1 ? "left" : "top", a = r === 1 ? "right" : "bottom", f = r === 1 ? "height" : "width", l = '<div class="iris-ie-gradient-shim" style="position:absolute;' + f + ":100%;" + u + ":%start%;" + a + ":%end%;" + o + ':%filter%;" data-color:"%color%"></div>', c = "", i.css("position") === "static" && i.css({
            position: "relative"
        }), n = v(n), e.each(n, function (e, t) {
            var i, o, u;
            if (e === s) return !1;
            i = n[e + 1];
            if (t.stop === i.stop) return;
            o = 100 - parseFloat(i.stop) + "%", t.octoHex = (new Color(t.color)).toIEOctoHex(), i.octoHex = (new Color(i.color)).toIEOctoHex(), u = "progid:DXImageTransform.Microsoft.Gradient(GradientType=" + r + ", StartColorStr='" + t.octoHex + "', EndColorStr='" + i.octoHex + "')", c += l.replace("%start%", t.stop).replace("%end%", o).replace("%filter%", u)
        }), i.find(".iris-ie-gradient-shim").remove(), e(c).prependTo(i)
    }
    function d(t, n) {
        var r = [];
        return t = t === "top" ? "0% 0%,0% 100%," : "0% 100%,100% 100%,", n = v(n), e.each(n, function (e, t) {
            r.push("color-stop(" + parseFloat(t.stop) / 100 + ", " + t.color + ")")
        }), "-webkit-gradient(linear," + t + r.join(",") + ")"
    }
    function v(t) {
        var n = [],
            r = [],
            i = [],
            s = t.length - 1;
        return e.each(t, function (e, t) {
            var i = t,
                s = !1,
                o = t.match(/1?[0-9]{1,2}%$/);
            o && (i = t.replace(/\s?1?[0-9]{1,2}%$/, ""), s = o.shift()), n.push(i), r.push(s)
        }), r[0] === !1 && (r[0] = "0%"), r[s] === !1 && (r[s] = "100%"), r = m(r), e.each(r, function (e) {
            i[e] = {
                color: n[e],
                stop: r[e]
            }
        }), i
    }
    function m(t) {
        var n = 0,
            r = t.length - 1,
            i = 0,
            s = !1,
            o, u, a, f;
        if (t.length <= 2 || e.inArray(!1, t) < 0) return t;
        while (i < t.length - 1)!s && t[i] === !1 ? (n = i - 1, s = !0) : s && t[i] !== !1 && (r = i, i = t.length), i++;
        u = r - n, f = parseInt(t[n].replace("%"), 10), o = (parseFloat(t[r].replace("%")) - f) / u, i = n + 1, a = 1;
        while (i < r) t[i] = f + a * o + "%", a++, i++;
        return m(t)
    }
    var n, r, i, s, o, u, a, f, l;
    n = '<div class="iris-picker"><div class="iris-picker-inner"><div class="iris-square"><a class="iris-square-value" href="#"><span class="iris-square-handle ui-slider-handle"></span></a><div class="iris-square-inner iris-square-horiz"></div><div class="iris-square-inner iris-square-vert"></div></div><div class="iris-slider iris-strip"><div class="iris-slider-offset"></div></div></div></div>', a = navigator.userAgent.toLowerCase(), f = navigator.appName === "Microsoft Internet Explorer", l = f ? parseFloat(a.match(/msie ([0-9]{1,}[\.0-9]{0,})/)[1]) : 0, r = f && l < 10, i = !1, s = ["-moz-", "-webkit-", "-o-", "-ms-"], o = '.iris-picker{display:block;position:relative}.iris-picker,.iris-picker *{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input + .iris-picker{margin-top:4px}.iris-error{background-color:#ffafaf}.iris-border{border-radius:3px;border:1px solid #aaa;width:200px;background-color:#fff}.iris-picker-inner{position:absolute;top:0;right:0;left:0;bottom:0}.iris-border .iris-picker-inner{top:10px;right:10px;left:10px;bottom:10px}.iris-picker .iris-square-inner{position:absolute;left:0;right:0;top:0;bottom:0}.iris-picker .iris-square,.iris-picker .iris-slider,.iris-picker .iris-square-inner,.iris-picker .iris-palette{border-radius:3px;box-shadow:inset 0 0 5px rgba(0,0,0,0.4);height:100%;width:12.5%;float:left;margin-right:5%}.iris-picker .iris-square{width:76%;margin-right:10%;position:relative}.iris-picker .iris-square-inner{width:auto;margin:0}.iris-ie-9 .iris-square,.iris-ie-9 .iris-slider,.iris-ie-9 .iris-square-inner,.iris-ie-9 .iris-palette{box-shadow:none;border-radius:0}.iris-ie-9 .iris-square,.iris-ie-9 .iris-slider,.iris-ie-9 .iris-palette{outline:1px solid rgba(0,0,0,.1)}.iris-ie-lt9 .iris-square,.iris-ie-lt9 .iris-slider,.iris-ie-lt9 .iris-square-inner,.iris-ie-lt9 .iris-palette{outline:1px solid #aaa}.iris-ie-lt9 .iris-square .ui-slider-handle{outline:1px solid #aaa;background-color:#fff;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=30)"}.iris-ie-lt9 .iris-square .iris-square-handle{background:none;border:3px solid #fff;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"}.iris-picker .iris-strip{margin-right:0;position:relative}.iris-picker .iris-strip .ui-slider-handle{position:absolute;background:none;right:-3px;left:-3px;border:4px solid #aaa;border-width:4px 3px;width:auto;height:6px;border-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,.2);opacity:.9;z-index:5;cursor:ns-resize}.iris-strip .ui-slider-handle:before{content:" ";position:absolute;left:-2px;right:-2px;top:-3px;bottom:-3px;border:2px solid #fff;border-radius:3px}.iris-picker .iris-slider-offset{position:absolute;top:11px;left:0;right:0;bottom:-3px}.iris-picker .iris-square-handle{background:transparent;border:5px solid #aaa;border-radius:50%;border-color:rgba(128,128,128,.5);box-shadow:none;width:12px;height:12px;position:absolute;left:-10px;top:-10px;cursor:move;opacity:1;z-index:10}.iris-picker .ui-state-focus .iris-square-handle{opacity:.8}.iris-picker .iris-square-handle:hover{border-color:#999}.iris-picker .iris-square-value:focus .iris-square-handle{box-shadow:0 0 2px rgba(0,0,0,.75);opacity:.8}.iris-picker .iris-square-handle:hover::after{border-color:#fff}.iris-picker .iris-square-handle::after{position:absolute;bottom:-4px;right:-4px;left:-4px;top:-4px;border:3px solid #f9f9f9;border-color:rgba(255,255,255,.8);border-radius:50%;content:" "}.iris-picker .iris-square-value{width:8px;height:8px;position:absolute}.iris-ie-lt9 .iris-square-value,.iris-mozilla .iris-square-value{width:1px;height:1px}.iris-palette-container{position:absolute;bottom:0;left:0;margin:0;padding:0}.iris-border .iris-palette-container{left:10px;bottom:10px}.iris-picker .iris-palette{margin:0;cursor:pointer}';
    if (r && l <= 7) {
        e.fn.iris = e.noop, e.support.iris = !1;
        return
    }
    e.support.iris = !0, e.fn.gradient = function (t) {
        var n = arguments;
        return this.each(function () {
            r ? p.apply(this, n) : e(this).css("backgroundImage", h.apply(this, n))
        })
    }, e.fn.raninbowGradient = function (t, n) {
        var r, i, s, o;
        t = t || "top", r = e.extend({}, {
            s: 100,
            l: 50
        }, n), i = "hsl(%h%," + r.s + "%," + r.l + "%)", s = 0, o = [];
        while (s <= 360) o.push(i.replace("%h%", s)), s += 30;
        return this.each(function () {
            e(this).gradient(t, o)
        })
    }, u = {
        options: {
            color: !1,
            mode: "hsl",
            controls: {
                horiz: "s",
                vert: "l",
                strip: "h"
            },
            hide: !0,
            border: !0,
            target: !1,
            width: 200,
            palettes: !1
        },
        _color: "",
        _palettes: ["#000", "#fff", "#d33", "#d93", "#ee2", "#81d742", "#1e73be", "#8224e3"],
        _inited: !1,
        _defaultHSLControls: {
            horiz: "s",
            vert: "l",
            strip: "h"
        },
        _defaultHSVControls: {
            horiz: "h",
            vert: "v",
            strip: "s"
        },
        _scale: {
            h: 360,
            s: 100,
            l: 100,
            v: 100
        },
        _create: function () {
            var t = this,
                r = t.element,
                s = t.options.color || r.val(),
                o;
            i === !1 && c(), r.is("input") ? (t.options.target ? t.picker = e(n).appendTo(t.options.target) : t.picker = e(n).insertAfter(r), t._addInputListeners(r)) : (r.append(n), t.picker = r.find(".iris-picker")), f ? l === 9 ? t.picker.addClass("iris-ie-9") : l <= 8 && t.picker.addClass("iris-ie-lt9") : a.indexOf("compatible") < 0 && a.indexOf("khtml") < 0 && a.match(/mozilla/) && t.picker.addClass("iris-mozilla"), t.options.palettes && t._addPalettes(), t._color = (new Color(s)).setHSpace(t.options.mode), t.options.color = t._color.toString(), t.controls = {
                square: t.picker.find(".iris-square"),
                squareDrag: t.picker.find(".iris-square-value"),
                horiz: t.picker.find(".iris-square-horiz"),
                vert: t.picker.find(".iris-square-vert"),
                strip: t.picker.find(".iris-strip"),
                stripSlider: t.picker.find(".iris-strip .iris-slider-offset")
            }, t.options.mode === "hsv" && t._has("l", t.options.controls) ? t.options.controls = t._defaultHSVControls : t.options.mode === "hsl" && t._has("v", t.options.controls) && (t.options.controls = t._defaultHSLControls), t.hue = t._color.h(), t.options.hide && t.picker.hide(), t.options.border && t.picker.addClass("iris-border"), t._initControls(), t.active = "external", t._dimensions(), t._change()
        },
        _has: function (t, n) {
            var r = !1;
            return e.each(n, function (e, n) {
                if (t === n) return r = !0, !1
            }), r
        },
        _addPalettes: function () {
            var t = e("<div class='iris-palette-container' />"),
                n = e("<a class='iris-palette' tabindex='0' />"),
                r = e.isArray(this.options.palettes) ? this.options.palettes : this._palettes;
            this.picker.find(".iris-palette-container").length && (t = this.picker.find(".iris-palette-container").detach().html("")), e.each(r, function (e, r) {
                n.clone().data("color", r).css("backgroundColor", r).appendTo(t).height(10).width(10)
            }), this.picker.append(t)
        },
        _paint: function () {
            var e = this;
            e._paintDimension("top", "strip"), e._paintDimension("top", "vert"), e._paintDimension("left", "horiz")
        },
        _paintDimension: function (e, t) {
            var n = this,
                r = n._color,
                i = n.options.mode,
                s = n._getHSpaceColor(),
                o = n.controls[t],
                u = n.options.controls,
                a;
            if (t === n.active || n.active === "square" && t !== "strip") return;
            switch (u[t]) {
                case "h":
                    if (i === "hsv") {
                        s = r.clone();
                        switch (t) {
                            case "horiz":
                                s[u.vert](100);
                                break;
                            case "vert":
                                s[u.horiz](100);
                                break;
                            case "strip":
                                s.setHSpace("hsl")
                        }
                        a = s.toHsl()
                    } else t === "strip" ? a = {
                        s: s.s,
                        l: s.l
                    } : a = {
                        s: 100,
                        l: s.l
                    };
                    o.raninbowGradient(e, a);
                    break;
                case "s":
                    i === "hsv" ? t === "vert" ? a = [r.clone().a(0).s(0).toCSS("rgba"), r.clone().a(1).s(0).toCSS("rgba")] : t === "strip" ? a = [r.clone().s(100).toCSS("hsl"), r.clone().s(0).toCSS("hsl")] : t === "horiz" && (a = ["#fff", "hsl(" + s.h + ",100%,50%)"]) : t === "vert" && n.options.controls.horiz === "h" ? a = ["hsla(0, 0%, " + s.l + "%, 0)", "hsla(0, 0%, " + s.l + "%, 1)"] : a = ["hsl(" + s.h + ",0%,50%)", "hsl(" + s.h + ",100%,50%)"], o.gradient(e, a);
                    break;
                case "l":
                    t === "strip" ? a = ["hsl(" + s.h + ",100%,100%)", "hsl(" + s.h + ", " + s.s + "%,50%)", "hsl(" + s.h + ",100%,0%)"] : a = ["#fff", "rgba(255,255,255,0) 50%", "rgba(0,0,0,0) 50%", "rgba(0,0,0,1)"], o.gradient(e, a);
                    break;
                case "v":
                    t === "strip" ? a = [r.clone().v(100).toCSS(), r.clone().v(0).toCSS()] : a = ["rgba(0,0,0,0)", "#000"], o.gradient(e, a);
                    break;
                default:
            }
        },
        _getHSpaceColor: function () {
            return this.options.mode === "hsv" ? this._color.toHsv() : this._color.toHsl()
        },
        _dimensions: function (t) {
            var n = this,
                r = n.options,
                i = n.picker.find(".iris-picker-inner"),
                s = n.controls,
                o = s.square,
                u = n.picker.find(".iris-strip"),
                a = "77.5%",
                f = "12%",
                l = 20,
                c = r.border ? r.width - l : r.width,
                h, p = e.isArray(r.palettes) ? r.palettes.length : n._palettes.length,
                d, v, m;
            t && (o.css("width", ""), u.css("width", ""), n.picker.css({
                width: "",
                height: ""
            })), a = c * (parseFloat(a) / 100), f = c * (parseFloat(f) / 100), h = r.border ? a + l : a, o.width(a).height(a), u.height(a).width(f), n.picker.css({
                width: r.width,
                height: h
            });
            if (!r.palettes) return;
            d = a * 2 / 100, m = a - (p - 1) * d, v = m / p, n.picker.find(".iris-palette").each(function (t, n) {
                var r = t === 0 ? 0 : d;
                e(this).css({
                    width: v,
                    height: v,
                    marginLeft: r
                })
            }), n.picker.css("paddingBottom", v + d), u.height(v + d + a)
        },
        _addInputListeners: function (e) {
            var t = this,
                n = 100,
                r = function (n) {
                    var r = new Color(e.val()),
                        i = e.val().replace(/^#/, "");
                    e.removeClass("iris-error"), r.error ? i !== "" && e.addClass("iris-error") : r.toString() !== t._color.toString() && (n.type !== "keyup" || !i.match(/^[0-9a-fA-F]{3}$/)) && t._setOption("color", r.toString())
                };
            e.on("change", r).on("keyup", t._debounce(r, n)), t.options.hide && e.one("focus", function () {
                t.show()
            })
        },
        _initControls: function () {
            var t = this,
                n = t.controls,
                r = n.square,
                i = t.options.controls,
                s = t._scale[i.strip];
            n.stripSlider.slider({
                orientation: "vertical",
                max: s,
                slide: function (e, n) {
                    t.active = "strip", i.strip === "h" && (n.value = s - n.value), t._color[i.strip](n.value), t._change.apply(t, arguments)
                }
            }), n.squareDrag.draggable({
                containment: "parent",
                zIndex: 1e3,
                cursor: "move",
                drag: function (e, n) {
                    t._squareDrag(e, n)
                },
                start: function () {
                    r.addClass("iris-dragging"), e(this).addClass("ui-state-focus")
                },
                stop: function () {
                    r.removeClass("iris-dragging"), e(this).removeClass("ui-state-focus")
                }
            }).on("mousedown mouseup", function (n) {
                var r = "ui-state-focus";
                n.preventDefault(), n.type === "mousedown" ? (t.picker.find("." + r).removeClass(r).blur(), e(this).addClass(r).focus()) : e(this).removeClass(r)
            }).on("keydown", function (e) {
                var r = n.square,
                    i = n.squareDrag,
                    s = i.position(),
                    o = t.options.width / 100;
                e.altKey && (o *= 10);
                switch (e.keyCode) {
                    case 37:
                        s.left -= o;
                        break;
                    case 38:
                        s.top -= o;
                        break;
                    case 39:
                        s.left += o;
                        break;
                    case 40:
                        s.top += o;
                        break;
                    default:
                        return !0
                }
                s.left = Math.max(0, Math.min(s.left, r.width())), s.top = Math.max(0, Math.min(s.top, r.height())), i.css(s), t._squareDrag(e, {
                    position: s
                }), e.preventDefault()
            }), r.mousedown(function (n) {
                var r, i;
                if (n.which !== 1) return;
                if (!e(n.target).is("div")) return;
                r = t.controls.square.offset(), i = {
                    top: n.pageY - r.top,
                    left: n.pageX - r.left
                }, n.preventDefault(), t._squareDrag(n, {
                    position: i
                }), n.target = t.controls.squareDrag.get(0), t.controls.squareDrag.css(i).trigger(n)
            }), t.options.palettes && t._paletteListeners()
        },
        _paletteListeners: function () {
            var t = this;
            t.picker.find(".iris-palette-container").on("click.palette", ".iris-palette", function (n) {
                t._color.fromCSS(e(this).data("color")), t.active = "external", t._change()
            }).on("keydown.palette", ".iris-palette", function (t) {
                if (t.keyCode !== 13 && t.keyCode !== 32) return !0;
                t.stopPropagation(), e(this).click()
            })
        },
        _squareDrag: function (e, t) {
            var n = this,
                r = n.options.controls,
                i = n._squareDimensions(),
                s = Math.round((i.h - t.position.top) / i.h * n._scale[r.vert]),
                o = n._scale[r.horiz] - Math.round((i.w - t.position.left) / i.w * n._scale[r.horiz]);
            n._color[r.horiz](o)[r.vert](s), n.active = "square", n._change.apply(n, arguments)
        },
        _setOption: function (t, n) {
            var r = this,
                i = r.options[t],
                s = !1,
                o, u, a;
            r.options[t] = n;
            switch (t) {
                case "color":
                    n = "" + n, o = n.replace(/^#/, ""), u = (new Color(n)).setHSpace(r.options.mode), u.error ? r.options[t] = i : (r._color = u, r.options.color = r.options[t] = r._color.toString(), r.active = "external", r._change());
                    break;
                case "palettes":
                    s = !0, n ? r._addPalettes() : r.picker.find(".iris-palette-container").remove(), i || r._paletteListeners();
                    break;
                case "width":
                    s = !0;
                    break;
                case "border":
                    s = !0, a = n ? "addClass" : "removeClass", r.picker[a]("iris-border");
                    break;
                case "mode":
                case "controls":
                    if (i === n) return;
                    return a = r.element, i = r.options, i.hide = !r.picker.is(":visible"), r.destroy(), r.picker.remove(), e(r.element).iris(i)
            }
            s && r._dimensions(!0)
        },
        _squareDimensions: function (e) {
            var n = this.controls.square,
                r, i;
            return e !== t && n.data("dimensions") ? n.data("dimensions") : (i = this.controls.squareDrag, r = {
                w: n.width(),
                h: n.height()
            }, n.data("dimensions", r), r)
        },
        _isNonHueControl: function (e, t) {
            return e === "square" && this.options.controls.strip === "h" ? !0 : t === "external" || t === "h" && e === "strip" ? !1 : !0
        },
        _change: function (t, n) {
            var r = this,
                i = r.controls,
                s = r._getHSpaceColor(),
                o = r._color.toString(),
                u = ["square", "strip"],
                a = r.options.controls,
                f = a[r.active] || "external",
                l = r.hue;
            r.active === "strip" ? u = [] : r.active !== "external" && u.pop(), e.each(u, function (e, t) {
                var n, o, u;
                if (t !== r.active) switch (t) {
                    case "strip":
                        n = a.strip === "h" ? r._scale[a.strip] - s[a.strip] : s[a.strip], i.stripSlider.slider("value", n);
                        break;
                    case "square":
                        o = r._squareDimensions(), u = {
                            left: s[a.horiz] / r._scale[a.horiz] * o.w,
                            top: o.h - s[a.vert] / r._scale[a.vert] * o.h
                        }, r.controls.squareDrag.css(u)
                }
            }), s.h !== l && r._isNonHueControl(r.active, f) && r._color.h(l), r.hue = r._color.h(), r.options.color = r._color.toString(), r._inited && r._trigger("change", {
                type: r.active
            }, {
                color: r._color
            }), r.element.is(":input") && !r._color.error && (r.element.removeClass("iris-error"), r.element.val() !== r._color.toString() && r.element.val(r._color.toString())), r._paint(), r._inited = !0, r.active = !1
        },
        _debounce: function (e, t, n) {
            var r, i;
            return function () {
                var s = this,
                    o = arguments,
                    u, a;
                return u = function () {
                    r = null, n || (i = e.apply(s, o))
                }, a = n && !r, clearTimeout(r), r = setTimeout(u, t), a && (i = e.apply(s, o)), i
            }
        },
        show: function () {
            this.picker.show()
        },
        hide: function () {
            this.picker.hide()
        },
        toggle: function () {
            this.picker.toggle()
        },
        color: function (e) {
            if (e === !0) return this._color.clone();
            if (e === t) return this._color.toString();
            this.option("color", e)
        }
    }, e.widget("a8c.iris", u), e('<style id="iris-css">' + o + "</style>").appendTo("head")
})(jQuery),
function (e, t) {
    var n = function (e, t) {
        return this instanceof n ? this._init(e, t) : new n(e, t)
    };
    n.fn = n.prototype = {
        _color: 0,
        _alpha: 1,
        error: !1,
        _hsl: {
            h: 0,
            s: 0,
            l: 0
        },
        _hsv: {
            h: 0,
            s: 0,
            v: 0
        },
        _hSpace: "hsl",
        _init: function (e) {
            var n = "noop";
            switch (typeof e) {
                case "object":
                    return e.a !== t && this.a(e.a), n = e.r !== t ? "fromRgb" : e.l !== t ? "fromHsl" : e.v !== t ? "fromHsv" : n, this[n](e);
                case "string":
                    return this.fromCSS(e);
                case "number":
                    return this.fromInt(parseInt(e, 10))
            }
            return this
        },
        _error: function () {
            return this.error = !0, this
        },
        clone: function () {
            var e = new n(this.toInt()),
                t = ["_alpha", "_hSpace", "_hsl", "_hsv", "error"];
            for (var r = t.length - 1; r >= 0; r--) e[t[r]] = this[t[r]];
            return e
        },
        setHSpace: function (e) {
            return this._hSpace = e === "hsv" ? e : "hsl", this
        },
        noop: function () {
            return this
        },
        fromCSS: function (e) {
            var t, n, r = /^(rgb|hs(l|v))a?\(/;
            this.error = !1, e = e.replace(/^\s+/, "").replace(/\s+$/, "").replace(/;$/, "");
            if (e.match(r) && e.match(/\)$/)) {
                n = e.replace(/(\s|%)/g, "").replace(r, "").replace(/,?\);?$/, "").split(",");
                if (n.length < 3) return this._error();
                if (n.length === 4) {
                    this.a(parseFloat(n.pop()));
                    if (this.error) return this
                }
                for (var i = n.length - 1; i >= 0; i--) {
                    n[i] = parseInt(n[i], 10);
                    if (isNaN(n[i])) return this._error()
                }
                return e.match(/^rgb/) ? this.fromRgb({
                    r: n[0],
                    g: n[1],
                    b: n[2]
                }) : e.match(/^hsv/) ? this.fromHsv({
                    h: n[0],
                    s: n[1],
                    v: n[2]
                }) : this.fromHsl({
                    h: n[0],
                    s: n[1],
                    l: n[2]
                })
            }
            return this.fromHex(e)
        },
        fromRgb: function (e, n) {
            return typeof e != "object" || e.r === t || e.g === t || e.b === t ? this._error() : (this.error = !1, this.fromInt(parseInt((e.r << 16) + (e.g << 8) + e.b, 10), n))
        },
        fromHex: function (e) {
            return e = e.replace(/^#/, "").replace(/^0x/, ""), e.length === 3 && (e = e[0] + e[0] + e[1] + e[1] + e[2] + e[2]), this.error = !/^[0-9A-F]{6}$/i.test(e), this.fromInt(parseInt(e, 16))
        },
        fromHsl: function (e) {
            var n, r, i, s, o, u, a, f;
            return typeof e != "object" || e.h === t || e.s === t || e.l === t ? this._error() : (this._hsl = e, this._hSpace = "hsl", u = e.h / 360, a = e.s / 100, f = e.l / 100, a === 0 ? n = r = i = f : (s = f < .5 ? f * (1 + a) : f + a - f * a, o = 2 * f - s, n = this.hue2rgb(o, s, u + 1 / 3), r = this.hue2rgb(o, s, u), i = this.hue2rgb(o, s, u - 1 / 3)), this.fromRgb({
                r: n * 255,
                g: r * 255,
                b: i * 255
            }, !0))
        },
        fromHsv: function (e) {
            var n, r, i, s, o, u, a, f, l, c, h;
            if (typeof e != "object" || e.h === t || e.s === t || e.v === t) return this._error();
            this._hsv = e, this._hSpace = "hsv", n = e.h / 360, r = e.s / 100, i = e.v / 100, a = Math.floor(n * 6), f = n * 6 - a, l = i * (1 - r), c = i * (1 - f * r), h = i * (1 - (1 - f) * r);
            switch (a % 6) {
                case 0:
                    s = i, o = h, u = l;
                    break;
                case 1:
                    s = c, o = i, u = l;
                    break;
                case 2:
                    s = l, o = i, u = h;
                    break;
                case 3:
                    s = l, o = c, u = i;
                    break;
                case 4:
                    s = h, o = l, u = i;
                    break;
                case 5:
                    s = i, o = l, u = c
            }
            return this.fromRgb({
                r: s * 255,
                g: o * 255,
                b: u * 255
            }, !0)
        },
        fromInt: function (e, n) {
            return this._color = parseInt(e, 10), isNaN(this._color) && (this._color = 0), this._color > 16777215 ? this._color = 16777215 : this._color < 0 && (this._color = 0), n === t && (this._hsv.h = this._hsv.s = this._hsl.h = this._hsl.s = 0), this
        },
        hue2rgb: function (e, t, n) {
            return n < 0 && (n += 1), n > 1 && (n -= 1), n < 1 / 6 ? e + (t - e) * 6 * n : n < .5 ? t : n < 2 / 3 ? e + (t - e) * (2 / 3 - n) * 6 : e
        },
        toString: function () {
            var e = parseInt(this._color, 10).toString(16);
            if (this.error) return "";
            if (e.length < 6) for (var t = 6 - e.length - 1; t >= 0; t--) e = "0" + e;
            return "#" + e
        },
        toCSS: function (e, t) {
            e = e || "hex", t = parseFloat(t || this._alpha);
            switch (e) {
                case "rgb":
                case "rgba":
                    var n = this.toRgb();
                    return t < 1 ? "rgba( " + n.r + ", " + n.g + ", " + n.b + ", " + t + " )" : "rgb( " + n.r + ", " + n.g + ", " + n.b + " )";
                case "hsl":
                case "hsla":
                    var r = this.toHsl();
                    return t < 1 ? "hsla( " + r.h + ", " + r.s + "%, " + r.l + "%, " + t + " )" : "hsl( " + r.h + ", " + r.s + "%, " + r.l + "% )";
                default:
                    return this.toString()
            }
        },
        toRgb: function () {
            return {
                r: 255 & this._color >> 16,
                g: 255 & this._color >> 8,
                b: 255 & this._color
            }
        },
        toHsl: function () {
            var e = this.toRgb(),
                t = e.r / 255,
                n = e.g / 255,
                r = e.b / 255,
                i = Math.max(t, n, r),
                s = Math.min(t, n, r),
                o, u, a = (i + s) / 2;
            if (i === s) o = u = 0;
            else {
                var f = i - s;
                u = a > .5 ? f / (2 - i - s) : f / (i + s);
                switch (i) {
                    case t:
                        o = (n - r) / f + (n < r ? 6 : 0);
                        break;
                    case n:
                        o = (r - t) / f + 2;
                        break;
                    case r:
                        o = (t - n) / f + 4
                }
                o /= 6
            }
            return o = Math.round(o * 360), o === 0 && this._hsl.h !== o && (o = this._hsl.h), u = Math.round(u * 100), u === 0 && this._hsl.s && (u = this._hsl.s), {
                h: o,
                s: u,
                l: Math.round(a * 100)
            }
        },
        toHsv: function () {
            var e = this.toRgb(),
                t = e.r / 255,
                n = e.g / 255,
                r = e.b / 255,
                i = Math.max(t, n, r),
                s = Math.min(t, n, r),
                o, u, a = i,
                f = i - s;
            u = i === 0 ? 0 : f / i;
            if (i === s) o = u = 0;
            else {
                switch (i) {
                    case t:
                        o = (n - r) / f + (n < r ? 6 : 0);
                        break;
                    case n:
                        o = (r - t) / f + 2;
                        break;
                    case r:
                        o = (t - n) / f + 4
                }
                o /= 6
            }
            return o = Math.round(o * 360), o === 0 && this._hsv.h !== o && (o = this._hsv.h), u = Math.round(u * 100), u === 0 && this._hsv.s && (u = this._hsv.s), {
                h: o,
                s: u,
                v: Math.round(a * 100)
            }
        },
        toInt: function () {
            return this._color
        },
        toIEOctoHex: function () {
            var e = this.toString(),
                t = parseInt(255 * this._alpha, 10).toString(16);
            return t.length === 1 && (t = "0" + t), "#" + t + e.replace(/^#/, "")
        },
        toLuminosity: function () {
            var e = this.toRgb();
            return .2126 * Math.pow(e.r / 255, 2.2) + .7152 * Math.pow(e.g / 255, 2.2) + .0722 * Math.pow(e.b / 255, 2.2)
        },
        getDistanceLuminosityFrom: function (e) {
            if (e instanceof n) {
                var t = this.toLuminosity(),
                    r = e.toLuminosity();
                return t > r ? (t + .05) / (r + .05) : (r + .05) / (t + .05)
            }
            throw "getDistanceLuminosityFrom requires a Color object"
        },
        getMaxContrastColor: function () {
            var e = this.toLuminosity(),
                t = e >= .5 ? "000000" : "ffffff";
            return new n(t)
        },
        getGrayscaleContrastingColor: function (e) {
            if (!e) return this.getMaxContrastColor();
            var t = e < 5 ? 5 : e,
                n = this.getMaxContrastColor();
            e = n.getDistanceLuminosityFrom(this);
            if (e <= t) return n;
            var r = 0 === n.toInt() ? 1 : -1;
            while (e > t) n = n.incrementLightness(r), e = n.getDistanceLuminosityFrom(this);
            return n
        },
        getReadableContrastingColor: function (e, r) {
            if (!e instanceof n) return this;
            var i = r === t ? 5 : r,
                s = e.getDistanceLuminosityFrom(this),
                o = e.getMaxContrastColor(),
                u = o.getDistanceLuminosityFrom(e);
            if (u <= i) return o;
            if (s >= i) return this;
            var a = 0 === o.toInt() ? -1 : 1;
            while (s < i) {
                this.incrementLightness(a), s = this.getDistanceLuminosityFrom(e);
                if (this._color === 0 || this._color === 16777215) break
            }
            return this
        },
        a: function (e) {
            if (e === t) return this._alpha;
            var n = parseFloat(e);
            return isNaN(n) ? this._error() : (this._alpha = n, this)
        },
        darken: function (e) {
            return e = e || 5, this.l(-e, !0)
        },
        lighten: function (e) {
            return e = e || 5, this.l(e, !0)
        },
        saturate: function (e) {
            return e = e || 15, this.s(e, !0)
        },
        desaturate: function (e) {
            return e = e || 15, this.s(-e, !0)
        },
        toGrayscale: function () {
            return this.setHSpace("hsl").s(0)
        },
        getComplement: function () {
            return this.h(180, !0)
        },
        getSplitComplement: function (e) {
            e = e || 1;
            var t = 180 + e * 30;
            return this.h(t, !0)
        },
        getAnalog: function (e) {
            e = e || 1;
            var t = e * 30;
            return this.h(t, !0)
        },
        getTetrad: function (e) {
            e = e || 1;
            var t = e * 60;
            return this.h(t, !0)
        },
        getTriad: function (e) {
            e = e || 1;
            var t = e * 120;
            return this.h(t, !0)
        },
        _partial: function (e) {
            var n = r[e];
            return function (r, i) {
                var s = this._spaceFunc("to", n.space);
                return r === t ? s[e] : (i === !0 && (r = s[e] + r), n.mod && (r %= n.mod), n.range && (r = r < n.range[0] ? n.range[0] : r > n.range[1] ? n.range[1] : r), s[e] = r, this._spaceFunc("from", n.space, s))
            }
        },
        _spaceFunc: function (e, t, n) {
            var r = t || this._hSpace,
                i = e + r.charAt(0).toUpperCase() + r.substr(1);
            return this[i](n)
        }
    };
    var r = {
        h: {
            mod: 360
        },
        s: {
            range: [0, 100]
        },
        l: {
            space: "hsl",
            range: [0, 100]
        },
        v: {
            space: "hsv",
            range: [0, 100]
        },
        r: {
            space: "rgb",
            range: [0, 255]
        },
        g: {
            space: "rgb",
            range: [0, 255]
        },
        b: {
            space: "rgb",
            range: [0, 255]
        }
    };
    for (var i in r) r.hasOwnProperty(i) && (n.fn[i] = n.fn._partial(i));
    e.Color = n
}(typeof exports == "object" && exports || this);
$ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    $('.colour-picker').iris();
    $(document).click(function (e) {
        if (!$(e.target).is(".colour-picker, .iris-picker, .iris-picker-inner")) {
            $('.colour-picker').iris('hide');
            return false;
        }
    });
    $('.colour-picker').click(function (event) {
        $('.colour-picker').iris('hide');
        $(this).iris('show');
        return false;
    });
});