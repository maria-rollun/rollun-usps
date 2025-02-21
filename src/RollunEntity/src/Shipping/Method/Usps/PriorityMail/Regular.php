<?php
declare(strict_types=1);

namespace rollun\Entity\Shipping\Method\Usps\PriorityMail;

use rollun\Entity\Product\Item\Product;
use rollun\Entity\Shipping\Method\Usps\ShippingsAbstract;
use rollun\Entity\Shipping\ShippingRequest;

/**
 * Class Regular
 *
 * @author    Roman Ratsun <r.ratsun.rollun@gmail.com>
 *
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license   LICENSE.md New BSD License
 */
class Regular extends ShippingsAbstract
{
    /**
     * @var bool
     */
    protected $canShipDangerous = false;

    /**
     * Click_N_Shipp => ['id', 'Click_N_Shipp', 'USPS_API_Service', 'USPS_API_FirstClassMailType', 'USPS_API_Container', 'Width', 'Length', 'Height', 'Weight']
     *
     * 1) The weight limit for Priority Mail items is 70 lbs.
     * 2) The maximum size for Priority Mail items is 108 inches in combined length and girth (the length of the longest side, plus the distance around its thickest part).
     * 3) Length, Width, Height are required for accurate pricing of a rectangular package when any dimension of the item exceeds 12 inches. In addition, Girth is required for non-rectangular packages.
     */
    const USPS_BOXES
        = [
            ['PM-Regular', 'Priority Mail', 'PRIORITY COMMERCIAL', '', 'VARIABLE', 12, 12, 12, 70],
            ['PM-Large', 'Priority Mail', 'PRIORITY COMMERCIAL', '', 'RECTANGULAR', 108, 108, 108, 70],
        ];

