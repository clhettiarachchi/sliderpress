<?php

/**
 * SliderPress -- Slideshow list table view.
 *
 * Rendered by Admin::render_list_page(). No logic or DB calls here.
 * Data is prepared by the calling method before require.
 *
 * @package SliderPress
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! current_user_can('edit_posts')) {
    wp_die(esc_html__('You do not have permission to access this page.', 'sliderpress'));
}

$shows = get_posts([
    'post_type'      => \SliderPress\CPT::SHOW,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);
?>
<div class="wrap">

    <h1 class="wp-heading-inline"><?php esc_html_e('Slideshows', 'sliderpress'); ?></h1>

    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=' . \SliderPress\CPT::SHOW)); ?>" class="page-title-action">
        <?php esc_html_e('Add New', 'sliderpress'); ?>
    </a>

    <hr class="wp-header-end">

    <?php if (empty($shows)) : ?>

        <p><?php esc_html_e('No slideshows found. Create your first one!', 'sliderpress'); ?></p>

    <?php else : ?>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th scope="col" class="column-title column-primary"><?php esc_html_e('Name', 'sliderpress'); ?></th>
                    <th scope="col" class="column-sp-slides"><?php esc_html_e('Slides', 'sliderpress'); ?></th>
                    <th scope="col" class="column-sp-shortcode"><?php esc_html_e('Shortcode', 'sliderpress'); ?></th>
                    <th scope="col" class="column-date"><?php esc_html_e('Last Modified', 'sliderpress'); ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($shows as $show) : ?>
                    <?php
                    $edit_url      = get_edit_post_link($show->ID);
                    $delete_url    = get_delete_post_link($show->ID, '', true);
                    $slide_count   = count(get_posts([
                        'post_type'      => \SliderPress\CPT::SLIDE,
                        'post_parent'    => $show->ID,
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'no_found_rows'  => true,
                    ]));
                    $shortcode     = '[sliderpress id="' . $show->ID . '"]';
                    $modified_date = get_the_modified_date(get_option('date_format'), $show);
                    ?>
                    <tr>
                        <td class="column-title column-primary" data-colname="<?php esc_attr_e('Name', 'sliderpress'); ?>">
                            <strong>
                                <a href="<?php echo esc_url($edit_url); ?>" class="row-title">
                                    <?php echo esc_html($show->post_title ?: __('(no title)', 'sliderpress')); ?>
                                </a>
                            </strong>

                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo esc_url($edit_url); ?>">
                                        <?php esc_html_e('Edit', 'sliderpress'); ?>
                                    </a>
                                </span>
                                |
                                <span class="trash">
                                    <a href="<?php echo esc_url($delete_url); ?>"
                                        onclick="return confirm( '<?php echo esc_js(__('Delete this slideshow and all its slides?', 'sliderpress')); ?>' );"
                                        class="submitdelete">
                                        <?php esc_html_e('Delete', 'sliderpress'); ?>
                                    </a>
                                </span>
                            </div>

                            <button type="button" class="toggle-row">
                                <span class="screen-reader-text"><?php esc_html_e('Show more details', 'sliderpress'); ?></span>
                            </button>
                        </td>

                        <td class="column-sp-slides" data-colname="<?php esc_attr_e('Slides', 'sliderpress'); ?>">
                            <?php echo (int) $slide_count; ?>
                        </td>

                        <td class="column-sp-shortcode" data-colname="<?php esc_attr_e('Shortcode', 'sliderpress'); ?>">
                            <code><?php echo esc_html($shortcode); ?></code>
                            <button type="button"
                                class="button button-small sp-copy-shortcode"
                                data-shortcode="<?php echo esc_attr($shortcode); ?>"
                                aria-label="<?php esc_attr_e('Copy shortcode', 'sliderpress'); ?>">
                                <?php esc_html_e('Copy', 'sliderpress'); ?>
                            </button>
                        </td>

                        <td class="column-date" data-colname="<?php esc_attr_e('Last Modified', 'sliderpress'); ?>">
                            <?php echo esc_html($modified_date); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <th scope="col" class="column-title column-primary"><?php esc_html_e('Name', 'sliderpress'); ?></th>
                    <th scope="col" class="column-sp-slides"><?php esc_html_e('Slides', 'sliderpress'); ?></th>
                    <th scope="col" class="column-sp-shortcode"><?php esc_html_e('Shortcode', 'sliderpress'); ?></th>
                    <th scope="col" class="column-date"><?php esc_html_e('Last Modified', 'sliderpress'); ?></th>
                </tr>
            </tfoot>
        </table>

    <?php endif; ?>

</div><!-- .wrap -->