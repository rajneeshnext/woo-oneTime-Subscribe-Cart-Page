<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {
    if (isset($_POST['attribute_purchase-option']) && $_POST['attribute_purchase-option'] === 'One-Time') {
        $cart_item_data['subscription_renewal'] = false; // Ensure no subscription data is saved
		$cart_item_data['subscription_period'] = false;
        $cart_item_data['subscription_length'] = false;
        $cart_item_data['subscription_sign_up_fee'] = false;
    	$cart_item_data['subscription_trial_length'] = false;
        $cart_item_data['subscription_trial_period'] = false;
        $cart_item_data['subscription_period_interval'] = false;
    }
    return $cart_item_data;
}, 10, 2);


add_filter('woocommerce_cart_item_price', function ($price, $cart_item, $cart_item_key) {
    if (isset($cart_item['subscription_renewal']) && !$cart_item['subscription_renewal']) {
        $price = wc_price($cart_item['line_subtotal']); // Override the subscription price
    }
    return $price;
}, 10, 3);


// Remove subscription details from one-time purchases on the cart and checkout pages
add_filter('woocommerce_cart_item_price', function ($price, $cart_item, $cart_item_key) {
    if (!empty($cart_item['variation']['attribute_purchase-option']) && $cart_item['variation']['attribute_purchase-option'] === 'One-Time') {
        $price = wc_price($cart_item['line_subtotal']); // Show only the regular price
    }
    return $price;
}, 10, 3);

