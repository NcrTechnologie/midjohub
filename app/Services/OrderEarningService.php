<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Earned;
use App\Models\Earning;
use App\Models\Remittance;
use App\Models\Wallet;
use App\Models\User;

class OrderEarningService
{
    public function __constuct()
    {
        //
    }


    public function updateEarning($order)
    {

        //'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if (in_array($order->status, ["delivered", "successful"])) {

            try {

                \DB::beginTransaction();

                $isCashOrder = $order->payment_method->slug == "cash";

                //only if online or driver wallet
                $enableDriverWallet = (bool) setting('enableDriverWallet', "0");
                $vendorEarningEnabled = (bool) setting('vendorEarningEnabled', "0");
                $generalVendorCommission = setting('vendorsCommission', "0");
                $generalDriverCommission = setting('driversCommission', "0");
                //update vendor earning 
                if ($order->vendor != null) {
                    // 
                    $earned = Earned::where(
                        [
                            'vendor_id' => $order->vendor_id,
                            'order_id' => $order->id,
                        ]
                    )->first();

                    if (empty($earned)) {
                        //get/create vendor earning record
                        $earning = Earning::firstOrCreate(
                            ['vendor_id' => $order->vendor_id],
                            ['amount' => 0]
                        );
                        //get vendor commission
                        $vendorCommission = $order->vendor->commission;
                        if (empty($vendorCommission)) {
                            $vendorCommission = $generalVendorCommission;
                        }
                        //get system commission in amount from the order subtotal
                        $systemCommission = ($vendorCommission / 100) * $order->sub_total;

                        //for booking/service order, COD
                        if ($isCashOrder && ($order->is_service || $order->is_booking)) {
                            //minus our commission from their earning
                            $earning->amount -= $systemCommission;
                            $earning->save();
                        }
                        //if order is pickup and its cash, then remove our earning from vendor account
                        else if ($isCashOrder && empty($order->delivery_address_id)) {
                            //minus our commission from their earning
                            $earning->amount -= $systemCommission;
                            $earning->save();
                        } else if (($vendorEarningEnabled || !$isCashOrder || !$enableDriverWallet)) {
                            //get the earned amount for vendor from the subTotal order
                            $earnedAmount = ($order->sub_total - $systemCommission) - ($order->discount ?? 0);
                            //add their earned amount to their earning balance
                            $earning->amount += $earnedAmount;
                            $earning->save();
                        }
                        //save admin commission data
                        Commission::updateOrCreate(
                            ['order_id' => $order->id],
                            ['vendor_commission' => $systemCommission]
                        );
                        //save earned
                        $earned = Earned::updateOrCreate(
                            ['order_id' => $order->id],
                            ['vendor_id' => $order->vendor_id]
                        );
                    }
                }



                //update driver
                try {
                    if (!empty($order->driver_id)) {

                        // 
                        $earned = Earned::where(
                            [
                                'driver_id' => $order->driver_id,
                                'order_id' => $order->id,
                            ]
                        )->first();

                        if (empty($earned)) {
                            //
                            $driverEarning = Earning::firstOrCreate(
                                ['user_id' => $order->driver_id],
                                ['amount' => 0]
                            );

                            $driver = User::find($order->driver_id);
                            //
                            if (empty($driver->commission)) {
                                $driver->commission = $generalDriverCommission;
                            }
                            //driver commission from delivery fee + tip from customer
                            if (!empty($order->taxi_order)) {
                                $earnedAmount = ($driver->commission / 100) * $order->total;
                            } else {
                                $earnedAmount = (($driver->commission / 100) * $order->delivery_fee) + $order->tip;
                            }

                            //if system is using driver wallet
                            //if its online order payment
                            if (!$isCashOrder) {
                                $driverEarning->amount = $driverEarning->amount + $earnedAmount;
                            } else  if ($enableDriverWallet) {

                                //
                                $driverWallet = $order->driver->wallet;
                                if (empty($driverWallet)) {
                                    $driverWallet = $order->driver->updateWallet(0);
                                }

                                //
                                $totalToDeduct  = $order->total - $earnedAmount;
                                $driverWallet->balance = $driverWallet->balance - $totalToDeduct;

                                //
                                $driverWallet->save();
                            } else {
                                $driverEarning->amount = $driverEarning->amount + $earnedAmount;
                                //save the record of the order that needs to be collected fromm driver
                                //log the order for driver remittance 
                                $remittance = new Remittance();
                                $remittance->user_id = $order->driver_id;
                                $remittance->order_id = $order->id;
                                if (\Schema::hasColumn('remittances', 'earned')) {
                                    $remittance->earned = $earnedAmount;
                                }
                                $remittance->save();
                            }
                            $driverEarning->save();


                            //save earned
                            $earned = Earned::updateOrCreate(
                                ['order_id' => $order->id],
                                ['driver_id' => $order->driver_id]
                            );

                            //save admin commission data
                            $systemDriverCommission = $order->delivery_fee - $earnedAmount;
                            $commission = Commission::updateOrCreate(
                                ['order_id' => $order->id],
                                ['driver_commission' => $systemDriverCommission]
                            );
                        }
                    }
                } catch (\Exception $ex) {
                    \DB::rollback();
                    logger("Driver earnig error", [$ex->getMessage()]);
                    logger("Order Code", [$order->code]);
                    logger("----------");
                }



                \DB::commit();
            } catch (\Exception $ex) {
                \DB::rollback();
                logger("earnig error", [$ex]);
            }
        }
    }
}
