<!-- wp:group {"align":"full","className":"bn-header-wrapper","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bn-header-wrapper">
    <!-- wp:html -->
    <?php
    $utility_menu_assigned = has_nav_menu( 'header-utility' );
    $utility_urls         = function_exists( 'bn_get_utility_urls' ) ? bn_get_utility_urls() : array(
        'join_url'   => '/join',
        'donate_url' => '/donate',
    );

    $join_url   = isset( $utility_urls['join_url'] ) ? $utility_urls['join_url'] : '/join';
    $donate_url = isset( $utility_urls['donate_url'] ) ? $utility_urls['donate_url'] : '/donate';
    ?>
    <?php
    // Helper function to render logo for sticky header (SVG)
    function bn_render_header_logo() {
        $logo_path = get_stylesheet_directory() . '/assets/imgs/bn-white-logo.svg';
        $logo_url = get_stylesheet_directory_uri() . '/assets/imgs/bn-white-logo.svg';
        ?>
        <div class="bn-header-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <?php
                if ( file_exists( $logo_path ) ) {
                    echo file_get_contents( $logo_path );
                } else {
                    // Fallback if file doesn't exist
                    echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
                }
                ?>
            </a>
        </div>
        <?php
    }

    // Helper function to render logo for static header (site logo from customizer)
    function bn_render_static_header_logo() {
        ?>
        <div class="bn-header-logo">
            <?php
            $custom_logo = get_custom_logo();
            if ( $custom_logo ) {
                echo $custom_logo;
            } else {
                // Fallback to site title styled as text logo
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="bn-header-title">
                    <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <?php
    }

    // Helper function to render menu and actions
    function bn_render_header_menu_actions( $utility_menu_assigned, $utility_urls, $join_url, $donate_url, $header_type = 'sticky' ) {
        ?>
        <div class="bn-header-right">
            <div class="bn-header-nav-wrap">
                <?php if ( 'static' === $header_type ) : ?>
                    <?php // Static header: render buttons instead of menu ?>
                    <div class="bn-header-buttons">
                        <a href="<?php echo esc_url( $join_url ); ?>" class="bn-header-join-button">
                            <?php esc_html_e( 'Join', 'bn-newspack-child' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $donate_url ); ?>" class="bn-header-donate-button">
                            <?php esc_html_e( 'Donate', 'bn-newspack-child' ); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <?php // Sticky header: render utility menu ?>
                    <?php if ( $utility_menu_assigned ) : ?>
                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'header-utility',
                            'container'      => 'nav',
                            'container_class'=> 'bn-header-nav',
                            'menu_class'     => 'bn-header-menu',
                            'depth'          => 1,
                            'fallback_cb'    => false,
                            'aria_label'     => __( 'Header utility menu', 'bn-newspack-child' ),
                        ) );
                        ?>
                    <?php else : ?>
                        <nav class="bn-header-nav" aria-label="<?php esc_attr_e( 'Header utility menu', 'bn-newspack-child' ); ?>">
                            <ul class="bn-header-menu">
                                <li class="bn-header-menu-item">
                                    <a class="bn-header-menu-link" href="<?php echo esc_url( $join_url ); ?>">
                                        <?php esc_html_e( 'Join', 'bn-newspack-child' ); ?>
                                    </a>
                                </li>
                                <li class="bn-header-menu-item">
                                    <a class="bn-header-menu-link" href="<?php echo esc_url( $donate_url ); ?>">
                                        <?php esc_html_e( 'Donate', 'bn-newspack-child' ); ?>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="bn-header-actions">
                <button type="button" class="bn-header-icon bn-header-search" aria-label="<?php esc_attr_e( 'Open search', 'bn-newspack-child' ); ?>">
                    <span class="bn-header-icon-inner" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" role="img">
                            <path d="M15.5 14h-.79l-.28-.27a6 6 0 1 0-.71.71l.27.28v.79l5 5a1 1 0 0 0 1.41-1.41zm-5.5 0a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" fill="currentColor" />
                        </svg>
                    </span>
                </button>
                <button class="bn-hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'bn-newspack-child' ); ?>" aria-expanded="false" aria-controls="bn-overlay-menu">
                    <span class="bn-hamburger-line"></span>
                    <span class="bn-hamburger-line"></span>
                    <span class="bn-hamburger-line"></span>
                </button>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- Pre-scroll header: tagline left, logo center, menu right -->
    <header class="bn-header-bar-pre-scroll" aria-label="<?php esc_attr_e( 'Site header', 'bn-newspack-child' ); ?>">
        <div class="bn-header-tagline">
            <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
        </div>
        <?php bn_render_static_header_logo(); ?>
        <?php bn_render_header_menu_actions( $utility_menu_assigned, $utility_urls, $join_url, $donate_url, 'static' ); ?>
    </header>

    <!-- After-scroll header: logo left, menu right (hidden initially) -->
    <header class="bn-header-bar-after-scroll" aria-label="<?php esc_attr_e( 'Site header', 'bn-newspack-child' ); ?>">
        <?php bn_render_header_logo(); ?>
        <?php bn_render_header_menu_actions( $utility_menu_assigned, $utility_urls, $join_url, $donate_url ); ?>
    </header>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"bn-primary-row","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bn-primary-row">
    <!-- wp:html -->
    <?php
    // Primary navigation row under the top header bar (Grist-style).
    ?>
    <div class="bn-primary-inner">
        <div class="bn-primary-center">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location'  => 'primary',
                    'container'       => 'nav',
                    'container_class' => 'bn-primary-nav',
                    'menu_class'      => 'bn-primary-menu',
                    'depth'           => 1,
                    'fallback_cb'     => false,
                    'aria_label'      => __( 'Primary navigation', 'bn-newspack-child' ),
                ) );
            }
            ?>
        </div>
        <div class="bn-primary-right">
            <a class="bn-primary-all" href="/topics"><?php esc_html_e( 'All Topics', 'bn-newspack-child' ); ?></a>
        </div>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->

