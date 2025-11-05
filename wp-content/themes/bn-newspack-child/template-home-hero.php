<?php
/**
 * Template Name: Homepage with Hero Issue
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$page_id = get_the_ID();
$issue_id = (int) get_field( 'hero_issue_post', $page_id );

if ( $issue_id ) {
    $data = array(
        'layout'          => get_field( 'hero_content_layout', $page_id ) ?: 'left',
        'overlay_color'   => get_field( 'hero_overlay_color', $page_id ) ?: '#000000',
        'overlay_opacity' => (int) get_field( 'hero_overlay_opacity', $page_id ) ?: 55,
        'show_excerpt'    => (bool) get_field( 'hero_show_excerpt', $page_id ),
        'show_meta'       => (bool) get_field( 'hero_show_author_date', $page_id ),
        'custom_excerpt'  => get_field( 'hero_custom_excerpt', $page_id ),
        'issue_id'        => $issue_id,
    );

    get_template_part( 'template-parts/hero/issue-hero', null, $data );
}

while ( have_posts() ) {
    the_post();
    the_content();
}

get_footer();


