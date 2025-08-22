## 📸 Image Preview
![Preview](https://github.com/elias1435/wp-elementor-youtube-shorts-widget/blob/main/Edit%20_Video%20Testimonials_%20with%20Elementor.jpg?raw=true)


# Elementor YouTube Shorts Embed (Widget + Grid)

An Elementor widget for WordPress that embeds **YouTube Shorts and regular YouTube videos**.  
Features responsive **9:16 / 16:9 / 1:1 / custom** ratios, **lazy-loading**, **privacy-enhanced** mode (youtube-nocookie), **autoplay/mute**, and a **repeater grid** to mix Shorts + regular links.

## Features
- ✅ Paste any YouTube URL (Shorts, `watch?v=`, `youtu.be`) or just the video ID  
- ✅ Auto-detects Shorts (vertical 9:16) vs regular (16:9)  
- ✅ Custom ratios (e.g. `9:19.5`)  
- ✅ Lazy-load iframes for performance  
- ✅ Privacy-enhanced mode (`youtube-nocookie.com`)  
- ✅ Autoplay with mobile-friendly mute  
- ✅ Grid layout: columns & gap controls; tablet/mobile responsive

## Requirements
- WordPress 6.x+
- Elementor 3.5+ (Free or Pro)
- PHP 7.4+ (8.x recommended)

## Install (Plugin version)
1. Create a folder: `wp-content/plugins/elementor-youtube-shorts-embed/`
2. Add:
   - `elementor-youtube-shorts-embed.php` (main plugin file)
   - `includes/class-shortsembed-widget.php`
3. Activate **Elementor YouTube Shorts Embed** in **Plugins**.

> This repo also includes a **functions.php-only** variant if you prefer to keep it in a child theme.

## Install (Theme `functions.php` version)
1. Open your **child theme** `functions.php`.
2. Paste the `Shorts_And_YT_List_Widget` code from `/theme-functions-variant/functions.php.example`.
3. Visit a page → **Edit with Elementor** and add the widget.

## Usage
1. **Elementor → Add Widget**: “YouTube Shorts Embed” (single) or “YouTube & Shorts – List” (repeater grid).
2. Paste a **URL** or **Video ID**.
3. Optional:
   - Start at (seconds)
   - Autoplay / Mute
   - Privacy-enhanced (nocookie)
   - Lazy-load
   - Aspect ratio (Auto/9:16/16:9/1:1/Custom)
   - Columns & Gap (grid)

## Screenshots
_(Add your screenshots here)_
- `assets/screenshot-1.png` – Single Shorts embed
- `assets/screenshot-2.png` – Mixed grid (Shorts + regular)

## Roadmap
- Style controls (borders, radius, shadows)
- Click-to-play (thumbnail → iframe) for even better LCP
- Per-item captions under videos
- Modest branding / controls toggles

## Contributing
PRs welcome! Please keep code WP-coding-standards friendly and target `main`.

## License
GPL-2.0-or-later — matches WordPress plugin guidelines.
