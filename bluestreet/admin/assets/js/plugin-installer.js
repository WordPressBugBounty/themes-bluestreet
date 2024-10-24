jQuery(document).ready(function($) {
    var pluginSlug = 'webriti-companion'; // Adjust this based on your plugin slug
    var pluginUrl = 'https://webriti.com/extensions/webriti-companion.zip'; // The plugin URL
    var button_id='';
    //console.log(pluginInstallerAjax.hook_suffix);
    if(pluginInstallerAjax.hook_suffix ==='appearance_page_bluestreet-welcome'){
        console.log(pluginInstallerAjax.hook_suffix);
        button_id='#install-plugin-button-options-page'
    }
    else{
        button_id='#install-plugin-button-welcome-page';
    }
    // Check the plugin status when the page loads
    $.ajax({
        url: pluginInstallerAjax.ajax_url,
        method: 'POST',
        data: {
            action: 'bluestreet_check_plugin_status',
            plugin_slug: pluginSlug,
            _ajax_nonce: pluginInstallerAjax.nonce
        },
        success: function(response) {
            var $button = $(button_id);

            if (response.success) {
                if (response.data.status === 'not_installed') {
                    $button.text('Install').attr('disabled', false);
                } else if (response.data.status === 'installed') {
                    $button.text('Activate').attr('disabled', false);
                } else if (response.data.status === 'activated') {
                    $button.text('Activated').attr('disabled', true);
                }
            } else {
                alert('Error: ' + response.data);
                $button.attr('disabled', true);
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });

    // Handle plugin installation and activation on button click
    $(button_id).on('click', function(e) {
        e.preventDefault('dev');

        var $button = $(this);

        if ($button.text() === 'Install') {
            $button.text('Installing...').attr('disabled', true);

            // Install the plugin
            $.ajax({
                url: pluginInstallerAjax.ajax_url,
                method: 'POST',
                data: {
                    action: 'bluestreet_install_activate_plugin',
                    plugin_url: pluginUrl,
                    plugin_slug: pluginSlug,
                    _ajax_nonce: pluginInstallerAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $button.text('Activate').attr('disabled', false);

                        // Redirect after activation
                        $button.on('click', function() {
                            $button.text('Activating...').attr('disabled', true);
                            if(pluginInstallerAjax.hook_suffix ==='customize.php'){
                                // Delay the redirect
                                setTimeout(function() {
                                     window.location.reload();
                                }, 6000); // Adjust time in milliseconds (3000 = 3 seconds)
                            }else{
                                // Delay the redirect
                                setTimeout(function() {
                                    window.location.href = response.data.redirect_url;
                                }, 6000); // Adjust time in milliseconds (3000 = 3 seconds)
                            }
                        });
                    } else {
                        alert('Error: ' + response.data);
                        $button.text('Install').attr('disabled', false);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.text('Install').attr('disabled', false);
                }
            });
        } else if ($button.text() === 'Activate') {
            $button.text('Activating...').attr('disabled', true);

            // Redirect after activation
            $.ajax({
                url: pluginInstallerAjax.ajax_url,
                method: 'POST',
                data: {
                    action: 'bluestreet_install_activate_plugin',
                    plugin_url: pluginUrl,
                    plugin_slug: pluginSlug,
                    _ajax_nonce: pluginInstallerAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Delay the redirect
                        if(pluginInstallerAjax.hook_suffix ==='customize.php'){
                            // Delay the redirect
                            setTimeout(function() {
                                window.location.reload();
                            }, 6000); // Adjust time in milliseconds (3000 = 3 seconds)
                        }else{
                            // Delay the redirect
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 6000); // Adjust time in milliseconds (3000 = 3 seconds)
                        }
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