    /**
     * Regular costs, got from https://pe.usps.com/text/dmm300/Notice123.htm#_c078
     */
    const USPS_BOXES_COSTS
        = [
            [1, 7.41, 7.41, 7.71, 7.92, 8.63, 9.09, 9.27, 9.55, 15],
            [2, 8.04, 8.04, 8.21, 8.49, 9.6, 11.19, 11.76, 12.44, 22.56],
            [3, 8.25, 8.25, 8.6, 8.97, 10.39, 13.45, 14.85, 17.2, 30.34],
            [4, 8.35, 8.35, 8.83, 9.46, 11.18, 15.55, 17.53, 19.71, 36.38],
            [5, 8.45, 8.45, 8.88, 9.78, 12.45, 17.62, 20.04, 22.71, 42.21],
            [6, 8.56, 8.56, 8.92, 9.89, 15.07, 20.14, 23.27, 26.5, 48.26],
            [7, 8.98, 8.98, 10.33, 10.38, 17.28, 22.23, 26.14, 29.68, 54.1],
            [8, 9.04, 9.04, 10.83, 12.2, 18.79, 24.34, 28.71, 33.24, 60.64],
            [9, 9.9, 9.9, 11.23, 12.7, 20.09, 26.42, 31.03, 36.87, 67.35],
            [10, 10.4, 10.4, 11.79, 12.9, 21.86, 28.75, 34.38, 40.42, 73.18],
            [11, 13.65, 13.65, 16.04, 17.09, 27.26, 35.07, 42.05, 48.84, 82.13],
            [12, 14.4, 14.4, 16.97, 19.66, 28.99, 37.99, 45.24, 52.2, 87.83],
            [13, 15.07, 15.07, 17.86, 20.51, 30.38, 40.55, 46.95, 53.94, 90.86],
            [14, 15.77, 15.77, 18.76, 21.52, 31.98, 42.67, 49.4, 56.47, 95.21],
            [15, 16.32, 16.32, 19.67, 22.49, 33.47, 44.2, 50.29, 57.87, 97.65],
            [16, 16.97, 16.97, 20.82, 23.84, 35.28, 46.95, 53.42, 61.44, 102.85],
            [17, 17.46, 17.46, 21.71, 24.9, 36.86, 49.18, 56.04, 64.55, 108.11],
            [18, 17.78, 17.78, 22.33, 25.96, 38.38, 51.62, 58.66, 67.63, 113.41],
            [19, 18.15, 18.15, 22.82, 26.52, 39.31, 53.8, 61.25, 70.69, 118.65],
            [20, 18.81, 18.81, 23.15, 27.02, 39.98, 55.12, 63.42, 73.83, 123.98],
            [21, 20.57, 20.57, 24.67, 28.61, 42.64, 57.53, 66, 76.74, 128.56],
            [22, 21.14, 21.14, 25.27, 29.48, 43.39, 57.89, 66.47, 77.57, 130],
            [23, 21.7, 21.7, 25.81, 30.13, 44.09, 58.17, 66.89, 78, 130.74],
            [24, 22.48, 22.48, 26.8, 31.7, 45.62, 59.3, 68.5, 79.78, 133.81],
            [25, 23.25, 23.25, 27.66, 33.55, 46.99, 60.1, 70.09, 81.08, 136.03],
            [26, 24.51, 24.51, 29.47, 36.79, 49.23, 61.44, 71.69, 83.46, 140.13],
            [27, 25.82, 25.82, 30.69, 38.88, 53.2, 62.2, 73.24, 86.4, 145.24],
            [28, 26.53, 26.53, 31.06, 39.91, 54.46, 62.98, 74.83, 89.46, 150.5],
            [29, 27.27, 27.27, 31.35, 40.92, 55.12, 63.95, 76.43, 91.74, 154.39],
            [30, 28, 28, 31.77, 41.82, 55.8, 65.6, 78, 93.59, 157.63],
            [31, 28.72, 28.72, 32.06, 42.44, 56.45, 66.48, 79.61, 95.41, 162],
            [32, 29.02, 29.02, 32.69, 43.1, 57.05, 67.28, 81.22, 97.26, 165.2],
            [33, 29.43, 29.43, 33.52, 44.11, 57.74, 68.49, 82.79, 98.95, 168.16],
            [34, 29.68, 29.68, 34.33, 45.17, 58.88, 70, 84.39, 100.71, 171.25],
            [35, 29.98, 29.98, 35.08, 45.78, 60.02, 71.74, 85.98, 102.36, 174.08],
            [36, 30.32, 30.32, 36.03, 46.35, 61.21, 73.42, 87.08, 104.01, 176.96],
            [37, 30.61, 30.61, 36.65, 46.98, 62.21, 75.21, 88.13, 105.64, 179.8],
            [38, 30.89, 30.89, 37.48, 47.54, 63.35, 77.16, 89.08, 107.23, 182.59],
            [39, 31.16, 31.16, 38.3, 48.06, 64.56, 78.87, 91.3, 108.82, 185.34],
            [40, 31.46, 31.46, 39.05, 48.65, 65.8, 80.05, 93.23, 110.24, 187.78],
            [41, 31.77, 31.77, 39.66, 49.14, 66.34, 81.32, 95.11, 111.76, 191.89],
            [42, 31.99, 31.99, 39.94, 49.55, 67.37, 82.66, 96.34, 113.22, 194.44],
            [43, 32.33, 32.33, 40.22, 49.97, 68.4, 84.52, 97.48, 114.6, 196.86],
            [44, 32.54, 32.54, 40.49, 50.38, 69.42, 85.78, 98.58, 115.84, 199.08],
            [45, 32.73, 32.73, 40.76, 50.81, 70.46, 86.68, 99.59, 117.24, 201.53],
            [46, 33, 33, 41.04, 51.23, 71.49, 87.59, 100.61, 118.6, 203.86],
            [47, 33.22, 33.22, 41.31, 51.64, 72.52, 88.45, 101.7, 119.85, 206.09],
            [48, 33.48, 33.48, 41.59, 52.06, 73.54, 89.52, 102.63, 121.07, 208.28],
            [49, 33.72, 33.72, 41.85, 52.48, 74.57, 90.68, 103.65, 122.25, 210.26],
            [50, 33.85, 33.85, 42.12, 52.9, 75.61, 91.88, 104.9, 123.48, 212.46],
            [51, 34.31, 34.31, 42.4, 53.29, 76.81, 93.07, 106.33, 124.58, 216.06],
            [52, 34.78, 34.78, 42.68, 53.71, 77.32, 93.92, 107.86, 126, 218.53],
            [53, 35.38, 35.38, 42.94, 54.13, 77.91, 94.67, 109.54, 127.54, 221.26],
            [54, 35.86, 35.86, 43.23, 54.54, 78.54, 95.31, 111.04, 129.26, 224.29],
            [55, 36.38, 36.38, 43.49, 54.96, 79, 96.06, 112.72, 130.93, 227.24],
            [56, 36.85, 36.85, 43.77, 55.38, 79.56, 96.66, 114.24, 132.22, 229.54],
            [57, 37.39, 37.39, 44.04, 55.8, 80, 97.36, 115.9, 133.34, 231.55],
            [58, 37.92, 37.92, 44.31, 56.21, 80.48, 97.91, 117.37, 134.4, 233.38],
            [59, 38.43, 38.43, 44.59, 56.62, 80.94, 98.43, 118.14, 135.35, 235.09],
            [60, 38.88, 38.88, 44.86, 57.03, 81.36, 98.9, 118.8, 136.29, 236.7],
            [61, 39.47, 39.47, 45.12, 57.45, 81.74, 99.42, 119.46, 138.05, 239.85],
            [62, 39.92, 39.92, 45.4, 57.86, 82.08, 99.86, 119.96, 140.17, 243.55],
            [63, 40.6, 40.6, 45.68, 58.29, 82.49, 100.4, 120.51, 142.34, 247.38],
            [64, 40.93, 40.93, 45.94, 58.7, 82.83, 100.83, 121.04, 144.44, 251.11],
            [65, 41.49, 41.49, 46.22, 59.13, 83.07, 101.11, 121.62, 146.61, 254.96],
            [66, 42, 42, 46.5, 59.53, 83.42, 101.59, 121.97, 148.67, 258.59],
            [67, 42.59, 42.59, 46.77, 60.5, 83.7, 101.9, 122.44, 150.59, 261.93],
            [68, 43.06, 43.06, 47.04, 61.23, 83.92, 103.12, 123.05, 152.13, 264.65],
            [69, 43.61, 43.61, 47.32, 61.98, 84.15, 104.3, 123.6, 153.68, 267.43],
            [70, 44.04, 44.04, 47.59, 62.92, 84.4, 105.5, 124.03, 155.28, 270.24],
        ];


