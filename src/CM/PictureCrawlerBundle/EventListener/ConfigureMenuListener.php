<?php

namespace PictureCrawlerBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;
use Unicorn\Bundle\UserBundle\Event\ConfigureMainMenuLoggedEvent;

class ConfigureMenuListener
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Permet d'ajouter des entrées dans le menu principal
     * @param ConfigureMainMenuLoggedEvent $event
     */
    public function onMainMenuLoggedConfigure(ConfigureMainMenuLoggedEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('PictureCrawler', ['label' => 'Récupérer les images'])
            ->setAttribute('dropdown', true);

        $menu['PictureCrawler']->addChild('L\'Avant Gardiste', ['route' => 'picture_crawler_index']);
        $menu['PictureCrawler']->addChild('Cadeau Maestro', ['route' => 'picture_crawler_cm_index']);

    }

    
}