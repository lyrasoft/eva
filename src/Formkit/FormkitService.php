<?php

declare(strict_types=1);

namespace App\Formkit;

use App\Entity\Formkit;
use App\Formkit\Exception\FormkitUnpublishedException;
use App\Formkit\Type\AbstractFormType;
use App\FormkitPackage;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Service;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Form;
use Windwalker\ORM\ORM;
use Windwalker\Renderer\CompositeRenderer;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\collect;

#[Service]
class FormkitService
{
    use InstanceCacheTrait;

    public function __construct(
        protected ApplicationInterface $app,
        protected ORM $orm,
        protected RendererService $rendererService,
        protected FormkitPackage $formkit,
    ) {
    }

    public function getFormTypes(): Collection
    {
        return collect($this->formkit->config('types'));
    }

    /**
     * @param  string  $typeId
     *
     * @return  class-string<AbstractFormType>|null
     */
    public function getFormTypeById(string $typeId): ?string
    {
        return $this->getFormTypes()[$typeId] ?? null;
    }

    public function getFormInstance(string $type, mixed $data): AbstractFormType
    {
        $className = $this->getFormTypeById($type);

        if (!$className) {
            throw new \OutOfRangeException("FormType '$type' not found");
        }

        /** @var AbstractFormType $field */
        $field = $this->app->make($className);
        $field->setData($data);

        return $field;
    }

    public function getFieldLayout(AbstractFormType $field): string
    {
        return 'types/form-' . $field::getId();
    }

    public function render(int|Formkit $item, array $options = []): string
    {
        /**
         * @var Formkit    $item
         * @var Collection $fields
         * @var Form       $form
         */
        [$item, $fields, $form] = $this->getFormkitMeta($item, $options);

        $this->checkAvailable($item);

        $formkitService = $this;

        $id = $item->getId();

        return $this->getRenderer()->render(
            'formkit.formkit',
            compact(
                'id',
                'options',
                'fields',
                'item',
                'formkitService',
                'form'
            ),
        );
    }

    /**
     * @param  int|Formkit  $item
     * @param  array        $options
     *
     * @return  array{ 0: Formkit, 1: Collection<AbstractFormType>, 2: Form }
     *
     */
    public function getFormkitMeta(int|Formkit $item, array $options = []): array
    {
        if (!$item instanceof Formkit) {
            $item = $this->orm->mustFindOne(Formkit::class, $item);
        }
        $this->checkAvailable($item);

        $fields = collect($item->getContent());
        $formFactory = $this->app->retrieve(FormFactory::class);
        $form = $formFactory->create();
        $form->setNamespace($options['control'] ?? 'formkit');

        $fields = $fields->map(
            function (array $field) use ($form) {
                $data = collect($field);

                $fieldInstance = $this->getFormInstance($data['type'], $data);

                $form->addField($fieldInstance->toFormField($this->app))
                    ->required((bool) $data->required)
                    ->help((string) $data->description)
                    ->setAttribute('id', 'input-' . $data->uid)
                    ->set('uid', $data->uid);

                return $fieldInstance;
            }
        );

        // $form->add('catpcha', CaptchaField::class)
        //     ->autoValidate(true)
        //     ->jsVerify(true);

        return [$item, $fields, $form];
    }

    public function getForm(int|Formkit $id, array $options = []): Form
    {
        return $this->getFormkitMeta($id, $options)[2];
    }

    /**
     * @param  int|Formkit  $id
     * @param  array        $options
     *
     * @return  Collection<AbstractField>
     *
     */
    public function getFields(int|Formkit $id, array $options = []): Collection
    {
        return $this->getFormkitMeta($id, $options)[1];
    }

    /**
     * getFormattedContent
     *
     * @param  int|Formkit  $item
     * @param  array        $rawContent
     *
     * @return  array
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getFormattedContent(int|Formkit $item, array $rawContent): array
    {
        /** @var Collection|AbstractFormType[] $fields */
        $fields = $this->getFields($item);

        $content = [];

        foreach ($fields as $field) {
            $content = $field->prepareView($content, $rawContent);
        }

        return $content;
    }

    public function checkAvailable(Formkit $item): void
    {
        // Check published
        if (!$item->getState()->isPublished()) {
            throw new FormkitUnpublishedException('Not enabled');
        }

        $up = $item->getPublishUp();
        $down = $item->getPublishDown();

        if ($up !== null && $up->isFuture()) {
            throw new FormkitUnpublishedException('Formkit not publish up yet');
        }

        if ($down !== null && $down->isPast()) {
            throw new FormkitUnpublishedException('Formkit end publish');
        }
    }

    protected function getRenderer(): CompositeRenderer
    {
        return $this->once(
            'renderer',
            function () {
                /** @var CompositeRenderer $renderer */
                $renderer = $this->rendererService->createRenderer();

                $renderer->addPath(
                    FormkitPackage::path('views')
                );

                $renderer->addPath(
                    WINDWALKER_VIEWS . '/formkit'
                );

                return $renderer;
            }
        );
    }
}
