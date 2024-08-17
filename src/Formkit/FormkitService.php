<?php

declare(strict_types=1);

namespace App\Formkit;

use App\Entity\Formkit;
use App\FormkitPackage;
use App\Formkit\Exception\FormkitUnpublishedException;
use App\Formkit\Type\AbstractFormType;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Service;
use Windwalker\DI\Container;
use Windwalker\Form\Field\AbstractField;
use Windwalker\Form\Form;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\collect;

#[Service]
class FormkitService
{
    use InstanceCacheTrait;

    public function __construct(
        protected Container $container,
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

        return $this->container->newInstance($className, [$data]);
    }

    /**
     * render
     *
     * @param  int    $id
     * @param  array  $options
     *
     * @return  string
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @since  __DEPLOY_VERSION__
     */
    public function render(int $id, array $options = []): string
    {
        /**
         * @var Data       $formset
         * @var Collection $fields
         * @var Form       $form
         */
        [$formset, $fields, $form] = $this->getFormkitMeta($id, $options);

        $formsetService = $this;

        return $this->rendererService->render(
            '_widget.formkit.formkit',
            compact(
                'id',
                'options',
                'fields',
                'formset',
                'formsetService',
                'form'
            ),
        );
    }

    /**
     * @param  int    $id
     * @param  array  $options
     *
     * @return  array{ 0: Formkit, 1: Collection<AbstractField>, 2: Form }
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \Windwalker\DI\Exception\DependencyResolutionException
     */
    public function getFormkitMeta(int $id, array $options = []): array
    {
        return $this->once(
            'formkit.meta.' . $id,
            function () use ($id, $options) {
                $formkit = $this->orm->mustFindOne(Formkit::class, $id);

                // Check published
                $up = $formkit->getPublishUp();
                $down = $formkit->getPublishDown();

                if ($up !== null && $up->isFuture()) {
                    throw new FormkitUnpublishedException('Formkit unpublished');
                }

                if ($down !== null && $down->isPast()) {
                    throw new FormkitUnpublishedException('Formkit end published');
                }

                $fields = $formkit->getContent();
                $formFactory = $this->container->get(FormFactory::class);
                $form = $formFactory->create();
                $form->setNamespace($options['control'] ?? 'formkit');

                $fields = $fields->map(
                    function (array $field) use ($form) {
                        $fieldInstance = $this->getFormInstance($field['type'], $field);

                        $form->addField($fieldInstance->getFormField())
                            ->required((bool) $field['required'])
                            ->help($field['description'])
                            ->setAttribute('id', 'input-' . $field['uid'])
                            ->set('uid', $field['uid']);

                        return $fieldInstance;
                    }
                );

                // $form->add('catpcha', CaptchaField::class)
                //     ->autoValidate(true)
                //     ->jsVerify(true);

                return [$formkit, $fields, $form];
            }
        );
    }

    public function getForm(int $id, array $options = []): Form
    {
        return $this->getFormkitMeta($id, $options)[2];
    }

    /**
     * @param  int    $id
     * @param  array  $options
     *
     * @return  Collection<AbstractField>
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \Windwalker\DI\Exception\DependencyResolutionException
     */
    public function getFields(int $id, array $options = []): Collection
    {
        return $this->getFormkitMeta($id, $options)[1];
    }

    /**
     * getFormattedContent
     *
     * @param  int    $formsetId
     * @param  array  $rawContent
     *
     * @return  array
     *
     * @throws \Psr\Cache\InvalidArgumentException
     * @since  __DEPLOY_VERSION__
     */
    public function getFormattedContent(int $formsetId, array $rawContent): array
    {
        /** @var Collection|AbstractFormType[] $fields */
        $fields = $this->getFields($formsetId);

        $content = [];

        foreach ($fields as $field) {
            $content = $field->prepareView($content, $rawContent);
        }

        return $content;
    }
}
