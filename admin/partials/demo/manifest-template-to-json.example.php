<?php
// The output of this file will be a json file, for the manifest
// The var for the language must be defined when called this file.


    global $g_lang; // en or it

    $start_folder = substr( site_url(), strlen( $_SERVER['HTTP_ORIGIN'] ) ); 
?>
{
    "name": "<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>",
    "short_name": "<?php echo esc_attr( get_option( 'short_name_' . $g_lang ) ); ?>",
    "description": "<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>",
    "icons": [
        {
            "src": "<?php echo addcslashes( esc_attr($start_folder), '/'); ?>\/wp-content\/icons\/android-chrome-192x192.png",
            "sizes": "192x192",
            "type": "image\/png",
            "purpose": "any maskable"
        },
        {
            "src": "<?php echo addcslashes( esc_attr($start_folder), '/'); ?>\/wp-content\/icons\/android-chrome-512x512.png",
            "sizes": "512x512",
            "type": "image\/png",
            "purpose": "any maskable"
        }
    ],
    "background_color": "#b91d47",
    "theme_color": "#b91d47",
    "display": "standalone",
    "orientation": "landscape",
    "start_url": "<?php echo addcslashes( esc_attr($start_folder . ( $g_lang !== 'it' ? '/' . $g_lang : '' )  ), '/'); ?>\/",
    "scope": "<?php echo addcslashes( esc_attr($start_folder), '/'); ?>\/"
}

