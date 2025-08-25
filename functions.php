add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) return;

    class Shorts_And_YT_List_Widget extends \Elementor\Widget_Base {
        public function get_name() { return 'shorts_and_yt_list_widget'; }
        public function get_title() { return __( 'YouTube & Shorts – List', 'shorts-embed-widget' ); }
        public function get_icon() { return 'eicon-youtube'; }
        public function get_categories() { return [ 'basic' ]; }

        protected function register_controls() {
            $repeater = new \Elementor\Repeater();

            $repeater->add_control( 'url', [
                'label' => __( 'YouTube URL (Shorts or regular)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'https://www.youtube.com/shorts/abc123xyz or https://youtu.be/abc123xyz',
                'dynamic' => [ 'active' => true ],
            ] );

            $repeater->add_control( 'video_id', [
                'label' => __( 'OR Video ID', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'abc123xyz',
                'description' => __( 'If provided, this overrides URL parsing.', 'shorts-embed-widget' ),
            ] );

            $repeater->add_control( 'title', [
                'label' => __( 'Title (aria-label)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'YouTube player', 'shorts-embed-widget' ),
            ] );

            $repeater->add_control( 'start', [
                'label' => __( 'Start at (seconds)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 0,
            ] );

            $repeater->add_control( 'ratio', [
                'label' => __( 'Aspect Ratio', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'auto'   => 'Auto (detect Shorts vs normal)',
                    '9x16'   => '9:16 (vertical)',
                    '16x9'   => '16:9 (standard)',
                    '1x1'    => '1:1',
                    'custom' => 'Custom',
                ],
                'default' => 'auto',
            ] );

            $repeater->add_control( 'custom_ratio', [
                'label' => __( 'Custom ratio W:H (e.g., 9:19.5)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => '9:16',
                'condition' => [ 'ratio' => 'custom' ],
            ] );

            // Section: list items
            $this->start_controls_section( 'list_section', [ 'label' => __( 'Videos', 'shorts-embed-widget' ) ] );
            $this->add_control( 'videos', [
                'label' => __( 'Items', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [ [ 'url' => '' ] ],
                'title_field' => '{{{ url || video_id || "(video)" }}}',
            ] );
            $this->end_controls_section();

            // Section: behavior
            $this->start_controls_section( 'behavior_section', [ 'label' => __( 'Behavior', 'shorts-embed-widget' ) ] );
            $this->add_control( 'autoplay', [
                'label' => __( 'Autoplay', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
            ] );
            $this->add_control( 'mute', [
                'label' => __( 'Mute (recommended for autoplay)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ] );
            $this->add_control( 'privacy', [
                'label' => __( 'Privacy-enhanced (youtube-nocookie.com)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ] );
            $this->add_control( 'lazy', [
                'label' => __( 'Lazy-load iframes', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ] );
            $this->add_control( 'open_in_modal', [
                'label' => __( 'Open in modal', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
            ] );
            $this->end_controls_section();

            // Section: layout
            $this->start_controls_section( 'layout_section', [ 'label' => __( 'Layout', 'shorts-embed-widget' ) ] );
            $this->add_control( 'columns', [
                'label' => __( 'Columns (desktop)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1, 'max' => 6, 'step' => 1,
                'default' => 3,
            ] );
            $this->add_control( 'gap', [
                'label' => __( 'Gap (px)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0, 'max' => 64, 'step' => 1,
                'default' => 16,
            ] );
            // New: responsive column controls
            $this->add_control( 'columns_tablet', [
                'label' => __( 'Columns (tablet)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1, 'max' => 6, 'step' => 1,
                'default' => 2,
            ] );
            $this->add_control( 'columns_mobile', [
                'label' => __( 'Columns (mobile)', 'shorts-embed-widget' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1, 'max' => 6, 'step' => 1,
                'default' => 1,
            ] );
            $this->end_controls_section();
        }

        private function extract_video_id( $url_or_id ) {
            $val = trim( (string) $url_or_id );
            if ( $val === '' ) return '';
            if ( preg_match( '/^[a-zA-Z0-9_-]{6,}$/', $val ) ) return $val; // raw ID

            $patterns = [
                '#youtu\.be/([a-zA-Z0-9_-]{6,})#i',
                '#youtube\.com/(?:shorts|embed)/([a-zA-Z0-9_-]{6,})#i',
                '#youtube\.com/watch\?v=([a-zA-Z0-9_-]{6,})#i',
                '#youtube\.com/.*[?&]v=([a-zA-Z0-9_-]{6,})#i',
            ];
            foreach ( $patterns as $p ) {
                if ( preg_match( $p, $val, $m ) ) return $m[1];
            }
            return '';
        }

        private function is_shorts( $url_or_id ) {
            $u = strtolower( (string) $url_or_id );
            return ( strpos( $u, '/shorts/' ) !== false );
        }

        private function ratio_to_padding( $key, $custom, $is_shorts_guess ) {
            if ( $key === 'auto' ) {
                // auto: shorts => 9:16 (padding=16/9*100), regular => 16:9 (padding=9/16*100)
                return $is_shorts_guess ? (16/9)*100 : (9/16)*100;
            }
            $w=9; $h=16;
            if ( $key === '16x9' ) { $w=16; $h=9; }
            elseif ( $key === '1x1' ) { $w=1; $h=1; }
            elseif ( $key === 'custom' && $custom && preg_match( '#^\s*([0-9.]+)\s*:\s*([0-9.]+)\s*$#', $custom, $m ) ) {
                $w=(float)$m[1]; $h=(float)$m[2];
                if ( $w<=0 || $h<=0 ) { $w=9; $h=16; }
            }
            return ($h/$w)*100.0;
        }

        // Modal assets — printed once per page if modal is enabled
        private static $yse_modal_printed = false;
        private function print_modal_assets_once() {
            if ( self::$yse_modal_printed ) return;
            self::$yse_modal_printed = true;

            echo '<style id="yse-modal-css">
            .yse-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:99999;display:none;align-items:center;justify-content:center;padding:24px}
            .yse-modal-inner{position:relative;width:min(100%,960px);aspect-ratio:16/9;background:#000;border-radius:12px;overflow:hidden}
            .yse-modal-close{position:absolute;top:8px;right:8px;background:rgba(0,0,0,.55);color:#fff;border:0;border-radius:8px;padding:8px 10px;cursor:pointer}
            .yse-modal-overlay.is-open{display:flex}
            .yse-modal-inner iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
            body.yse-modal-open{overflow:hidden}
            </style>';

            echo '<div class="yse-modal-overlay" role="dialog" aria-modal="true" aria-label="Video modal">
                    <div class="yse-modal-inner">
                        <button type="button" class="yse-modal-close" aria-label="Close">✕</button>
                    </div>
                  </div>';

            echo '<script id="yse-modal-js">
            (function(){
              const overlay = document.querySelector(".yse-modal-overlay");
              const inner   = overlay ? overlay.querySelector(".yse-modal-inner") : null;
              const closeBtn= overlay ? overlay.querySelector(".yse-modal-close") : null;
              const allow   = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share;";
              function openWith(src){
                if(!overlay||!inner) return;
                inner.querySelectorAll("iframe").forEach(n=>n.remove());
                const f = document.createElement("iframe");
                f.setAttribute("allow", allow);
                f.setAttribute("allowfullscreen", "");
                f.src = src;
                inner.appendChild(f);
                overlay.classList.add("is-open");
                document.body.classList.add("yse-modal-open");
                closeBtn && closeBtn.focus();
              }
              function close(){
                if(!overlay||!inner) return;
                overlay.classList.remove("is-open");
                document.body.classList.remove("yse-modal-open");
                inner.querySelectorAll("iframe").forEach(n=>n.remove());
              }
              document.addEventListener("click", function(e){
                const t = e.target.closest(".yse-modal-trigger");
                if(t){
                  const src = t.getAttribute("data-yse-src");
                  if(src){ e.preventDefault(); openWith(src); }
                }
              });
              overlay && overlay.addEventListener("click", function(e){
                if(e.target === overlay) close();
              });
              closeBtn && closeBtn.addEventListener("click", close);
              document.addEventListener("keydown", function(e){
                if(e.key === "Escape" && overlay && overlay.classList.contains("is-open")) close();
              });
            })();
            </script>';
        }

        protected function render() {
            $s = $this->get_settings_for_display();
            $items = is_array( $s['videos'] ?? null ) ? $s['videos'] : [];
            if ( empty( $items ) ) {
                echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Add at least one YouTube item.', 'shorts-embed-widget' ) . '</div>';
                return;
            }

            $base    = ( ! empty( $s['privacy'] ) && $s['privacy'] === 'yes' ) ? 'https://www.youtube-nocookie.com' : 'https://www.youtube.com';
            $loading = ( ! empty( $s['lazy'] ) && $s['lazy'] === 'yes' ) ? ' loading="lazy"' : '';
            $allow   = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share;';
            $gap     = max(0, intval($s['gap'] ?? 16));

            // Responsive columns (desktop/tablet/mobile)
            $cols_d = max(1, min(6, intval($s['columns'] ?? 3)));
            $cols_t = max(1, min(6, intval($s['columns_tablet'] ?? 2)));
            $cols_m = max(1, min(6, intval($s['columns_mobile'] ?? 1)));

            // If using modal, print assets once
            if ( ! empty( $s['open_in_modal'] ) && $s['open_in_modal'] === 'yes' ) {
                $this->print_modal_assets_once();
            }

            echo '<div class="yse-grid" style="display:grid;grid-template-columns:repeat(' . esc_attr($cols_d) . ',minmax(0,1fr));gap:' . esc_attr($gap) . 'px;">';

            foreach ( $items as $it ) {
                $vid = $this->extract_video_id( $it['video_id'] ?: ($it['url'] ?? '') );
                if ( ! $vid ) continue;

                $isShorts = $this->is_shorts( $it['url'] ?? '' );
                $ratioKey = $it['ratio'] ?? 'auto';
                $padding  = $this->ratio_to_padding( $ratioKey, $it['custom_ratio'] ?? '', $isShorts );
                $padding  = number_format( $padding, 6, '.', '' );

                $params = [ 'enablejsapi' => '1', 'rel' => '0', 'playsinline' => '1' ];
                if ( ! empty( $s['autoplay'] ) && $s['autoplay'] === 'yes' ) {
                    $params['autoplay'] = '1';
                    $params['mute']     = ( ! empty( $s['mute'] ) && $s['mute'] === 'yes' ) ? '1' : '0';
                }
                $start = isset($it['start']) ? max(0, intval($it['start'])) : 0;
                if ( $start > 0 ) $params['start'] = (string)$start;

                $query = http_build_query( $params, '', '&' );
                $src   = esc_url( $base . '/embed/' . rawurlencode( $vid ) . ( $query ? ( '?' . $query ) : '' ) );

                // Modal src: always force autoplay+mute for better UX
                $modal_params = $params; $modal_params['autoplay'] = '1'; $modal_params['mute'] = '1';
                $modal_query  = http_build_query( $modal_params, '', '&' );
                $modal_src    = esc_url( $base . '/embed/' . rawurlencode( $vid ) . ( $modal_query ? ( '?' . $modal_query ) : '' ) );

                $title = ! empty( $it['title'] ) ? $it['title'] : __( 'YouTube player', 'shorts-embed-widget' );
                $thumb = esc_url( 'https://i.ytimg.com/vi/' . rawurlencode( $vid ) . '/hqdefault.jpg' );

                if ( ! empty( $s['open_in_modal'] ) && $s['open_in_modal'] === 'yes' ) {
                    // Modal trigger tile
                    echo '<button type="button" class="yse-modal-trigger" data-yse-src="' . esc_attr( $modal_src ) . '" aria-label="' . esc_attr( $title ) . '" style="position:relative;width:100%;height:0;padding-bottom:' . esc_attr($padding) . '%;overflow:hidden;border:0;background:transparent;border-radius:16px;cursor:pointer;">';
                    echo '  <span class="yse-modal-thumb" style="position:absolute;inset:0;background-image:url(' . $thumb . ');background-size:cover;background-position:center;border-radius:inherit;"></span>';
                    echo '  <span class="yse-modal-play" aria-hidden="true" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:64px;height:64px;border-radius:50%;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;">';
                    echo '    <svg width="30" height="30" viewBox="0 0 24 24" fill="#fff" xmlns="http://www.w3.org/2000/svg"><path d="M8 5v14l11-7z"/></svg>';
                    echo '  </span>';
                    echo '</button>';
                } else {
                    // Inline iframe
                    echo '<div class="yse-item" style="position:relative;width:100%;height:0;padding-bottom:' . esc_attr($padding) . '%;overflow:hidden;border-radius:16px;">';
                    echo '  <iframe src="' . $src . '" title="' . esc_attr($title) . '"' . $loading . ' allow="' . esc_attr($allow) . '" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;border-radius:inherit;"></iframe>';
                    echo '</div>';
                }
            }

            echo '</div>';

            // Responsive CSS for the grid
            echo '<style>.elementor-element-' . esc_attr($this->get_id()) . ' .yse-grid{grid-auto-rows:1fr}@media(max-width:1024px){.elementor-element-' . esc_attr($this->get_id()) . ' .yse-grid{grid-template-columns:repeat(' . esc_attr($cols_t) . ',minmax(0,1fr))}}@media(max-width:640px){.elementor-element-' . esc_attr($this->get_id()) . ' .yse-grid{grid-template-columns:repeat(' . esc_attr($cols_m) . ',minmax(0,1fr))}}</style>';
        }
    }

    $widgets_manager->register( new \Shorts_And_YT_List_Widget() );
});
