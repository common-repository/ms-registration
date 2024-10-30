<?php

function ms_admin_init() {
    
    if(isset($_POST["ms_config"])) {
        ms_options_validate($_POST["ms_config"]);
    }
    
    add_settings_section('ms_permalinks', 'Authentication Permalinks', 'ms_permalinks_section', 'permalink');
    add_settings_field('ms_login_url', 'Login URL', 'ms_login_url_input', 'permalink', 'ms_permalinks');
    add_settings_field('ms_register_url', 'Registration URL', 'ms_register_url_input', 'permalink', 'ms_permalinks');
    add_settings_field('ms_lostpassword_url', 'Lost Password URL', 'ms_lostpassword_url_input', 'permalink', 'ms_permalinks');
    add_settings_field('ms_logout_url', 'Logout URL', 'ms_logout_url_input', 'permalink', 'ms_permalinks');
    
    add_settings_section('ms_redirects', 'Authentication Redirects', 'ms_redirects_section', 'permalink');
    add_settings_field('ms_login_redirect', 'Login Redirect URL', 'ms_login_redirect_input', 'permalink', 'ms_redirects');
    add_settings_field('ms_logout_redirect', 'Logout Redirect URL', 'ms_logout_redirect_input', 'permalink', 'ms_redirects');
}

function ms_options_validate($input) {

    $options = get_option('ms_config');
    
    if(!is_array($options)) {
        $options = array();
    }
    
    $params = array('login', 'register', 'lostpassword', 'logout', "redirect_login", "redirect_logout");
    
    foreach($params as $action) {
        $value = trim($input[$action]);
        if(!empty($value)) {
            $options[$action] = "/".ltrim($value, "/");
        } else {
            $options[$action] = null;
        }
    }

    update_option("ms_config", $options);
}

function ms_permalinks_section() {

}

function ms_redirects_section() {

}

function ms_login_url_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_login_url' name='ms_config[login]' size='40' type='text' value='<?php esc_attr_e($options["login"]) ?>' placeholder="/wp-login.php" />
    <?php
}

function ms_register_url_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_register_url' name='ms_config[register]' size='40' type='text' value='<?php esc_attr_e($options["register"]) ?>' placeholder="/wp-login.php?action=register" />
    <?php
}

function ms_lostpassword_url_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_lostpassword_url' name='ms_config[lostpassword]' size='40' type='text' value='<?php esc_attr_e($options["lostpassword"]) ?>' placeholder="/wp-login.php?action=lostpassword" />
    <?php
}

function ms_logout_url_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_logout_url' name='ms_config[logout]' size='40' type='text' value='<?php esc_attr_e($options["logout"]) ?>' placeholder="/wp-login.php?action=logout" />
    <?php
}

function ms_login_redirect_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_login_redirect' name='ms_config[redirect_login]' size='40' type='text' value='<?php esc_attr_e($options["redirect_login"]) ?>' placeholder="/wp-admin/" />
    <?php
}

function ms_logout_redirect_input() {
    $options = get_option('ms_config');
    ?>
        <code><?php esc_html_e(site_url()) ?></code>
        <input id='ms_logout_redirect' name='ms_config[redirect_logout]' size='40' type='text' value='<?php esc_attr_e($options["redirect_logout"]) ?>' placeholder="/" />
    <?php
}

function ms($url = null) {
    $ms = array(
        'login' => null,
        'logout' => null,
        'register' => null,
        'lostpassword' => null
    );
    
    $config = get_option("ms_config");
    
    if(is_array($config)) {
        $ms = $config;
    }
    
    $ms = apply_filters("ms", $ms);
    
    if($url === null) {
        return $ms;
    } elseif(isset($ms[$url]) and $ms[$url]) {
        return $ms[$url];
    } else {
        return false;
    }
}

function ms_sort($a, $b) {
    if(strlen($a) < strlen($b)) {
        return 1;
    } else {
        return -1;
    }
}

