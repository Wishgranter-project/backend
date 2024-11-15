<?php

namespace WishgranterProject\Backend\Collection\Controller;

use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\DescriptiveManager\PlaylistManager;

/**
 * Collection controller..
 */
abstract class CollectionController extends ControllerBase
{
    /**
     * The playlist manager.
     *
     * @var WishgranterProject\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * The describer service.
     *
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * Constructor.
     *
     * @param WishgranterProject\DescriptiveManager\PlaylistManager $playlistManager
     *   The playlist manager.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
     */
    public function __construct(PlaylistManager $playlistManager, Describer $describer)
    {
        $this->playlistManager = $playlistManager;
        $this->describer       = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        $called = get_called_class();
        return new $called(
            $servicesManager->get('playlistManager'),
            $servicesManager->get('describer')
        );
    }
}
