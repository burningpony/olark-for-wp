<?php
/*
Plugin Name: Olark for WP
Plugin URI: http://www.burningpony.com/blog/portfolio/olark-for-wp/
Description: A plugin that allows website authors to easily place a <a href="http://www.olark.com/">Olark</a> live help widget on their wpwebsite.
Version: 2.5.1
Author: Russell Osborne
Author URI: http://www.burningpony.com/

=== VERSION HISTORY ===
04.28.09 - v1.0 - The first version
08.28.09 - v2.0 - Updated the plugin to reflect the brand change from Hab.la to Olark
06.03.11 - v2.1 - Forked From Olark for Wordpress/ Upgraded to New Olark Async Code, Added Callout Widget
06.04.11 - v2.2 - Major Rewrite Moving to More Modern Plugin Codex/API
06.05.11 - v2.3 - Added Olark API for logged in Users
06.07.11 - v2.3.1-3 - Fixing Typos
06.07.11 - v2.4 - In plugin Sign Up Beta!!
06.08.11 - v2.4.1 - Bug Fix on Signup (Sessions Will Now persist between page loads)
09.01.11 - v2.4.2 - Removed iFrame
05.09.13 - v2.5 Updating Olark Includes, Removing Deprecated Code, Removing referral Links.   This project is now dead.
05.09.13 - v2.5.1 Bad Build

=== LEGAL INFORMATION ===
Copyright (C) 2011 Russell Osborne <projects@burningpony.com> - www.burningpony.com
Original Work By James Dimick <mail@jamesdimick.com> - www.jamesdimick.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

$plugurldir = get_option('siteurl') . '/' . PLUGINDIR . '/olark-for-wp/';
$ofw_domain = 'OlarkForWP';
load_plugin_textdomain($ofw_domain, 'wp-content/plugins/olark-for-wp');
add_action('init', 'ofw_init');
add_action('wp_footer', 'ofw_insert');
add_action('admin_notices', 'ofw_admin_notice');
add_filter('plugin_action_links', 'ofw_plugin_actions', 10, 2);

function ofw_init()
{
    if (function_exists('current_user_can') && current_user_can('manage_options'))
        add_action('admin_menu', 'ofw_add_settings_page');
    if (!function_exists('get_plugins'))
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $options = get_option('ofwDisable');
}
function ofw_settings()
{
    register_setting('olark-for-wp-group', 'ofwID');
    register_setting('olark-for-wp-group', 'ofwDisable');
    add_settings_section('olark-for-wp', "Olark for WP", "", 'olark-for-wp-group');

}
function plugin_get_version()
{
    if (!function_exists('get_plugins'))
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $plugin_folder = get_plugins('/' . plugin_basename(dirname(__FILE__)));
    $plugin_file   = basename((__FILE__));
    return $plugin_folder[$plugin_file]['Version'];
}
function ofw_insert()
{
    global $current_user;
    if (get_option('ofwID')) {
        echo ("<!-- begin olark code -->
<script data-cfasync=\"false\" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol==\"https:\"?\"https:\":\"http:\",z=c.name,r=\"load\";var nt=function(){
f[z]=function(){
(a.s=a.s||[]).push(arguments)};var a=f[z]._={
},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){
f[z](\"call\",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={
0:+new Date};a.P=function(u){
a.p[u]=new Date-a.p[0]};function s(){
a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent(\"on\"+r,s);var ld=function(){function p(hd){
hd=\"head\";return[\"<\",hd,\"></\",hd,\"><\",i,' onl' + 'oad=\"var d=',g,\";d.getElementsByTagName('head')[0].\",j,\"(d.\",h,\"('script')).\",k,\"='\",l,\"//\",a.l,\"'\",'\"',\"></\",i,\">\"].join(\"\")}var i=\"body\",m=d[i];if(!m){
return setTimeout(ld,100)}a.P(1);var j=\"appendChild\",h=\"createElement\",k=\"src\",n=d[h](\"div\"),v=n[j](d[h](z)),b=d[h](\"iframe\"),g=\"document\",e=\"domain\",o;n.style.display=\"none\";m.insertBefore(n,m.firstChild).id=z;b.frameBorder=\"0\";b.id=z+\"-loader\";if(/MSIE[ ]+6/.test(navigator.userAgent)){
b.src=\"javascript:false\"}b.allowTransparency=\"true\";v[j](b);try{
b.contentWindow[g].open()}catch(w){
c[e]=d[e];o=\"javascript:var d=\"+g+\".open();d.domain='\"+d.domain+\"';\";b[k]=o+\"void(0);\"}try{
var t=b.contentWindow[g];t.write(p());t.close()}catch(x){
b[k]=o+'d.write(\"'+p().replace(/\"/g,String.fromCharCode(92)+'\"')+'\");d.close();'}a.P(2)};ld()};nt()})({
loader: \"static.olark.com/jsclient/loader0.js\",name:\"olark\",methods:[\"configure\",\"extend\",\"declare\",\"identify\"]});
/* custom configuration goes here (www.olark.com/documentation) */

olark.identify('" . get_option('ofwID') . "');