function add_subscription_options_before_cart() {
    global $product;
	//echo $product->is_type('product-variable-subscription');exit();
	//print_r($product);exit();
    if (!$product) {
        return;
    }
	$regular_price = $product->get_regular_price();
	$sale_price = $product->get_sale_price(); // Get sale price
	// Use sale price if available, otherwise use regular price
	$display_price = !empty($sale_price) ? $sale_price : $regular_price;
    $saving_price = floatval($display_price) * 0.90; // 10% discount for subscription
    ?>
<style>
.radio-container {
    display: flex;
    flex-direction: column;
    gap: 15px; /* Space between radio buttons */
}
.radio-label {
    display: flex;
    flex-direction: column;
    width: 100%;
    padding: 22px 15px;
    border: 2px solid #ccc;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s, border-color 0.3s;
    position: relative;
}
p.price{display: none;}
.radio-label.selected {
	background-color: #f9f8f6;
}
.radio-label:hover,
.radio-input:checked + .radio-label {
    background-color: #e0e0e0;
    border-color: black;
}
.radio-input {
    display: none; /* Hide default radio button */
}
/* Top Row: Radio Button & Price */
.radio-top {
	font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
/* Custom radio button */
.radio-custom {
    position: relative;
    width: 22px;
    height: 22px;
    border: 2px solid black;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    padding: 2px;
}
/* Inner dot */
.radio-dot.selected{
	background-color: #888;
}
.radio-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    transition: background-color 0.3s, transform 0.2s;
}
/* Keep dot highlighted when checked */
.radio-input:checked + .radio-label .radio-dot {
    background-color: #888;
}
/* Price alignment */
.radio-price {
    font-weight: bold;
}
/* Subscription details - Always visible */
.subscription-details {
    margin-top: 20px;
}
.subscription-title {
    font-weight: bold;
    margin-bottom: 5px;
}
.subscription-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.subscription-list li {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}
.subscription-list li::before {
    content: "✔"; /* Black checkmark */
    font-weight: bold;
    color: black;
    margin-right: 8px;
}
.radio-label:nth-of-type(2){
	height: 200px;
}
.bos4w-display-dropdown{
	display: block !important;
    opacity: 1 !important;
    margin: auto !important;
    padding: 0 !important;
    width: auto !important;
    height: auto !important;
}		
.bos4w-display-wrap {
	position: absolute;
	top: 185px;
	right: 45px;
	width: 200px;
}
.radio-top > div.radio-custom {
    margin-right: 10px; /* Adds spacing between the radio button and text */
}
.radio-top span.radio-price {
    margin-left: auto; /* Pushes price to the right */
    text-align: right;
}
label[for="bos4w-dropdown-plan"]{font-weight: bold; color: #A0A0A0;}
.bos4w-display-plan-text, .bos4w-display-wrap .bos4w-display-options{display: none;}
#bos4w-dropdown-plan{
	border: 1px solid #ccc;
	border-radius: 6px;
	height: 40px;
	padding-left: 10px;
	margin-top: 5px;
	
	appearance: auto; /* Keep default dropdown arrow */
	-webkit-appearance: auto; /* Safari-specific */
	-moz-appearance: auto; /* Firefox-specific */
	background-color: white !important; /* Change background */
	color: black !important; /* Change text color */
	border: 1px solid #ccc; /* Optional: Custom border */
}
@media (max-width: 600px) {
	.bos4w-display-wrap {
		position: absolute;
		top: 270px;
		left: 20px;
		width: 200px;
	}
	.radio-label:nth-of-type(2){
		height: 280px;
	}
}
@media (min-width: 800px) {	
	.elementor-27581 .elementor-element.elementor-element-266b12f{padding-right: 80px;}
}	
form.cart{margin-top: 25px !important;margin-bottom: 25px !important;}
.quantity {
    display: flex;
    align-items: center;
    gap: 5px;
}
.quantity input.qty::-webkit-outer-spin-button,
.quantity input.qty::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.woocommerce .product .elementor-jet-single-add-to-cart .cart .quantity .qty{
	background: #f9f8f6;
}
	button.plus{
		border-radius: 0px 15px 15px 0;
	}
	button.minus{
		border-radius: 15px 0px 0px 15px;
	}
.woocommerce .product .elementor-jet-single-add-to-cart .cart .single_add_to_cart_button{min-height: 50px; width: 350px;}
.quantity button, .quantity button:focus, .quantity button:hover {
    background: #f9f8f6;
    color: #000;
    border: none;
    padding: 5px 10px;
    font-size: 16px;
    cursor: pointer;
}
.quantity input.qty {
    width: 50px !important;
	border: 0 !important;
    text-align: center;
    font-size: 16px;
	padding: 5px 9px !important;
    border-radius: 0 !important;
}
.quantity{gap: 0;}	
</style>
  <div class="radio-container">
    <!-- One-Time Purchase -->
    <label class="radio-label selected">
        <input type="radio" name="purchase" class="radio-input" checked>
        <div class="radio-top">
            <div class="radio-custom">
                <div class="radio-dot selected"></div>
            </div>
            One-Time
            <span class="radio-price">$<?php echo $display_price;?></span>
        </div>
    </label>

    <!-- Subscribe & Save -->
    <label class="radio-label">
        <input type="radio" name="purchase" class="radio-input">
        <div class="radio-top">
            <div class="radio-custom">
                <div class="radio-dot"></div>
            </div>
            Subscribe & Save 10%
            <span class="radio-price">$<?php echo $saving_price;?></span>
        </div>

        <!-- Subscription details (Always Visible) -->
        <div class="subscription-details">
            <div class="subscription-title">How subscription works:</div>
            <ul class="subscription-list">
                <li>Lowest price option</li>
                <li>10% off all recurring orders</li>
                <li>Easily swap & skip deliveries</li>
                <li>Cancel quickly anytime</li>
            </ul>
        </div>
    </label>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const radioLabels = document.querySelectorAll(".radio-label");
    const oneTimeLabel = document.querySelector('label[for="bos4w-one-time"]');
    const subscribeLabel = document.querySelector('label[for="bos4w-subscribe-to"]');
	const dropdown = document.getElementById("bos4w-dropdown-plan");
    if (dropdown) {
        dropdown.value = "4_week_10"; // Set the default selected option
    }
    radioLabels.forEach(label => {
        label.addEventListener("click", function () {
            // Remove 'selected' class from all labels and radio dots
            radioLabels.forEach(l => {
                l.classList.remove("selected");
                const dot = l.querySelector(".radio-dot");
                if (dot) dot.classList.remove("selected");
            });

            // Add 'selected' class to the clicked label
            this.classList.add("selected");

            // Add 'selected' class to the corresponding radio-dot
            const selectedDot = this.querySelector(".radio-dot");
            if (selectedDot) selectedDot.classList.add("selected");

            // Check which radio is selected and trigger click on the correct label
            if (this.textContent.includes("One-Time")) {
                oneTimeLabel.click(); // Trigger click for "One-Time" label
            } else {
                subscribeLabel.click(); // Trigger click for "Subscribe & Save" label
            }
        });
    });
});

