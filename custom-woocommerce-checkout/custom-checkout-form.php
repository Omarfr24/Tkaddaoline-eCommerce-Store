// Function to process the custom checkout form
function process_custom_checkout_form() {
    if (isset($_POST['custom_checkout_submit']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'custom_checkout_action')) {
        // Sanitize input data
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $city = sanitize_text_field($_POST['city']);
        $address = sanitize_text_field($_POST['address']);
        $notes = sanitize_textarea_field($_POST['notes']);
        $product_id = absint($_POST['product_id']); // Sanitize and validate the product ID

        // Check if the product ID is valid
        if (!$product_id || !get_post($product_id)) {
            wp_die('Invalid product ID.');
        }

        // Create a new WooCommerce order
        $order = wc_create_order();

        // Add the product to the order
        $product = wc_get_product($product_id);
        $order->add_product($product, 1); // Add 1 quantity of the product

        // Set order billing details
        $order->set_address(array(
            'first_name' => $name,
            'phone'      => $phone,
            'city'       => $city,
            'address_1'  => $address,
        ), 'billing');

        // Add a custom note with the user's message
        $order->add_order_note('Notes: ' . $notes);

        // Calculate totals
        $order->calculate_totals();

        // Mark order as "processing" (or another status as per your needs)
        $order->update_status('processing');

        // Redirect to the WooCommerce "Thank You" page
        $thankyou_url = $order->get_checkout_order_received_url();
        wp_redirect($thankyou_url);
        exit;
    }
}
add_action('template_redirect', 'process_custom_checkout_form');

// Function to display the custom checkout form on the product page
function display_custom_checkout_form() {
    if (is_product()) {
        echo '<form id="checkoutForm" method="POST">';
        wp_nonce_field('custom_checkout_action');
        echo '<h2 style="text-align: center; margin-bottom: 20px;">للطلب المجو ادخال معلوماتك أسفله</h2>';
        echo '<label for="name">الاسم الكامل:</label>';
        echo '<input type="text" id="name" name="name" required>';
        echo '<label for="phone">رقم الهاتف:</label>';
        echo '<input type="tel" id="phone" name="phone" pattern="\d{10}" required title="يجب أن يحتوي على 10 أرقام">';
        echo '<label for="city">المدينة:</label>';
        echo '<input type="text" id="city" name="city" required>';
        echo '<label for="address">العنوان:</label>';
        echo '<input type="text" id="address" name="address" required>';
        echo '<label for="notes">الملاحظة:</label>';
        echo '<textarea id="notes" name="notes" rows="4" optional></textarea>';
        echo '<input type="hidden" name="custom_checkout_submit" value="1">';
        echo '<input type="hidden" name="product_id" value="' . esc_attr(get_the_ID()) . '">';
        echo '<button type="submit">اضغط هنا للطلب</button>';
        echo '</form>';
    }
}
add_action('woocommerce_single_product_summary', 'display_custom_checkout_form', 25);

// Add an "اطلب الآن" button below "Add to Cart" on single product pages
function add_order_now_button() {
    $checkout_form_url = '#checkoutForm'; // This will scroll to the custom form on the same page
    echo '<a href="' . esc_url($checkout_form_url) . '" class="order-now-button button secondary" style="margin-top: 10px; display: inline-block;">' . __('اطلب الآن', 'woocommerce') . '</a>';
}
add_action('woocommerce_after_add_to_cart_button', 'add_order_now_button');

// Function to add a sticky "اطلب الآن" button to the product page
function add_sticky_order_now_button() {
    if (is_product()) {
        // Generate the URL to scroll to the custom checkout form
        $checkout_form_url = '#checkoutForm'; // This will scroll to the custom form on the same page
		
        // Output the sticky "Order Now" button HTML
        echo '<div class="sticky-order-now-button">
                <a href="' . esc_url($checkout_form_url) . '" class="order-now-button button secondary">' . __('اطلب الآن', 'woocommerce') . '</a>
              </div>';
    }
}
add_action('wp_footer', 'add_sticky_order_now_button');

add_action('woocommerce_after_add_to_cart_button', 'add_order_now_button');

add_action('wp_footer', 'add_sticky_order_now_button');

