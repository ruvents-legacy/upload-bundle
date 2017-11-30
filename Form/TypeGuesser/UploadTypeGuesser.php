<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Form\TypeGuesser;

use Doctrine\Common\Persistence\ManagerRegistry;
use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess;

class UploadTypeGuesser implements FormTypeGuesserInterface
{
    private $registry;

    private $type;

    public function __construct(ManagerRegistry $registry, string $type)
    {
        $this->registry = $registry;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property)
    {
        if (!$manager = $this->registry->getManagerForClass($class)) {
            return null;
        }

        $metadata = $manager->getClassMetadata($class);

        if (!$metadata->hasAssociation($property)) {
            return null;
        }

        $targetClass = $metadata->getAssociationTargetClass($property);

        if (!is_subclass_of($targetClass, AbstractUpload::class)) {
            return null;
        }

        if ($metadata->isSingleValuedAssociation($property)) {
            return new Guess\TypeGuess($this->type, [], Guess\TypeGuess::VERY_HIGH_CONFIDENCE);
        }

        return new Guess\TypeGuess(CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => $this->type,
        ], Guess\TypeGuess::VERY_HIGH_CONFIDENCE);
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired($class, $property)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessMaxLength($class, $property)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessPattern($class, $property)
    {
        return null;
    }
}
