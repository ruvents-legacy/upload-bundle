# RUVENTS Upload Bundle

This bundle provides an immutable upload entity implementation.

## Installation

`composer require ruvents/upload-bundle`.

## Getting started

1. Create your upload entity.
    ```php
    <?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Ruvents\UploadBundle\Entity\AbstractUpload;
    
    /**
     * @ORM\Entity()
     */
    class Upload extends AbstractUpload
    {
    }
   ```

1. Create the corresponding form type.
    ```php
    <?php
    
    namespace App\Form\Type;
    
    use App\Entity\Upload;
    use Ruvents\UploadBundle\Entity\AbstractUpload;
    use Ruvents\UploadBundle\Form\Type\AbstractUploadType;
    
    class UploadType extends AbstractUploadType
    {
        /**
         * {@inheritdoc}
         */
        protected function createUpload($file): AbstractUpload
        {
            return new Upload($file);
        }
    }
    ```

## Basic usage

```php
<?php

use App\Entity\Upload;
use Doctrine\ORM\EntityManagerInterface;

$upload = new Upload('path/to/file' /** or File instance */);

/** @var $em EntityManagerInterface */
$em->persist($upload);
$em->flush();

// path relative to {web_dir}/{uploads_dir}
$upload->getPath();
// full url based on master request or request context
$upload->getUrl();
// an instance of Symfony\Component\HttpFoundation\File\File
$upload->getFile();
// client name of a file (only if Upload was created from an UploadedFile)
// Symfony\Component\HttpFoundation\File\UploadedFile::getClientOriginalName()
$upload->getClientName();
```

## Serving upload entity for downloading

```yaml
# config/routes.yaml
download:
    prefix: /download
    resource: '@RuventsUploadBundle/Resources/config/download_route.yaml'
    defaults:
        entity: App\Entity\Upload
```

```twig
<a href="{{ path('ruvents_upload_download', {path: upload.path}) }}">Download</a>
```

## Default configuration

```yaml
ruvents_upload:
    web_dir: "%kernel.project_dir%/public"
    uploads_dir: "uploads"
```
