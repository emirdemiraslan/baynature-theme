<?php
/**
 * MEC Events List block server-side rendering.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MEC' ) ) {
    echo '<div class="bn-mec-events-list bn-mec-no-events"><p>' . esc_html__( 'Modern Events Calendar plugin is required for this block.', 'bn-newspack-child' ) . '</p></div>';
    return;
}

// Ensure list skin is loaded.
if ( ! class_exists( 'MEC_skin_list' ) ) {
    MEC::import( 'app.skins.list' );
}

// Get block attributes with defaults.
$heading         = isset( $attributes['heading'] ) ? trim( wp_strip_all_tags( $attributes['heading'] ) ) : '';
$number_of_events = isset( $attributes['numberOfEvents'] ) ? max( 1, absint( $attributes['numberOfEvents'] ) ) : 5;
$show_date        = isset( $attributes['showDate'] ) ? (bool) $attributes['showDate'] : true;
$show_excerpt     = isset( $attributes['showExcerpt'] ) ? (bool) $attributes['showExcerpt'] : false;
$category_filter  = isset( $attributes['categoryFilter'] ) ? sanitize_text_field( $attributes['categoryFilter'] ) : '';
$show_past_events = isset( $attributes['showPastEvents'] ) ? (bool) $attributes['showPastEvents'] : false;
$order_by         = isset( $attributes['orderBy'] ) ? sanitize_key( $attributes['orderBy'] ) : 'date_asc';

$order_direction = ( 'date_desc' === $order_by ) ? 'DESC' : 'ASC';

$atts = array(
    'category'                 => $category_filter,
    'show_only_past_events'    => $show_past_events ? 1 : 0,
    'show_past_events'         => $show_past_events ? 1 : 0,
    'start_date_type'          => $show_past_events ? 'past' : 'today',
    'atts'                     => array(),
    'sk-options'               => array(
        'list' => array(
            'limit'        => $number_of_events,
            'order_method' => $order_direction,
        ),
    ),
);

$list = new MEC_skin_list();
$list->initialize( $atts );
$list->atts['return_items'] = true;
$list->fetch();

$events = $list->events;

$items = array();

if ( is_array( $events ) ) {
    foreach ( $events as $day_events ) {
        foreach ( $day_events as $event ) {
            $items[] = $event;
            if ( count( $items ) >= $number_of_events ) {
                break 2;
            }
        }
    }
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'bn-mec-events-list' ) );

if ( empty( $items ) ) {
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        <?php if ( $heading ) : ?>
            <h2 class="article-section-title">
                <span><?php echo esc_html( $heading ); ?></span>
            </h2>
        <?php endif; ?>
        <div class="bn-mec-no-events"><p><?php esc_html_e( 'No events found.', 'bn-newspack-child' ); ?></p></div>
    </div>
    <?php
    return;
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $heading ) : ?>
        <h2 class="article-section-title">
            <span><?php echo esc_html( $heading ); ?></span>
        </h2>
    <?php endif; ?>
    <ul class="bn-mec-events-items">
        <?php foreach ( $items as $event ) :
            $event_id = $event->data->ID ?? ( $event->ID ?? 0 );
            $title    = apply_filters( 'mec_occurrence_event_title', $event->data->title ?? ( $event_id ? get_the_title( $event_id ) : '' ), $event );
            $link     = $event->data->permalink ?? ( $event_id ? get_permalink( $event_id ) : '#' );
            $start    = $event->date['start']['date'] ?? '';
            $formatted_date = ( $show_date && $start ) ? date_i18n( 'M j, Y', strtotime( $start ) ) : '';

            $excerpt = '';
            if ( $show_excerpt ) {
                if ( ! empty( $event->data->post->post_excerpt ) ) {
                    $excerpt = wp_strip_all_tags( $event->data->post->post_excerpt );
                } elseif ( ! empty( $event->data->post->post_content ) ) {
                    $excerpt = wp_trim_words( wp_strip_all_tags( $event->data->post->post_content ), 20 );
                }
            }
            ?>
            <li class="bn-mec-event-item">
        <div class="bn-mec-event-content">
            <div class="bn-mec-event-header">
                <h3 class="bn-mec-event-title">
                    <a href="<?php echo esc_url( $link ); ?>">
                        <?php echo esc_html( $title ); ?>
                    </a>
                </h3>

                <?php if ( $formatted_date ) : ?>
                    <div class="bn-mec-event-date">
                        <time datetime="<?php echo esc_attr( $start ); ?>">
                            <?php echo esc_html( $formatted_date ); ?>
                        </time>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $excerpt ) : ?>
                <div class="bn-mec-event-excerpt">
                    <?php echo esc_html( $excerpt ); ?>
                </div>
            <?php endif; ?>
        </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

