<?php
/**
 * Paywall settings page (Appearance ▸ Bay Nature ▸ Paywall).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const BN_PAYWALL_OPTION_KEY = 'bn_paywall_options';

/**
 * Register settings and admin page.
 */
add_action( 'admin_init', function () {
    register_setting( 'bn_paywall', BN_PAYWALL_OPTION_KEY, [
        'type' => 'array',
        'default' => [
            'mode' => 'hybrid', // template|automatic|hybrid
            'latest_n' => 3,
            'preview_paragraphs' => 3,
            'free_views' => 3,
            'manual_issues' => [],
            'exceptions' => [],
        ],
    ] );

    add_settings_section( 'bn_pw_main', __( 'Paywall Rules', 'bn-newspack-child' ), '__return_false', 'bn_paywall' );

    add_settings_field( 'mode', __( 'Mode', 'bn-newspack-child' ), function () {
        $opts = get_option( BN_PAYWALL_OPTION_KEY );
        $mode = isset( $opts['mode'] ) ? $opts['mode'] : 'hybrid';
        ?>
        <select name="<?php echo esc_attr( BN_PAYWALL_OPTION_KEY ); ?>[mode]">
            <option value="template" <?php selected( $mode, 'template' ); ?>>Template only</option>
            <option value="automatic" <?php selected( $mode, 'automatic' ); ?>>Latest N printed issues</option>
            <option value="hybrid" <?php selected( $mode, 'hybrid' ); ?>>Hybrid (either condition)</option>
        </select>
        <?php
    }, 'bn_paywall', 'bn_pw_main' );

    add_settings_field( 'latest_n', __( 'Latest N printed issues', 'bn-newspack-child' ), function () {
        $opts = get_option( BN_PAYWALL_OPTION_KEY );
        $n = isset( $opts['latest_n'] ) ? intval( $opts['latest_n'] ) : 3;
        echo '<input type="number" min="0" name="' . esc_attr( BN_PAYWALL_OPTION_KEY ) . '[latest_n]" value="' . esc_attr( $n ) . '" />';
    }, 'bn_paywall', 'bn_pw_main' );

    add_settings_field( 'preview_paragraphs', __( 'Preview paragraphs', 'bn-newspack-child' ), function () {
        $opts = get_option( BN_PAYWALL_OPTION_KEY );
        $v = isset( $opts['preview_paragraphs'] ) ? intval( $opts['preview_paragraphs'] ) : 3;
        echo '<input type="number" min="0" name="' . esc_attr( BN_PAYWALL_OPTION_KEY ) . '[preview_paragraphs]" value="' . esc_attr( $v ) . '" />';
    }, 'bn_paywall', 'bn_pw_main' );

    add_settings_field( 'free_views', __( 'Anonymous free views', 'bn-newspack-child' ), function () {
        $opts = get_option( BN_PAYWALL_OPTION_KEY );
        $v = isset( $opts['free_views'] ) ? intval( $opts['free_views'] ) : 3;
        echo '<input type="number" min="0" name="' . esc_attr( BN_PAYWALL_OPTION_KEY ) . '[free_views]" value="' . esc_attr( $v ) . '" />';
    }, 'bn_paywall', 'bn_pw_main' );
} );

