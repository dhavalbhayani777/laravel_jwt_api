<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Shipping;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ShippingController extends Controller
{
    /**
     * Add user shipping details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function AddShippingAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $InsertData = array(
            'user_id' => $input['user_id'],
            "sample_address" => $input['sample_address'],
            "warehouse_address" => $input['warehouse_address'],
            "create_datetime" => date('Y-m-d H:i:s')
        );

        $ShippingId = DB::table('shipping_details')->insertGetId($InsertData);

        if ($ShippingId) {
            $shipping = DB::table('shipping_details')->select('id', 'sample_address', 'warehouse_address')->where('id', $ShippingId)->get();
            return response()->json(['status' => $ShippingId, 'data' => $shipping, 'message' => 'Shipping details created successfully.'], 200);
        } else {
            return response()->json(['status' => $ShippingId, 'data' => $shipping, 'message' => 'Shipping details cannot be created, Please try again after sometime.'], 401);
        }
    }

    /**
     *get user wise shipping details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetUserWiseShippingDetails($id)
    {
        $Shipping = DB::table('shipping_details')->select('id', 'sample_address', 'warehouse_address', 'create_datetime', 'updated_datetime')->where('user_id', $id)->get();

        if (count($Shipping) == 0) {
            return response()->json(['status' => 0, 'data' => $Shipping, 'message' => 'Shipping details cannot be found.'], 400);
        } else {
            return response()->json(['status' => 1, 'data' => $Shipping, 'message' => 'Shipping details get successfully.'], 200);
        }
    }

    /**
     * update user shipping details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function UpdateShipingDetails(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $updateData = array(
            'user_id' => $input['user_id'],
            "sample_address" => $input['sample_address'],
            "warehouse_address" => $input['warehouse_address'],
            "updated_datetime" => date('Y-m-d H:i:s')
        );
        $ShippingId = DB::table('shipping_details')->where('id', $id)->update($updateData);

        if ($ShippingId) {
            $Shipping = DB::table('shipping_details')->select('id', 'sample_address', 'warehouse_address', 'create_datetime', 'updated_datetime')->where('id', $id)->get();
            return response()->json(['status' => $ShippingId, 'data' => $Shipping, 'message' => 'Shipping details update successfully.'], 200);
        } else {
            return response()->json(['status' => $ShippingId, 'data' => $Shipping, 'message' => 'Shipping details cannot be updated, Please try again after sometime.'], 400);
        }
    }

    /**
     * Delete user account details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function DeleteShippingDetails($id)
    {
        $Shipping = DB::table('shipping_details')->where('id', $id)->delete();

        if ($Shipping) {
            return response()->json(['status' => $Shipping, 'message' => 'Shipping details deleted successfully.'], 200);
        } else {
            return response()->json(['status' => $Shipping, 'message' => 'Account cannot be deleted, Please try again after sometime.'], 400);
        }
    }
}
