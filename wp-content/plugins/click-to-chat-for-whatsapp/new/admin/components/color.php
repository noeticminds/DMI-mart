<?php
/**
 * Color input.
 * This code snippet handles the integration of a color picker in WordPress settings. 
 * It allows users to select colors that will dynamically update various sections 
 * of the greetings dialog (header, main content, and message box).
 * 
 * @var string $title         The title for the color input field.
 * @var string $default_color The default color for the color picker.
 * @var string $description   A brief description displayed under the color input.
 * @var string $parent_class  The parent CSS class for styling.
 * @var string $data_update_type The type of CSS property to update (e.g., background-color).
 * @var string $data_update_selector The CSS selector of the element to update.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$title = (isset($input['title'])) ? $input['title'] : '';
$default_color = (isset($input['default_color'])) ? $input['default_color'] : '';
$description = (isset($input['description'])) ? $input['description'] : '';
$parent_class = (isset($input['parent_class'])) ? $input['parent_class'] : '';
$data_update_type = (isset($input['data_update_type'])) ? $input['data_update_type'] : '';
$data_update_selector = (isset($input['data_update_selector'])) ? $input['data_update_selector'] : '';
$data_update_2_type = (isset($input['data_update_2_type'])) ? $input['data_update_2_type'] : '';
$data_update_2_selector = (isset($input['data_update_2_selector'])) ? $input['data_update_2_selector'] : '';

$add_data_update_type = '';
$add_data_update_selector = '';
$add_data_update_2_type = '';
$add_data_update_2_selector = '';

if ('' !== $data_update_type) {    
    $add_data_update_type = "data-update-type='$data_update_type' ";
}

if ('' !== $data_update_selector) {
    $add_data_update_selector = "data-update-selector='$data_update_selector' ";
}

if ('' !== $data_update_2_type) {
    $add_data_update_2_type = "data-update-2-type='$data_update_2_type' ";
}

if ('' !== $data_update_2_selector) {
    $add_data_update_2_selector = "data-update-2-selector='$data_update_2_selector' ";
}

?>
<div class="row ctc_component_color <?= $parent_class ?>">
    <?php
    if ('' !== $title) {
        ?>
        <div class="col s6">
            <p><?= $title ?></p>
        </div>
        <?php
    }
    ?>
    <div class="input-field col s6">
        <input class="ht-ctc-color" name="<?= $dbrow ?>[<?= $db_key ?>]" data-default-color="<?= $default_color ?>" <?= $add_data_update_type ?> <?= $add_data_update_selector ?> <?= $add_data_update_2_type ?> <?= $add_data_update_2_selector ?> id="<?= $db_key ?>" value="<?= $db_value ?>" type="text">
        <?php
        if ('' !== $description) {
            ?>
            <p class="description"><?= $description ?></p>
            <?php
        }
        ?>
    </div>
</div>