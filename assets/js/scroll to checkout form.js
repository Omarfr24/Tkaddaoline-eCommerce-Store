
jQuery(document).ready(function($) {
    // Smooth scroll to checkout form when "Buy Now" button is clicked
    $('a.buy-now-button').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#checkoutForm').offset().top
        }, 1000); // 1000 milliseconds = 1 second smooth scroll
    });
});
