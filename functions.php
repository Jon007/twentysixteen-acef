<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'genericons' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION


/*
Allow bulk addition of users to Buddypress groups as per eg: https://gist.github.com/rohmann/6151699
*/
add_action('load-users.php',function() {
if(isset($_GET['action']) && isset($_GET['bp_gid']) && isset($_GET['users'])) {
    $group_id = $_GET['bp_gid'];
    $users = $_GET['users'];
    foreach ($users as $user_id) {
        groups_join_group( $group_id, $user_id );
    }
}
    //Add some Javascript to handle the form submission
    add_action('admin_footer',function(){ ?>
    <script>
        jQuery("select[name='action']").append(jQuery('<option value="groupadd">Add to BP Group</option>'));
        jQuery("#doaction").click(function(e){
            if(jQuery("select[name='action'] :selected").val()=="groupadd") { e.preventDefault();
                gid=prompt("Please enter a BuddyPres Group ID","1");
                jQuery(".wrap form").append('<input type="hidden" name="bp_gid" value="'+gid+'" />').submit();
            }
        });
    </script>
    <?php
    });
});



function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(https://community.anglochinaeducation.org.uk/wp-content/uploads/2017/03/logo@2xthin.png);
		height:116px;
		width:300px;
		background-size: 300px 116px;
		background-repeat: no-repeat;
        }
#wp-submit{background-color:#dd3333;}
#wp-submit:hover, #wp-submit:active{background-color:#c45555;}
@media (min-width: 450px){
#site-main {
width: 400px;
margin: 0 auto;
background-color: white;
box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
vertical-align: middle;
padding: 15px;
margin-top: 100px;
}				
}
</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return 'http://anglochinaeducation.org/';
    //return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Anglo China Education Foundation';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function my_login_header() {
	echo ('<div id="site-main">');
}
add_filter( 'login_header', 'my_login_header' );
function my_login_footer() {
    echo ( '</div>');
}
add_filter( 'login_footer', 'my_login_footer' );
