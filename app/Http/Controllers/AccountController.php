<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccountController extends Controller
{

    /**
     * Add user account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function Addaccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'payment' => 'required',
            'billing_address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $InsertData = array(
            'user_id' => $input['user_id'],
            "payment" => $input['payment'],
            "billing_address" => $input['billing_address'],
            "create_datetime" => date('Y-m-d H:i:s')
        );

        $AccountId = DB::table('account')->insertGetId($InsertData);

        if ($AccountId) {
            $company = DB::table('account')->select('id', 'payment', 'billing_address', 'account_api')->where('id', $AccountId)->get();
            return response()->json(['status' => $AccountId, 'data' => $company, 'message' => 'Account created successfully.'], 200);
        } else {
            return response()->json(['status' => $AccountId, 'data' => $company, 'message' => 'Account cannot be created, Please try again after sometime.'], 401);
        }
    }

    /**
     *get user wise account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetUserWiseAccountDetails($id)
    {
        $Account = DB::table('account')->select('id', 'payment', 'billing_address', 'account_api')->where('user_id', $id)->get();

        if (count($Account) == 0) {
            return response()->json(['status' => 0, 'data' => $Account, 'message' => 'Account details cannot be found.'], 400);
        } else {
            return response()->json(['status' => 1, 'data' => $Account, 'message' => 'Account details get successfully.'], 200);
        }
    }

    /**
     * update user Account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAccount(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'payment' => 'required',
            'billing_address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $updateData = array(
            'user_id' => $input['user_id'],
            "payment" => $input['payment'],
            "billing_address" => $input['billing_address'],
            "update_datetime" => date('Y-m-d H:i:s')
        );
        $AccountId = DB::table('account')->where('id', $id)->update($updateData);

        if ($AccountId) {
            $Account = DB::table('account')->select('id', 'payment', 'billing_address', 'account_api')->where('id', $id)->get();
            return response()->json(['status' => $AccountId, 'data' => $Account, 'message' => 'Account update successfully.'], 200);
        } else {
            return response()->json(['status' => $AccountId, 'data' => $Account, 'message' => 'Account cannot be updated, Please try again after sometime.'], 400);
        }
        
    }

    /**
     * Delete user account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function DeleteAccount($id)
    {
        $Account = DB::table('account')->where('id', $id)->delete();

        if ($Account) {
            return response()->json(['status' => $Account, 'message' => 'Account deleted successfully.'], 200);
        } else {
            return response()->json(['status' => $Account, 'message' => 'Account cannot be deleted, Please try again after sometime.'], 400);
        }
    }

}
