let $resourceLinkCards;
let $UcfResourceDirectoryItems;

const PostTypeSearchDataManager = {
  searches: [],
  register: function (search) {
    this.searches.push(search);
  }
};

const PostTypeSearchData = (column_count, column_width, data) => {
  this.column_count = column_count;
  this.column_width = column_width;
  this.data = data;
};

const resourceSearch = ($) => {

  $('.resource-search')
    .each((post_type_search_index, post_type_search) => {
      let $post_type_search = $(post_type_search),
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
      post_type_search_data = PostTypeSearchDataManager.searches[post_type_search_index];
      if (typeof post_type_search_data === 'undefined') { // Search data missing
        return false;
      }

      search_data_set = post_type_search_data.data;
      column_count = post_type_search_data.column_count;
      column_width = post_type_search_data.column_width;

      if (column_count === 0 || column_width === '') { // Invalid dimensions
        return false;
      }

      // Sorting toggle
      sorting_by_term.click(() => {
        by_alpha.fadeOut('fast', () => {
          by_term.fadeIn();
          sorting_by_alpha.removeClass('active');
          sorting_by_term.addClass('active');
        });
      });
      sorting_by_alpha.click(() => {
        by_term.fadeOut('fast', () => {
          by_alpha.fadeIn();
          sorting_by_term.removeClass('active');
          sorting_by_alpha.addClass('active');
        });
      });

      // Search form
      form
        .submit((event) => {
          // Don't allow the form to be submitted
          event.preventDefault();
          perform_search(field.val());
        });
      field
        .keyup(() => {
          // Use a timer to determine when the user is done typing
          if (typing_timer !== null) {
            clearTimeout(typing_timer);
          }
          typing_timer = setTimeout(() => {
            form.trigger('submit');
          }, typing_delay);
        });

      function display_search_message(message) {
        results.empty();
        results.append($(`<p class="resource-search-message"><big>${message}</big></p>`));
        results.show();
      }

      function perform_search(search_term) {
        let matches = [],
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
        $.each(search_data_set, (post_id, search_data) => {
          $.each(search_data, (search_data_index, term) => {
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
          $.each(matches, (match_index, post_id) => {
            const element = by_alpha.find(`li[data-post-id="${post_id}"]:eq(0)`),
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
              for (let i = 0; i < column_count; i++) {
                const start = i * elements_per_column,
                  end = start + elements_per_column;
                if (elements.length > start) {
                  columns[i] = elements.slice(start, end);
                }
              }

              // Setup results HTML
              results.append($('<div class="row"></div>'));
              $.each(columns, (column_index, column_elements) => {
                const column_wrap = $(`<div class="${column_width}"><ul class="resource-search-result-list"></ul></div>`),
                  column_list = column_wrap.find('ul');

                $.each(column_elements, (element_index, element) => {
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

const setDisabledJumpLinks = ($) => {
  $links = $('.jump-to-list').find('a');
  $.each($links, (index, element) => {
    if ($($(element).attr('href')).length === 0) {
      $(element).addClass('disabled');
    }
  }, this);
};

function scrollToElement($element) {
  $([document.documentElement, document.body]).animate({
    scrollTop: $element.offset().top - 20
  }, 1000);
}

function filterCards(e) {
  e.preventDefault();
  let hash = window.location.hash;
  if (hash) {
    if (hash === '#all') {
      $resourceLinkCards.show();
    } else {
      hash = hash.replace('#', '');
      $resourceLinkCards
        .show()
        .not(`.${hash}`).hide();
    }
    scrollToElement($UcfResourceDirectoryItems);
  }
}

const addEventHandlers = ($) => {
  $(window).on('hashchange load', filterCards);
};

if (typeof jQuery !== 'undefined') {
  jQuery(document).ready(($) => {
    $UcfResourceDirectoryItems = $('.ucf-resource-directory-items');
    $resourceLinkCards = $UcfResourceDirectoryItems.find('.card-wrapper');

    resourceSearch($);
    setDisabledJumpLinks($);
    addEventHandlers($);
  });
}