/*]]>*/</script><noscript>");
        //Make user info Avaliable in the Dom for the JS API
        if (0 != $current_user->ID) {
            echo ("olark('api.chat.updateVisitorNickname', {snippet: '$current_user->display_name'})\n"); //This will be overwritten if you require a name and email
            echo ("olark('api.chat.updateVisitorStatus', {snippet: [
        'Wordpress User Info',
        'Username: " . $current_user->user_login . "',
        'User email:  $current_user->user_email',
        'User first name: " . $current_user->user_firstname . "',
        'User last name: " . $current_user->user_lastname . "',
        'User display name: " . $current_user->display_name . "',
        'User ID: " . $current_user->ID . "'
        ]})
        ");
            // On chat start send basic info to Operator
            echo "olark('api.chat.onBeginConversation', function() {
        olark('api.chat.sendNotificationToOperator', {body: \"Wordpress Information: $current_user->display_name   Email:$current_user->user_email \"});
        });";
        }
        echo ('<noscript><a href="https://www.olark.com/site/' . get_option('ofwID') . '/contact" title="Contact us" target="_blank">Questions? Feedback?</a> powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a></noscript>
<!-- end olark code -->');
        echo ("\n</script>\n<!-- End Olark Code <http://www.olark.com/> -->\n\n");
    }
}

function ofw_admin_notice()
{
    if (!get_option('ofwID'))
        echo ('<div class="error"><p><strong>' . sprintf(__('Olark for WP is disabled. Please go to the <a href="%s">plugin page</a> and enter a valid account ID to enable it.'), admin_url('options-general.php?page=olark-for-wp')) . '</strong></p></div>');
}
function ofw_plugin_actions($links, $file)
{
    static $this_plugin;
    if (!$this_plugin)
        $this_plugin = plugin_basename(__FILE__);
    if ($file == $this_plugin && function_exists('admin_url')) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=olark-for-wp') . '">' . __('Settings', $ofw_domain) . '</a>';
        array_unshift($links, $settings_link);
    }
    return ($links);
}

    function ofw_add_settings_page()
    {
        function ofw_settings_page()
        {
            global $ofw_domain, $plugurldir, $olark_options;
?>
      <div class="wrap">
        <?php
            screen_icon();
?>
        <h2><?php
            _e('Olark for WP', $ofw_domain);
?> <small><?
            echo plugin_get_version();
?></small></h2>
        <div class="metabox-holder meta-box-sortables ui-sortable pointer">
          <div class="postbox" style="float:left;width:30em;margin-right:20px">
            <h3 class="hndle"><span><?php
            _e('Olark Account ID', $ofw_domain);
?></span></h3>
            <div class="inside" style="padding: 0 10px">
              <p style="text-align:center"><a href="http://www.olark.com/" title="<?php
            _e('Chat with your website&rsquo;s visitors using your favorite IM client', $ofw_domain);
?>"><img src="<?php
            echo ($plugurldir);
?>olark.png" height="132" width="244" alt="<?php
            _e('Olark Logo', $ofw_domain);
?>" /></a></p>
              <form method="post" action="options.php">
                <?php
            settings_fields('olark-for-wp-group');
?>
                <p><label for="ofwID"><?php
            printf(__('Enter your %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sOlark%3$s account ID below to activate the plugin.', $ofw_domain), '<strong><a href="http://www.olark.com//" title="', '">', '</a></strong>');
?></label><br />

                  <input type="text" name="ofwID" value="<?php
            echo get_option('ofwID');
?>" style="width:100%" /></p>
                    <p class="submit">
                      <input type="submit" class="button-primary" value="<?php
            _e('Save Changes');
?>" />
                    </p>
                  </form>

                  <small class="nonessential"><?php
            _e('Entering an incorrect ID will result in an error!', $ofw_domain);
?></small></p>
                  <p style="font-size:smaller;color:#999239;background-color:#ffffe0;padding:0.4em 0.6em !important;border:1px solid #e6db55;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px"><?php
            printf(__('Don&rsquo;t have an account? No problem! %1$sRegister for a free Olark account today!%2$sRegister for a <strong>FREE</strong> Olark account right now!%3$s Start chatting with your site visitors today!', $ofw_domain), '<a href="http://www.olark.com/portal/wizard" title="', '">', '</a>');
?></p>
                  </div>
                </div>



                  <div class="postbox" style="float:left;width:20%;margin-right:20px">
                    <h3 class="hndle"><span><?php
            _e('Change Notes', $ofw_domain);
?></span></h3>
                    <div class="inside" style="padding: 10px">
                      <ul>
                        <li>04.28.09 - v1.0 - The first version</li>
                        <li>08.28.09 - v2.0 - Updated the plugin to reflect the brand change from Hab.la to Olark</li>
                        <li>06.03.11 - v2.1 - Forked From Olark for Wordpress/  Upgraded to New Olark Async Code, Added Callout Widget, Added Chat Tabs</li>
                        <li>06.04.11 - v2.2 - Major Rewrite Moving to More Modern Plugin Codex/API</li>
                        <li>06.05.11 - v2.3 - Added Olark API for logged in Users</li>
                        <li>06.07.11 - v2.3.1-3 - Fixing Typos</li>
                        <li>06.07.11 - v2.4 - In Plugin Olark Sign up!</li>
                        <li>06.08.11 - v2.4.1 - Bug Fix on Signup (Sessions Will Now persist between page loads) </li>
                        <li>09.01.11 - v2.4.2 - Removed iFrame </li>
                        <li>05.09.13 - v2.5 Updating Olark Includes, Removing Deprecated Code, Removing referral Links.   This project is now dead.</li>
                        <li>05.09.13 - v2.5.1 Bad Build </li>
                        <li> <h4> <a href ='http://www.olark.com/customer/portal/articles/314795-wordpress-integration-guide' >This Project is DEAD! For all Future updates please remove this plugin and follow Olark's offical wordpress integration guide here </a> </h4> </li>

                      </ul>
                    </div>
                  </div>

                </div>
              </div>
              <?php
        }
        add_action('admin_init', 'ofw_settings');
        add_submenu_page('options-general.php', __('Olark for WP', $ofw_domain), __('Olark for WP', $ofw_domain), 'manage_options', 'olark-for-wp', 'ofw_settings_page');
    }

?>
