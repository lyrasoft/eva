<?php

declare(strict_types=1);

namespace App\Formkit;

use App\Entity\Formkit;
use App\FormkitPackage;
use App\Formkit\Exception\FormkitUnpublishedException;
use App\Formkit\Type\AbstractFormType;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Core\Router\Exception\RouteNotFoundException;
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

        return $this->app->make($className, [$data]);
    }

    public function render(int|Formkit $item, array $options = []): string
    {
        /**
         * @var Formkit    $item
         * @var Collection $fields
         * @var Form       $form
         */
        [$item, $fields, $form] = $this->getFormkitMeta($item, $options);

        $formkitService = $this;

        $id = $item->getId();

        return $this->rendererService->render(
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
     * @return  array{ 0: Formkit, 1: Collection<AbstractField>, 2: Form }
     *
     */
    public function getFormkitMeta(int|Formkit $item, array $options = []): array
    {
        if (!$item instanceof Formkit) {
            $item = $this->orm->mustFindOne(Formkit::class, $item);
        }

        // Check published
        if (!$item->getState()->isPublished()) {
            throw new FormkitUnpublishedException();
        }

        $up = $item->getPublishUp();
        $down = $item->getPublishDown();

        if ($up !== null && $up->isFuture()) {
            throw new FormkitUnpublishedException('Formkit unpublished');
        }

        if ($down !== null && $down->isPast()) {
            throw new FormkitUnpublishedException('Formkit end published');
        }

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
