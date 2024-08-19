<?php

declare(strict_types=1);

namespace App\Formkit\Type;

use Psr\Http\Message\UploadedFileInterface;
use Unicorn\Field\FileDragField;
use Windwalker\Core\Application\Context\AppRequestInterface;
use Windwalker\Core\Application\ServiceAwareInterface;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Filesystem\Path;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Http\Helper\HeaderHelper;
use Windwalker\Http\Helper\UploadedFileHelper;
use Windwalker\Utilities\Contract\LanguageInterface;

/**
 * The FormsetText class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FormFile extends AbstractFormType
{
    protected $s3;

    /**
     * getIcon
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getIcon(): string
    {
        return 'far fa-upload';
    }

    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getTitle(): string
    {
        return '檔案上傳';
    }

    public static function getGroup(LanguageInterface $lang): string
    {
        return '其他';
    }

    /**
     * getName
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getId(): string
    {
        return 'file';
    }

    /**
     * getDescription
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    public static function getDescription(): string
    {
        return '上傳檔案的欄位';
    }

    /**
     * getDefaultParams
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
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

    /**
     * getFormField
     *
     * @param  ServiceAwareInterface  $app  *
     *
     * @return  AbstractField
     *
     * @since  __DEPLOY_VERSION__
     */
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

    /**
     * prepareStore
     *
     * @param array                 $data
     * @param  AppRequestInterface  $request
     * @param string                $control
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function prepareStore(array $data, AppRequestInterface $request, string $control): array
    {
        $files = $request->files->get($control . '.' . $this->getLabel());

        if ($this->data->max > 1) {
            foreach ($files as $i => $file) {
                $data[$this->getLabel() . '_' . ($i + 1)] = $this->upload($file);
            }
        } else {
            $data[$this->getLabel()] = $this->upload($files);
        }

        return $data;
    }

    /**
     * prepareView
     *
     * @param array $data
     * @param array $content
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
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

    /**
     * upload
     *
     * @param UploadedFileInterface $file
     *
     * @return  string
     *
     * @since  __DEPLOY_VERSION__
     */
    protected function upload(UploadedFileInterface $file): string
    {
        if ($file->getError() === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new ValidateFailException(
                UploadedFileHelper::getUploadMessage($file->getError())
            );
        }

        $filename = $file->getClientFilename();
        $ext = Path::getExtension($filename);
        $path = 'formkit/files/' . md5(uniqid('F', true)) . '.' . $ext;

        $contentDisposition = str_starts_with($file->getClientMediaType(), 'image')
            ? null
            : HeaderHelper::attachmentContentDisposition($filename);

        return $this->s3->uploadFileData(
            $file->getStream(),
            $path,
            [
                'ContentType' => $file->getClientMediaType(),
                'ContentDisposition' => $contentDisposition,
                'ACL' => S3Service::ACL_PUBLIC_READ
            ]
        )->get('ObjectURL');
    }
}
