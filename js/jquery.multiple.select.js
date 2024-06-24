/**
 * @author zhixin wen <wenzhixin2010@gmail.com>
 * @version 1.0.7
 * 
 * http://wenzhixin.net.cn/p/multiple-select/
 */

(function($) {

    'use strict';

    // it only does '%s', and return '' when arguments are undefined
    var sprintf = function (str) {
        var args = arguments,
            flag = true,
            i = 1;

        str = str.replace(/%s/g, function () {
            var arg = args[i++];

            if (typeof arg === 'undefined') {
                flag = false;
                return '';
            }
            return arg;
        });
        return flag ? str : '';
    };

    function MultipleSelect($el, options) {
        var that = this,
            elWidth = $el.width();

        this.$el = $el.hide();
        this.options = options;
         this.$parent = $(sprintf(
            '<div class="ms-parent %s" %s/>',
            $el.attr('class') || '',
            sprintf('title="%s"', $el.attr('title'))));
        this.$choice = $('<button type="button" class="ms-choice" tabindex="' + options.input_tabindex +'"><span class="placeholder">' +
           this.options.placeholder+ '</span><div></div></button>');
        this.$drop = $('<div class="ms-drop ' + options.position + '"></div>');
        this.$el.after(this.$parent);
        this.$parent.append(this.$choice);
        this.$parent.append(this.$drop);

        if (this.$el.prop('disabled')) {
            this.$choice.addClass('disabled');
        }
        this.$parent.css('width',
            this.options.width ||
            this.$el.css('width') ||
            this.$el.outerWidth() + 20);
        this.$drop.css({
            width: (options.width || elWidth) + 'px'
        });

        $('body').click(function(e) {
            if ($(e.target)[0] === that.$choice[0] ||
                    $(e.target).parents('.ms-choice')[0] === that.$choice[0]) {
                return;
            }
            if (($(e.target)[0] === that.$drop[0] ||
                    $(e.target).parents('.ms-drop')[0] !== that.$drop[0]) &&
                    that.options.isopen) {
                that.close();
            }
        });

        if (this.options.isopen) {
            this.open();
        }
    }

    MultipleSelect.prototype = {
        constructor : MultipleSelect,

        init: function() {
            var that = this,
                html = [];
            if (this.options.filter) {
                html.push(
                    '<div class="ms-search">',
                        '<input type="text" autocomplete="off" autocorrect="off" autocapitilize="off" spellcheck="false">',
                    '</div>'
                );
            }
            html.push('<ul>');
            if (this.options.selectAll && !this.options.single) {
                html.push(
                    '<li>',
                        '<label id="display">',
                            '<input type="checkbox" name="selectAll" value="all" /> ',
                            '[' + this.options.selectAllText + ']',
                        '</label>',
                    '</li>'
                );
            }
            var input_id = this.options.input_id;
            $.each(this.$el.children(), function(i, elm) {
                if( elm.value != 0 ) // modified by MWT
                    html.push(that.optionToHtml(i, elm, '', '', input_id));
            });
            html.push('</ul>');
            this.$drop.html(html.join(''));
            this.$drop.find('ul').css('max-height', this.options.maxHeight + 'px');
            this.$drop.find('.multiple').css('width', this.options.multipleWidth + 'px');

            this.$searchInput = this.$drop.find('.ms-search input');
            this.$selectAll = this.$drop.find('input[name="selectAll"]');
            this.$selectGroups = this.$drop.find('input[name="selectGroup"]');
            this.$selectItems = this.$drop.find('input[name="selectItem[]"]:enabled');
            this.$disableItems = this.$drop.find('input[name="selectItem[]"]:disabled');
            this.events();
            this.update();
            
            if( input_id ) // modified by MWT
            {
                var text_arr = [];
                var cookie_obj = this.options;
                checked_data_onload(cookie_obj.input_id, jQuery.cookie ( cookie_obj.cookie + '[' + cookie_obj.input_id + ']' ));
                this.$drop.find('input[name="selectItem[]"]:checked').each(function() {
                    text_arr.push($(this).parent().text());
                });
                this.$choice.find('>span').removeClass('placeholder').html(text_arr.join(', '));
                if( that.$selectItems.filter(':checked').length == 0 )
                    that['checkAll']();
            }
        },
        
        optionToHtml: function(i, elm, group, groupDisabled, input_id) {
            var that = this,
                $elm = $(elm),
                html = [],
                multiple = this.options.multiple,
                disabled,
                type = this.options.single ? 'radio' : 'checkbox';

            if ($elm.is('option')) {
                var value = $elm.val(),
                    text = $elm.text(),
                    selected = $elm.prop('selected');
                    
                var position = text.slice(0,text.lastIndexOf('('));
                var salary= text.slice(text.lastIndexOf('('),text.lastIndexOf(''));
                    
                disabled = groupDisabled || $elm.prop('disabled');
                if( input_id == 'cri_selrank' )
                {
                    html.push(
                        '<li' + (multiple ? ' class="multiple"' : '') + '>',
                        '<label' + (disabled ? ' class="disabled"' : '') + ' id="display">',
                            '<input type="' + type + '" name="selectItem[]" value="' + value + '"' +
                                (selected ? ' checked="checked"' : '') +
                                (disabled ? ' disabled="disabled"' : '') +
                                (group ? ' data-group="' + group + '"' : '') +
                                '/> ',
                          '<div style="float:left;">'+ position+'</div><div style="float:right;">'+salary+'</div>',
                        '</label>',
                    '</li>'
                    );
                }
                else
                {
                    html.push(
                        '<li' + (multiple ? ' class="multiple"' : '') + '>',
                        '<label' + (disabled ? ' class="disabled"' : '') + ' id="display">',
                            '<input type="' + type + '" name="selectItem[]" value="' + value + '"' +
                                (selected ? ' checked="checked"' : '') +
                                (disabled ? ' disabled="disabled"' : '') +
                                (group ? ' data-group="' + group + '"' : '') +
                                '/> ',
                            text,
                        '</label>',
                    '</li>'
                    );
                }
            } else if (!group && $elm.is('optgroup')) {
                var _group = 'group_' + i,
                    label = $elm.attr('label');

                disabled = $elm.prop('disabled');
                html.push(
                    '<li class="group">',
                        '<label class="optgroup' + (disabled ? ' disabled' : '') + '" data-group="' + _group + '">',
                            '<input type="checkbox" name="selectGroup"' + (disabled ? ' disabled="disabled"' : '') + ' /> ',
                            label,
                        '</label>',
                    '</li>');
                $.each($elm.children(), function(i, elm) {
                    html.push(that.optionToHtml(i, elm, _group, disabled));
                });
            }
            return html.join('');
        },

        events: function() {
            var that = this;
            this.$choice.off('click').on('click', function() {
                that[that.options.isopen ? 'close' : 'open']();
            });
            this.$parent.off('keydown').on('keydown', function(e) {
                switch (e.which) {
                    case 27: // esc key
                        that.close();
                        that.$choice.focus();
                        break;
                }
            });
            this.$searchInput.off('keyup').on('keyup', function() {
                that.filter();
            });
            this.$selectAll.off('click').on('click', function() {
                var checked = $(this).prop('checked'),
                    $items = that.$selectItems.filter(':visible');
                if ($items.length === that.$selectItems.length) {
                    that[checked ? 'checkAll' : 'uncheckAll']();
                } else { // when the filter option is true
                    that.$selectGroups.prop('checked', checked);
                    $items.prop('checked', checked);
                    that.update();
                }
                $('#'+ that.options.input_id).change(); // modified by MWT
            });
            this.$selectGroups.off('click').on('click', function() {
                var group = $(this).parent().attr('data-group'),
                    $items = that.$selectItems.filter(':visible'),
                    $children = $items.filter('[data-group="' + group + '"]'),
                    checked = $children.length !== $children.filter(':checked').length;
                $children.prop('checked', checked);
                that.updateSelectAll();
                that.update();
                that.options.onOptgroupClick({
                    label: $(this).parent().text(),
                    checked: checked,
                    children: $children.get()
                });
            });
            this.$selectItems.off('click').on('click', function() {
                if( ! that.$searchInput.val() ) // modified by MWT
                    that.updateSelectAll();
                that.update();
                that.updateOptGroupSelect();
                $('#'+ that.options.input_id).change(); // modified by MWT
                that.options.onClick({
                    label: $(this).parent().text(),
                    value: $(this).val(),
                    checked: $(this).prop('checked')
                });
            });
        },

        open: function() {
            if (this.$choice.hasClass('disabled')) {
                return;
            }
            this.options.isopen = true;
            this.$choice.find('>div').addClass('open');
            this.$drop.show();
            if (this.options.container) {
                var offset = this.$drop.offset();
                this.$drop.appendTo($(this.options.container));
                this.$drop.offset({ top: offset.top, left: offset.left });
            }
            if (this.options.filter) {
                this.$searchInput.val('');
                this.filter();
            }
            this.options.onOpen();
        },

        close: function() {
            this.options.isopen = false;
            this.$choice.find('>div').removeClass('open');
            this.$drop.hide();
            if (this.options.container) {
                this.$parent.append(this.$drop);
                this.$drop.css({
                    'top': 'auto',
                    'left': 'auto'
                })
            }
            this.options.onClose();
        },

        update: function() {
            var selects = this.getSelects('text'),
                $span = this.$choice.find('>span');
            if(selects.length == this.$selectItems.length && this.options.overrideButtonText) {
                $span.removeClass('placeholder').html(this.options.selectAllText);
            } else if (selects.length) {    // modified by wwl but need to find for using from list page. this is not good to modified original file.
                if(selects.length == this.$selectItems.length)
                    $span.removeClass('placeholder').html('-- ' + this.options.selectAllText + ' --');
                else
                    $span.removeClass('placeholder').html(selects.join(', '));
            } else {
                $span.addClass('placeholder').html(this.options.placeholder);
            }
            // set selects to select
            this.$el.val(this.getSelects());
        },

        updateSelectAll: function() {
            var $items = this.$selectItems.filter(':visible');
            this.$selectAll.prop('checked', $items.length &&
                $items.length === $items.filter(':checked').length);
        },

        updateOptGroupSelect: function() {
            var $items = this.$selectItems.filter(':visible');
            $.each(this.$selectGroups, function(i, val) {
                var group = $(val).parent().attr('data-group'),
                    $children = $items.filter('[data-group="' + group + '"]');
                $(val).prop('checked', $children.length &&
                    $children.length === $children.filter(':checked').length);
            });
        },

        //value or text, default: 'value'
        getSelects: function(type) {
            var that = this,
                texts = [],
                values = [];
            this.$drop.find('input[name="selectItem[]"]:checked').each(function() {
                texts.push($(this).parent().text());
                values.push($(this).val());
            });

            if (type === 'text' && this.$selectGroups.length) {
                texts = [];
                this.$selectGroups.each(function() {
                    var html = [],
                        text = $.trim($(this).parent().text()),
                        group = $(this).parent().data('group'),
                        $children = that.$drop.find('[name="selectItem[]"][data-group="' + group + '"]'),
                        $selected = $children.filter(':checked');

                    if ($selected.length === 0) {
                        return;
                    }

                    html.push('[');
                    html.push(text);
                    if ($children.length > $selected.length) {
                        var list = [];
                        $selected.each(function() {
                            list.push($(this).parent().text());
                        });
                        html.push(': ' + list.join(', '));
                    }
                    html.push(']');
                    texts.push(html.join(''));
                });
            }
            return type === 'text' ? texts : values;
        },

        setSelects: function(values) {
            var that = this;
            this.$selectItems.prop('checked', false);
            $.each(values, function(i, value) {
                that.$selectItems.filter('[value="' + value + '"]').prop('checked', true);
            });
            this.$selectAll.prop('checked', this.$selectItems.length ===
                this.$selectItems.filter(':checked').length);
            this.update();
        },

        enable: function() {
            this.$choice.removeClass('disabled');
        },

        disable: function() {
            this.$choice.addClass('disabled');
        },

        checkAll: function() {
            this.$selectItems.prop('checked', true);
            this.$selectGroups.prop('checked', true);
            this.$selectAll.prop('checked', true);
            this.update();
            this.options.onCheckAll();
        },

        uncheckAll: function() {
            this.$selectItems.prop('checked', false);
            this.$selectGroups.prop('checked', false);
            this.$selectAll.prop('checked', false);
            this.update();
            this.options.onUncheckAll();
        },

        refresh: function() {
            this.init();
        },

        filter: function() {
            var that = this,
                text = $.trim(this.$searchInput.val()).toLowerCase();
            if (text.length === 0) {
                this.$selectItems.parent().show();
                this.$disableItems.parent().show();
                this.$selectGroups.parent().show();
            } else {
                this.$selectItems.each(function() {
                    var $parent = $(this).parent();
                    $parent[$parent.text().toLowerCase().indexOf(text) != 1 ? 'hide' : 'show'](); // modified by MWT
                });
                this.$disableItems.parent().hide();
                this.$selectGroups.each(function() {
                    var $parent = $(this).parent();
                    var group = $parent.attr('data-group'),
                        $items = that.$selectItems.filter(':visible');
                    $parent[$items.filter('[data-group="' + group + '"]').length === 0 ? 'hide' : 'show']();
                });
            }
            this.updateOptGroupSelect();
            this.updateSelectAll();
        }
    };

    $.fn.multipleSelect = function() {
        var option = arguments[0],
            args = arguments,

            value,
            allowedMethods = ['getSelects', 'setSelects', 'enable', 'disable', 'checkAll', 'uncheckAll', 'refresh'];

        this.each(function() {
            var $this = $(this),
                data = $this.data('multipleSelect'),
                options = $.extend({}, $.fn.multipleSelect.defaults, typeof option === 'object' && option);

            if (!data) {
                data = new MultipleSelect($this, options);
                $this.data('multipleSelect', data);
            }

            if (typeof option === 'string') {
                if ($.inArray(option, allowedMethods) < 0) {
                    throw "Unknown method: " + option;
                }
                value = data[option](args[1]);
            } else {
                data.init();
            }
        });

        return value ? value : this;
    };

    $.fn.multipleSelect.defaults = {
        isopen: false,
        placeholder: '',
        selectAll: true,
        selectAllText: 'Select all',
        multiple: false,
        multipleWidth: 80,
        single: false,
        filter: false,
        width: undefined,
        maxHeight: 250,
        container: null,
        position: 'bottom', // 'bottom' or 'top',
        input_id: '', // modified by MWT
        cookie: '', // modified by MWT

        onOpen: function() {return false;},
        onClose: function() {return false;},
        onCheckAll: function() {return false;},
        onUncheckAll: function() {return false;},
        onOptgroupClick: function() {return false;},
        onClick: function() {return false;}
    };
})(jQuery);