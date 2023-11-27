// Add this script to your 'scripts.js' file or include it in your page
jQuery(document).ready(function ($) {
    // Submit form on checkbox change
    $('input[class="category-filter"]').change(function () {    
        // Trigger Ajax request with the selected category
        filterProducts();
    });

    $('input[name="color_filter"]').change(function () {
        // Trigger Ajax request with the selected category
        filterProducts();
    });

    $('#min-price').on('input', function() {
        //alert('min working!');
        filterProducts();
    });

    $('#max-price').on('input', function() {
        //alert('max working!');
        filterProducts();
    });

    // Filter button click event
    $('#category-filter-form').submit(function (event) {
        event.preventDefault(); // Prevent default form submission
        filterProducts();
    });

    // Reset button click event
    $('#reset-filter').click(function () {
        // Clear checkboxes
        $('input[type="radio"]').prop('checked', false);
        $('input[type="checkbox"]').prop('checked', false);
        $('input[id="all-radio"][value="all"]').prop('checked', true);


        // Trigger Ajax request with no selected category (empty value)
        filterProducts();
    });

    // Ajax request function
    function filterProducts() {
        var selectedCategory = $('input[class="category-filter"]:checked').val() || ''; 
        var selectedColor = $('input[name="color_filter"]:checked').val() || ''; 
        var selectedMinPrice = $('#min-price').val() || 0;
        var selectedMaxPrice = $('#max-price').val() || Number.MAX_SAFE_INTEGER;

        // Update min price label with selected value in brackets
        $('#min-price-label').html('Min Price (' + selectedMinPrice + '):');

        // Update max price label with selected value in brackets
        $('#max-price-label').html('Max Price (' + selectedMaxPrice + '):');

        $.ajax({
            type: 'POST',
            url: ah65_category_filter_ajax.ajax_url,
            data: {
                action: 'ah65_category_filter',
                selected_category: selectedCategory,
                selected_color: selectedColor,
                selected_min_price: selectedMinPrice,
                selected_max_price: selectedMaxPrice,
            },
            beforeSend: function () {
                $('.database__spinner').removeClass('hidden')
            },
            success: function (response) {
                $('.products').html(response);
            },
            complete: function () {
                $('.database__spinner').addClass('hidden');
            }
        });
    }


});
