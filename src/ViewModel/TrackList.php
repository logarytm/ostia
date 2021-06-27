<?php

declare(strict_types=1);

namespace App\ViewModel;

use ArrayIterator;
use Countable;
use IteratorAggregate;

final class TrackList implements Countable, IteratorAggregate
{
    private array $tracks;

    public function __construct(TrackListItem ...$tracks)
    {
        $this->tracks = $tracks;
    }

    public function count(): int
    {
        return count($this->tracks);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->tracks);
    }
}
