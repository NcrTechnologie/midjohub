<?php

namespace App\Observers;

use App\Models\Wallet;

class WalletObserver
{

    public function creating(Wallet $model)
    {

        $currentRouteName = \Route::currentRouteName();
        if ($currentRouteName == 'wallet.topup.callback') {
            // logger("updating ==> from callback");
            $oldBalance = $model->getOriginal('balance');
            $newBalance = $model->balance;
            $balanceDif = $newBalance - $oldBalance;
            //
            if ($balanceDif > 0) {
                $walletTopupPercentage = setting('walletTopupPercentage', 100);
                $topupAmount = ($walletTopupPercentage / 100) * $balanceDif;
                $model->balance -= $balanceDif;
                $model->balance += $topupAmount;
                // logger("Wallet data", [$oldBalance, $newBalance, $balanceDif, $topupAmount]);
            }
        }
    }

    public function updating(Wallet $model)
    {

        $currentRouteName = \Route::currentRouteName();
        if ($currentRouteName == 'wallet.topup.callback') {
            // logger("updating ==> from callback");
            $oldBalance = $model->getOriginal('balance');
            $newBalance = $model->balance;
            $balanceDif = $newBalance - $oldBalance;
            //
            if ($balanceDif > 0) {
                $walletTopupPercentage = setting('walletTopupPercentage', 100);
                $topupAmount = ($walletTopupPercentage / 100) * $balanceDif;
                $model->balance -= $balanceDif;
                $model->balance += $topupAmount;
                // logger("Wallet data", [$oldBalance, $newBalance, $balanceDif, $topupAmount]);
            }
        }
    }
}
