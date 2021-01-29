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
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param object                                $document
     *
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Exception
     */
    public function generate(DocumentManager $dm, object $document): UuidInterface
    {
        return Uuid::uuid4();
    }
}
