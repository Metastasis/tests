<?php

class AdminPanelTest extends WebDriverTestCase
{
    public function testGetTitleFromIndexPage()
    {
        $this->driver->get($this->getTestPath(''));
        self::assertEquals('Тестирование', $this->driver->getTitle());
    }

    // public function testAuthForm()
    // {
    //     $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    //     self::assertEquals('Панель администрирования', $this->driver->getTitle());
    // }

    // public function testCreateNewSpecialityThenDeleteIt()
    // {
    //     $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    //     $this->driver->get($this->getTestPath('admin.php?spec=1&create=1'));

    //   	$elements = WebDriverBy::cssSelector('form input');
    // 		$input_elements = $this->driver->findElements($elements);

    //     $input_elements[0]->sendKeys("Ультра крутая новая специальность");
    //     $input_elements[1]->sendKeys("99999999");
    //     $input_elements[2]->sendKeys("Уруру");
    //     $input_elements[3]->click();

    //     $href_value   = 'admin.php?spec=1&deleteid=99999999';
    //     $css_selector = WebDriverBy::cssSelector('a[href="'.$href_value.'"]');
    //     $a_tag        = $this->driver->findElement($css_selector);

    //     self::assertEquals($this->getTestPath('').$href_value, $a_tag->getAttribute('href'));

    //     $a_tag_after_exec = $a_tag->click();
    //     $this->driver->switchTo()->alert()->accept();

    //     self::assertEquals(true, $a_tag_after_exec->equals($a_tag));
    // }

    // public function testCreateNewDisciplineThenDeleteIt()
    // {
    //     $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

    //     $this->driver->get($this->getTestPath('admin.php?disc=1&create=1'));

    //     $elements = WebDriverBy::cssSelector('form input');
    // 		$input_elements = $this->driver->findElements($elements);

    //     $input_elements[0]->sendKeys("Нанопротезирование");
    //     $input_elements[1]->sendKeys("НПртз");
    //     $input_elements[2]->click();

    //     $css_selector = WebDriverBy::cssSelector('tr:last-child');
    //     $tr_tag       = $this->driver->findElement($css_selector);

    //     self::assertEquals("Нанопротезирование НПртз", $tr_tag->getText());

    //     $css_selector = WebDriverBy::cssSelector('tr:last-child a');
    //     $a_tag       = $this->driver->findElement($css_selector);

    //     $a_tag_after_exec = $a_tag->click();
    //     $this->driver->switchTo()->alert()->accept();
        
    //     self::assertEquals(true, $a_tag_after_exec->equals($a_tag));
    // }

}
