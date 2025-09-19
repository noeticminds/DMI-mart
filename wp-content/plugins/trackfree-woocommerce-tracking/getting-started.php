<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap bootstrap-wrapper">
    <div class="trackfree_promotions_container">
        <div class="trackfree_promotions_container__box">
            <div class="trackfree-OB-wrapper">
                <div class="trackfree-OB-header">
                    <img alt="TrackFree" src="<?php echo plugins_url('/trackfree-woocommerce-tracking/assets/images/trackfree-logo.svg'); ?>">
                    <p class="trackfree-OB-mh"><?php _e('Ship with confidence and Sell more!', 'trackfree-woocommerce-tracking');?></p>
                    <span class="trackfree-OB-sh"><?php _e('Delight your customers with an exceptional post-purchase experience and drive more sales.', 'trackfree-woocommerce-tracking');?></span>
                </div>

                <div class="trackfree-OB-main">
                    <section class="trackfree-OB-section">
                        <img alt="" src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-value-props-1.svg">
                        <p class="trackfree-OB-ct"><?php _e('Monitor all shipments in one go', 'trackfree-woocommerce-tracking');?></p>
                    </section>
                    <section class="trackfree-OB-section">
                        <img alt="" src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-value-props-2.svg">
                        <p class="trackfree-OB-ct"><?php _e('Keep customers informed proactively', 'trackfree-woocommerce-tracking');?></p>

                    </section>
                    <section class="trackfree-OB-section">
                        <img alt="" src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-value-props-3.svg">
                        <p class="trackfree-OB-ct"><?php _e('Provide a branded experience', 'trackfree-woocommerce-tracking');?></p>
                    </section>
                </div>


                <hr class="trackfree-OB-hr-line">

                <?php if (!$appVerifyContent) { ?>
                    <form method="post" action="<?php echo admin_url('admin.php?page=trackfree-getting-started');?>" style="margin:0px">
                        <?php settings_fields('trackfree_options_group'); ?>
                        <input type="hidden" name="trackfree" value="true" />
                        <input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce" />
                        <div class="trackfree-OB-frm-field">
                            <div class="trackfree-OB-frm-lbl"><?php _e('Enter your email address', 'trackfree-woocommerce-tracking');?></div>
                            <div>
                                <input type="text" class="trackfree-OB-form-input" id="trackfree_account_email" name="trackfree_account_email" value="<?php echo get_option('admin_email'); ?>" required="" />
                            </div>
                        </div>
                        <div class="trackfree-GS-frm-field">
                            <button type="submit" class="trackfree-GS-btn-1"><?php _e('Get Started', 'trackfree-woocommerce-tracking');?></button>
                        </div>
                    </form>
                <?php } else { ?>
                    <div class="trackfree-OB-vc">
                        <?php echo $appVerifyContent; ?>
                    </div>
                <?php } ?>
            </div>

            <div style="text-align: center; margin-top: 100px; margin-bottom: 16px">
                <a href="https://help.trackfree.io/en/support/tickets/new" target="_blank" style="text-decoration: none;" >support@trackfree.io</a>
            </div>
        </div>
    </div>
</div>
