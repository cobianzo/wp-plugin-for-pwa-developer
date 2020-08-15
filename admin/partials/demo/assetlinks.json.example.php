<?php
// The output of this file will be a json file, for the .well-known/assetlinks.json .
// you can use php variables or embed js code using php code. Awesome isnt it?
 $pwa_gen = new Pwa_Generator_Admin('','');
?>
[
    {
        "relation": ["delegate_permission/common.query_webapk"],
        "target": {
            "namespace": "web",
            "site": "<?php echo esc_js($pwa_gen->manifest_it_url); ?>"
        }
    },
    {
        "relation": ["delegate_permission/common.handle_all_urls"],
        "target": {
            "namespace": "web",
            "site": "<?php echo esc_js($pwa_gen->manifest_it_url); ?>"
        }
    }
]