<?php

namespace App\Traits;

trait OrderAttributeTrait
{


    //TAX
    public function getCodeOrderTypeAttribute()
    {
        if ($this->vendor_id) {
            return \Str::upper(substr($this->vendor->vendor_type->slug, 0, 1));
        }
        if ($this->taxi_order) {
            return "T";
        }
        if ($this->package_type) {
            return "P";
        }

        return  "A";
    }

    public function getFormattedCodeAttribute()
    {
        if ($this->vendor_id) {
            return \Str::upper(substr($this->vendor->vendor_type->slug, 0, 1)) . " - " . $this->code;
        }
        if ($this->taxi_order) {
            return "T - " . $this->code;
        }
        if ($this->package_type) {
            return "P - " . $this->code;
        }

        return  $this->code;
    }
}
