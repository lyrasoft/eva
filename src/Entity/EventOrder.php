<?php

declare(strict_types=1);

namespace App\Entity;

use AllowDynamicProperties;
use App\Data\EventOrderHistories;
use App\Data\EventOrderTotals;
use App\Data\InvoiceData;
use App\Enum\EventOrderState;
use App\Enum\InvoiceType;
use Brick\Math\BigNumber;
use DateTimeInterface;
use Lyrasoft\Luna\Attributes\Author;
use Lyrasoft\Luna\Attributes\Modifier;
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
use Windwalker\ORM\Metadata\EntityMetadata;

#[Table('event_orders', 'event_order')]
#[AllowDynamicProperties]
class EventOrder implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('user_id')]
    protected int $userId = 0;

    #[Column('event_id')]
    protected int $eventId = 0;

    #[Column('stage_id')]
    protected int $stageId = 0;

    #[Column('no')]
    protected string $no = '';

    #[Column('transaction_no')]
    protected string $transactionNo = '';

    #[Column('invoice_type')]
    #[Cast(InvoiceType::class)]
    protected InvoiceType $invoiceType;

    #[Column('invoice_data')]
    #[Cast(JsonCast::class)]
    #[Cast(InvoiceData::class)]
    protected InvoiceData $invoiceData;

    #[Column('total')]
    protected float $total = 0.0;

    #[Column('totals')]
    #[Cast(JsonCast::class)]
    #[Cast(EventOrderTotals::class)]
    protected EventOrderTotals $totals;

    #[Column('name')]
    protected string $name = '';

    #[Column('email')]
    protected string $email = '';

    #[Column('nick')]
    protected string $nick = '';

    #[Column('mobile')]
    protected string $mobile = '';

    #[Column('phone')]
    protected string $phone = '';

    #[Column('address')]
    protected string $address = '';

    #[Column('details')]
    #[Cast(JsonCast::class)]
    protected array $details = [];

    #[Column('histories')]
    #[Cast(JsonCast::class)]
    #[Cast(EventOrderHistories::class)]
    protected EventOrderHistories $histories;

    #[Column('state')]
    #[Cast(EventOrderState::class)]
    protected EventOrderState $state;

    #[Column('attends')]
    protected int $attends = 0;

    #[Column('alternates')]
    protected int $alternates = 0;

    #[Column('payment')]
    protected string $payment = '';

    #[Column('payment_data')]
    #[Cast(JsonCast::class)]
    protected array $paymentData = [];

    #[Column('expired_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $expiredAt = null;

    #[Column('paid_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $paidAt = null;

    #[Column('done_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $doneAt = null;

    #[Column('screenshots')]
    #[Cast(JsonCast::class)]
    protected array $screenshots = [];

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

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

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId): static
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getStageId(): int
    {
        return $this->stageId;
    }

    public function setStageId(int $stageId): static
    {
        $this->stageId = $stageId;

        return $this;
    }

    public function getNo(): string
    {
        return $this->no;
    }

    public function setNo(string $no): static
    {
        $this->no = $no;

        return $this;
    }

    public function getTransactionNo(): string
    {
        return $this->transactionNo;
    }

    public function setTransactionNo(string $transactionNo): static
    {
        $this->transactionNo = $transactionNo;

        return $this;
    }

    public function getInvoiceType(): InvoiceType
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(int|string|InvoiceType $invoiceType): static
    {
        $this->invoiceType = InvoiceType::wrap($invoiceType);

        return $this;
    }

    public function getInvoiceData(): InvoiceData
    {
        return $this->invoiceData ??= new InvoiceData();
    }

    public function setInvoiceData(InvoiceData|array $invoiceData): static
    {
        $this->invoiceData = InvoiceData::wrap($invoiceData);

        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float|BigNumber $total): static
    {
        if ($total instanceof BigNumber) {
            $total = $total->toFloat();
        }

        $this->total = $total;

        return $this;
    }

    public function getTotals(): EventOrderTotals
    {
        return $this->totals ??= new EventOrderTotals();
    }

    public function setTotals(EventOrderTotals|array $totals): static
    {
        $this->totals = EventOrderTotals::wrap($totals);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getNick(): string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getState(): EventOrderState
    {
        return $this->state;
    }

    public function setState(string|EventOrderState $state): static
    {
        $this->state = EventOrderState::wrap($state);

        return $this;
    }

    public function getAlternates(): int
    {
        return $this->alternates;
    }

    public function setAlternates(int $alternates): static
    {
        $this->alternates = $alternates;

        return $this;
    }

    public function getPayment(): string
    {
        return $this->payment;
    }

    public function setPayment(string $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getExpiredAt(): ?Chronos
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTimeInterface|string|null $expiredAt): static
    {
        $this->expiredAt = Chronos::tryWrap($expiredAt);

        return $this;
    }

    public function getPaidAt(): ?Chronos
    {
        return $this->paidAt;
    }

    public function setPaidAt(DateTimeInterface|string|null $paidAt): static
    {
        $this->paidAt = Chronos::tryWrap($paidAt);

        return $this;
    }

    public function getDoneAt(): ?Chronos
    {
        return $this->doneAt;
    }

    public function setDoneAt(DateTimeInterface|string|null $doneAt): static
    {
        $this->doneAt = Chronos::tryWrap($doneAt);

        return $this;
    }

    public function getScreenshots(): array
    {
        return $this->screenshots;
    }

    public function setScreenshots(array $screenshots): static
    {
        $this->screenshots = $screenshots;

        return $this;
    }

    public function getCreated(): ?Chronos
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface|string|null $created): static
    {
        $this->created = Chronos::tryWrap($created);

        return $this;
    }

    public function getModified(): ?Chronos
    {
        return $this->modified;
    }

    public function setModified(DateTimeInterface|string|null $modified): static
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

    public function getHistories(): EventOrderHistories
    {
        return $this->histories ??= new EventOrderHistories();
    }

    public function setHistories(EventOrderHistories|array $histories): static
    {
        $this->histories = EventOrderHistories::wrap($histories);

        return $this;
    }

    public function getPaymentData(): array
    {
        return $this->paymentData;
    }

    public function setPaymentData(array $paymentData): static
    {
        $this->paymentData = $paymentData;

        return $this;
    }

    public function getAttends(): int
    {
        return $this->attends;
    }

    public function setAttends(int $attends): static
    {
        $this->attends = $attends;

        return $this;
    }
}
