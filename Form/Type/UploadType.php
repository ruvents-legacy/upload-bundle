<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Form\Type;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadType extends AbstractType implements DataMapperInterface
{
    private $entity;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
            ])
            ->setDataMapper($this);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AbstractUpload::class,
                'empty_data' => null,
                'error_bubbling' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface $fileForm */
        $fileForm = iterator_to_array($forms)['file'];

        if (!$fileForm->isEmpty() && $fileForm->isSubmitted() && $fileForm->isSynchronized()) {
            $data = $this->createUpload($fileForm->getData());
        }
    }

    protected function createUpload($file): AbstractUpload
    {
        $class = $this->entity;

        return new $class($file);
    }
}
