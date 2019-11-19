<?php

namespace JDR\Uuid\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\AbstractIdGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidGenerator extends AbstractIdGenerator
{
    /**
     * Generates an identifier for an entity.
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager
     * @param object                                $document
     *
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Exception
     */
    public function generate(DocumentManager $documentManager, object $document): UuidInterface
    {
        return Uuid::uuid4();
    }
}
