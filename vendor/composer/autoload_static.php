<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit012baa13f072c3dc70c727c2e780966c
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit012baa13f072c3dc70c727c2e780966c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit012baa13f072c3dc70c727c2e780966c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit012baa13f072c3dc70c727c2e780966c::$classMap;

        }, null, ClassLoader::class);
    }
}
