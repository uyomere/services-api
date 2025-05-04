<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Trait\FileUploadTrait;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    use FileUploadTrait;

    public function Userregister(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $response['name'] = $user->name;
        $response['email'] = $user->email;
        $response['token'] = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'New user Registered Successfully',
            'data' => $response
        ], 200);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validate->errors()
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;
            $response['role'] = $user->role;

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successfully',
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => 'invalid credentials'
            ], 400);
        }
    }

    public function allUser()
    {
        $users = User::get();
        if (!$users) {
            return response()->json([
                'status' => 'fail',
                'count' => count($users),
                'message' => 'No User Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($users),
            'data' => $users
        ], 200);
    }

    public function editUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validate->errors()
            ], 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully',
            'data' => $user
        ]);
    }

    public function deleteUser(Request $request, $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fails',
                'message' => 'No User Found'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User Deleted Successfully'
        ], 200);
    }

    //category functions here
    public function createCategory(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->slug);
        $imagePath = $this->uploadImage($request, 'image');
        $data['image'] = isset($imagePath) ? $imagePath : '';

        ProductCategory::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'category created successfully',
            'data' => $data
        ], 200);
    }

    //getting all category 
    public function allCategory()
    {
        $categories = ProductCategory::get();
        if (!$categories) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Category Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($categories),
            'message' => 'No Record Found',
            'data' => $categories
        ], 200);
    }

    //category edit function
    public function editCategory(Request $request, $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Category Found'
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $category->name = $request->name;
        $imagePath = $this->uploadImage($request, 'image');
        $category->image = isset($imagePath) ? $imagePath : $category->image;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category Updated Successfully'
        ], 200);
    }

    //delete category 
    public function deleteCategory($id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Record Found'
            ], 404);
        }
        $this->removeImage($category->image);
        $category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Record Deleted Successfully'
        ], 200);
    }

    //products functions
    public function createProduct(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fails',
                'message' => $validate->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);
        $data['category_id'] = $request->category_id;
        $imagePath = $this->uploadImage($request, 'image');
        $data['image'] = isset($imagePath) ? $imagePath : '';
        $data['price'] = $request->price;
        $data['description'] = $request->description;

        Product::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product Created Successfully'
        ], 200);
    }

    //end product functions

    //shipping function start here 

    public function createShipping(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'method_code' => 'required',
            'shipping_price' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['method_code'] = $request->method_code;
        $data['shipping_price'] = $request->shipping_price;

        ShippingMethod::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'data' => $data
        ], 200);
    }

    //shipping ends

    //payment start here 
    public function createPayment(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'method_code' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['method_code'] = $request->method_code;

        PaymentMethod::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'data' => $data
        ], 200);
    }

    //end payment 

    //order methode start here 
    public function createOrder(Request $request)
    {

        $validate = Validator::make($request->all(), [

            'user_id' => 'required',
            'email' => 'required|email',
            'shipping_price' => 'required',
            'tax' => 'required',
            'grand_total' => 'required',
            'qty' => 'required',
            'shipping_method_id' => 'required',
            'payment_method_id' => 'required',
            'cart_items' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data['user_id'] = $request->user_id;
        $data['email'] = $request->email;
        $data['shipping_price'] = $request->shipping_price;
        $data['tax'] = $request->tax;
        $data['grand_total'] = $request->grand_total;
        $data['qty'] = $request->qty;
        $data['grand_total'] = $request->grand_total;
        $data['shipping_method_id'] = $request->shipping_method_id;
        $data['payment_method_id'] = $request->payment_method_id;

        $order = Order::create($data);
        $orderId = $order->id;
        $cartItems = $request->cart_items;

        foreach ($cartItems as $cartItem) {

            $orderData['order_id'] = $orderId;
            $orderData['product_id'] = $cartItem['product_id'];
            $orderData['price'] = $cartItem['price'];
            $orderData['qty'] = $cartItem['qty'];

            OrderItem::create($orderData);
        }

        return response()->json([

            'status' => 'success',
            'message' => 'New Order Created Successfully'

        ], 200);
    }

    //end order function
    public function createAddress(Request $request)
    {

        $validate = Validator::make($request->all(), [

            'user_id' => 'required',
            'address_line_one' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $user = User::find($request->user_id);

        if (!$user) {

            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found'
            ], 404);
        }

        $data = $request->all();
        UserAddress::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Created Successfully'
        ], 200);
    }

    //address ends

    //review start here 
    public function createReview(Request $request)
    {

        $validate = Validator::make($request->all(), [

            'user_id' => 'required',
            'product_id' => 'required',
            'rating' => 'required',
            'review' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validate->errors()
            ], 400);
        }

        $data = $request->all();
        ProductReview::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Created Successfully'
        ], 200);
    }
}
