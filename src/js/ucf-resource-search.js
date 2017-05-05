/* Call A-Z Index Scrollspy, organize post type search */
var azIndex = function ($) {
    if ($('#azindex').length > 0) {

        // Post type search customizations
        $('.post-type-search-header').prepend($('#azIndexList'));

        $('.post-type-search-alpha h3').each(function () {
            $(this)
                .parent('div').prepend('<div class="az-jumpto-anchor" id="az-' + $(this).text().toLowerCase() + '" />')
                .children('h3').after('<span class="backtotop"><span class="glyphicon glyphicon-arrow-up"></span> <a href="#top">Back to Top</a></span>');
        });

        // Activate Scrollspy
        $('body').attr({ 'data-spy': 'scroll', 'data-offset': 80, 'data-target': '#azIndexList' });
        $('#azIndexList').scrollspy();

        if (jQuery.browser.safari) {
            $('#azIndexList').attr('data-spy', '');
        }

        // Force 'A' as the active starting letter, since it likes to
        // default to 'Z' for whatever reason
        $('#azIndexList .nav li.active').removeClass('active');
        $('#azIndexList .nav li:first-child').addClass('active');

        // Reset active letter link when 'Back to Top' is clicked
        $('.backtotop a').click(function () {
            $('#azIndexList .nav li.active').removeClass('active');
            $('#azIndexList .nav li:first-child').addClass('active');
        });

        // Set disabled letters for sections with no content
        $('.az-jumpto-anchor').each(function () {
            if ($(this).siblings('ul').children().length < 1) {
                var href = '#' + $(this).attr('id');
                $('#azIndexList .nav li a[href="' + href + '"]').addClass('disabled');
            }
        });
        $('#azIndexList .nav li a.disabled').click(function (e) {
            e.preventDefault();
        });
    }
};

if (typeof jQuery != 'undefined') {
    jQuery(document).ready(function ($) {
        azIndex($);
    });
} else {
    console.log('jQuery dependency failed to load');
}