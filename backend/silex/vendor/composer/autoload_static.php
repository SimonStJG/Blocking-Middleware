<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf73fc75f348f2de6c5b619a8403fa560
{
    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'S' => 
        array (
            'Symfony\\Component\\Routing\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/routing',
            ),
            'Symfony\\Component\\HttpKernel\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/http-kernel',
            ),
            'Symfony\\Component\\HttpFoundation\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/http-foundation',
            ),
            'Symfony\\Component\\EventDispatcher\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/event-dispatcher',
            ),
            'Symfony\\Component\\Debug\\' => 
            array (
                0 => __DIR__ . '/..' . '/symfony/debug',
            ),
            'Silex' => 
            array (
                0 => __DIR__ . '/..' . '/silex/silex/src',
            ),
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 
            array (
                0 => __DIR__ . '/..' . '/psr/log',
            ),
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/lib',
            ),
            'PEAR' => 
            array (
                0 => __DIR__ . '/..' . '/pear/pear_exception',
            ),
        ),
        'H' => 
        array (
            'HTTP_Request2' => 
            array (
                0 => __DIR__ . '/..' . '/pear/http_request2',
            ),
            'HTTP2' => 
            array (
                0 => __DIR__ . '/..' . '/pear/http2',
            ),
        ),
    );

    public static $classMap = array (
        'EasyPeasyICS' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
        'Net_URL2' => __DIR__ . '/..' . '/pear/net_url2/Net/URL2.php',
        'PHPMailer' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'PHPMailerOAuth' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauth.php',
        'PHPMailerOAuthGoogle' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
        'POP3' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.pop3.php',
        'SMTP' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.smtp.php',
        'SessionHandlerInterface' => __DIR__ . '/..' . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs/SessionHandlerInterface.php',
        'ntlm_sasl_client_class' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
        'phpmailerException' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitf73fc75f348f2de6c5b619a8403fa560::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitf73fc75f348f2de6c5b619a8403fa560::$classMap;

        }, null, ClassLoader::class);
    }
}