jQuery(document).ready(function ($) {
	// Store the original prices
    let originalPrices = [];

    $(".radio-price").each(function (index) {
        originalPrices[index] = parseFloat($(this).text().replace("$", ""));
    });
    // Add plus and minus buttons dynamically
    $(".quantity").each(function () {
        var $this = $(this);
        if (!$this.find(".plus").length) {
            $this.prepend('<button type="button" class="minus">-</button>');
            $this.append('<button type="button" class="plus">+</button>');
        }
    });

    // Quantity change functionality
    $(".quantity").on("click", ".plus, .minus", function () {
        var $qty = $(this).siblings("input.qty");
        var currentVal = parseFloat($qty.val());
        var max = $qty.attr("max") ? parseFloat($qty.attr("max")) : null;
        var min = $qty.attr("min") ? parseFloat($qty.attr("min")) : 1;
        var step = $qty.attr("step") ? parseFloat($qty.attr("step")) : 1;

        if ($(this).hasClass("plus")) {
            if (max === null || currentVal < max) {
                $qty.val(currentVal + step).change();
            }
        } else {
            if (currentVal > min) {
                $qty.val(currentVal - step).change();
            }
        }
    });
	
	// Update .radio-price when quantity changes
    $(".quantity input.qty").on("change keyup", function () {
        var quantity = parseFloat($(this).val()) || 1; // Ensure valid number

        $(".radio-price").each(function (index) {
            var newPrice = (originalPrices[index] * quantity).toFixed(2);
            $(this).text(`$${newPrice}`);
        });
    });
});

</script>
<?php
}
add_action('woocommerce_before_add_to_cart_form', 'add_subscription_options_before_cart');
function custom_footer_css() {
    echo '<style>
        .woocommerce-checkout #content{
			width: 90%;
			margin: 0 auto;
			margin-bottom: 80px;
		}
		.woocommerce-checkout .wc-block-components-sidebar{padding: 0;}
    </style>';
}
add_action('wp_footer', 'custom_footer_css');
add_filter( 'bos4w_dropdown_label_text', function( $text ) {
   $text = 'Delivers every';
   return $text;
}, 10, 1 );
add_filter( 'ssd_subscription_plan_display', function( $option_text, $period_interval, $discounted_price, $display_discount ) {
   return $period_interval;  
}, 10, 4);
add_filter('woocommerce_subscription_period_interval_strings', function($intervals) {
	$i = 8;
    $intervals[ $i ] = sprintf( _x( 'every %s', 'period interval with ordinal number (e.g. "every 2nd"', 'woocommerce-subscriptions' ), wcs_append_numeral_suffix( $i ) );
    return $intervals;
}, 10, 2);
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let interval = setInterval(() => {
            let stripeElement = document.querySelector('#wc-stripe-express-checkout-element');
            if (stripeElement) {
                let expressDiv = document.createElement('div');
                expressDiv.style.display = 'flex';
                expressDiv.style.alignItems = 'center';
                expressDiv.style.justifyContent = 'center';
                expressDiv.style.marginTop = '15px';
                expressDiv.innerHTML = `
                    <hr style="flex-grow: 1; border: none; border-top: 1px solid #ddd; margin: 0 10px;">
                    <span style="font-weight: bold; white-space: nowrap;">Express Checkout</span>
                    <hr style="flex-grow: 1; border: none; border-top: 1px solid #ddd; margin: 0 10px;">
                `;
                stripeElement.parentNode.insertBefore(expressDiv, stripeElement);
                clearInterval(interval); // Stop checking once it's added
            }
        }, 500); // Check every 500ms
    });
    </script>
    <?php
}, 99);
