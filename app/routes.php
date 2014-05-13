<?php
use Nette\Application\Routers\Route,
	 Nette\Application\Routers\RouteList;

Route::addStyle('webalize');
Route::setStyleProperty('webalize', Route::FILTER_OUT, 'Nette\Utils\Strings::webalize');
Route::setStyleProperty('webalize', Route::FILTER_IN, 'Nette\Utils\Strings::webalize');

/* Nyní pokud něco má být webalizováno (zbaveno diakritiky atd.) stačí to 'zdědit' z webalize */
Route::addStyle('name', 'webalize');
Route::addStyle('nick', 'webalize');
Route::addStyle('id', 'webalize');
Route::addStyle('category', 'webalize');

$router = new RouteList();
$router[] = new Route('odhlasit-se[!.html]', 'Sign:logout');
$router[] = new Route('pridat-projekt[/<category>][!.html]', 'QuickAdd:default');
$router[] = new Route('konverzace/<id>', 'Messages:detail');
$router[] = new Route('zpravy[!.html]', 'Messages:default');
$router[] = new Route('projekt/<id>[-<name>][!.html]', 'Project:detail');
$router[] = new Route('projekt/upravit/<id>[!.html]', 'Project:modify');
$router[] = new Route('prace[!.html]', 'Jobs:default');
$router[] = new Route('nastaveni[!.html]', 'Settings:default');
$router[] = new Route('projekty[/<category>][!.html]', 'Projects:default');
$router[] = new Route('me-projekty[!.html]', 'Projects:mine');
$router[] = new Route('uzivatel-nenalezen[!.html]', 'User:notfound');
$router[] = new Route('uzivatel/<id>[-<nick>]', 'User:default');
$router[] = new Route('uzivatele[!.html]', 'Users:default');
$router[] = new Route('obnova-hesla/<code>[!.html]', 'Reset:default');
$router[] = new Route('notifikace[!.html]', 'Notifications:default');
$router[] = new Route('o-nas[!.html]', 'AboutUs:default');
$router[] = new Route('domu[!.html]', 'Homepage:default');
$container->router = $router;

// Setup router
$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('<presenter>[/<action>[/<id>]][!.html]', 'Homepage:default');

