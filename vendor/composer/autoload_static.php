<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83b9d598f91260c29e8352ad881a21aa
{
    public static $files = array (
        '882e69c7e69bbfd5aae4f5acf55cd659' => __DIR__ . '/../..' . '/Libs/License/manager.php',
        'f70643baf7893fb4545830f2d968eecd' => __DIR__ . '/../..' . '/Inc/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FORMXTRACF7\\Libs\\License\\' => 25,
            'FORMXTRACF7\\Libs\\' => 17,
            'FORMXTRACF7\\Inc\\Admin\\' => 22,
            'FORMXTRACF7\\Inc\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FORMXTRACF7\\Libs\\License\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Libs/License',
        ),
        'FORMXTRACF7\\Libs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Libs',
        ),
        'FORMXTRACF7\\Inc\\Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Inc/Admin',
        ),
        'FORMXTRACF7\\Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83b9d598f91260c29e8352ad881a21aa::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83b9d598f91260c29e8352ad881a21aa::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit83b9d598f91260c29e8352ad881a21aa::$classMap;

        }, null, ClassLoader::class);
    }
}