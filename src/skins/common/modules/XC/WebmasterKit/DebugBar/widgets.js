if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
    PhpDebugBar.$ = jQuery;
}

(function ($) {

    /**
     * @namespace
     */
    PhpDebugBar.XCartWidgets = {};

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    var treeTimelineWidgetClassName = csscls('tree-timeline');

    /**
     * Widget for the WidgetTimeDataCollector
     *
     * Options:
     *  - data
     */
    PhpDebugBar.XCartWidgets.TreeTimelineWidget = PhpDebugBar.Widget.extend({

        tagName: 'ul',

        className: treeTimelineWidgetClassName,

        render: function () {
            this.bindAttr('data', function (data) {
                var backtraceDict = [];

                this.$el.empty();
                if (data.measures) {
                    for (var i = 0; i < data.measures.length; i++) {
                        this.$el.append(renderMeasure(data.measures[i]));
                    }

                    var root = this.$el.parent();

                    root.jstree({
                        'core': {
                            'themes': {
                                'icons': false
                            },
                            'dblclick_toggle': false
                        }
                    });

                    root.unbind('dblclick.jstree');

                    var replaceTagWith = function (elems, replaceWith) {
                        var tags = [],
                            i = elems.length;
                        while (i--) {
                            var newElement = document.createElement(replaceWith),
                                thisi = elems[i],
                                thisia = thisi.attributes;
                            for (var a = thisia.length - 1; a >= 0; a--) {
                                var attrib = thisia[a];
                                newElement.setAttribute(attrib.name, attrib.value);
                            }
                            newElement.innerHTML = thisi.innerHTML;
                            $(thisi).after(newElement).remove();
                            tags[i] = newElement;
                        }
                        return $(tags);
                    };

                    root.jstree('open_all');

                    // Replace <a> jstree anchors with <span> so that we can place clickable elements inside
                    replaceTagWith(root.find('a.jstree-anchor'), 'span');

                    root.children('ul').addClass(treeTimelineWidgetClassName);

                    root.on('click', '.' + csscls('toggle-db-queries'), function () {
                        $(this).siblings('.' + csscls('db-queries')).toggleClass(csscls('db-queries-visible'));
                    });

                    $(root).on('click', '.' + csscls('db-query-sql'), function () {
                        if ($(this).siblings('.' + csscls('db-query-backtrace')).length == 0) {
                            var backtrace = backtraceDict[$(this).attr('data-backtrace-id')];

                            $(this).after($('<div>').addClass(csscls('db-query-backtrace')).append(renderBacktrace(backtrace)));
                        }

                        $(this).siblings('.' + csscls('db-query-backtrace')).toggleClass(csscls('db-query-backtrace-visible'));
                    });
                }

                function renderMeasure(measure) {
                    var m = $('<div />').addClass(csscls('measure')),
                        li = $('<li />'),
                        left = (measure.relative_start * 100 / data.duration).toFixed(2),
                        width = Math.min((measure.duration * 100 / data.duration).toFixed(2), 100 - left);

                    m.append($('<span />').addClass(csscls('value')).css({
                        left: left + "%",
                        width: width + "%"
                    }));

                    m.append($('<span />').addClass(csscls('row')));

                    var dbQueryCount = '<span title="DB queries">'
                        + (measure.queries.length > 0
                            ? ' <i class="fa fa-database"></i> ' + measure.queries.length
                            : '')
                        + '</span>';

                    var visibility = measure.visible
                        ? '' /*' <i class="fa fa-eye" title="Visible"></i>'*/
                        : ' <i class="fa fa-eye-slash" title="Not visible"></i>';

                    var cacheStatus = measure.cached
                        ? ' <i class="glyphicon glyphicon-fire" title="Cached"></i>'
                        : '';

                    var label = measure.label + dbQueryCount + visibility + cacheStatus + " (" + measure.duration_str + ")";

                    m.append($('<span />').addClass(csscls('label')).html(label));

                    if (measure.collector) {
                        $('<span />').addClass(csscls('collector')).text(measure.collector).appendTo(m);
                    }

                    m.appendTo(li);

                    if (measure.params && !$.isEmptyObject(measure.params)) {
                        var table = $('<table><tr><th colspan="2">Widget params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                        for (var key in measure.params) {
                            if (typeof measure.params[key] !== 'function') {
                                table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                    '"><pre><code>' + measure.params[key] + '</code></pre></td></tr>');
                            }
                        }

                        $('<div>').addClass('clearfix').appendTo(li);
                    }

                    if (measure.queries.length > 0) {
                        $('<span>').text('DB queries (' + measure.queries.length + ')').addClass(csscls('toggle-db-queries')).appendTo(li);

                        var dbQueries = $('<ol>').addClass(csscls('db-queries')).appendTo(li);

                        $.each(measure.queries, function () {
                            backtraceDict.push(this.backtrace);

                            $('<li>')
                                .addClass(csscls('db-query'))
                                .append($('<span>').addClass(csscls('db-query-sql')).html(PhpDebugBar.Widgets.highlight(this.sql, 'sql')).attr('data-backtrace-id', backtraceDict.length-1))
                                .appendTo(dbQueries);
                        });
                    }

                    if (measure.children.length > 0) {
                        var children = $('<ul>');

                        $.each(measure.children, function () {
                            children.append(renderMeasure(this));
                        });

                        li.append(children);
                    }

                    return li;
                }

                function renderBacktrace(backtrace) {
                    var list = $('<ol>');

                    $.each(backtrace, function () {
                      if (typeof this === 'string' || this instanceof String) {
                        $('<li>').text(this).appendTo(list);
                      } else {
                        var frame = this.file + ' (' + this.line + '): '
                            + (this.class ? this.class : '')
                            + (this.type ? this.type : '')
                            + this['function'] + '()';

                        $('<li>').text(frame).appendTo(list);
                      }
                    });

                    return list;
                }
            });
        }

    });

    var settingsClass = csscls('settings');

    /**
     * Widget for the SettingsDataCollector
     *
     * Options:
     *  - data
     */
    PhpDebugBar.XCartWidgets.SettingsWidget = PhpDebugBar.Widget.extend({

        tagName: 'div',

        className: settingsClass,

        render: function () {
            this.bindAttr('data', function (data) {
                var submitUrl = 'cart.php?target=debug_bar_settings&action=update_settings';

                var container = this.$el.empty(),
                    form = $('<form>', {action: submitUrl, method: 'POST'}).appendTo(container),
                    list = $('<ul>').appendTo(form);

                var widgetsSqlStacktraceSetting = this.addSetting(
                    'Display stacktrace for every SQL query in the Widgets tab (may cause generated html size to grow significantly)',
                    'widgetsSqlQueryStacktracesEnabled',
                    data.widgetsSqlQueryStacktracesEnabled
                );
                $('<li>', {'class': settingsClass + '-setting'}).append(widgetsSqlStacktraceSetting).appendTo(list);

                var sqlStacktraceSetting = this.addSetting(
                    'Display stacktrace for SQL query in the Database tab (may cause generated html size to grow significantly)',
                    'sqlQueryStacktracesEnabled',
                    data.sqlQueryStacktracesEnabled
                );
                $('<li>', {'class': settingsClass + '-setting'}).append(sqlStacktraceSetting).appendTo(list);

                var timeTabSettings = this.addSetting(
                    'Display widgets timeline tab',
                    'widgetsTabEnabled',
                    data.widgetsTabEnabled
                );
                $('<li>', {'class': settingsClass + '-setting'}).append(timeTabSettings).appendTo(list);

                var databaseDetailedMode = this.addSetting(
                    'Database tab: detailed mode',
                    'databaseDetailedModeEnabled',
                    data.databaseDetailedModeEnabled
                );
                $('<li>', {'class': settingsClass + '-setting'}).append(databaseDetailedMode).appendTo(list);

                $('<li>').append($('<button>', {type: 'submit', text: 'Save and reload the page', 'class': settingsClass + '-submit'})).appendTo(list);
            });
        },

        addSetting: function(text, name, checked) {
            var setting = $('<label>', {text: text})
                .prepend($('<input>', {
                    type: 'checkbox',
                    name: name,
                    checked: checked,
                    'class': settingsClass + '-checkbox'
                }));

            setting.prepend($('<input>', {type: 'hidden', name: name}));

            return setting;
        }
    });

    /**
     * Widget for the displaying templates data
     *
     * Options:
     *  - data
     */
    var DoctrineUOWWidget = PhpDebugBar.XCartWidgets.DoctrineUOWWidget = PhpDebugBar.Widgets.VariableListWidget.extend({

        className: PhpDebugBar.Widgets.VariableListWidget.prototype.className + ' ' + csscls('doctrine-uow'),

        render: function () {
            this.bindAttr('data', function (data) {
                this.$el.empty();

                this.$el.append(this.getHeaderLine());

                _.each(data, _.bind(
                    function(elem, key) {
                        this.$el.append(
                            this.getTableLine(key, elem)
                        );
                    },
                    this
                ));
            });
        },

        getTableLine: function(className, count) {
            return $('<tr><td>' + className + '</td><td>' + count + '<td/></tr>');
        },

        getHeaderLine: function() {
            return $('<tr><th>Class</th><th>Count<th/></tr>');
        },
    });

    /**
     * Widget for the WidgetTimeDataCollector
     *
     * Options:
     *  - data
     */
    var MemoryPointsWidget = PhpDebugBar.XCartWidgets.MemoryPointsWidget = PhpDebugBar.Widget.extend({

        tagName: 'table',

        className: csscls('memory-points'),

        render: function () {
            this.bindAttr('data', function (data) {
                this.$el.empty();

                this.$el.append(this.getHeaderLine());

                for (var i = 0; i < data.length; i++) {
                    this.$el.append(this.getTableLine(data[i]));
                };

                this.assignHandlers();
            });
        },

        assignHandlers: function(){
            this.$el.find('.backtrace').click(function(){
                $(this).find('.anchor').toggleClass('hidden');
                $(this).find('.list').toggleClass('hidden');
            });
        },

        getTableLine: function(memoryPoint) {
            var template = _.template(
                "<td><%- point.memory %></td>" +
                "<td><%- point.memoryDiff %></td>" +
                "<td class='backtrace'>" +
                    "<div class='anchor'><%- point.backtraceLabel %></div>" +
                    "<ul class='list hidden'>" +
                        "<% _.each(point.backtrace, function(line) { %>" +
                        "<li>>> <%= line %></li>" +
                        "<% }); %>" +
                    "</ul>" +
                "</td>"
            );
            var point = {
                memory:         memoryPoint.memory,
                memoryDiff:     memoryPoint.memoryDiff,
                backtrace:      memoryPoint.stacktrace.split(' << '),
            }
            point.backtraceLabel = _.last(point.backtrace);

            return $('<tr/>').append(
                template({
                    point: point
                })
            );
        },

        getHeaderLine: function() {
            var point       = $('<th/>').text('Memory, Mbytes');
            var diff        = $('<th/>').text('Changes, Mbytes');
            var backtrace   = $('<th/>').text('Backtrace');

            var line = $('<tr/>');
            line.append(point);
            line.append(diff);
            line.append(backtrace);

            return line;
        },

    });

    /**
     * Widget for the displaying templates data
     *
     * Options:
     *  - data
     */
    var SQLQueriesCompactWidget = PhpDebugBar.XCartWidgets.SQLQueriesCompactWidget = PhpDebugBar.Widgets.SQLQueriesWidget.extend({

        className: PhpDebugBar.Widgets.SQLQueriesWidget.prototype.className + ' ' + csscls('sql-queries-compact'),

        onFilterClick: function(el) {
            $(el).toggleClass(csscls('excluded'));

            var excludedLabels = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                excludedLabels.push(this.rel);
            });

            this.$list.$el.find("li[connection=" + $(el).attr("rel") + "]").toggle();

            this.set('exclude', excludedLabels);
        },

        render: function() {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$toolbar = $('<div></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            var filters = [], self = this;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, stmt) {
                $('<code />').addClass(csscls('sql')).html(PhpDebugBar.Widgets.highlight(stmt.sql, 'sql')).appendTo(li);
                if (stmt.duration_str) {
                    var classes = csscls('duration');
                    if (stmt.duration_warning) {
                        classes += ' warning'
                    };
                    $('<span title="Duration" />').addClass(classes).text(stmt.duration_str).appendTo(li);
                }
                if (stmt.count > 1) {
                    var classes = csscls('count');
                    if (stmt.count_warning) {
                        classes += ' warning'
                    };
                    var count = $('<span title="Count" />').addClass(classes).text(stmt.count + ' times').appendTo(li);
                }
                if (stmt.memory_str) {
                    $('<span title="Memory usage" />').addClass(csscls('memory')).text(stmt.memory_str).appendTo(li);
                }
                if (typeof(stmt.row_count) != 'undefined') {
                    $('<span title="Row count" />').addClass(csscls('row-count')).text(stmt.row_count).appendTo(li);
                }
                if (typeof(stmt.stmt_id) != 'undefined' && stmt.stmt_id) {
                    $('<span title="Prepared statement ID" />').addClass(csscls('stmt-id')).text(stmt.stmt_id).appendTo(li);
                }
                if (stmt.connection) {
                    $('<span title="Connection" />').addClass(csscls('database')).text(stmt.connection).appendTo(li);
                    li.attr("connection",stmt.connection);
                    if ( $.inArray(stmt.connection, filters) == -1 ) {
                        filters.push(stmt.connection);
                        $('<a />')
                            .addClass(csscls('filter'))
                            .text(stmt.connection)
                            .attr('rel', stmt.connection)
                            .on('click', function() { self.onFilterClick(this); })
                            .appendTo(self.$toolbar);
                        if (filters.length>1) {
                            self.$toolbar.show();
                            self.$list.$el.css("margin-bottom","20px");
                        }
                    }
                }
                if (typeof(stmt.is_success) != 'undefined' && !stmt.is_success) {
                    li.addClass(csscls('error'));
                    li.append($('<span />').addClass(csscls('error')).text("[" + stmt.error_code + "] " + stmt.error_message));
                }
                if (stmt.backtrace) {
                    var wrapper = $('<div><span>Backtrace:</span></div>')
                        .addClass(csscls('backtrace'))
                        .appendTo(li);
                    var list = $('<ul/>').appendTo(wrapper);
                    _.each(
                        stmt.backtrace,
                        function(el, index) {
                            if (typeof el === 'string') {
                                list.append($('<li>' + el + '</li>'));
                            } else {
                                list.append($('<li>' + el.class + '#' + el['function'] + '() on line ' + el.line + '</li>'));
                            }
                        }
                    );

                    li.css('cursor', 'pointer').click(function() {
                        if (wrapper.is(':visible')) {
                            wrapper.hide();
                        } else {
                            wrapper.show();
                        }
                    });
                }
            }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data.statements);
                this.$status.empty();

                var t = $('<span />').text(data.nb_statements + " unique statements were executed").appendTo(this.$status);
                if (data.nb_failed_statements) {
                    t.append(", " + data.nb_failed_statements + " of which failed");
                }
                if (data.nb_total_statements) {
                    t.append(", " + data.nb_total_statements + " total");
                };
                if (data.accumulated_duration_str) {
                    this.$status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
            });
        },
    });

})(PhpDebugBar.$);