add_action( 'admin_menu', function () {
    add_theme_page(
        __( 'Bay Nature Settings', 'bn-newspack-child' ),
        __( 'Bay Nature', 'bn-newspack-child' ),
        'manage_options',
        'bn-settings',
        function () {
            $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'paywall';
            ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Bay Nature', 'bn-newspack-child' ); ?></h1>
                <h2 class="nav-tab-wrapper">
                    <a href="?page=bn-settings&tab=paywall" class="nav-tab <?php echo 'paywall' === $active_tab ? 'nav-tab-active' : ''; ?>">
                        <?php esc_html_e( 'Paywall', 'bn-newspack-child' ); ?>
                    </a>
                    <a href="?page=bn-settings&tab=navigation" class="nav-tab <?php echo 'navigation' === $active_tab ? 'nav-tab-active' : ''; ?>">
                        <?php esc_html_e( 'Navigation', 'bn-newspack-child' ); ?>
                    </a>
                </h2>
                <?php if ( 'paywall' === $active_tab ) : ?>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields( 'bn_paywall' );
                        do_settings_sections( 'bn_paywall' );
                        submit_button();
                        ?>
                    </form>
                    <?php
                    // Debug info: show currently detected latest issues
                    if ( function_exists( 'bn_latest_printed_issues' ) ) {
                        $opts = bn_paywall_options();
                        $n = intval( $opts['latest_n'] );
                        // Force fresh calculation (bypass cache for debug display)
                        delete_transient( 'bn_latest_issues_' . $n );
                        delete_transient( 'bn_latest_issues_100' ); // For full list
                        $latest = bn_latest_printed_issues( $n );
                        $all_sorted = bn_latest_printed_issues( 100 ); // Get more to show full sorted list
                        ?>
                        <div class="notice notice-info" style="margin-top: 20px; padding: 15px;">
                            <h3 style="margin-top: 0;">Debug Info: Currently Paywalled Issues</h3>
                            <p><strong>Latest <?php echo esc_html( $n ); ?> issue(s) that WILL be paywalled:</strong></p>
                            <?php if ( ! empty( $latest ) ) : ?>
                                <ul style="margin-left: 20px; color: #d63638; font-weight: bold;">
                                    <?php foreach ( $latest as $issue_key ) : ?>
                                        <li><code><?php echo esc_html( $issue_key ); ?></code> (PAYWALLED)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p><em>No issues detected. Check that articles have issue_key values set.</em></p>
                            <?php endif; ?>
                            
                            <?php if ( count( $all_sorted ) > $n ) : ?>
                                <p style="margin-top: 15px;"><strong>Other issues (NOT paywalled, sorted newest to oldest):</strong></p>
                                <ul style="margin-left: 20px; color: #00a32a;">
                                    <?php foreach ( array_slice( $all_sorted, $n, 20 ) as $issue_key ) : ?>
                                        <li><code><?php echo esc_html( $issue_key ); ?></code></li>
                                    <?php endforeach; ?>
                                    <?php if ( count( $all_sorted ) > $n + 20 ) : ?>
                                        <li><em>...and <?php echo count( $all_sorted ) - $n - 20; ?> more</em></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <p style="color: #666; font-size: 12px; margin-bottom: 0; margin-top: 15px;">
                                Issue keys are sorted by volume then number (e.g., v25n4 > v25n3 > v24n4 > v01n1).
                            </p>
                        </div>
                        <?php
                    }
                    ?>
                <?php elseif ( 'navigation' === $active_tab ) : ?>
                    <form method="post" action="options.php">
                        <?php
                        settings_fields( 'bn_navigation' );
                        do_settings_sections( 'bn_navigation' );
                        submit_button();
                        ?>
                    </form>
                <?php endif; ?>
            </div>
            <?php
        }
    );
} );

/**
 * Clear paywall transient cache when settings are updated.
 * This ensures changes to "Latest N" take effect immediately.
 */
add_action( 'update_option_' . BN_PAYWALL_OPTION_KEY, function () {
    global $wpdb;
    $keys = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_bn_latest_issues_%'" );
    if ( $keys ) {
        foreach ( $keys as $key ) {
            $name = str_replace( '_transient_', '', $key );
            delete_transient( $name );
        }
    }
} );

/** Helper accessor. */
function bn_paywall_options() {
    $opts = get_option( BN_PAYWALL_OPTION_KEY );
    if ( ! is_array( $opts ) ) {
        $opts = array();
    }
    return wp_parse_args( $opts, array(
        'mode' => 'hybrid',
        'latest_n' => 3,
        'preview_paragraphs' => 3,
        'free_views' => 3,
        'manual_issues' => array(),
        'exceptions' => array(),
    ) );
}



