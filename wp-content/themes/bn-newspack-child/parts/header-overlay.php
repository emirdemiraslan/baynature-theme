<!-- wp:group {"className":"bn-overlay","layout":{"type":"constrained"}} -->
<div id="bn-overlay-menu" class="wp-block-group bn-overlay" aria-hidden="true">
    <!-- wp:group {"className":"bn-overlay-inner","layout":{"type":"constrained"}} -->
    <div class="wp-block-group bn-overlay-inner">
        <!-- wp:html -->
        <?php
        // Close button - positioned top-right, overlapping
        ?>
        <button class="bn-overlay-close" aria-label="<?php esc_attr_e( 'Close menu', 'bn-newspack-child' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg>
        </button>
        <!-- /wp:html -->

        <!-- wp:group {"className":"bn-overlay-content","layout":{"type":"flex","orientation":"vertical"}} -->
        <div class="wp-block-group bn-overlay-content">
            <!-- wp:html -->
            <?php
            // Intro text section
            $nav_options = bn_get_utility_urls();
            $intro_text = isset( $nav_options['overlay_intro_text'] ) ? $nav_options['overlay_intro_text'] : '';
            if ( ! empty( $intro_text ) ) :
                ?>
                <div class="bn-overlay-intro">
                    <?php echo wp_kses_post( wpautop( $intro_text ) ); ?>
                </div>
                <?php
            endif;
            ?>
            <!-- /wp:html -->

            <!-- wp:html -->
            <?php
            // Search section
            ?>
            <div class="bn-overlay-search-section">
                <label class="bn-overlay-search-label"><?php esc_html_e( 'Search', 'bn-newspack-child' ); ?></label>
                <form role="search" method="get" class="bn-overlay-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="bn-overlay-search-input-wrapper">
                        <span class="bn-overlay-search-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </span>
                        <input type="search" class="bn-overlay-search-input" placeholder="<?php esc_attr_e( 'Search', 'bn-newspack-child' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="bn-overlay-search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'bn-newspack-child' ); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <!-- /wp:html -->

            <!-- wp:html -->
            <?php
            // Print Edition section - show if magazine cover image is set
            $nav_options = bn_get_utility_urls();
            $magazine_cover_id = isset( $nav_options['magazine_cover_image'] ) ? intval( $nav_options['magazine_cover_image'] ) : 0;
            if ( $magazine_cover_id ) :
                $magazine_cover_url = wp_get_attachment_image_url( $magazine_cover_id, 'medium' );
                if ( $magazine_cover_url ) :
                    ?>
                    <div class="bn-overlay-print-edition-section">
                        <label class="bn-overlay-section-label"><?php esc_html_e( 'THE PRINT EDITION', 'bn-newspack-child' ); ?></label>
                        <div class="bn-overlay-print-edition-separator"></div>
                        <div class="bn-overlay-print-edition-content">
                            <div class="bn-overlay-print-edition-cover">
                                <a href="/magazine">
                                    <img src="<?php echo esc_url( $magazine_cover_url ); ?>" alt="<?php esc_attr_e( 'Magazine Cover', 'bn-newspack-child' ); ?>" />
                                </a>
                            </div>
                            <nav class="bn-overlay-print-edition-nav">
                                <ul class="bn-overlay-print-edition-menu">
                                    <li><a href="/magazine"><?php esc_html_e( 'Current Issue', 'bn-newspack-child' ); ?></a></li>
                                    <li><a href="/magazine-archive"><?php esc_html_e( 'Past Issues', 'bn-newspack-child' ); ?></a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <?php
                endif;
            endif;
            ?>
            <!-- /wp:html -->

            <!-- wp:html -->
            <?php
            // "Go To" section - always show if menu exists
            ?>
            <div class="bn-overlay-goto-section">
                <label class="bn-overlay-section-label"><?php esc_html_e( 'Go To', 'bn-newspack-child' ); ?></label>
                <div class="bn-overlay-goto-separator"></div>
                <?php
                if ( has_nav_menu( 'popup' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'popup',
                        'container'      => 'nav',
                        'container_class' => 'bn-popup-nav bn-overlay-goto-nav',
                        'menu_class'     => 'bn-overlay-goto-menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ) );
                } else {
                    // Fallback: show empty nav structure
                    echo '<nav class="bn-popup-nav bn-overlay-goto-nav"><ul class="bn-overlay-goto-menu"></ul></nav>';
                }
                ?>
            </div>
            <!-- /wp:html -->

            <!-- wp:html -->
            <?php
            // Topics section - always show
            $topics_location = get_nav_menu_locations();
            $topics_menu_id = isset( $topics_location['topics'] ) ? $topics_location['topics'] : 0;
            ?>
            <div class="bn-overlay-topics-section">
                <label class="bn-overlay-section-label"><?php esc_html_e( 'Topics', 'bn-newspack-child' ); ?></label>
                <div class="bn-overlay-topics-separator"></div>
                <nav class="bn-overlay-topics-nav">
                    <ul class="bn-overlay-topics-menu">
                        <?php
                        // Get topics menu items
                        if ( $topics_menu_id && has_nav_menu( 'topics' ) ) {
                            $topics_menu = wp_get_nav_menu_items( $topics_menu_id );
                            if ( $topics_menu && is_array( $topics_menu ) ) {
                                foreach ( $topics_menu as $item ) {
                                    if ( ! $item->menu_item_parent ) {
                                        ?>
                                        <li><a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a></li>
                                        <?php
                                    }
                                }
                            }
                        }
                        // Append "All Topics" link
                        ?>
                        <li><a href="/topics"><?php esc_html_e( 'All Topics', 'bn-newspack-child' ); ?></a></li>
                    </ul>
                </nav>
            </div>
            <!-- /wp:html -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->

