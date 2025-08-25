<?php
require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// Configura o Selenium WebDriver
$host = 'http://localhost:4444/wd/hub'; // URL do Selenium Server
$capabilities = DesiredCapabilities::chrome(); // Ou DesiredCapabilities::firefox()
$driver = RemoteWebDriver::create($host, $capabilities);

// Acessa a página de login
$driver->get('https://admin.avec.beauty/jesscorporal/admin');

// Preenche o formulário de login
$driver->findElement(WebDriverBy::name('email'))->sendKeys('tiago@jesscorporal.com.br');
$driver->findElement(WebDriverBy::name('senha'))->sendKeys('!B8e3p2q8');

// Envia o formulário clicando no botão de login
$driver->findElement(WebDriverBy::cssSelector('button.btn-login'))->click();

// Aguarda até que a URL contenha '/admin/agenda', indicando que o login foi bem-sucedido
$driver->wait(10, 500)->until(
    WebDriverExpectedCondition::urlContains('/admin/agenda')
);

// Acessa a página da tabela que você deseja extrair dados (se não for a página atual)
$driver->get('https://admin.avec.beauty/admin/agenda');

// Obtém o conteúdo da página
$pageSource = $driver->getPageSource();

// Fecha o navegador
$driver->quit();

// Processa os dados da página conforme necessário
// Por exemplo, usar DOMDocument para parsear o HTML
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($pageSource);
libxml_clear_errors();

$xpath = new DOMXPath($dom);


?>
