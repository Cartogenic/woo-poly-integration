<?php
if (!defined('ABSPATH')) {
    exit('restricted access');
}
?>
<h3>
    <span>
        <?php _e( 'Support the Plugin', 'woo-poly-integration' ); ?>
    </span>
</h3>
<div class="inside">
    <p>
        <?php
        _e(
           'This plugin is free and it will always be free. But the plugin stills
           need your support. Please support by rating this plugin on
            <a href="https://wordpress.org/support/view/plugin-reviews/woo-poly-integration">Wordpress Repository</a> ,
            or by giving the plugin a star on <a href="https://github.com/decarvalhoaa/woo-poly-integration">Github</a>.',
            'woo-poly-integration'
        );
        ?>
    </p>
    <p>
        <?php echo \Hyyan\WPI\Plugin::getView('social') ?>
    </p>
</div>
