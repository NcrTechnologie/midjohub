<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\FirebaseAuthTrait;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Value\Provider;

class SocialLoginController extends Controller
{

    //traits
    use FirebaseAuthTrait;

    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'firebase_id_token' => 'required',
            ],
            $messages = [
                'email.exists' => __('Email not associated with any account'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => $this->readalbeError($validator),
            ], 401);
        }

        //        
        $user = User::where('email', 'like', '%' . $request->email . '')->first();

        //verify firebase token
        try {


            if (!empty($request->firebase_id_token)) {

                if ($request->provider == "google") {
                    $firebaseUser = $this->getFirebaseAuth()->signInWithGoogleIdToken($request->firebase_id_token);
                    $firebaseUserEmail = $firebaseUser->data()["email"];
                } else if ($request->provider == "facebook") {
                    $firebaseUser = $this->getFirebaseAuth()->signInWithFacebookAccessToken($request->firebase_id_token);
                    $firebaseUserEmail = $firebaseUser->data()["email"];
                    //TODO
                } else if ($request->provider == "apple") {
                    try {
                        $firebaseUser = $this->getFirebaseAuth()->signInWithAppleIdToken($request->firebase_id_token, $request->nonce);
                        $firebaseUserEmail = $firebaseUser->data()["email"];
                    } catch (\Exception $ex) {
                        // logger("Apple login error", [$ex]);
                        $signInResult = $this->getFirebaseAuth()->signInAsUser($request->uid);
                        $firebaseUser = $this->verifyFirebaseIDToken($signInResult->data()['idToken']);
                        logger("Apple login error", [$signInResult->data(), $firebaseUser]);
                        $firebaseUserEmail = $firebaseUser->email;
                    }
                }


                //if there is no user account, then create new account base on the data
                if (empty($user) && (bool) setting('auto_create_social_account', 0)) {
                    //
                    try {
                        \DB::beginTransaction();

                        $user = new User();

                        if ($request->provider == "apple") {
                            $fbUserData = $firebaseUser;
                            $user->name = $fbUserData->displayName;
                            $user->phone = $fbUserData->phoneNumber ?? null;
                        } else {
                            $fbUserData = $firebaseUser->data() ?? $firebaseUser;
                            $user->name = $fbUserData["fullName"] ?? $fbUserData["name"] ?? $fbUserData["displayName"];
                            $user->phone = $fbUserData['phone'] ?? null;
                        }

                        $user->email = $firebaseUserEmail;
                        $user->country_code = "";
                        $user->password = \Hash::make(\Str::random(8));
                        $user->is_active = true;
                        $user->save();
                        $user->syncRoles("client");
                        \DB::commit();
                    } catch (\Expection $ex) {
                        \DB::rollback();
                        logger("Social login error", [$ex]);
                    }
                }


                //verify that the token belongs to the right user
                if ($user != null && $firebaseUserEmail == ($user->email ?? '')) {
                    $authController = new AuthController();
                    return $authController->authObject($user);
                } else {
                    return response()->json([
                        "message" => __("Invalid credentials. Please check your phone and try again"),
                    ], 400);
                }
            } else {
                //verify that the token belongs to the right user
                return response()->json([
                    "message" => __("Invalid Account"),
                ], 200);
            }
        } catch (\Expection $ex) {
            return response()->json([
                "message" => $ex->getMessage(),
            ], 400);
        }
    }
}
