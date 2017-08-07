<?php

namespace Ruvents\UploadBundle\Form;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractUploadType extends AbstractType implements DataMapperInterface
{
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

        if (!$fileForm->isEmpty()
            && $fileForm->isSubmitted()
            && $fileForm->isSynchronized()
        ) {
            $data = $this->createUpload($fileForm->getData());
        }
    }

    /**
     * @param string|File $file
     *
     * @return AbstractUpload
     */
    abstract protected function createUpload($file);
}
