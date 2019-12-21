<?php
/**
 * This script will allow you to enable or disable WORDPRESS maintenance mode via webhooks.
 * 
 * This is not an official Wordpress script file, so use it at your own risk.
 * Copy and paste this file at the root of Wordpress (file name "wp-maintenance-webhook.php").
 * 
 * Access to this webhook will required basic HTML authentication. The credentials are defined as
 * global constants in this file.
 * 
 * @author 10 Quality <info@10quality.com>
 * @license MIT
 * @package wp-maintenance-webhook
 * @version 1.0.0
 */

/**
 * File authentication credentials.
 * @since 1.0.0
 */
define( 'HTTP_USER', 'YOUR USER NAME HERE' );
define( 'HTTP_PASSWORD', 'YOUR PASSWORD HERE' );
define( 'ABSPATH', __DIR__ );
define( 'FS_CHMOD_FILE', 0755 );

/**
 * Webhook class listener / handler.
 * @since 1.0.0
 */
class WebhookHandler
{
    /**
     * Flag that indicates if webhook endpoint has been authenticated.
     * @since 1.0.0
     * 
     * @var bool
     */
    protected $has_auth = false;
    /**
     * Init handler.
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->authenticate();
        if ( $this->has_auth )
            $this->listener();
    }
    /**
     * Webhook "enable_maintenance" handler.
     * @since 1.0.0
     */
    protected function webhook_enable_maintenance()
    {
        $file = ABSPATH . '/.maintenance';
        $maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
        if ( file_exists( $file ) )
            unlink( $file );
        file_put_contents( $file, $maintenance_string );
        chmod( $file, FS_CHMOD_FILE );
    }
    /**
     * Webhook "disable_maintenance" hadler.
     * @since 1.0.0
     */
    protected function webhook_disable_maintenance()
    {
        $file = ABSPATH . '/.maintenance';
        if ( file_exists( $file ) )
            unlink( $file );
    }
    /**
     * Listens to webhook requests.
     * @since 1.0.0
     */
    private function listener()
    {
        $webhook = $this->input( 'webhook' );
        $webhooks = ['enable_maintenance', 'disable_maintenance'];
        if ( ! in_array( $webhook, $webhooks ) ) {
            header( 'HTTP/1.0 401 Unauthorized' );
            exit;
        }
        $this->{'webhook_' . $webhook}();
    }
    /**
     * Checks HTTP authentication
     * @since 1.0.0
     */
    private function authenticate()
    {
        $auth = $this->input( 'auth', ':' );
        list( $user, $password ) = explode( ':', strpos( $auth, ':' ) !== false ? $auth : ( $auth . ':' ) );
        if ( ! isset( $user )
            || ! isset( $password )
            || empty( $user )
            || empty( $password )
            || $user !== HTTP_USER
            || $password !== HTTP_PASSWORD
        ) {
            header( 'HTTP/1.0 401 Unauthorized' );
            exit;
        } else {
            $this->has_auth = true;
        }
    }
    /**
     * Retrieves a value form query string.
     * @since 1.0.0
     * 
     * @param string $key
     * @param string $default
     * 
     * @return string
     */
    private function input( $key, $default = null )
    {
        if ( array_key_exists( $key, $_GET ) ) {
            return preg_replace( '/[\<\?\>\.\\\[\]\{\}\'\"]/', '', strip_tags( trim( $_GET[$key] ) ) );
        }
        return $default;
    }
}

new WebhookHandler();