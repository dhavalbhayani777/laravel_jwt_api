<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    /* public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login']]);
    } */

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Enter email or password wrong', 'code' => 401], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phone'      => 'required',
            'address' => 'required',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'code' => 401], 401);
        }

        if ($files = $request->file('image')) {
            $destinationpath = 'public/image/';
            $profileImage = date('YmdHis').".". $files->getClientOriginalExtension();
            $files->move($destinationpath, $profileImage);

            $Image = url('/').'/'.$destinationpath.$profileImage;
        }

        $input = $request->all();
        $input['image'] = $Image;
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = JWTAuth::fromUser($user);
        $success['name'] =  $user->name;

        return response()->json(['data' => $user, 'token' => $success['token'], 'message' => 'User register successfully.', 'code' => 200], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = $this->me();
        return response()->json([
            'code' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user->original,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Check token status
     * 
     * @param string $token
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json($user);
    }

    /**
     * Update user
     * 
     * @param user data
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'code' => 401], 401);
        }

        $User = User::find($id);
        $input = $request->all();

        $User->email = $input['email'];
        $User->name = $input['name'];
        $User->phone = $input['phone'];
        $User->address = $input['address'];

        if ($files = $request->file('image')) {
            $destinationpath = 'public/image/';
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationpath, $profileImage);

            $Image = url('/') . '/' . $destinationpath . $profileImage;
            $User->image = $Image;
        }

        $User->save();
        $token = JWTAuth::fromUser($User);

        return response()->json(['data' => $User, 'token' => $token, 'message' => 'User update successfully.', 'code' => 200], 200);
    }

    /**
     * Delete user
     * 
     * @param userId
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteUser($id)
    {
        $User = User::find($id);
        $User->delete();

        return response()->json(['message' => 'Delete user successfully.', 'code' => 200], 200);
    }

    /**
     * Update user
     * 
     * @param user data
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'code' => 401], 401);
        }

        $User = User::find($id);
        $input = $request->all();

        $User->password = bcrypt($input['password']);
        $User->save();

        return response()->json(['data' => $User, 'message' => 'Reset password successfully.', 'code' => 200], 200);
    }

    /**
     * Get user details and Company details and Account details and Shipping Details
     * 
     * @param user Id
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function GetAllUserDetails($id)
    {
        $Details = array();
        $user = DB::table('users')->select('id', 'name', 'email', 'phone', 'address', 'image')->where('id', $id)->get();
        $Details['user'] = $user[0];

        $company = DB::table('company')->select('id', 'name', 'abn', 'address')->where('user_id', $id)->get();
        $Details['company'] = $company;

        $account = DB::table('account')->select('id', 'payment', 'billing_address', 'account_api')->where('user_id', $id)->get();
        $Details['account'] = $account;

        $shipping = DB::table('shipping_details')->select('id', 'sample_address', 'warehouse_address')->where('user_id', $id)->get();
        $Details['shipping_details'] = $shipping;

        if (count($Details) == 0) {
            return response()->json(['status' => 0, 'data' => $Details, 'message' => 'User details cannot be found.', 'code' => 400], 400);
        } else {
            return response()->json(['status' => 1, 'data' => $Details, 'message' => 'User details get successfully.', 'code' => 200], 200);
        }
    }
}
