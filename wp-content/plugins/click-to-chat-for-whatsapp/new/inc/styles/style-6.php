<?php
/**
 * Style - 6
 * 
 * link
 * 
 * $s6_styles is added to change the text color and text decoration 
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$s6_options = get_option( 'ht_ctc_s6' );
$s6_options = apply_filters( 'ht_ctc_fh_s6_options', $s6_options );

$s6_txt_color = isset( $s6_options['s6_txt_color']) ? esc_attr( $s6_options['s6_txt_color'] ) : '';
$s6_txt_color_on_hover = isset( $s6_options['s6_txt_color_on_hover'] ) ? esc_attr( $s6_options['s6_txt_color_on_hover'] ) : '';
$s6_txt_decoration = isset( $s6_options['s6_txt_decoration'] ) ? esc_attr( $s6_options['s6_txt_decoration'] ) : 'none';
$s6_txt_decoration_on_hover = isset( $s6_options['s6_txt_decoration_on_hover'] ) ? esc_attr( $s6_options['s6_txt_decoration_on_hover'] ) : 'underline';

$s6_styles = "";

if ( '' != $s6_txt_color ) {
    $s6_styles .= "color: '.$s6_txt_color.'; ";
}
if ( '' != $s6_txt_decoration ) {
    $s6_styles .= 'text-decoration: '.$s6_txt_decoration.'; ';
}

?>

<a class="ctc-analytics ctc_s_6 ctc_cta" style="<?= $s6_styles ?>"
    onmouseover = "this.style.color = '<?= $s6_txt_color_on_hover ?>', this.style.textDecoration = '<?= $s6_txt_decoration_on_hover ?>' "
    onmouseout  = "this.style.color = '<?= $s6_txt_color ?>', this.style.textDecoration = '<?= $s6_txt_decoration ?>' "
    >
    <?= $call_to_action ?>
</a>