    /**
     * @var bool
     */
    protected $hasDefinedCost = true;

    /**
     * @inheritDoc
     */
    public function canBeShipped(ShippingRequest $shippingRequest): bool
    {
        $item = $shippingRequest->item;
        if (!($item instanceof Product)) {
            return false;
        }

        if ($this->shortName === 'PM-Large') {
            // The weight limit for Priority Mail items is 70 lbs.
            if ($this->getWeight($shippingRequest) > 70) {
                return false;
            }

            /** @var array $dimensions */
            $dimensions = $item->getDimensionsList()[0]['dimensions']->getDimensionsRecord();

            // exit because it PM-Regular shipping
            if ($dimensions['Length'] <= 12) {
                return false;
            }

            // The maximum size for Priority Mail items is 108 inches in combined length and girth
            if (($dimensions['Girth'] + $dimensions['Length']) > 108) {
                return false;
            }

            return parent::canShipDangerousMaterials($shippingRequest);
        }

        return parent::canBeShipped($shippingRequest);
    }

    /**
     * @inheritDoc
     */
    public function getCost(ShippingRequest $shippingRequest, $shippingDataOnly = false)
    {
        if ($this->canBeShipped($shippingRequest)) {
            // prepare weight
            $weight = $this->getWeight($shippingRequest);

            foreach (self::USPS_BOXES_COSTS as $row) {
                if ($row[0] >= $weight) {
                    // get zone
                    $zone = $this->getZone($shippingRequest->getOriginationZipCode(), $shippingRequest->getDestinationZipCode());

                    return $row[$zone];
                }
            }
        }

        return 'Can not be shipped';
    }

    /**
     * @param ShippingRequest $shippingRequest
     *
     * @return float
     */
    protected function getWeight(ShippingRequest $shippingRequest): float
    {
        $weight = $shippingRequest->item->getWeight();
        if ($this->shortName === 'PM-Large') {
            $lbs = $shippingRequest->item->getVolume() / 166;
            if ($lbs > $weight) {
                $weight = $lbs;
            }
        }

        return $weight;
    }
}
