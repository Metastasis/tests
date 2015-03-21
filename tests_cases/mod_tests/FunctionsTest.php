<?php
class FunctionsTest extends PHPUnit_Framework_TestCase
{
    public function testPrepearWritedAnswerShouldBeOk()
    {
        $ResultString = prepear_writed_answer("<Test*Str>");
		$this->assertEquals('&#060;Test&#042;Str&#062;', $ResultString);
    }
	public function testPrepearReadedAnswerShouldBeOk()
    {
        $ResultString = prepear_readed_answer("[b][/b][i][/i][s][/s]");
		$this->assertEquals('<b></b><i></i><s></s>', $ResultString);
    }
	public function testMode_EncoderShouldBeOk()
    {
        $test_mode = array();
		$this->assertEmpty($test_mode);
		$test_mode['test_active'] = "ON";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "ON";
		$this->assertEquals(3, count($test_mode));
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_ON,DATE_ON,PASS_ON', $ResultString);
		$test_mode['test_active'] = "OFF";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "ON";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_OFF,DATE_ON,PASS_ON', $ResultString);
		$test_mode['test_active'] = "LOCK";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "ON";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_LOCK,DATE_ON,PASS_ON', $ResultString);
		$test_mode['test_active'] = "ON";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "ON";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_ON,DATE_OFF,PASS_ON', $ResultString);
		$test_mode['test_active'] = "OFF";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "ON";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_OFF,DATE_OFF,PASS_ON', $ResultString);
		$test_mode['test_active'] = "LOCK";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "ON";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_LOCK,DATE_OFF,PASS_ON', $ResultString);
		$test_mode['test_active'] = "ON";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_ON,DATE_ON,PASS_OFF', $ResultString);
		$test_mode['test_active'] = "OFF";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_OFF,DATE_ON,PASS_OFF', $ResultString);
		$test_mode['test_active'] = "LOCK";
		$test_mode['select_date'] = "ON";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_LOCK,DATE_ON,PASS_OFF', $ResultString);
		$test_mode['test_active'] = "ON";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_ON,DATE_OFF,PASS_OFF', $ResultString);
		$test_mode['test_active'] = "OFF";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_OFF,DATE_OFF,PASS_OFF', $ResultString);
		$test_mode['test_active'] = "LOCK";
		$test_mode['select_date'] = "OFF";
		$test_mode['select_pass'] = "OFF";
		$ResultString = test_mode_encoder($test_mode);
		$this->assertEquals('ACTIVE_LOCK,DATE_OFF,PASS_OFF', $ResultString);
		return $ResultString;
    }
	/**
     * @depends testMode_EncoderShouldBeOk
     */
	public function testModeDecoderShouldBeOk($ResultString)
    {
        $arr = test_mode_decoder($ResultString);
		$this->assertEquals(3, count($arr));
		$this->assertEquals('PASS_OFF', $arr[count($arr) - 1]);
		$this->assertEquals('DATE_OFF', $arr[count($arr) - 2]);
		$this->assertEquals('ACTIVE_LOCK', $arr[count($arr) - 3]);
    }
	public function testConvertDataForLogShouldBeOk()
    {
        $ResultString = convert_data_for_log('2007-12-15 23:50:26');
		$this->assertEquals('15-12-2007 23:50:26', $ResultString);
    }
}
?>