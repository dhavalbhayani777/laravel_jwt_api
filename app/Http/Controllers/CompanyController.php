<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CompanyController extends Controller
{
    /**
     * Add user company details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function AddCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            'abn' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $InsertData = array(
            'user_id' => $input['user_id'],
            "name" => $input['name'],
            "address" => $input['address'],
            "abn" => $input['abn'],
            "create_datetime" => date('Y-m-d H:i:s')
        );
        $companyId = DB::table('company')->insertGetId($InsertData);

        $company = DB::table('company')->where('id', $companyId)->get();

        return response()->json(['data' => $company, 'message' => 'Company register successfully.'], 200);
    }

    /**
     *get user wise compnay details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getuserwisecompnay($id)
    {
        $company = DB::table('company')->where('id', $id)->get();

        if (count($company) == 0) {
            return response()->json(['status' => 0, 'data' => $company, 'message' => 'Company cannot be found.'], 400);
        } else {
            return response()->json(['status' => 1, 'data' => $company, 'message' => 'Company get successfully.'], 200);
        }
        
    }


    /**
     * update user company details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'address' => 'required',
            'abn' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $InsertData = array(
            'user_id' => $input['user_id'],
            "name" => $input['name'],
            "address" => $input['address'],
            "abn" => $input['abn'],
            "update_datetime" => date('Y-m-d H:i:s')
        );
        $companyId = DB::table('company')->where('id', $id)->update($InsertData);

        $company = DB::table('company')->where('id', $id)->get();

        if ($companyId) {
            return response()->json(['status' => $companyId, 'data' => $company, 'message' => 'Company update successfully.'], 200);
        } else {
            return response()->json(['status' => $companyId, 'data' => $company, 'message' => 'Company cannot be updated, Please try again after sometime.'], 400);
        }
        
    }

    /**
     * Delete user company details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function DeleteCompany($id)
    {
        $companyId = DB::table('company')->where('id', $id)->delete();

        if ($companyId) {
            return response()->json(['status' => $companyId, 'message' => 'Company deleted successfully.'], 200);
        } else {
            return response()->json(['status' => $companyId, 'message' => 'Company cannot be deleted, Please try again after some time.'], 400);
        }

        
    }
}
