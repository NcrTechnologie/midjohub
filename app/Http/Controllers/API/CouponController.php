<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUser;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponController extends Controller
{

    public function index(Request $request)
    {
        return Coupon::get();
    }

    public function show(Request $request, $code)
    {

        try {

            $coupon =  Coupon::with('products', 'vendors')->where('code', "=", $code)->active()->first();
            if (empty($coupon)) {
                return response()->json([
                    "message" => __("No Coupon Found")
                ], 400);
            } else if ($coupon->expires_on < Carbon::now()) {
                return response()->json([
                    "message" => __("Coupon has exipred")
                ], 400);
            }

            //check times used 
            if (!empty($coupon)) {
                $usedTimes = CouponUser::where('coupon_id', $coupon->id)
                    ->where('user_id', auth('api')->user()->id)
                    ->count();
                //
                if ($usedTimes >= $coupon->times) {
                    throw new \Exception(__("You have exceeded number of use"), 1);
                }
            }
            return response()->json($coupon, 200);
        } catch (\Exception $ex) {
            logger("coupon error", [$ex]);
            return response()->json([
                "message" => $ex->getMessage() ?? __("No Coupon Found")
            ], 400);
        }
    }
}
