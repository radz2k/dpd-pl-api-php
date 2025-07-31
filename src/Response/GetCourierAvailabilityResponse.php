<?php

namespace radz2k\Dpd\Response;

use radz2k\Dpd\Objects\CourierAvailability;
use radz2k\Dpd\Soap\Types\GetCourierOrderAvailabilityV1Response;

class GetCourierAvailabilityResponse
{
    /**
     * @var CourierAvailability[]
     */
    private $ranges;

    /**
     * GetCourierAvailabilityResponse constructor.
     *
     * @param CourierAvailability[] $ranges
     */
    public function __construct(array $ranges)
    {
        $this->ranges = $ranges;
    }

    /**
     * @param GetCourierOrderAvailabilityV1Response $response
     *
     * @return GetCourierAvailabilityResponse
     */
    public static function from(GetCourierOrderAvailabilityV1Response $response): GetCourierAvailabilityResponse
    {
        $ranges = $response->getReturn()->getRanges();
        $availabilityRanges = [];
        foreach ($ranges as $range) {
            if (null !== $range->getOffset() && null !== $range->getRange()) {
                $availabilityRanges[] = new CourierAvailability($range->getOffset(), $range->getRange());
            }
        }

        return new static($availabilityRanges);
    }

    /**
     * @return CourierAvailability[]
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }
}
