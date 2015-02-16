<?php

/**
 * The base class for test cases.
 */
class WebDriverTestCase extends PHPUnit_Framework_TestCase
{

  /** @var RemoteWebDriver $driver */
  protected $driver;

  /** @var Array $user_credentials*/
  protected $user_credentials = array("user" => "admin", "password" => "admin");

    protected function setUp()
    {
        $this->driver = RemoteWebDriver::create(
          'http://localhost:4444/wd/hub',
          array(
            WebDriverCapabilityType::BROWSER_NAME => WebDriverBrowserType::FIREFOX,
              // => WebDriverBrowserType::HTMLUNIT,
          )
        );
    }

    protected function tearDown()
    {
        $this->driver->quit();
    }

  /**
   * Get the URL of the test html.
   *
   * @param $path
   * @return string
   */
  protected function getTestPath($path)
  {
      return 'http://localhost/tests/'.$path;
  }
}
