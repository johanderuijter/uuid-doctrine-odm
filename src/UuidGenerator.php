<?php

namespace JDR\Uuid\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\IdGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidGenerator implements IdGenerator
{
    /**
     * Generates an identifier for an entity.
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager
     * @param object $document
     *
     * @throws \Exception
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function generate(DocumentManager $documentManager, object $document): UuidInterface
    {
        return Uuid::uuid4();
    }
}
