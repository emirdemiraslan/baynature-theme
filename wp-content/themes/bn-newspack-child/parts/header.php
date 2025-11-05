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
    $tagline    = get_bloginfo( 'description', 'display' );
    ?>
    <header class="bn-header-bar" aria-label="<?php esc_attr_e( 'Site header', 'bn-newspack-child' ); ?>">
        <?php if ( $tagline ) : ?>
            <p class="bn-header-tagline"><?php echo esc_html( $tagline ); ?></p>
        <?php endif; ?>

        <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
            <div class="bn-header-logo">
                <?php the_custom_logo(); ?>
            </div>
        <?php endif; ?>

        <div class="bn-header-right">
            <div class="bn-header-nav-wrap">
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

