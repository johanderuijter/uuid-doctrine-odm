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
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param object                                $document
     *
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Exception
     */
    public function generate(DocumentManager $dm, object $document): UuidInterface
    {
        return $this->factory->uuid1();
    }
}
