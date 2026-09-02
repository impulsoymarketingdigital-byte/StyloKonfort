<?php
spl_autoload_register(function($class) {
    echo "Autoloading $class\n";
    eval('class ' . str_replace('\\', '_', $class) . ' {}');
    class_alias(str_replace('\\', '_', $class), $class);
});
class_alias('MyClass', 'MyAlias');
echo "Done\n";
