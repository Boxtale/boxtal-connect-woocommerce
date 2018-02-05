<?php
if (!class_exists('BW_Autoloader')) {
    /**
     * Generic autoloader for classes named in WordPress coding style.
     */
    class BW_Autoloader
    {
        public $src_dir;

        function __construct($dir = '')
        {
            $this->src_dir = $this->get_src_dir();
            if (!empty($dir)) {
                $this->dir = $dir;
            }
            spl_autoload_register(array($this, 'spl_autoload_register'));
        }

        function spl_autoload_register($class_name)
        {
            $class_path = $this->dir . '/class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';
            if (file_exists($class_path)) {
                include $class_path;
            }
        }

        function get_src_dir() {
            return dirname(__DIR__) . '/src';
        }
    }
}