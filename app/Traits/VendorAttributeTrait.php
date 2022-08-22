<?php

namespace App\Traits;

trait VendorAttributeTrait
{


    //TAX
    public function getTaxAttribute($value)
    {
        return $value != null ? $value : (string) setting('finance.generalTax', 0);
    }

    public function getMinOrderAttribute($value)
    {
        return $value != null ? $value : setting('finance.minOrderAmount', null);
    }

    public function getMaxOrderAttribute($value)
    {
        return $value != null ? $value : setting('finance.maxOrderAmount', null);
    }
    //

    
    public function getChargePerKmAttribute($value)
    {
        return $value != null ? $value : (int) setting('finance.delivery.charge_per_km', 0);
    }

    public function getBaseDeliveryFeeAttribute($value)
    {
        return $value != null ? $value : (float) setting('finance.delivery.base_delivery_fee', 0);
    }

    public function getDeliveryFeeAttribute($value)
    {
        return $value != null ? $value : (float) setting('finance.delivery.delivery_fee', 0);
    }

    public function getDeliveryRangeAttribute($value)
    {
        return $value != null ? $value : (float) setting('finance.delivery.delivery_range', 0);
    }

    
}
