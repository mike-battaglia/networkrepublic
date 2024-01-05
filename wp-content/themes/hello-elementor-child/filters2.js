jQuery(document).ready(function ($) {
    // Hide Series and Sub-series initially
    // $('.facetwp-facet-series, .facetwp-facet-subseries').hide();	
	
	console.log('filters.js called 🥜!')
	
 	$('#mbatt-pager').on('click', 'a', function(event) {
		console.log('pager-clicked');
        // Vanilla JavaScript inside a jQuery event listener
        var products = document.getElementById('mbatt-products');
        if (products) {
            event.preventDefault(); // Prevent the default anchor action
            products.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
    
	// Function to show Brand tab
    function showBrandTab() {
        $('#elementor-tab-title-1151').show();
        $('.elementor-tab-title[data-tab="1"]').addClass('elementor-active');
        $('#elementor-tab-content-1151').addClass('elementor-active').show();
    }

    showBrandTab();

			
    // Function to show or hide Series based on the active tab
    function toggleSeriesVisibility() {
        if ($('.elementor-tab-title[data-tab="2"]').hasClass('elementor-active')) {
            // Show Series
            $('.facetwp-facet-series').show();
			console.log('Series logic start.');
			if (!$('#elementor-tab-content-1152 > div > div').children().length) {
                console.log('no series');
				// insert a message into the empty series tab
                $('#elementor-tab-content-1152 > div').append('<div class="facetwp-radio checked" data-value="" role="checkbox" aria-checked="true" aria-label="All Series" tabindex="" id="all-series-radio"><span class="facetwp-display-value">All Series</span><span class="facetwp-counter"></span></div>');
			} else {
				console.log('some series');
			}
			console.log('Series logic end.')
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
			console.log('Sub-Series logic start.');
			if (!$('#elementor-tab-content-1153 > div > div').children().length) {
                console.log('no sub-series');
				// insert a message into the empty series tab
				if (document.getElementById('all-sub-series-radio')) {
					console.log('All Sub Msg Already Exists');
					var deleteMe = document.getElementById('all-sub-series-radio');
					deleteMe.parentNode.removeChild(deleteMe);
				}
                $('#elementor-tab-content-1153 > div').append('<div class="facetwp-radio checked" data-value="" role="checkbox" aria-checked="true" aria-label="All Sub Series" tabindex="" id="all-sub-series-radio"><span class="facetwp-display-value">All Sub-Series</span><span class="facetwp-counter"></span></div>');
			} else {
				console.log('some sub-series');
			}
			console.log('Sub-series logic end.')
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
