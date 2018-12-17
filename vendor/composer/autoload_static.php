<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1feb9efd68470c6692f986fb00f4e9d4
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Blog\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Blog\\' => 
        array (
            0 => '/wamp64/www/Blog',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1feb9efd68470c6692f986fb00f4e9d4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1feb9efd68470c6692f986fb00f4e9d4::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}