function ms_init_urls() {
    foreach(ms() as $k => $rewrite) {
        if(!is_null($rewrite)) {
            add_filter($k."_url", "ms_".$k."_url");
        }
    }
    
    if(ms("redirect_login")) {
        add_filter("login_redirect", "ms_login_redirect");
    }
    
    add_filter("site_url", "ms_site_url", 10, 3);
    add_filter("wp_redirect", "ms_wp_redirect", 10, 2);
}

function ms_login_redirect($url) {
    return site_url().ms("redirect_login");
}

function ms_wp_redirect($url, $status) {
    
    $login = ms("login");
    
    if(!$login) {
        return $url;
    }
    
    $trigger = array(
        "wp-login.php?checkemail=registered",
        "wp-login.php?checkemail=confirm"
    );
    
    foreach($trigger as $t) {
        if($url == $t) {
            return str_replace("wp-login.php", site_url().$login, $url);
        }
    }
    
    return $url;
}

function ms_site_url($url, $path, $scheme = null) {

    $from = array(
        'lostpassword' => '/wp-login.php?action=lostpassword',
        'register' => '/wp-login.php?action=register',
        'logout' => '/wp-login.php?action=logout',
        'login' => '/wp-login.php',
    );
        
    foreach($from as $k => $find) {
        if(ms($k)) {
            $url = str_replace($find, ms($k), $url);
        }
    }

    return $url;
}

function ms_generate_rewrite_rules() {
	global $wp_rewrite;
    
    $rewrite = ms();    
    uasort($rewrite, "ms_sort");

	$from = array(
        'login' => 'wp-login.php',
        'lostpassword' => 'wp-login.php?action=lostpassword',
        'register' => 'wp-login.php?action=register',
		'logout' => 'wp-login.php?action=logout'
	);

    $non_wp_rules = array();
    
    // @todo: remove this
    unset($rewrite["registration"]);
    
    foreach(array_keys($from) as $k) {
        if(isset($rewrite[$k]) && !is_null($rewrite[$k])) {
            $non_wp_rules[ltrim($rewrite[$k], "/")] = $from[$k];
        }
    }
    
	$wp_rewrite->non_wp_rules = $non_wp_rules + $wp_rewrite->non_wp_rules;
}

function ms_login_url($login_url, $redirect = "") {
	$login_url = site_url( ms('login') );

	if ( !empty($redirect) ) {
		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
    }

	return $login_url;
}

function ms_register_url($url) {
    return site_url( ms('register') );
}

function ms_lostpassword_url($lostpassword_url, $redirect = "") {
	$args = array();
	if ( !empty($redirect) ) {
		$args['redirect_to'] = $redirect;
	}

	$lostpassword_url = add_query_arg( $args, site_url( ms('lostpassword') ) );
	return $lostpassword_url;
}

function ms_logout_url($redirect = "") {
	$args = array();
    
    if ( ms("redirect_logout") ) {
        $args['redirect_to'] = site_url().ms("redirect_logout");
    } elseif ( !empty($redirect) ) {
		$args['redirect_to'] = site_url();
	}

	$logout_url = add_query_arg($args, site_url( ms('logout') ));
	$logout_url = wp_nonce_url( $logout_url, 'log-out' );

	return $logout_url;
}

function ms_init_redirect() {

    if(!isset($_SERVER["REQUEST_URI"])) {
        return;
    }
    
    $file = basename($_SERVER["REQUEST_URI"]);

    if(substr($file, 0, 12) != "wp-login.php") {
        return;
    }
    
    if(isset($_GET["action"])) {
        $action = $_GET["action"];
    } else {
        $action = "login";
    }
    
    if(isset($_GET["redirect_to"])) {
        $redirect = $_GET["redirect_to"];
    } else {
        $redirect = "";
    }
    
    if($action == "login" && ms("login")) {
        $url = ms_login_url("", $redirect);
    } elseif($action == "lostpassword" && ms("lostpassword")) {
        $url = ms_lostpassword_url("", $redirect);
    } elseif($action == "register" && ms("register")) {
        $url = ms_register_url("");
    } elseif($action == "logout" && ms("logout")) {
        $url = ms_logout_url($redirect);
    } else {
        $url = null;
    }

    if($url) {
        wp_redirect($url);
        exit;
    }
}