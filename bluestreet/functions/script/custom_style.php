<?php 
$bluestreet_options = wp_parse_args(get_option('wallstreet_pro_options', array()), bluestreet_theme_data_setup());

if(get_theme_mod('logo_layout', 'top-logo-title-tagline')=='logo-title-tagline' &&  $bluestreet_options['header_center_layout_setting'] == 'center' ) { ?>

<style>
body .navbar-header.index3 .navbar-brand {
display: inline;

}

.index3 .logo-link-url { 
    display: inline-block;
    clear: both;
    text-align: left;
}
@media only screen and (min-width: 200px) and (max-width: 600px){
    .index3 .logo-link-url { 
         text-align: center;
    }
}
@media only screen and (min-width: 200px) and (max-width: 480px){
body .navbar-header.index3 .navbar-brand {
    display: inline-block;   
}
body .navbar-header.index3 h2 .navbar-brand{
    padding: 0px 0px 0.5px 0px; 
}
}
</style>

<?php } 

if(get_theme_mod('logo_layout', 'top-logo-title-tagline')=='title-tagline-logo' &&  $bluestreet_options['header_center_layout_setting'] == 'center' ) { ?>

<style>
body .navbar-header.index3 .navbar-brand {
display: inline;
padding: 7px 0 20px 15px;
}
.index3 .logo-link-url { 
    display: inline-block;
    clear: both;
    text-align: left;
}
@media only screen and (min-width: 200px) and (max-width: 600px){
    .index3 .logo-link-url { 
         text-align: center;
    }
}
@media only screen and (min-width: 200px) and (max-width: 480px){
body .navbar-header.index3 .logo-link-url {
   margin-bottom: 15px;
}
}
</style>

<?php } 

if(get_theme_mod('logo_layout','top-logo-title-tagline') == 'top-logo-title-tagline' &&  $bluestreet_options['header_center_layout_setting'] == 'center' ) { ?>

<style>
.index3 .logo-link-url { display: block; clear: both;  padding:3px 0px 5px }
body .navbar-header.index3 h2 .navbar-brand{
    padding: 0px 0px 0.5px 0px; 
}
</style>

<?php }

if(get_theme_mod('logo_layout','top-logo-title-tagline') == 'top-title-tagline-logo' &&  $bluestreet_options['header_center_layout_setting'] == 'center' ) { ?>

<style>
body .navbar-header.index3 h2 .navbar-brand{
    padding: 0px 0px 0.5px 0px; 
}
<?php if( (get_theme_mod('header_text')==true) && ( ($bluestreet_options['display_site_title'] ==true) ||  ($bluestreet_options['display_site_tagline'] == true) ) ){ ?>

    body .navbar-header.index3 .navbar-brand {
    padding: 20px 0px 20px;
}

<?php } ?>

</style>

<?php }

if(get_theme_mod('logo_layout', 'top-logo-title-tagline')=='logo-title-tagline' &&  $bluestreet_options['header_center_layout_setting'] == 'default' ) { ?>

<style>
.logo-link-url { display: inline-block;padding:25px 25px 12px 0}
@media (min-width: 481px) and (max-width: 768px){
body .logo-link-url {
    padding: 25px 15px 12px 15px;
} }
@media only screen and (min-width: 1100px){
    .navbar-header{
    display: flex;
    align-items: center;
}
}
</style>

<?php } 

if(get_theme_mod('logo_layout', 'top-logo-title-tagline')=='title-tagline-logo' &&  $bluestreet_options['header_center_layout_setting'] == 'default' ) { ?>

<style>
.navbar-brand.custom-logo{ display: inline-block; }
.logo-link-url { display: inline-block;padding:25px 15px 12px 0; float: left;}
.navbar > .container .navbar-brand {
    margin-right: 0px;
}
@media only screen and (min-width: 200px) and (max-width: 480px){
.logo-link-url{
	float: none;
}
}
@media (min-width: 481px) and (max-width: 768px){
body .logo-link-url {
    padding: 25px 15px 12px 15px;
} }
@media only screen and (min-width: 1100px){
    .navbar-header{
    display: flex;
    align-items: center;
}
}
</style>

<?php } 

if(get_theme_mod('logo_layout','top-logo-title-tagline') == 'top-logo-title-tagline' &&  $bluestreet_options['header_center_layout_setting'] == 'default' ) { ?>

<style>
.navbar .logo-link-url {display: block;clear: both;float: left;padding:4px 0px 8px 15px;}
.navbar .navbar-brand { display: inline-block;}
@media only screen and (max-width: 480px) and (min-width: 200px){
 .navbar .logo-link-url { float: none; text-align: center; }
}
</style>

<?php } 

if(get_theme_mod('logo_layout','top-logo-title-tagline') == 'top-title-tagline-logo' &&  $bluestreet_options['header_center_layout_setting'] == 'default' ) { ?>

<style>
.navbar .logo-link-url {padding:4px 0px 8px 15px;}
</style>
<?php } 
if(get_theme_mod('footer_divider',true)==true ) { ?>
<style>
    .footer-copyright {
     border-top: 1px solid;
     border-top-color:<?php echo esc_attr( get_theme_mod('footer_divider_color', '#575963') ); ?>;
     border-top-width:<?php echo intval( get_theme_mod('footer_divider_size', '1') ) . 'px'; ?> ;
     }
</style>
<?php }
if(get_theme_mod('footer_top_divider',true)==true ) { ?>
<style>
    .footer-social-area { box-shadow: 0 7px 2px -6px #2A2C33; }
    .footer-social-icons {
        border-bottom: 1px solid; 
        border-bottom-color:<?php echo esc_attr( get_theme_mod('footer_top_divider_color', '#575963') ); ?>;
        border-bottom-width:<?php echo intval( get_theme_mod('footer_top_divider_size', '1') ) . 'px'; ?> ;
     }
</style>
<?php } ?>