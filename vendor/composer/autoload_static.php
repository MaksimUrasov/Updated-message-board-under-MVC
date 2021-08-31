<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1df846c50120a641b55bf2882fbe07e2
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'App\\Controllers\\Controller' => __DIR__ . '/../..' . '/app/Controllers/Controller.php',
        'App\\Controllers\\CreateTableController' => __DIR__ . '/../..' . '/app/Controllers/CreateTableController.php',
        'App\\Models\\CreateTableModel' => __DIR__ . '/../..' . '/app/Models/CreateTableModel.php',
        'App\\Models\\GetPdo' => __DIR__ . '/../..' . '/app/Models/GetPdo.php',
        'App\\Models\\Model' => __DIR__ . '/../..' . '/app/Models/Model.php',
        'App\\Views\\CreateTableView' => __DIR__ . '/../..' . '/app/Views/CreateTableView.php',
        'App\\Views\\OldMessage' => __DIR__ . '/../..' . '/app/Views/OldMessage.php',
        'App\\Views\\View' => __DIR__ . '/../..' . '/app/Views/View.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1df846c50120a641b55bf2882fbe07e2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1df846c50120a641b55bf2882fbe07e2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1df846c50120a641b55bf2882fbe07e2::$classMap;

        }, null, ClassLoader::class);
    }
}
