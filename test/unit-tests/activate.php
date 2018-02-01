<?php

/**
 * Class WC_Tests_Install.
 * @package Boxtal\Tests
 */
class BW_Test_Activate extends BW_Unit_Test_Case {

    /**
     * Test hello world.
     */
    public function test_hello_world() {
        $this->assertTrue( boxtal_woocommerce()->hello() === 'hello world' );
    }
}
