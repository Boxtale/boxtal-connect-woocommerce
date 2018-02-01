<?php
/**
 * Bootstrap for unit tests.
 *
 * Enables autoload and php version compatibility
 *
 * @package Boxtal\Test
 */

if ( ! class_exists( 'PHPUnit_Framework_TestCase' ) && class_exists( 'PHPUnit\Framework\TestCase' ) ) {
	class_alias( 'PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase' );
}

require 'vendor/autoload.php';
