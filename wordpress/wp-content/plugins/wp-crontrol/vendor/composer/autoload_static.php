<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4c49856ac3207dfc106ca85aa82682e6
{
    public static $classMap = array (
        'Crontrol\\Event\\Table' => __DIR__ . '/../..' . '/src/event-list-table.php',
        'Crontrol\\Request' => __DIR__ . '/../..' . '/src/request.php',
        'Crontrol\\Schedule_List_Table' => __DIR__ . '/../..' . '/src/schedule-list-table.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit4c49856ac3207dfc106ca85aa82682e6::$classMap;

        }, null, ClassLoader::class);
    }
}
