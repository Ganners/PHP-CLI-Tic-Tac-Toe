<?php

use \Application\App_Exception;
use \Application\Util\Input_Handler;

class Input_HandlerTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->Input_Handler = new Input_Handler();
	}

	public function testToBoardSizeArray() {
		$this->assertEquals(
			$this->Input_Handler->toBoardSizeArray('1,1'),
			array('1','1'));

		$this->assertEquals(
			$this->Input_Handler->toBoardSizeArray('3,3'),
			array('3','3'));

		$this->assertEquals(
			$this->Input_Handler->toBoardSizeArray('0,0,,,'),
			array('0','0'));
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testToBoardSizeArrayInvalidTooManyException() {
		$this->Input_Handler->toBoardSizeArray('1,2,3');
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testToBoardSizeArrayInvalidTooFewException() {
		$this->Input_Handler->toBoardSizeArray('1');
	}

	public function testToString() {
		$this->assertEquals(
			$this->Input_Handler->toString(' String '),
			'String'
			);
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testToStringInvalidException() {
		$this->Input_Handler->toString('');
	}

	public function testToBool() {

		$this->assertTrue(
			$this->Input_Handler->toBool('y'));

		$this->assertTrue(
			$this->Input_Handler->toBool('yes'));

		$this->assertFalse(
			$this->Input_Handler->toBool('n'));

		$this->assertFalse(
			$this->Input_Handler->toBool('no'));

		//test on case insensitivity
		$this->assertTrue(
			$this->Input_Handler->toBool('Y'));

		$this->assertFalse(
			$this->Input_Handler->toBool('N'));

	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testToBoolInvalidException() {
		$this->Input_Handler->toBool('Not Allowed');
	}

}