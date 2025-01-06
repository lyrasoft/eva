<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\CommentService;
use Lyrasoft\Luna\Attributes\Author;
use Lyrasoft\Luna\Attributes\Modifier;
use Unicorn\Enum\BasicState;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\DateTime\ServerTimeCast;
use Windwalker\ORM\Attributes\AutoIncrement;
use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\CreatedTime;
use Windwalker\ORM\Attributes\CurrentTime;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\Cast\JsonCast;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Event\AfterDeleteEvent;
use Windwalker\ORM\Event\EnergizeEvent;
use Windwalker\ORM\Metadata\EntityMetadata;

use function Windwalker\unwrap_enum;

#[Table('comments', 'comment')]
#[\AllowDynamicProperties]
class Comment implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('parent_id')]
    protected int $parentId = 0;

    #[Column('target_id')]
    protected int $targetId = 0;

    #[Column('user_id')]
    protected int $userId = 0;

    #[Column('type')]
    protected string $type = '';

    #[Column('title')]
    protected string $title = '';

    #[Column('content')]
    protected string $content = '';

    #[Column('avatar')]
    protected string $avatar = '';

    #[Column('nickname')]
    protected string $nickname = '';

    #[Column('email')]
    protected string $email = '';

    #[Column('reply')]
    protected string $reply = '';

    #[Column('reply_user_id')]
    protected int $replyUserId = 0;

    #[Column('last_reply_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $lastReplyAt = null;

    #[Column('last_reply_id')]
    protected int $lastReplyId = 0;

    #[Column('rating')]
    protected float $rating = 0.0;

    #[Column('state')]
    #[Cast('int')]
    #[Cast(BasicState::class)]
    protected BasicState $state;

    #[Column('ordering')]
    protected int $ordering = 0;

    #[Column('created')]
    #[CastNullable(ServerTimeCast::class)]
    #[CreatedTime]
    protected ?Chronos $created = null;

    #[Column('modified')]
    #[CastNullable(ServerTimeCast::class)]
    #[CurrentTime]
    protected ?Chronos $modified = null;

    #[Column('created_by')]
    #[Author]
    protected int $createdBy = 0;

    #[Column('modified_by')]
    #[Modifier]
    protected int $modifiedBy = 0;

    #[Column('params')]
    #[Cast(JsonCast::class)]
    protected array $params = [];

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    #[EnergizeEvent]
    public static function energize(EnergizeEvent $event): void
    {
        $event->storeCallback(
            'comment.service',
            fn(CommentService $commentService) => $commentService
        );
    }

    #[AfterDeleteEvent]
    public static function afterDelete(AfterDeleteEvent $event): void
    {
        $orm = $event->getORM();
        /** @var static $item */
        $item = $event->getEntity();

        $orm->deleteWhere(static::class, ['parent_id' => $item->getId()]);
        $orm->deleteWhere(Rating::class, ['type' => 'comment', 'target_id' => $item->getId()]);
    }

    public function count(): int
    {
        /** @var CommentService $commentService */
        $commentService = $this->retrieveMeta('comment.service')();

        return $commentService->countComments(
            $this->getType(),
            $this->getTargetId(),
            $this->getParentId()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getTargetId(): int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string|\BackedEnum $type): static
    {
        $this->type = unwrap_enum($type);

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getReply(): string
    {
        return $this->reply;
    }

    public function setReply(string $reply): static
    {
        $this->reply = $reply;

        return $this;
    }

    public function getReplyUserId(): int
    {
        return $this->replyUserId;
    }

    public function setReplyUserId(int $replyUserId): static
    {
        $this->replyUserId = $replyUserId;

        return $this;
    }

    public function getState(): BasicState
    {
        return $this->state;
    }

    public function setState(int|BasicState $state): static
    {
        $this->state = BasicState::wrap($state);

        return $this;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }

    public function setOrdering(int $ordering): static
    {
        $this->ordering = $ordering;

        return $this;
    }

    public function getCreated(): ?Chronos
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface|string|null $created): static
    {
        $this->created = Chronos::tryWrap($created);

        return $this;
    }

    public function getModified(): ?Chronos
    {
        return $this->modified;
    }

    public function setModified(\DateTimeInterface|string|null $modified): static
    {
        $this->modified = Chronos::tryWrap($modified);

        return $this;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedBy(): int
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(int $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getLastReplyAt(): ?Chronos
    {
        return $this->lastReplyAt;
    }

    public function setLastReplyAt(\DateTimeInterface|string|null $lastReplyAt): static
    {
        $this->lastReplyAt = Chronos::tryWrap($lastReplyAt);

        return $this;
    }

    public function getLastReplyId(): int
    {
        return $this->lastReplyId;
    }

    public function setLastReplyId(int $lastReplyId): static
    {
        $this->lastReplyId = $lastReplyId;

        return $this;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}
