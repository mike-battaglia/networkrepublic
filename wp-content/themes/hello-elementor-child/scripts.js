jQuery(document).ready(function ($) {
    // Hide Series and Sub-series initially
    // $('.facetwp-facet-series, .facetwp-facet-subseries').hide();

    // Function to show or hide Series based on the active tab
    function toggleSeriesVisibility() {
        if ($('.elementor-tab-title[data-tab="2"]').hasClass('elementor-active')) {
            // Show Series
            $('.facetwp-facet-series').show();
        } else {
            // Hide Series
            $('.facetwp-facet-series').hide();
        }
    }

    // Function to show or hide Sub-series based on the active tab
    function toggleSubSeriesVisibility() {
        if ($('.elementor-tab-title[data-tab="3"]').hasClass('elementor-active')) {
            // Show Sub-series
            $('.facetwp-facet-subseries').show();
        } else {
            // Hide Sub-series
            $('.facetwp-facet-subseries').hide();
        }
    }

    // Initial setup
    toggleSeriesVisibility();
    toggleSubSeriesVisibility();

    // When Brand is selected
    $(document).on('facetwp-loaded', function () {
        var selectedBrand = FWP.facets.brand;
        var selectedSeries = FWP.facets.series;

        if (selectedBrand.length) {
            // Show Series based on selected Brand
            $('#elementor-tab-title-1152').show();
            $('.elementor-tab-title[data-tab="2"]').addClass('elementor-active');
            $('#elementor-tab-content-1152').addClass('elementor-active').show();
            toggleSeriesVisibility();

            if (selectedSeries.length) {
                // If Series is selected, show Sub-series
                $('#elementor-tab-title-1153').show();
                $('.elementor-tab-title[data-tab="3"]').addClass('elementor-active');
                $('#elementor-tab-content-1153').addClass('elementor-active').show();
                toggleSubSeriesVisibility();
            }
        } else {
            // Hide Series and Sub-series if no Brand is selected
            FWP.facets.series = [];
            FWP.facets.subseries = [];
            $('.facetwp-facet-series, .facetwp-facet-subseries').hide();
            // Manually uncheck radio buttons for Series and Sub-series
            $('.facetwp-facet-series .facetwp-radio.checked, .facetwp-facet-subseries .facetwp-radio.checked').trigger('click');
            $('#elementor-tab-title-1152, #elementor-tab-content-1152, #elementor-tab-title-1153, #elementor-tab-content-1153').removeClass('elementor-active').hide();


        }
    });

    // When Series is selected
    $(document).on('facetwp-refresh', function () {
        var selectedSeries = FWP.facets.series;

        if (selectedSeries.length > 0) {
            // Show Sub-series based on selected Series
            $('#elementor-tab-title-1153').show();
            $('.elementor-tab-title[data-tab="3"]').addClass('elementor-active');
            $('#elementor-tab-content-1153').addClass('elementor-active').show();
            toggleSubSeriesVisibility();
        } else {
            // Hide Sub-series if no Series is selected
            FWP.facets.subseries = [];
            $('.facetwp-facet-subseries').hide();
            // Manually uncheck radio buttons for Sub-series
            $('.facetwp-facet-subseries .facetwp-radio.checked').trigger('click');
            $('#elementor-tab-title-1153, #elementor-tab-content-1153').removeClass('elementor-active').hide();

        }
    });
});
