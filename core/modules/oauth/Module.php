<?php
/**
 * Phanbook : Delightfully simple forum and Q&A software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @author  Phanbook <hello@phanbook.com>
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Oauth;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phanbook\Common\Library\Events\UserLogins;
use Phanbook\Common\Library\Events\ViewListener;
use Phanbook\Common\Library\Events\DispatcherListener;

/**
 * \Phanbook\Oauth\Module
 *
 * @package Phanbook\Oauth
 */
class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module.
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $namespaces = [
            'Phanbook\Oauth\Controllers' => __DIR__ . '/controllers/',
            'Phanbook\Oauth\Forms'       => __DIR__ . '/forms/',
        ];

        $loader->registerNamespaces($namespaces, true);

        $loader->register();
    }

    /**
     * Registers services related to the module.
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        // Read configuration
        $moduleConfig = require_once __DIR__ . '/config/config.php';

        $eventsManager = $di->getShared('eventsManager');
        $eventsManager->attach('user', new UserLogins($di));

        // Tune Up the URL Component
        $url = $di->getShared('url');
        $url->setBaseUri($moduleConfig->application->baseUri);

        // Setting up the MVC Dispatcher
        $eventsManager = $di->getShared('eventsManager');
        $eventsManager->attach('dispatch:beforeException', new DispatcherListener($di));

        // Setting up the View Component
        $di->setShared(
            'view',
            function () {
                /** @var DiInterface $this */
                $view = new View();

                $view->setDI($this);
                $view->setViewsDir(__DIR__ . '/views/');

                $view->registerEngines(
                    [
                        '.volt' => $this->getShared('volt', [$view, $this])
                    ]
                );

                $view->disableLevel(
                    [
                        View::LEVEL_MAIN_LAYOUT => true,
                        View::LEVEL_LAYOUT      => true
                    ]
                );

                $eventsManager = $this->getShared('eventsManager');
                $eventsManager->attach('view:notFoundView', new ViewListener($this));

                $view->setEventsManager($eventsManager);

                return $view;
            }
        );
    }
}
