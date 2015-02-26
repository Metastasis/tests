<?php

class AdminTestsListTest extends WebDriverTestCase
{
    public function testCreateNewTest()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php?create=1'));

        $elements = WebDriverBy::cssSelector('form input');
        $input_elements = $this->driver->findElements($elements);

        $textarea_name = WebDriverBy::name('test_name');
        $textarea = $this->driver->findElement($textarea_name);
        $textarea->sendKeys("Простой тест");

        $input_elements[0]->click();
        $input_elements[3]->sendKeys("TESTGROUP");
        $input_elements[6]->click();
        $input_elements[8]->sendKeys("test");
        $input_elements[13]->sendKeys("3");

        $input_elements[18]->click();

        $xpath = WebDriverBy::xpath('//td[text()="Простой тест"]');
        $td_tag = $this->driver->findElement($xpath);

        self::assertEquals("Простой тест", $td_tag->getText());
    }

    public function testFillAFilterThenResetIt()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php?filter=1'));

        $elements = WebDriverBy::name('filter_by_name');
        $filter = $this->driver->findElement($elements);

        $filter->sendKeys("Some text");

        $elements = WebDriverBy::name('reset_filter');
        $reset_button = $this->driver->findElement($elements);
        $reset_button->click();

        $xpath = WebDriverBy::xpath('//h3[text()="Список тестов в системе"]');
        $h_tag = $this->driver->findElement($xpath);

        self::assertEquals("Список тестов в системе", $h_tag->getText());
    }

    public function testFilter()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php?filter=1'));

        $elements = WebDriverBy::cssSelector('form input');
        $input_elements = $this->driver->findElements($elements);

        $input_elements[0]->click();
        $input_elements[1]->sendKeys("Простой тест");

        $input_elements[2]->click();

        $xpath = WebDriverBy::xpath('//td[text()="Простой тест"]');
        $td_tag = $this->driver->findElement($xpath);

        self::assertEquals("Простой тест", $td_tag->getText());
    }

    public function testEditSettings()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php'));

        $xpath = WebDriverBy::xpath('//td[text()="Простой тест"]/../td/a[@title="Изменить настройки теста"]');
        $a_tag = $this->driver->findElement($xpath);
        $a_tag->click();

        $elements = WebDriverBy::cssSelector('form input');
        $input_elements = $this->driver->findElements($elements);

        $input_elements[1]->click();
        $input_elements[3]->sendKeys(", ОИТ19к");

        $submit_button_name = WebDriverBy::name('accept_edit');
        $submit_button = $this->driver->findElement($submit_button_name);

        $submit_button->click();

        $xpath = WebDriverBy::xpath('//td[text()="TESTGROUP ОИТ19к"]');
        $td_tag = $this->driver->findElement($xpath);

        self::assertEquals("TESTGROUP ОИТ19к", $td_tag->getText());
    }

    public function testEditQuestionsAndAnswers()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php'));

        $xpath = WebDriverBy::xpath('//td[text()="Простой тест"]/../td/a[@title="Изменить вопросы и ответы"]');
        $a_tag = $this->driver->findElement($xpath);
        $a_tag->click();
    }

    public function testDeleteNewTest()
    {
        $this->login($this->user_credentials["user"], $this->user_credentials["password"]);

        $this->driver->get($this->getTestPath('admin/test_list.php'));

        $xpath = WebDriverBy::xpath('//td[text()="Простой тест"]/../td/a[@title="Удалить тест"]');
        $a_tag = $this->driver->findElement($xpath);

        $link = $a_tag->getAttribute("href");
        $this->driver->get($link);

        $this->driver->switchTo()->alert()->accept();
    }
}
