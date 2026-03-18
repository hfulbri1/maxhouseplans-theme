<?php
/**
 * Register ACF fields programmatically
 * Adds: price, price_cad, faqs, feature_bullets, gallery
 *
 * IMPORTANT: Do NOT rename existing field slugs. This file only ADDs new fields.
 *
 * @package MaxHousePlans
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return; // ACF Pro not active — bail silently
}

add_action( 'acf/init', 'mhp_register_acf_fields' );
function mhp_register_acf_fields() {

    acf_add_local_field_group( array(
        'key'                   => 'group_mhp_plan_extras',
        'title'                 => 'Plan Extras (Price, FAQ, Gallery)',
        'fields'                => array(

            // ─── Price (USD) ─────────────────────────────────────────────
            array(
                'key'               => 'field_mhp_price',
                'label'             => 'Price (USD)',
                'name'              => 'price',
                'type'              => 'number',
                'instructions'      => 'PDF set price in USD (e.g. 1500). Do not include $ sign.',
                'required'          => 1,
                'min'               => 0,
                'step'              => 1,
                'prepend'           => '$',
                'append'            => 'USD',
                'default_value'     => '',
            ),

            // ─── Price (CAD) ─────────────────────────────────────────────
            array(
                'key'               => 'field_mhp_price_cad',
                'label'             => 'Price (CAD)',
                'name'              => 'price_cad',
                'type'              => 'number',
                'instructions'      => 'Optional: Canadian dollar price.',
                'required'          => 0,
                'min'               => 0,
                'step'              => 1,
                'prepend'           => 'CA$',
                'append'            => 'CAD',
                'default_value'     => '',
            ),

            // ─── FAQs Repeater ────────────────────────────────────────────
            array(
                'key'               => 'field_mhp_faqs',
                'label'             => 'FAQs',
                'name'              => 'faqs',
                'type'              => 'repeater',
                'instructions'      => 'Add frequently asked questions for this plan. Used for FAQ schema (Google rich results).',
                'required'          => 0,
                'min'               => 0,
                'max'               => 0,
                'layout'            => 'block',
                'button_label'      => 'Add FAQ',
                'sub_fields'        => array(
                    array(
                        'key'           => 'field_mhp_faq_question',
                        'label'         => 'Question',
                        'name'          => 'question',
                        'type'          => 'text',
                        'required'      => 1,
                        'placeholder'   => 'e.g. Can I modify this plan?',
                    ),
                    array(
                        'key'           => 'field_mhp_faq_answer',
                        'label'         => 'Answer',
                        'name'          => 'answer',
                        'type'          => 'textarea',
                        'required'      => 1,
                        'rows'          => 4,
                        'placeholder'   => 'Detailed answer to the question.',
                    ),
                ),
            ),

            // ─── Feature Bullets Repeater ─────────────────────────────────
            array(
                'key'               => 'field_mhp_feature_bullets',
                'label'             => 'Feature Bullets',
                'name'              => 'feature_bullets',
                'type'              => 'repeater',
                'instructions'      => 'Short feature highlights shown as a bullet list on the plan page.',
                'required'          => 0,
                'min'               => 0,
                'max'               => 0,
                'layout'            => 'table',
                'button_label'      => 'Add Feature',
                'sub_fields'        => array(
                    array(
                        'key'           => 'field_mhp_bullet_text',
                        'label'         => 'Feature',
                        'name'          => 'bullet_text',
                        'type'          => 'text',
                        'required'      => 1,
                        'placeholder'   => 'e.g. Open-concept kitchen and living room',
                    ),
                ),
            ),

            // ─── Gallery ─────────────────────────────────────────────────
            array(
                'key'               => 'field_mhp_gallery',
                'label'             => 'Plan Gallery',
                'name'              => 'gallery',
                'type'              => 'gallery',
                'instructions'      => 'Upload plan photos, renderings, and floor plan images. These replace the native WP gallery shortcode.',
                'required'          => 0,
                'return_format'     => 'array',
                'preview_size'      => 'medium',
                'insert'            => 'append',
                'library'           => 'all',
                'min'               => 0,
                'max'               => 0,
                'min_width'         => '',
                'min_height'        => '',
                'max_width'         => '',
                'max_height'        => '',
                'mime_types'        => 'jpg,jpeg,png,webp',
            ),

        ),

        // Show on 'plans' custom post type only
        'location'              => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'plans',
                ),
            ),
        ),

        'menu_order'            => 10,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => '',
        'active'                => true,
        'description'           => 'New fields: price, price_cad, faqs, feature_bullets, gallery',
    ) );
}
