<?php

namespace Blazon\Event;

use Blazon\Model\Publication;

/**
 * Generic publication event
 */
abstract class PublicationEvent
{
    protected $publication;

    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }

    public function getPublication(): Publication
    {
        return $this->publication;
    }
}
