(function (_, $) {
    var colors = {
        'aliceblue': '#f0f8ff',
        'antiquewhite': '#faebd7',
        'aqua': '#00ffff',
        'aquamarine': '#70db93',
        'azure': '#f0ffff',
        'beige': '#f5f5dc',
        'bisque': '#ffe4c4',
        'black': '#000000',
        'blanchedalmond': '#ffebcd',
        'blue': '#3232cd',
        'blueviolet': '#8a2be2',
        'brass': '#b5a642',
        'brightgold': '#d9d919',
        'bronze': '#8c7853',
        'bronzeii': '#a67d3d',
        'brown': '#a52a2a',
        'burlywood': '#deb887',
        'cadetblue': '#5f9ea0',
        'chartreuse': '#7fff00',
        'chocolate': '#5c3317',
        'coolcopper': '#d98719',
        'copper': '#b87333',
        'coral': '#ff7f00',
        'cornflowerblue': '#42426f',
        'cornsilk': '#fff8dc',
        'crimson': '#dc143c',
        'cyan': '#00ffff',
        'darkblue': '#00008b',
        'darkbrown': '#5c4033',
        'darkcyan': '#008b8b',
        'darkgoldenrod': '#b8860b',
        'darkgreen': '#2f4f2f',
        'darkgreencopper': '#4a766e',
        'darkgrey': '#a9a9a9',
        'darkkhaki': '#bdb76b',
        'darkmagenta': '#8b008b',
        'darkolivegreen': '#4f4f2f',
        'darkorange': '#ff8c00',
        'darkorchid': '#9932cd',
        'darkpurple': '#871f78',
        'darkred': '#8b0000',
        'darksalmon': '#e9967a',
        'darkseagreen': '#8fbc8f',
        'darkslateblue': '#6b238e',
        'darkslategrey': '#2f4f4f',
        'darktan': '#97694f',
        'darkturquoise': '#7093db',
        'darkviolet': '#9400d3',
        'darkwood': '#855e42',
        'deeppink': '#ff1493',
        'deepskyblue': '#00bfff',
        'dimgrey': '#545454',
        'dodgerblue': '#1e90ff',
        'dustyrose': '#856363',
        'fadedbrown': '#f5ccb0',
        'feldspar': '#d19275',
        'firebrick': '#8e2323',
        'floralwhite': '#fffaf0',
        'forestgreen': '#238e23',
        'fuchsia': '#ff00ff',
        'gainsboro': '#dcdcdc',
        'ghostwhite': '#f8f8ff',
        'gold': '#ffd700',
        'goldenrod': '#dbdb70',
        'green': '#238e23',
        'greencopper': '#527f76',
        'greenyellow': '#93db70',
        'grey': '#d8d8d8',
        'gray': '#d8d8d8',
        'honeydew': '#f0fff0',
        'hotpink': '#ff69b4',
        'huntergreen': '#215e21',
        'indianred': '#4e2f2f',
        'indigo': '#4b0082',
        'ivory': '#fffff0',
        'khaki': '#9f9f5f',
        'lavender': '#e6e6fa',
        'lavenderblush': '#fff0f5',
        'lawngreen': '#7cfc00',
        'lemonchiffon': '#fffacd',
        'lightblue': '#c0d9d9',
        'lightcoral': '#f08080',
        'lightcyan': '#e0ffff',
        'lightgoldenrodyellow': '#fafad2',
        'lightgreen': '#90ee90',
        'lightgrey': '#a8a8a8',
        'lightpink': '#ffb6c1',
        'lightsalmon': '#ffa07a',
        'lightseagreen': '#20b2aa',
        'lightskyblue': '#87cefa',
        'lightslateblue': '#8470ff',
        'lightslategrey': '#778899',
        'lightsteelblue': '#8f8fbd',
        'lightwood': '#e9c2a6',
        'lightyellow': '#ffffe0',
        'lime': '#00ff00',
        'limegreen': '#32cd32',
        'linen': '#faf0e6',
        'magenta': '#ff00ff',
        'mandarianorange': '#e47833',
        'maroon': '#800000',
        'mediumaquamarine': '#32cd99',
        'mediumblue': '#3232cd',
        'mediumforestgreen': '#6b8e23',
        'mediumgoldenrod': '#eaeaae',
        'mediumorchid': '#9370db',
        'mediumpurple': '#9370db',
        'mediumseagreen': '#426f42',
        'mediumslateblue': '#7f00ff',
        'mediumspringgreen': '#7fff00',
        'mediumturquoise': '#70dbdb',
        'mediumvioletred': '#db7093',
        'mediumwood': '#a68064',
        'midnightblue': '#2f2f4f',
        'mintcream': '#f5fffa',
        'mistyrose': '#ffe4e1',
        'moccasin': '#ffe4b5',
        'navajowhite': '#ffdead',
        'navy': '#000080',
        'navyblue': '#23238e',
        'neonblue': '#4d4dff',
        'neonpink': '#ff6ec7',
        'newmidnightblue': '#00009c',
        'newtan': '#ebc79e',
        'oldgold': '#cfb53b',
        'oldlace': '#fdf5e6',
        'olive': '#808000',
        'olivedrab': '#6b8e23',
        'orange': '#ff7f00',
        'orangered': '#ff2400',
        'orchid': '#db70db',
        'palegoldenrod': '#eee8aa',
        'palegreen': '#8fbc8f',
        'paleturquoise': '#afeeee',
        'palevioletred': '#d87093',
        'papayawhip': '#ffefd5',
        'peachpuff': '#ffdab9',
        'peru': '#cd853f',
        'pink': '#bc8f8f',
        'plum': '#eaadea',
        'powderblue': '#b0e0e6',
        'purple': '#800080',
        'quartz': '#d9d9f3',
        'red': '#ff0000',
        'richblue': '#5959ab',
        'rosybrown': '#bc8f8f',
        'royalblue': '#4169e1',
        'rosegold': '#f3cec8',
        'saddlebrown': '#8b4513',
        'salmon': '#fa8072',
        'sandybrown': '#f4a460',
        'scarlet': '#8c1717',
        'seagreen': '#238e68',
        'seashell': '#fff5ee',
        'semi-sweetchocolate': '#6b4226',
        'sienna': '#8e6b23',
        'silver': '#e6e8fa',
        'skyblue': '#87ceeb',
        'slateblue': '#007fff',
        'slategrey': '#708090',
        'snow': '#fffafa',
        'spacegray': '#65737e',
        'spicypink': '#ff1cae',
        'springgreen': '#00ff7f',
        'steelblue': '#236b8e',
        'summersky': '#38b0de',
        'tan': '#db9370',
        'teal': '#008080',
        'thistle': '#d8bfd8',
        'tomato': '#ff6347',
        'turquoise': '#adeaea',
        'verylightgrey': '#cdcdcd',
        'violet': '#4f2f4f',
        'violetred': '#cc3299',
        'wheat': '#d8d8bf',
        'white': '#ffffff',
        'whitesmoke': '#f5f5f5',
        'yellow': '#ffff00',
        'yellowgreen': '#99cc32',
    };

    var sizes = ['XXXS', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '2XL', '3XL', '4XL', '5XL'];

    var getColorByName = function (name) {
        var normalized = name.toLowerCase().replace(/[^a-z]/, '');

        return colors.hasOwnProperty(normalized) ? colors[normalized] : null;
    };

    var FILTER_PARAM_PREFIX = 'filter_';

    var paramsToQueryString = function (params) {
        return _.map(params, function (p) {
            return encodeURIComponent(p[0]) + '=' + encodeURIComponent(p[1]);
        }).join('&');
    };

    var filtersToParamArray = function (filters) {
        var ARRAY_VALUES_SEP = '__';

        return _.flatten(_.map(filters, function (_values, key) {
            var values = _.filter(Array.isArray(_values) ? _values : [_values], function (value) {
                return value !== null;
            });

            return _.map(values, function (value) {
                return [
                    FILTER_PARAM_PREFIX + key,
                    Array.isArray(value) ? value.join(ARRAY_VALUES_SEP) : value
                ];
            })
        }), true);
    };

    var hashAdd = window.hash.add;

    window.hash.add = function (params) {
        return hashAdd.call(this, _.omit(params, 'cloudFilters'));
    };

    var productList = (function () {
        var load, cloudFilters, searchListeners = [];

        function decorateConcreteWidgetClasses(widgetClasses, c, methodName, method) {
            var f = function () {
                method.previousMethod = arguments.callee.previousMethod;

                return widgetClasses.indexOf(this.widgetClass) !== -1
                    ? method.apply(this, arguments)
                    : arguments.callee.previousMethod.apply(this, arguments);
            };

            decorate(c, methodName, f);
        }

        var decorateClasses = [
            'XLite\\View\\ItemsList\\Product\\Customer\\Search',
            'XLite\\View\\ItemsList\\Product\\Customer\\Category\\Main',
            'XLite\\Module\\XC\\ProductFilter\\View\\ItemsList\\Product\\Customer\\Category\\CategoryFilter'
        ];

        core.bind(
            'load',
            function () {
                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'postprocess',
                    function (isSuccess, initial) {
                        arguments.callee.previousMethod.apply(this, arguments);

                        load = this.load.bind(this);
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'buildWidgetRequestURL',
                    function (params) {
                        if (!params) {
                            return arguments.callee.previousMethod.call(this, params);
                        }

                        var filtersString = paramsToQueryString(filtersToParamArray(params.cloudFilters));

                        return arguments.callee.previousMethod.call(this, _.omit(params, 'cloudFilters'))
                            + (filtersString.length > 0 ? '&' + filtersString : '');
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'load',
                    function (_params) {
                        var params = _.extend({}, _params, {cloudFilters: cloudFilters});

                        return arguments.callee.previousMethod.call(this, params);
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'postprocess',
                    function (isSuccess, initial) {
                        arguments.callee.previousMethod.apply(this, arguments);

                        if (isSuccess) {
                            if ($(this.base).hasClass('products-search-result')) {
                                // Workaround to run after skins/customer/items_list/product/search/controller.js
                                setTimeout(function () {
                                    var form = $('.search-product-form form');

                                    form.submit(function () {
                                        var query = form.find('input[name="substring"]').val(),
                                            categoryId = form.find('select[name="categoryId"]').val() || null;

                                        _.each(searchListeners, function (listener) {
                                            listener({
                                                query: query,
                                                categoryId: categoryId
                                            });
                                        });
                                    });
                                }, 0);
                            }
                        }
                    }
                );
            }
        );

        return {
            reload: function (params) {
                load(params);
            },
            setFilters: function (filters) {
                cloudFilters = filters;
            },
            subscribeToNewSearches: function (f) {
                searchListeners.push(f);
            }
        };
    })();

    // TODO: do not remove old filter values when fetching new facet data. That way we will avoid height changes for the filter widget. Or at least scroll into view.

    var selector = '#cloud-filters';

    var initialData = core.getCommentedData($(selector));

    var facets = initialData.facets,
        filters = initialData.filters,
        stats = initialData.stats,
        numFound = initialData.numFound,
        facetApi = initialData.facetApi,
        currencyFormat = initialData.currencyFormat;

    if (typeof filters.min_price == 'undefined') {
        filters.min_price = [null];
    }

    if (typeof filters.max_price == 'undefined') {
        filters.max_price = [null];
    }

    Vue.filter('priceFilterValue', {
        read: function (val) {
            return val !== null && val !== undefined ? formatPrice(val) : val;
        },
        write: function (val) {
            var number = +val.replace(/[^\d.,]/g, '');

            return isNaN(number) || number == 0 ? null : number;
        }
    });

    var formatPrice = function (price) {
        var n = price,
            c = currencyFormat.numDecimals,
            d = currencyFormat.decimalDelimiter,
            t = currencyFormat.thousandsDelimiter,
            s = n < 0 ? "-" : "",
            i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
            j = (j = i.length) > 3 ? j % 3 : 0;

        var formatted = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");

        return currencyFormat.prefix + formatted + currencyFormat.suffix;
    };

    var TextValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-text-value-renderer'
    });

    var ColorValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-color-value-renderer',
        computed: {
            color: function () {
                return getColorByName(this.value) || getColorByName('white');
            },
            hasBorder: function () {
                return this.color == getColorByName('white');
            }
        }
    });

    var CategoryValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-category-value-renderer',
        methods: {
            getCategoryName: function () {
                return this.value.path[this.value.path.length - 1];
            },
            getFullCategoryName: function (separator) {
                return this.value.path.join(separator);
            }
        }
    });

    var defaultFilterMixin = {
        data: function () {
            return {
                unfolded: false
            }
        },
        props: ['id', 'title', 'facet', 'toggledValues', 'onToggle'],
        methods: {
            isToggled: function (value) {
                return _.any(this.toggledValues, function (v) {
                    return _.isEqual(value, v);
                });
            },
            unfoldValues: function () {
                this.unfolded = true;
            },
            getValueRenderer: function () {
                return 'text-value-renderer';
            },
            getFilterValue: function (facetValue) {
                return facetValue;
            }
        },
        computed: {
            values: function () {
                return this.facet.counts;
            },
            foldedValues: function () {
                var MAX_FOLDED_VALUES = 10;

                var folded = this.values.length <= MAX_FOLDED_VALUES + 1
                    ? this.values
                    : this.values.slice(0, MAX_FOLDED_VALUES);

                var numToggledInFolded = _.filter(folded, (function (v) {
                    return this.isToggled(v.value);
                }).bind(this)).length;

                return this.unfolded || numToggledInFolded < this.toggledValues.length
                    ? this.values
                    : folded;
            },
            showUnfoldButton: function () {
                return this.values.length > this.foldedValues.length;
            }
        },
        template: '#cloud-filters-template-default',
        components: {
            'text-value-renderer': TextValueRenderer,
            'color-value-renderer': ColorValueRenderer,
            'category-value-renderer': CategoryValueRenderer
        }
    };

    var DefaultFilter = Vue.extend({
        mixins: [defaultFilterMixin]
    });

    var ColorFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        methods: {
            getValueRenderer: function () {
                return 'color-value-renderer';
            }
        }
    });

    var SizeFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        computed: {
            values: function () {
                this.facet.counts.sort(function (a, b) {
                    var av = a.value, bv = b.value;

                    var ai = sizes.indexOf(av);
                    var bi = sizes.indexOf(bv);

                    if (ai != -1 && bi != -1) {
                        return ai - bi;
                    } else if (ai != -1 && bi == -1) {
                        return -1;
                    } else if (ai == -1 && bi != -1) {
                        return 1;
                    } else if (av < bv) {
                        return -1;
                    } else if (av > bv) {
                        return 1;
                    } else {
                        return 0;
                    }
                });

                return this.facet.counts;
            }
        }
    });

    var CategoryFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        methods: {
            getValueRenderer: function () {
                return 'category-value-renderer';
            },
            getFilterValue: function (facetValue) {
                return facetValue.id;
            }
        }
    });

    var PriceFilter = Vue.extend({
        data: function () {
            return {}
        },
        methods: {
            formatPrice: formatPrice
        },
        props: ['title', 'min', 'max', 'statsMin', 'statsMax'],
        template: '#cloud-filters-template-price'
    });

    var CloudFilters = new Vue({
        el: selector,

        data: {
            facets: facets,
            filters: filters,
            stats: stats,
            numFound: numFound,
            loaded: false
        },

        computed: {
            isAnyFilterSet: function () {
                return _.any(this.filters, function (fs) {
                    return (fs.length == 1 && fs[0] !== null) || fs.length > 1;
                });
            }
        },

        watch: {
            'filters.min_price[0]': function (val, oldVal) {
                if (val != oldVal) {
                    this.priceFilterChanged();
                }
            },
            'filters.max_price[0]': function (val, oldVal) {
                if (val != oldVal) {
                    this.priceFilterChanged();
                }
            }
        },

        methods: {
            toggleFilterAction: function (fieldId, fieldValue, isToggled) {
                if (isToggled) {
                    this.filters[fieldId].push(fieldValue);
                } else {
                    Vue.set(
                        this.filters,
                        fieldId,
                        _.filter(this.filters[fieldId], function (f) {
                            return !_.isEqual(f, fieldValue);
                        })
                    );
                }

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            priceFilterChanged: function () {
                productList.setFilters(this.filters);

                this.replaceHistoryState();

                this.fetchFacetsAndReload();
            },

            resetFiltersAction: function () {
                this.clearFilters();

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            searchAction: function (params) {
                facetApi.data.q = params.query;
                facetApi.data.categoryId = params.categoryId;

                this.clearFilters();

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            clearFilters: function () {
                _.each(this.filters, (function (val, key) {
                    this.filters[key] = [];
                }).bind(this));
            },

            fetchFacets: function () {
                // Do not submit empty filters:
                var filters = _.clone(this.filters);

                _.each(filters, function (value, key) {
                    if (_.isEmpty(value)) {
                        delete filters[key];
                    }
                });

                var data = _.extend({}, facetApi.data, {filters: filters});

                return $.ajax(
                    facetApi.url,
                    {
                        method: 'POST',
                        data: JSON.stringify(data),
                        // We use text/plain to avoid CORS preflight request
                        contentType: 'text/plain; charset=UTF-8',
                        dataType: 'json'
                    }
                )
                    .then((function (json) {
                        this.stats = json.stats;
                        this.facets = json.facets;
                        this.numFound = json.numFoundProducts;

                        this.syncFiltersAndFacets();
                    }).bind(this));
            },

            fetchFacetsAndReload: function () {
                return this.fetchFacets()
                    .then((function () {
                        productList.reload();
                    }).bind(this));
            },

            replaceHistoryState: function () {
                var other = _.chain(window.location.search.slice(1).split('&'))
                    .map(function (item) {
                        if (item) return _.map(item.split('='), decodeURIComponent);
                    })
                    .compact()
                    .filter(function (p) {
                        return p[0].indexOf(FILTER_PARAM_PREFIX) !== 0;
                    })
                    .value();

                var filters = filtersToParamArray(this.filters),
                    paramsStr = paramsToQueryString(other.concat(filters)),
                    query = paramsStr ? '?' + paramsStr : '';

                window.history.replaceState(
                    {cloudFilters: this.filters},
                    null,
                    window.location.pathname + query + window.location.hash
                );
            },

            replaceStateFromHistory: function (state) {
                this.filters = state.cloudFilters;

                this.fetchFacetsAndReload();
            },

            syncFiltersAndFacets: function () {
                // Create empty filters for all facets
                _.each(this.facets, (function (facet, fieldId) {
                    if (!(fieldId in this.filters)) {
                        Vue.set(this.filters, fieldId, []);
                    }
                }).bind(this));

                // Remove filters that don't have corresponding facets
                _.each(this.filters, (function (filterValues, filterId) {
                    if (
                        filterId != 'min_price'
                        && filterId != 'max_price'
                        && !(filterId in this.facets)
                    ) {
                        this.filters[filterId] = [];
                    }
                }).bind(this));
            },

            isVisible: function () {
                return this.loaded && (this.isAnyFilterSet || this.numFound > 0);
            },

            getFilterType: function (facet) {
                if (this.isColorFilter(facet)) {
                    return 'color-filter';
                } else if (this.isSizeFilter(facet)) {
                    return 'size-filter';
                } else {
                    return 'default-filter';
                }
            },

            isSizeFilter: function (facet) {
                var sizeFilterNames = ['size'];

                var sizeishName = _.any(sizeFilterNames, function (v) {
                    return facet.name.toLowerCase().indexOf(v) !== -1;
                });

                if (!sizeishName) {
                    return false;
                }

                return _.all(facet.counts, function (c) {
                    return sizes.indexOf(c.value) != -1;
                });
            },

            isColorFilter: function (facet) {
                var colorFilterNames = ['color', 'colour'];

                var colorishName = _.any(colorFilterNames, function (v) {
                    return facet.name.toLowerCase().indexOf(v) !== -1;
                });

                if (!colorishName) {
                    return false;
                }

                return _.any(facet.counts, function (c) {
                    return getColorByName(c.value) !== null;
                });
            }
        },

        components: {
            'default-filter': DefaultFilter,
            'color-filter': ColorFilter,
            'size-filter': SizeFilter,
            'category-filter': CategoryFilter,
            'price-filter': PriceFilter
        },

        created: function () {
            if (this.facets == null) {
                this.loaded = false;

                this.fetchFacets()
                    .then((function () {
                        this.loaded = !!this.facets;
                    }).bind(this));
            } else {
                this.loaded = true;
            }

            this.syncFiltersAndFacets();

            $(window).on('popstate', (function (event) {
                var state = event.originalEvent.state;

                if (state && state.cloudFilters != 'undefined') {
                    this.replaceStateFromHistory(state);
                }
            }).bind(this));

            this.replaceHistoryState();

            productList.setFilters(this.filters);

            productList.subscribeToNewSearches(this.searchAction.bind(this));
        }
    });
})(_, jQuery);