<?php

namespace JDR\Uuid\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\IdGenerator;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidOrderedTimeGenerator implements IdGenerator
{
    /**
     * @var \Ramsey\Uuid\UuidFactory
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = clone Uuid::getFactory();

        $codec = new OrderedTimeCodec(
            $this->factory->getUuidBuilder()
        );

        $this->factory->setCodec($codec);
    }

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
        return $this->factory->uuid1();
    }
}
