<?php

declare(strict_types=1);

namespace App\Query;

use App\Entity\Playlist;
use App\Entity\Track;
use App\Entity\User;
use App\ViewModel\PlaylistViewModel;
use App\ViewModel\TrackList;
use App\ViewModel\TrackListItem;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class GetPlaylist
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(User $user, UuidInterface $playlistId): ?PlaylistViewModel
    {
        /** @var Playlist $playlist */
        $playlist = $this->em->find(Playlist::class, $playlistId);

        if ($playlist === null) {
            return null;
        }

        if (!$playlist->getUser()->equals($user)) {
            return null;
        }

        $persister = $this->em->getUnitOfWork()->getEntityPersister(Track::class);

        $tracks = $persister->loadAll(
            ['user' => $user, 'status' => Track::STATUS_REVIEWED, 'playlist' => $playlist],
            ['ordering' => 'ASC', 'dateCreated' => 'ASC']
        );
        $items = array_map([TrackListItem::class, 'fromEntity'], $tracks);

        return new PlaylistViewModel($playlist->getName(), new TrackList(...$items));
    }
}