<?php

class AdminPageTest extends WebDriverTestCase
{

    public function testGetTitleFromIndexPageShouldBeOk()
    {
        $this->driver->get($this->getTestPath(''));
        self::assertEquals('Тестирование', $this->driver->getTitle());
    }

    private function login($user, $password)
    {
        $this->driver->get($this->getTestPath(''));
      	$elements = WebDriverBy::cssSelector('form input');
    		$input_elements = $this->driver->findElements($elements);

    		$input_elements[0]->sendKeys((string) $user);
    		$input_elements[1]->sendKeys((string) $password);
    		$input_elements[2]->click();
    }

    public function testAuthFormShouldBeOk()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        self::assertEquals('Панель администрирования', $this->driver->getTitle());
    }

    public function testCreateNewSpecialityThenDeleteItShouldBeOk()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin.php?spec=1&create=1'));

      	$elements = WebDriverBy::cssSelector('form input');
    		$input_elements = $this->driver->findElements($elements);

        $input_elements[0]->sendKeys("Ультра крутая новая специальность");
        $input_elements[1]->sendKeys("99999999");
        $input_elements[2]->sendKeys("Уруру");
        $input_elements[3]->click();

        $href_value   = 'admin.php?spec=1&deleteid=99999999';
        $css_selector = WebDriverBy::cssSelector('a[href="'.$href_value.'"]');
        $a_tag        = $this->driver->findElement($css_selector);

        self::assertEquals($this->getTestPath('').$href_value, $a_tag->getAttribute('href'));

        $a_tag_after_exec = $a_tag->click();
        $this->driver->switchTo()->alert()->accept();

        self::assertEquals(true, $a_tag_after_exec->equals($a_tag));
    }

    public function testCreateNewDisciplineThenDeleteItShouldBeOk()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin.php?disc=1&create=1'));

        $elements = WebDriverBy::cssSelector('form input');
    		$input_elements = $this->driver->findElements($elements);

        $input_elements[0]->sendKeys("Нанопротезирование");
        $input_elements[1]->sendKeys("НПртз");
        $input_elements[2]->click();

        $css_selector = WebDriverBy::cssSelector('tr:last-child');
        $tr_tag       = $this->driver->findElement($css_selector);

        self::assertEquals("Нанопротезирование НПртз", $tr_tag->getText());

        $css_selector = WebDriverBy::cssSelector('tr:last-child a');
        $a_tag       = $this->driver->findElement($css_selector);

        $a_tag_after_exec = $a_tag->click();
        $this->driver->switchTo()->alert()->accept();
        
        self::assertEquals(true, $a_tag_after_exec->equals($a_tag));
    }

    public function testCreateNewTestShouldBeOk()
    {
    	//TO-DO: этот тест будет валиться до тех пор, пока не починится скролл
    	$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/test_list.php?create=1'));

    	$elements = WebDriverBy::cssSelector('form input');
  		$input_elements = $this->driver->findElements($elements);

  		$textarea_name = WebDriverBy::name('test_name');
  		$textarea = $this->driver->findElement($textarea_name);
  		$textarea->sendKeys("Простой тест");

  		$input_elements[0]->click();
  		$input_elements[3]->sendKeys("ОИТ18к, ОИТ17");
  		$input_elements[6]->click();
  		$input_elements[8]->sendKeys("test");
  		$input_elements[13]->sendKeys("3");
  		$input_elements[18]->click();

			$xpath_selector = WebDriverBy::xpath('//td[text()="Простой тест"]');
      $td_tag       = $this->driver->findElement($xpath_selector);
      print($td_tag->getText());
  		self::assertEquals("Простой тест", $td_tag->getText());
    }

    public function testDeleteNewTestShouldBeOk()
    {
    	$this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    	$this->driver->get($this->getTestPath('admin/test_list.php'));

    	$xpath_selector = WebDriverBy::xpath('//td[text()="Простой тест"]/../td/a[@title="Удалить тест"]');
      $a_tag        = $this->driver->findElement($xpath_selector);

      $link = $a_tag->getAttribute("href");
      $this->driver->get($link);

      $this->driver->switchTo()->alert()->accept();
    }
}
