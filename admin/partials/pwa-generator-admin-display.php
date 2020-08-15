<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://cobianzo.com
 * @since      1.0.0
 *
 * @package    Pwa_Generator
 * @subpackage Pwa_Generator/admin/partials
 
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1><?php _e( 'PWA Generator', 'pwa-generator' ); ?></h1>

<h2><?php _e('Manifest', 'pwa-generator'); ?></h2>
<div style='border:1px solid gray; padding: 10px;'>
    <form action='<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>' method="post">
        <div style='display:flex; flex: 1 1 50%'>
        <?php
            // if (function_exists('pll_languages_list') ) { 
            // foreach( pll_languages_list() as $i => $lang ) :
            foreach( $this->languages_defined as $lang => $irrelevant ) :                
            ?>
            <div> 
            <?php
                if ( ! array_key_exists( $lang, $this->languages_defined) ) {
                    printf( __("Error. Language defined in polylang but not in the plugin pwa generator: %s ", 'pwa-generator'), $lang ) ;
                    exit();
                } else {
                    echo '<h3>' . __('Language', 'pwa-generator') . ': ' . $lang . '</h3>';
                    $var_path_name = 'manifest_'. $lang . '_path';
                    $var_url_name = 'manifest_'. $lang . '_url';
                    echo __('Manifest file', 'pwa-generator') . ": <a href='" . $this->$var_url_name . "' target='_blank'><pre>" . $this->$var_path_name . '</pre> </a>';

                    
                    
                    if (file_exists( $this->$var_path_name )) {
                        echo '<p style="color:darkgreen;">'.__('File exists!', 'pwa-generator').' 😀 </p>';
                        echo '<p>'.__('Make sure it has the right permissions with', 'pwa-generator').'</p>';
                    } else {
                        echo '<p style="color:red;">'.__('File doesnt exists 💩 Create IT', 'pwa-generator').'</p>';
                        echo "<pre>touch ".$this->$var_path_name."</pre>";
                    }
                    if (is_writable( $this->$var_path_name )) {
                        echo '<p style="color:darkgreen;">'.__('Writable', 'pwa-generator').' 😀 </p>';
                    } else {
                        echo '<p style="color:red;">'.__('Not writable though 💩 ', 'pwa-generator').'</p>';
                        echo '<p>'.__('Make sure it has the right permissions with', 'pwa-generator').'</p>';
                        echo "<pre>chmod 666 ".$this->$var_path_name."</pre>";
                    }
                    ?>


                    <input type="text" placeholder="short_name <?php echo $lang; ?>" name="<?php echo 'short_name_' . $lang; ?>" id="<?php echo 'short_name_' . $lang; ?>" 
                            class="input" value="<?php echo esc_attr( get_option('short_name_' . $lang) ); ?>"/><br><br>

                    <?php
                }
            ?>
            </div>
            <?php
            // }
            // else {
            //    printf( __("Error. This plugin was made to support polylang and the languages: %s ", 'pwa-generator'), implode(', ', $this->languages_defined ) );
            //    exit();
            //}
            endforeach; 
            ?>
        </div>
    <?php
        if (!empty($_GET['manifest']) && $_GET['manifest'] === 'generated' ) {
            echo '<p class="success" style="color:green">' . __('Good, the manifest files have been generated', 'pwa-generator') . '</p>';
        }
    ?>
    
        <input name="action" type="hidden" value="generate_manifests_form_submit" />
        <?php wp_nonce_field( 'action_generate_manifest', 'nonce_field' ); ?>        
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Generate Manifest files', 'pwa-generator'); ?>"/>
                
    </form>
    <p> <?php 
        printf( __("Files will be generated from source file: <pre>%s</pre> ", 'pwa-generator'), $this->manifest_source );
    ?> </p>
</div>


<h2><?php _e('Service worker', 'pwa-generator'); ?></h2>
<div style='border:1px solid gray; padding: 10px;'>
    <p><?php 
    echo "<a href='" . $this->sw_url . "' target='_blank'><pre>" . $this->sw_path . '</pre></a>';
    ?></p>

    <?php
        if (!empty($_GET['sw']) && $_GET['sw'] === 'generated' ) {
            echo '<p class="success" style="color:green">' . __('Good, the sw file has been generated', 'pwa-generator') . '</p>';
        } ?>
    <form action='<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>' method="post">
        <input name="action" type="hidden" value="generate_sw_form_submit" />
        <?php wp_nonce_field( 'action_generate_sw', 'nonce_field' ); ?>
        
        <?php
        if (file_exists( $this->sw_path )) {
            echo '<p style="color:darkgreen;">'.__('File exists!', 'pwa-generator').' 😀 </p>';
            echo '<p>'.__('Make sure it has the right permissions with', 'pwa-generator').'</p>';
        } else {
            echo '<p style="color:red;">'.__('File doesnt exists 💩 Create IT', 'pwa-generator').'</p>';
            echo "<pre>touch ".$this->sw_path."</pre>";
        }
        if (is_writable( $this->sw_path )) {
            echo '<p style="color:darkgreen;">'.__('Writable', 'pwa-generator').' 😀 </p>';
        } else {
            echo '<p style="color:red;">'.__('Not writable though 💩 ', 'pwa-generator').'</p>';
            echo '<p>'.__('Make sure it has the right permissions with', 'pwa-generator').'</p>';
            echo "<pre>chmod 666 ".$this->sw_path."</pre>";
        }
        ?>



        
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Generate Service worker file', 'pwa-generator'); ?>">
                
    </form>
    <p> <?php 
        printf( __("Files will be generated from source file: <pre>%s</pre> ", 'pwa-generator'), $this->sw_source );
    ?> </p>
</div>