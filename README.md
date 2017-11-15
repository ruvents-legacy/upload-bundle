# RUVENTS Upload Bundle

This bundle provides an immutable upload entity implementation.

## Configuration

```yaml
ruvents_upload:
    # required
    # an Entity, extending from Ruvents\UploadBundle\Entity\AbstractUpload
    entity: App\Entity\Upload
    
    # defaults
    web_dir: "%kernel.project_dir%/public"
    uploads_dir: "uploads"
```

## API

```php
<?php

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Ruvents\UploadBundle\Entity\AbstractUpload;

/**
 * @ORM\Entity()
 */
class Upload extends AbstractUpload
{
}

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

## Forms

Use `Ruvents\UploadBundle\Form\Type\UploadType` with your upload entity. It is a compound form with a file field which is configured to use file's data as a constructor argument.

## Serving upload entity for downloading

```yaml
# config/routes.yaml
download:
    prefix: /download
    resource: '@RuventsUploadBundle/Resources/config/download_route.yaml'
```

```twig
<a href="{{ path('ruvents_upload_download', {path: upload.path}) }}">Download</a>
```
