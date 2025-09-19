<?php
  $token = get_option( 'judgeme_shop_token' );
if ( ! empty( $token ) ):
	?>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <div class="judgeme__woocommerce-plugin-container">
    <div class="home-page-container">
      <div class="home-page__header-container">
        <div class="home-page__header-jdgm-info-container">
          <div class="home-page__header-rate-us-container">
            <div class="home-page__header-rate-us-text-container">
              <h1 class="home-page__header-rate-us-header">How's your experience with Judge.me?</h1>
              <p class="home-page__header-rate-us-text">Rate us by clicking on the stars</p>
            </div>
            <?php
              $link_to_rate_us = "https://wordpress.org/support/plugin/judgeme-product-reviews-woocommerce/reviews/#new-post";
            ?>
            <div class="home-page__header-rate-us-stars-container">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <a href="<?php echo $link_to_rate_us; ?>" target="_blank">
                  <i class="fa fa-star rate-us-star"></i>
                </a>
              <?php endfor; ?>
            </div>
          </div>
        </div>
        <div class="home-page__header-welcome-container">
          <img class="home-page__jdgm-image" src="<?php echo JGM_PLUGIN_URL.'assets/images/jdgm-logo-2025.png'; ?>"></image>
          <div class="home-page__jdgm-intro-container">
            <h1 class="home-page__title">Welcome to Judge.me Product Reviews!</h1>
            <p class="home-page__jdgm-intro-text">Judge.me powers the product reviews for your WooCommerce store. You can manage your reviews and settings directly in our app.</p>
            <a class="home-page__open-jdgm-btn" href= "<?php echo $url; ?>" target="_blank">Open the app</a>
          </div>
        </div>

      </div>

      <p class="home-page_terms-and-policy">By using this service, I confirm that I have read and agree to the <a href="https://judge.me/terms">Terms of Service</a> and <a href="https://judge.me/privacy">Privacy Policy</a>.</p>

      <div class="home-page__nav-container">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link active home-page__general-tab" data-toggle="tab" href="#home-page__general-tab" autofocus>General</a>
          </li>
          <li class="nav-item">
            <a class="nav-link home-page__faq-tab" data-toggle="tab" href="#home-page__faq-tab">FAQs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link home-page__advanced-tab" data-toggle="tab" href="#home-page__advanced-tab">Advanced</a>
          </li>
          <li class="nav-item">
            <a class="nav-link home-page__customer-support-tab" data-toggle="tab" href="#home-page__tab-for-customer-support">CS</a>
          </li>
        </ul>
      </div>

      <div class="tab-content">
        <div id="home-page__general-tab" class="tab-pane show active">
          <div class="home-page__general-container">
            <div class="home-page__quick-installation">
              <h3 class="home-page__quick-installation-title">Judge.me Widgets to get you started</h3>
              <div class="home-page__quick-installation-widget-info">
                <p>The Review Widget showcases customer reviews for specific products on the product page, while the Star Rating Badge (Preview Badge) displays the average star rating.</p>
                <p>To install, just enable the desired widget, and we'll add it to your live page. For more details and instructions, visit our help desk. If you need assistance customizing the widget's position,
                  contact us at <a class="contact-judgeme-support-email" href="mailto:support@judge.me">support@judge.me</a>, and we're here to help!</p></p>
              </div>

              <div class="home-page__quick-installation-widgets-container">
                <?php
                  $hide_widget = get_option('judgeme_option_hide_widget');
                  $hide_preview_badge_collection = get_option('judgeme_option_hide_preview_badge_collection');
                  $hide_preview_badge_single = get_option('judgeme_option_hide_preview_badge_single');
                ?>

                <div class="home-page__quick-installation-widgets-inner-container">

                  <div class="home-page__quick-installation-review-widget">
                    <h4 class="home-page__quick-installation-review-widget-title">Review Widget on Product page</h4>
                    <img src= "<?php echo JGM_PLUGIN_URL.'assets/images/review-widget-product-page.png'; ?>">
                    <div class="home-page__quick-installation-review-widget-toggle">
                      <label class="switch" for="review-widget-toggle">
                        <input <?php if(!$hide_widget) echo "checked" ?> type="checkbox" id="review-widget-toggle" data-type="judgeme_option_hide_widget" class="home-page__quick-installation-review-widget-toggle-switch widget-toggle" />
                        <div class="widget-checkbox"></div>
                      </label>
                      <span class="home-page__quick-installation-review-widget-install-uninstall-text-toggle">Installed on product page</span>
                    </div>
                  </div>

                  <div class="home-page__quick-installation-product-page">
                    <h4 class="home-page__quick-installation-product-page-title">Preview Badge on Product page</h4>
                    <img src= "<?php echo JGM_PLUGIN_URL.'assets/images/star-rating-badge-product-page.png'; ?>">
                    <div class="home-page__quick-installation-product-page-badge-toggle">
                      <label class="switch" for="product-page-badge-toggle">
                        <input <?php if(!$hide_preview_badge_single) echo "checked" ?> type="checkbox" id="product-page-badge-toggle" data-type="judgeme_option_hide_preview_badge_single" class="home-page__quick-installation-product-page-badge-toggle-switch widget-toggle" />
                        <div class="widget-checkbox"></div>
                      </label>
                      <span class="home-page__quick-installation-product-page-install-uninstall-text-toggle">Installed on collection page</span>
                    </div>
                  </div>

                  <div class="home-page__quick-installation-collection-page">
                    <h4 class="home-page__quick-installation-collection-page-title">Preview Badge on Collection page</h4>
                    <img src= "<?php echo JGM_PLUGIN_URL.'assets/images/star-rating-badge-collection-page.png'; ?>">
                    <div class="home-page__quick-installation-collection-page-badge-toggle">
                      <label class="switch" for="collection-page-badge-toggle">
                        <input <?php if(!$hide_preview_badge_collection) echo "checked" ?> type="checkbox" id="collection-page-badge-toggle" data-type="judgeme_option_hide_preview_badge_collection" class="home-page__quick-installation-collection-page-badge-toggle-switch widget-toggle" />
                        <div class="widget-checkbox"></div>
                      </label>
                      <span class="home-page__quick-installation-collection-page-badge-install-uninstall-text-toggle">Installed on product page</span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="home-page__install-widget-yourself">
              <div class="home-page__install-widget-install-widget-yourself-link-container">
                <p class="home-page__install-widget-install-widget-yourself-link">Install widgets by yourself in custom positions
                  <button class="home-page__install-widget-install-learn-more-button">
                    Learn more <span class="caret"></span>
                  </button>
                <p>
              </div>

              <div class="home-page__install-widget-yourself-installation-guide-container">
                <div class="home-page__install-widget-yourself-wrapper">
                  <p class="home-page__install-widget-yourself-installation-title">Install the Review Widget</p>
                  <p>You can use the code <code>[jgm-review-widget]</code> by adding this code snippet to your product page.</p>
                  <p style="margin: 0;">To show the review widget for a specific product, please use:</p>
                  <p class="home-page__install-widget-yourself-installation-guide-example">
                    <code id= "home-page__install-widget-yourself-review-widget-code" value= "[jgm-review-widget id= /insert product id/]"> [jgm-review-widget id= /insert product id/]</code>
                    <i class="far fa-clone fa-lg home-page__install-widget-yourself-copy-and-paste-icon" data-clipboard-target= "#home-page__install-widget-yourself-review-widget-code"></i>
                  </p>
                  <p class="home-page__install-widget-yourself-installation-guide-help-text">For example: [jgm-review-widget id= 342] to show the review widget of the product with id 342.</p>
                </div>

                <div class="home-page__install-widget-yourself-wrapper">
                  <p class="home-page__install-widget-yourself-installation-title">Install the Star Rating Badge</p>
                  <p>You can use the code <code> [jgm-preview-badge] </code> in the product page.</p>
                  <p style="margin: 0;">To show star rating for a specific product, please use:</p>
                  <p class="home-page__install-widget-yourself-installation-guide-example">
                    <code id= "home-page__install-widget-yourself-preview-badge-code" value= "[jgm-preview-badge id= /insert product id/]"> [jgm-preview-badge id= /insert product id/]</code>
                    <i class="far fa-clone fa-lg home-page__install-widget-yourself-copy-and-paste-icon" data-clipboard-target= "#home-page__install-widget-yourself-preview-badge-code"></i>
                  </p>
                  <p class="home-page__install-widget-yourself-installation-guide-help-text">For example: [jgm-preview-badge id= 342] to show the star rating of the product with id 342.</p>
                </div>

                <div class="home-page__install-widget-yourself-wrapper">
                  <p class="home-page__install-widget-yourself-installation-title">Install the Review Carousal</p>
                  <p style="margin: 0;">Insert this shortcode anywhere on your pages:</p>
                  <p class="home-page__install-widget-yourself-installation-guide-example">
                    <code id= "home-page__install-widget-yourself-carousal-code" value= "[jgm-featured-carousel title= 'Let customers speak for us' all-reviews-page='#']"> [jgm-featured-carousel title= 'Let customers speak for us' all-reviews-page='#'] </code>
                    <i class="far fa-clone fa-lg home-page__install-widget-yourself-copy-and-paste-icon" data-clipboard-target= "#home-page__install-widget-yourself-carousal-code"></i>
                  </p>
                  <p class="home-page__install-widget-yourself-installation-guide-help-text">
                    You can customize the title field and put the link of the all reviews page created above to the all-reviews-page field.
                    Note: You will need to feature at least 1 review (recommended: 3) to show the carousel.
                  </p>
                </div>

              </div>
            </div>

            <div class="home-page__judge-me-ideas">
              <p class="home-page__judge-me-ideas-title">Need ideas? Check out some of the Judge.me features!</p>
              <p class="home-page__judge-me-ideas-description">Increase trust for your brand by showing reviews in different ways with Judge.me widgets. Check out <a class="home-page__judge-me-ideas-marketing-link" href="">Marketing & Social</a> features to increase your traffic and sales.</p>

              <div class="home-page__judge-me-features-container">
                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/import_reviews.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Import reviews</p>
                  <ul class="home-page__judge-me-info">
                    <li>Effortlessly upload and match your review data with our smart import wizard.</li>
                    <li>Click 'Import' and watch your reviews enhance your store's reputation instantly!</li>
                  </ul>
                  <a href="https://app.judge.me/import" target="_blank" rel="noopener noreferrer">Import reviews</a>
                </div>

                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/request_timing.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Set up request timing on autopilot</p>
                  <ul class="home-page__judge-me-info">
                    <li>Collect reviews on autopilot via review request emails.</li>
                    <li>Customize review request timings and and priority for which products to collect reviews first.</li>
                  </ul>
                  <a href="https://app.judge.me/shop/requests/schedule_reminders" target="_blank" rel="noopener noreferrer">Request timing</a>
                </div>

                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/email_templates.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Setup up Email Templates</p>
                  <ul class="home-page__judge-me-info">
                    <li>Build your brand with fully customizable review request emails.</li>
                    <li>Create engaging emails with drag & drop editor and various content blocks.</li>
                  </ul>
                  <a href="https://app.judge.me/email_templates" target="_blank" rel="noopener noreferrer">Email templates</a>
                </div>

                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/customize_review_widget.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Customize Review Widget</p>
                  <ul class="home-page__judge-me-info">
                    <li>Show your reviews in style by setting up themes, colours, text for you Reviews widget.</li>
                    <li>Increase engagement with reviews by showing pictures first, adding thumbs up and social share buttons and much more.</li>
                  </ul>
                  <a href="https://app.judge.me/shop/widgets/review-widget" target="_blank" rel="noopener noreferrer">Customize widget</a>
                </div>

                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/reviews_carousel.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Set up Reviews Carousel</p>
                  <ul class="home-page__judge-me-info">
                    <li>Enhance your homepage with a reviews slider to showcase customer feedback and attract new clients.</li>
                    <li>Automatically display recent 5-star reviews or reviews with pictures, or manually select your favorites to highlight.</li>
                  </ul>
                  <a href="https://app.judge.me/shop/widgets/reviews-carousel" target="_blank" rel="noopener noreferrer">Customize widget</a>
                </div>

                <div class="home-page__judge-me-features">
                  <div class="home-page__judge-me-features-image">
                    <img src="<?php echo JGM_PLUGIN_URL.'assets/images/enable_coupons.png'; ?>">
                  </div>
                  <p class="home-page__judge-me-subtitle">Enable coupons</p>
                  <ul class="home-page__judge-me-info">
                    <li>Collect more reviews by encouraging customer to leave a review with coupons.</li>
                    <li>Customize every aspect of your coupons, from discount values to usage limits and eligibility.</li>
                  </ul>
                  <a href="https://app.judge.me/shop/marketing_and_social/coupons" target="_blank" rel="noopener noreferrer">Enable coupons</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="home-page__tab-for-customer-support" class="tab-pane fade">

          <a href="javascript:void(0);" class="toggle-advanced-debug">Toggle debug functions</a>
          <div class="advanced-debug" style="display: none;">
            <div>
              <p>Clear Synchronize Status of products:</p>
              <button class="clear-sync-btn">Clear full sync status</button>
              <button class="clear-sync-each-product-btn">Clear each product sync</button>
            </div>
            <div>
              <p>Reset Single Product Judge.me Review Data:</p>
              <input type="text" id="jgm-product-id" placeholder="Product ID"/>
              <button class="clean-product-btn">Reset Single Product</button>
            </div>
            <div>
              <p>Register products per page</p>
              <input type="number" id="jgm-per-page" placeholder="200" value="200" />
            </div>
          </div>
        </div>

        <div id="home-page__faq-tab" class="tab-pane fade">
          <div class="home-page__faq-container">
            <div class="home-page__faqs">
              <div class="home-page__faq-post home-page__first-faq-post">
                <i class="fa fa-solid fa-chevron-up home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    I still see the old version of the Interface!/I don't see the 'Advanced' tab on my Interface.
                  </div>
                  <div class="home-page__faq-answer">
                    If you don't see the "Advanced" tab, you need to update the Judge.me plugin to the latest version. The changes should happen automatically, but maybe your configuration in WordPress is blocking these automatic updates.<br><br>
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post home-page__second-faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    My WooCommerce products are not synchronized with Judge.me. How can I synchronize them?
                  </div>
                  <div class="home-page__faq-answer">
                    WooCommerce products should automatically synchronize with Judge.me products, but in rare cases, they may not and you may need to manually synchronize them.<br><br>

                    To synchronize your WooCommerce Products with Judge.me manually, go to Advanced > click the Synchronize Products button, then wait a few minutes and check if the products have synced.<br><br>

                    If you need to synchronize products again, before you click <b>Synchronize Products</b> button and click the <b>Reset status</b> button.<br><br>
                    Read more about <a href= "https://help.judge.me/en/articles/8399133-syncing-woocommerce-products" target= "_blank">How to synchronize your products correctly</a>.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    My Judge.me widgets are not updating. How can I force an update?
                  </div>
                  <div class="home-page__faq-answer">
                    Judge.me can work with several cache plugins, which automatically purge the cache whenever there is new review. Currently we support: <b>WP Super Cache, W3 Total Cache, Autoptimize, Cache Enabler, Breeze Cache, WP Fastest Cache, SG Optimizer</b>. <br> <br>

                    If your plugin is not in the list, please manually purge the cache daily, so the widgets will update.<br><br>

                    Judge.me widgets only support the following emojis ⌚ ⏩ ⏪ ⏫ ⏬ ⏰ ⏳ ⚽ ⛄ ⛅ ⛎ ⛔ ⛪ ⛲ ⛳ ⛵ ⛺ ⛽ ⬛ ⬜ ⭐ ⭕ ✂ ✅ ✊ ✋ ✨ ❌ ❎ ❓ ❔ ❕ ❗ ❤  so if your reviews contain other emojis this may prevent the widget from updating. Please remove them to allow the widgets to update again.<br><br>

                    Some other issues can prevent our widgets from updating correctly, read more about <a href= "https://help.judge.me/en/articles/8399065-fixing-our-server-cannot-access-your-shop-domain-error" target= "_blank">specific solutions to particular plugins</a>.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    I cannot access the Judge.me dashboard (admin.judge.me/admin). How can I access it?
                  </div>
                  <div class="home-page__faq-answer">
                    If you can access the Judge.me tab in your WooCommerce admin panel but are receiving an <b>"Oops Login Issue"</b> error message when clicking on the Get Started button to access Judge.me settings, it probably means that:<br><br>
                    <ul>
                      <li>Your WooCommerce account is the same but you <b>recently changed your domain</b>, so please check if you have recently changed your WordPress domain (‘www’ changes are also considered a change of domain). This change of domain is probably not updated in our servers yet so please contact support@judge.me so we can update your domain. </li>
                      <li>You <b>duplicated your shop</b> from a shop installed Judge.me. Any duplicated shop needs to be assigned to a different account in Judge.me, so please contact our support at <a href= "mailto:support@judge.me">support@judge.me</a> to solve this issue, as we’ll need to create a new account for the new shop by clearing the token.</li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    I do not see Judge.me widgets on my pages. How can I make them appear?
                  </div>
                  <div class="home-page__faq-answer">
                    In WooCommerce, the Judge.me product widgets (Preview Badge and Review Widget on Product and Collection Pages) are already <b>automatically installed</b> in a default position that is valid for most of the themes.<br><br>
                    You can find the <b>Judge.me widgets to get you started</b> under the <b>General tab</b> of Judge.me dashboard in Woocommerce admin panel, please make sure all widgets are enabled.<br><br>
                    If you want to install other widgets, or if the product widgets don’t get installed in the desired position, you can <b>disable the toggles and manually install the widgets</b> using widget shortcodes. The widgets shortcodes are easier to install and position if your shop is using page builders such as Elementor or Divi.<br><br>
                    Additionally, we have the option to <b>change the default position</b> of the product widgets (Star Rating Badge and Review Widget on Product and Collection Pages) if you can provide us with the right <b>visual hook to the position</b> in which you want them to be installed. In this case:<br><br>
                    <ul>
                      <li>Ask your theme developer for the right name of the visual hook</li>
                      <li>Contact us at <a href= "mailto:support@judge.me">support@judge.me</a> so we can guide you through the process. We normally change the default position of these product widgets with the help of the plugin Code Snippets or by adding a hook to your template <b>functions.php</b> file.</li>
                    </ul>

                    <p>See <a href="https://help.judge.me/en/articles/8398919-adding-judge-me-widgets-using-shortcodes" target="_blank">more information</a> about our widget shortcodes</p>
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    How can I add GTIN/EAN identifier to a product for the Google Product Review Feed?
                  </div>
                  <div class="home-page__faq-answer">
                    Your products perhaps need a <b>GTIN/EAN identifier</b> if you want to use the Google Product Review Feed feature. This value can be added to your products by a custom attribute named <b>GTIN or EAN or ISBN</b>. Alternatively, you can use the plugin <b>WooCommerce UPC, EAN, and ISBN</b> to add GTIN values to your products.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    How can I add additional customizations using CSS?
                  </div>
                  <div class="home-page__faq-answer">
                    If you wish to customize our widgets further you may add some CSS to style them. This can be done by going to your <b>WordPress Dashboard > Appearance > Customize > Additional CSS</b>.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    Will Judge.me affect my site's speed?
                  </div>
                  <div class="home-page__faq-answer">
                    Judge.me is a fast plugin. We only load a CSS and Javascript file on your storefront to display the widgets and these files typically load within ~300ms.<br><br>
                    On the WordPress dashboard, we only load our Javascript files on the Judge.me plugin page so it will not affect your WordPress dashboard. For further information see our <a href= "https://help.judge.me/en/articles/8415544-explaining-judge-me-fast-loading-speed" target= "_blank">Why is Judge.me so fast?</a> article.<br><br>
                    We store all the reviews on our own server. In your shop, we only store <b>the first 5 reviews of each product</b> for caching purposes (so that your customers will see the reviews immediately after loading the product page, it actually improves your site speed overall). The subsequent reviews will be <b>retrieved dynamically from our servers</b> in the customer's browser and will not affect your site's performance.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    Product identification values to import reviews
                  </div>
                  <div class="home-page__faq-answer">
                    If you receive errors regarding the products when trying to import reviews, please make sure that your product identification values included in your file are correct. Only one of the 2 values <b>(either product_handle or product_id)</b> is required to successfully import the reviews.<br><br>
                    Read more about <a href= "https://help.judge.me/en/articles/8223133-finding-product-id-and-product-handle" target= "_blank">How to identify product identification values correctly</a>.<br><br>
                    Please also make sure that your products are correctly synced, read more about <a href="https://help.judge.me/en/articles/8399133-syncing-woocommerce-products" target= "_blank">How to synchronize your products correctly</a>.
                  </div>
                </div>
              </div>
              <div class="home-page__faq-post">
                <i class="fa fa-solid fa-chevron-down home-page__faq-icon"></i>
                <div class="home-page__faq-info">
                  <div class="home-page__faq-title">
                    “Our server cannot access your shop domain” error / I can’t install the plugin in the shop
                  </div>
                  <div class="home-page__faq-answer">
                    To be able to install the plugins, your shop needs a <b>working domain (no localhost)</b> that is accessible from the Internet and not protected by passwords. Currently, we don’t support installing the application in a sub-directory, only the root domain is supported.<br><br>
                    Besides, there’s a number of issues that can arise when our plugin doesn’t have full access to your shop through our webhooks, but that can be also summarized in the plugin not being able to install, or the reviews or settings not updating correctly.<br><br>
                    Read more about <a href= "https://help.judge.me/en/articles/8399065-fixing-our-server-cannot-access-your-shop-domain-error" target= "_blank">specific solutions to particular plugins</a>.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id= "home-page__advanced-tab" class="tab-pane fade">
          <div class="home-page__advanced-tab-container">
            <div class="home-page-sychronize-products-container">
              <div class="judgeme-product-sync" data-token="<?php echo $token; ?>" data-domain="<?php echo get_option( 'judgeme_domain' );?>">
                <h3 class="home-page__advanced-tab-titles">Synchronize WooCommerce products with Judge.me<span class="home-page__customer-support-advanced-tab">Advanced</span></h3>
                <p>In order to use Product groups (multiple products share reviews), Shop sync (multiple shops share reviews),
                    or import reviews from other e-commerce platforms,
                    you must fully synchronize your WooCommerce products with Judge.me.
                    Please click the button below to synchronize products.</p>
                <p>Please note that this process may take some time if you have many reviews. We appreciate your patience.</p>
                <button class="sync-product-btn home-page__advanced-tab-sync-product">Synchronize products</button>
                <button class="clear-sync-btn home-page__advanced-tab-reset-product-sync">Reset status</button>
                <div class="product-sync-response"></div>
              </div>
            </div>
            <div class="judgeme-exporter" data-nonce="<?php echo wp_create_nonce( 'jgm_export_reviews' ); ?>">
              <h3 class="home-page__advanced-tab-titles">Export WooCommerce Reviews</h3>
              <p>You can export the WooCommerce's reviews to CSV and import it to Judge.me.</p>
              <p>Please note that this process may take some time if you have many products. We appreciate your patience.</p>
              <?php $jgm_reviews_count = JGM_ReviewExporter::get_total_reviews_count(); ?>
              <?php if ( $jgm_reviews_count > 0 ): ?>
                <p>There are <?php echo $jgm_reviews_count; ?> reviews to be exported.</p>
                <button class="wp-core-ui button-primary export-review-btn">Export to CSV</button>
              <?php else: ?>
                <p class="home-page__advanced-tab-no-reviews">There are no reviews to be exported.</p>
              <?php endif; ?>
              <div class="response"></div>
              <a id="jgm-csv-file" style="display:none;"
                href="<?php echo admin_url( 'admin-post.php?action=jgm_download_file' ); ?>">Download the CSV file.</a>
              <p class="result">You can <a href="<?php echo $import_url; ?>" target="_blank">go here</a> to import the CSV to
                  Judge.me.</p>
            </div>
            <div>
              <h3 class="home-page__advanced-tab-titles shop-token">Shop Token</h3>
              <p> Your shop domain: <?php echo get_option('judgeme_domain'); ?> </p>
              <p> Your internal token: <?php echo get_option('judgeme_shop_token');?> </p>
              <button class="clear-shop-token-btn home-page__advanced-tab-clear-shop-token">Clear token</button>
            </div>
            <?php
              $skip_clearing_cache_by_default = get_option('judgeme_option_skip_clearing_cache_by_default');
            ?>
              <div class="home-page__advanced-tab-clear-cache-option-container">
              <h3 class="home-page__advanced-tab-titles cache-clearing">Cache clearing by Judge.me</h3>
                <div class="advanced-tab__cache-clearing-toggle">
                  <label class="switch" for="cache-clearing-toggle">
                    <input <?php if(!$skip_clearing_cache_by_default) echo "checked" ?> type="checkbox" id="cache-clearing-toggle" data-type="judgeme_option_skip_clearing_cache_by_default" class="advanced-tab__cache-clearing-widget-toggle-switch widget-toggle" />
                    <div class="clear-cache-checkbox"></div>
                  </label>
                  <span class="home-page__advanced-tab-cache-clearing-disable-enable-text-toggle">Clear cache by default</span>
                </div>
              </div>
          </div>
        </div>

        <div class="home-page__knowledge-base-container">
          <div class="home-page__knowledge-base-wrapper">
            <img class="home-page__jdgm-chat-logo" src= "<?php echo JGM_PLUGIN_URL.'assets/images/icon-helpdesk.jpg'; ?>">
            <span class="home-page__knowledge-base-text-container">
              <h1 class="home-page__knowledge-base-header">Need help?</h1>
              <p class="home-page__knowledge-base-text">Dive into our Knowledge Base for quick answers, detailed guides, and expert tips to help you make the most of our features.</p>
              <button class="home-page__knowledge-base-button">
                <a href="https://support.judge.me/support/home" target= "_blank">Get support</a>
              </button>
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>
<?php else: ?>
  <p>Our server cannot access your shop domain. Please note you need a working domain (no localhost) that is accessible from the internet
      and is not password-protected.</p>
  <p>Note: Currently we do not support wordpress setup in a sub-directory (eg: example.com/this-is-a-sub-directory/), only root directory is supported, e.g.: yourdomain.com or shop.yourdomain.com</p>
  <p> Check your setting, hosting or cloudflare to whitelist the /wp-json/* urls, it is needed for our system to contact your shop.</p>

  <?php
    if ( get_option( 'judgeme_is_installing' ) ) {
      delete_option( 'judgeme_is_installing' );
    }
  ?>

  <!--
  <?php print_r( get_option( 'judgeme_register_error' ) ); ?>
  -->
<?php endif; ?>
