
var PostTypeSearchDataManager = {
    'searches': [],
    'register': function (search) {
        this.searches.push(search);
    }
};

var PostTypeSearchData = function (column_count, column_width, data) {
    this.column_count = column_count;
    this.column_width = column_width;
    this.data = data;
};

/* Call A-Z Index Scrollspy, organize post type search */
var resourceSearch = function ($) {
    if ($('.resource-search').length > 0) {

        var $indexList = $('#index-list');

        // Post type search customizations
        $('.resource-search-header').prepend($indexList);

        $('.resource-search-alpha h3').each(function () {
            $(this)
                .parent('div').prepend('<div class="az-jumpto-anchor" id="az-' + $(this).text().toLowerCase() + '" />')
                .children('h3').after('<span class="back-to-top"><span class="glyphicon glyphicon-arrow-up"></span> <a href="#top">Back to Top</a></span>');
        });

        // Activate Scrollspy
        if (typeof scrollspy !== 'undefined') {
            $('body').attr({ 'data-spy': 'scroll', 'data-offset': 80, 'data-target': '#index-list' });
            $indexList.scrollspy();

            if (jQuery.browser.safari) {
                $indexList.attr('data-spy', '');
            }

            var $indexListNav = $indexList.find('.nav');

            // Force 'A' as the active starting letter, since it likes to
            // default to 'Z' for whatever reason
            $indexListNav.find('li.active').removeClass('active');
            $indexListNav.find('li:first-child').addClass('active');

            // Reset active letter link when 'Back to Top' is clicked
            $('.backtotop a').click(function () {
                $indexListNav.find('li.active').removeClass('active');
                $indexListNav.find('li:first-child').addClass('active');
            });

            // Set disabled letters for sections with no content
            $('.az-jumpto-anchor').each(function () {
                if ($(this).siblings('ul').children().length < 1) {
                    var href = '#' + $(this).attr('id');
                    $indexListNav.find('li a[href="' + href + '"]').addClass('disabled');
                }
            });
            $indexListNav.find('li a.disabled').click(function (e) {
                e.preventDefault();
            });
        }
    }
};

var postTypeSearch = function ($) {

    $('.resource-search')
        .each(function (post_type_search_index, post_type_search) {
            var $post_type_search = $(post_type_search),
                form = $post_type_search.find('.resource-search-form'),
                field = form.find('input[type="text"]'),
                working = form.find('.working'),
                results = $post_type_search.find('.resource-search-results'),
                by_term = $post_type_search.find('.resource-search-term'),
                by_alpha = $post_type_search.find('.resource-search-alpha'),
                sorting = $post_type_search.find('.resource-search-sorting'),
                sorting_by_term = sorting.find('button:eq(0)'),
                sorting_by_alpha = sorting.find('button:eq(1)'),

                post_type_search_data = null,
                search_data_set = null,
                column_count = null,
                column_width = null,

                typing_timer = null,
                typing_delay = 300, // milliseconds

                prev_post_id_sum = null, // Sum of result post IDs. Used to cache results

                MINIMUM_SEARCH_MATCH_LENGTH = 2;

            // Get the post data for this search
            console.log(PostTypeSearchDataManager);
            post_type_search_data = PostTypeSearchDataManager.searches[post_type_search_index];
            if (typeof post_type_search_data === 'undefined') { // Search data missing
                console.log('search data missing');
                return false;
            }

            search_data_set = post_type_search_data.data;
            column_count = post_type_search_data.column_count;
            column_width = post_type_search_data.column_width;

            if (column_count === 0 || column_width === '') { // Invalid dimensions
                return false;
            }

            // Sorting toggle
            sorting_by_term.click(function () {
                by_alpha.fadeOut('fast', function () {
                    by_term.fadeIn();
                    sorting_by_alpha.removeClass('active');
                    sorting_by_term.addClass('active');
                });
            });
            sorting_by_alpha.click(function () {
                by_term.fadeOut('fast', function () {
                    by_alpha.fadeIn();
                    sorting_by_term.removeClass('active');
                    sorting_by_alpha.addClass('active');
                });
            });

            // Search form
            form
                .submit(function (event) {
                    // Don't allow the form to be submitted
                    event.preventDefault();
                    perform_search(field.val());
                });
            field
                .keyup(function () {
                    // Use a timer to determine when the user is done typing
                    if (typing_timer !== null) { clearTimeout(typing_timer); }
                    typing_timer = setTimeout(function () { form.trigger('submit'); }, typing_delay);
                });

            function display_search_message(message) {
                results.empty();
                results.append($('<p class="resource-search-message"><big>' + message + '</big></p>'));
                results.show();
            }

            function perform_search(search_term) {
                var matches = [],
                    elements = [],
                    elements_per_column = null,
                    columns = [],
                    post_id_sum = 0;

                if (search_term.length < MINIMUM_SEARCH_MATCH_LENGTH) {
                    results.empty();
                    results.hide();
                    return;
                }
                // Find the search matches
                $.each(search_data_set, function (post_id, search_data) {
                    $.each(search_data, function (search_data_index, term) {
                        if (term.toLowerCase().indexOf(search_term.toLowerCase()) !== -1) {
                            matches.push(post_id);
                            return false;
                        }
                    });
                });
                if (matches.length === 0) {
                    display_search_message('No results were found.');
                } else {

                    // Copy the associated elements
                    $.each(matches, function (match_index, post_id) {
                        var element = by_term.find('li[data-post-id="' + post_id + '"]:eq(0)'),
                            post_id_int = parseInt(post_id, 10);
                        post_id_sum += post_id_int;
                        if (element.length === 1) {
                            elements.push(element.clone());
                        }
                    });

                    if (elements.length === 0) {
                        display_search_message('No results were found.');
                    } else {

                        // Are the results the same as last time?
                        if (post_id_sum !== prev_post_id_sum) {
                            results.empty();
                            prev_post_id_sum = post_id_sum;


                            // Slice the elements into their respective columns
                            elements_per_column = Math.ceil(elements.length / column_count);
                            for (var i = 0; i < column_count; i++) {
                                var start = i * elements_per_column,
                                    end = start + elements_per_column;
                                if (elements.length > start) {
                                    columns[i] = elements.slice(start, end);
                                }
                            }

                            // Setup results HTML
                            results.append($('<div class="row"></div>'));
                            $.each(columns, function (column_index, column_elements) {
                                var column_wrap = $('<div class="' + column_width + '"><ul class="resource-search-result-list"></ul></div>'),
                                    column_list = column_wrap.find('ul');

                                $.each(column_elements, function (element_index, element) {
                                    column_list.append($(element));
                                });
                                results.find('div[class="row"]').append(column_wrap);
                            });
                            results.show();
                        }
                    }
                }
            }
        });
};

if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function ($) {
        resourceSearch($);
        postTypeSearch($);
    });
} else {
    console.log('jQuery dependency failed to load');
}