<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Psr\Http\Message\UploadedFileInterface;
use Unicorn\Aws\S3Service;
use Unicorn\Field\FileDragField;
use Unicorn\Upload\FileUploadService;
use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Http\AppRequest;
use Windwalker\Filesystem\Path;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Http\Helper\HeaderHelper;
use Windwalker\Http\Helper\UploadedFileHelper;
use Windwalker\Utilities\Contract\LanguageInterface;

use function Windwalker\uid;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormFile extends AbstractFormType
{
    public function __construct(protected FileUploadService $fileUploadService)
    {
    }

    public static function getIcon(): string
    {
        return 'far fa-upload';
    }

    public static function getTitle(): string
    {
        return '檔案上傳';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '其他';
    }

    public static function getId(): string
    {
        return 'file';
    }

    public static function getDescription(): string
    {
        return '上傳檔案的欄位';
    }

    public static function getDefaultParams(): array
    {
        return array_merge(
            parent::getDefaultParams(),
            [
                'accept' => '',
                'max' => '1',
                'max_size' => ''
            ]
        );
    }

    public function toFormField(ServiceAwareInterface $app): AbstractField
    {
        $field = $app->make(FileDragField::class)
            ->label($this->getLabel())
            ->setName($this->getLabel());

        if ($accept = trim((string) $this->data->accept)) {
            $field->accept($accept);
            $field->attr('data-accepted', $accept);
        }

        $size = (int) ($this->data->max_size ?: 10);

        if ($size) {
            $field->maxSize($size);
        }

        if ($this->data->max > 1) {
            $field->multiple(true)->maxFiles((int) $this->data->max);
        }

        return $field;
    }

    public function prepareStore(array $data, AppRequest $request, string $ns): array
    {
        $files = $request->file($ns)[$this->getLabel()];

        if ($this->data->max > 1) {
            /** @var UploadedFileInterface $file */
            foreach ($files as $i => $file) {
                $data[$this->getLabel() . '_' . ($i + 1)] = $this->upload($file);
            }
        } else {
            /** @var UploadedFileInterface $files */
            $data[$this->getLabel()] = $this->upload($files);
        }

        return $data;
    }

    public function prepareView(array $data, array $content): array
    {
        if ($this->data->max > 1) {
            foreach (range(1, $this->data->max) as $i => $file) {
                $data[$this->getLabel() . '_' . ($i + 1)] = $content[$this->getLabel() . '_' . ($i + 1)] ?? '';
            }
        } else {
            $data = parent::prepareView($data, $content);
        }

        return $data;
    }

    protected function upload(UploadedFileInterface $file): string
    {
        $filename = $file->getClientFilename();

        $contentDisposition = str_starts_with($file->getClientMediaType(), 'image')
            ? null
            : HeaderHelper::attachmentContentDisposition($filename);

        $result = $this->fileUploadService->handleFileIfUploaded(
            $file,
            'formkit/files/{year}/{month}/{day}/' . uid('file') . '.{ext}',
            [
                'options' => [
                    'ContentType' => $file->getClientMediaType(),
                    'ContentDisposition' => $contentDisposition,
                    'ACL' => S3Service::ACL_PUBLIC_READ
                ]
            ]
        );

        return (string) $result?->getUri();
    }
}
