<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Download;

interface DownloadInterface
{
    public function getDownloadName(): string;
}
