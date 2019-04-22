<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit38f1ddc0edafbcdb8ef177a0d548dedc
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpAmqpLib\\' => 11,
            'Pheanstalk\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpAmqpLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-amqplib/php-amqplib/PhpAmqpLib',
        ),
        'Pheanstalk\\' => 
        array (
            0 => __DIR__ . '/..' . '/pda/pheanstalk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit38f1ddc0edafbcdb8ef177a0d548dedc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit38f1ddc0edafbcdb8ef177a0d548dedc::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
