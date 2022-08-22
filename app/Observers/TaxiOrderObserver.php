<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\JobHandlerService;
use App\Traits\FirebaseAuthTrait;
use App\Traits\OrderTrait;
use App\Traits\OrderFCMTrait;

class TaxiOrderObserver
{

    use FirebaseAuthTrait, OrderTrait;
    use OrderFCMTrait;

    public function updated(Order $model)
    {

        $driver = $model->driver;
        //update driver node on firebase 
        if (!empty($driver)) {
            (new JobHandlerService())->driverDetailsJob($driver);
        }

        //
        $this->clearFirestore($model);
    }
}
