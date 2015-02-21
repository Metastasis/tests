<?php

class AdminUserListTest extends WebDriverTestCase
{
		public function testCreateNewUser()
    {
    	$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/user_list.php?create=true'));

  		$elements = WebDriverBy::cssSelector('form input');
  		$input_elements = $this->driver->findElements($elements);

  		$input_elements[0]->sendKeys("SUPERUSERwithUNIQUEname");
  		$input_elements[1]->sendKeys("SUPERUSERwithUNIQUEpassword");
  		$input_elements[2]->sendKeys("1");
  		$input_elements[5]->click();

  		$input_elements[6]->click();

  		$xpath = WebDriverBy::xpath('//td[text()="SUPERUSERwithUNIQUEname"]');
      $td_tag= $this->driver->findElement($xpath);

      self::assertEquals("SUPERUSERwithUNIQUEname", $td_tag->getText());
    }

    public function testFilterByName()
    {
  		$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/user_list.php?filter=1'));

  		$elements = WebDriverBy::cssSelector('form input');
  		$input_elements = $this->driver->findElements($elements);

  		$input_elements[0]->click();
  		$input_elements[1]->sendKeys("SUPERUSERwithUNIQUEname");

  		$input_elements[4]->click();

  		$xpath = WebDriverBy::xpath('//td[text()="SUPERUSERwithUNIQUEname"]');
      $td_tag = $this->driver->findElement($xpath);

      self::assertEquals("SUPERUSERwithUNIQUEname", $td_tag->getText());
    }

    public function testEditUser()
    {
  		$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/user_list.php'));

  		$xpath = WebDriverBy::xpath('//td[text()="SUPERUSERwithUNIQUEname"]/..//a[contains(@href, "editid")]');
      $a_tag = $this->driver->findElement($xpath);
      $a_tag->click();

      $elements = WebDriverBy::cssSelector('form input');
  		$input_elements = $this->driver->findElements($elements);

  		$input_elements[3]->click();
  		$input_elements[4]->sendKeys("NEWuniqueGROUP");

			$input_elements[6]->click();

			$xpath = WebDriverBy::xpath('//td[text()="NEWuniqueGROUP"]');
      $td_tag= $this->driver->findElement($xpath);

      self::assertEquals("NEWuniqueGROUP", $td_tag->getText());
    }

    public function testDeleteNewTest()
    {
    	$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/user_list.php'));

    	$xpath = WebDriverBy::xpath('//td[text()="SUPERUSERwithUNIQUEname"]/..//a[contains(@href, "deleteid")]');
      $a_tag = $this->driver->findElement($xpath);

      $link = $a_tag->getAttribute("href");
      $this->driver->get($link);

      $this->driver->switchTo()->alert()->accept();
    }
}