<?php

declare(strict_types=1);

use Lyrasoft\EventBooking\Entity\EventAttend;
use Lyrasoft\EventBooking\Entity\EventOrder;
use Lyrasoft\EventBooking\EventBookingPackage;
use Lyrasoft\EventBooking\Payment\TransferPayment;
use Lyrasoft\Sequence\Service\SequenceService;
use Lyrasoft\Toolkit\Encode\BaseConvert;

use function EventBooking\priceFormat;

return include EventBookingPackage::path('etc/event-booking.php');
