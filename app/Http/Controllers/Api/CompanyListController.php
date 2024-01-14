<?php

namespace App\Http\Controllers\Api;

use App\Models\CompanyList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CompanyListController extends Controller
{
    public function index()
    {
        $data = CompanyList::where('user_id', Auth::user()->id)->get();

        if($data) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Something wrong'
        ]);

    }


    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'alamat' => 'required|string|max:255',
                'no_handphone' => 'required|string|max:255',
                'website' => '|max:255'
            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $data = [
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'no_handphone' => $request->no_handphone,
                'website' => $request->website,
            ];

            // return $data;

            $companyList = CompanyList::create($data);


            if($companyList) {
                return response()->json([
                    'status' => 201,
                    'message' => 'data berhasil ditambahkan',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
        }
    }


    public function detail(Request $request)
    {
        $data = CompanyList::where('id', $request->id)->first();

        if($data) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'something worng'
        ]);
    }


    public function edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'alamat' => 'required|string|max:255',
                'no_handphone' => 'required|string|max:255',
                'website' => 'max:255'
            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $companyList = CompanyList::where('id', $request->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'no_handphone' => $request->no_handphone,
                'website' => $request->website
            ]);

            if($companyList) {
                return response()->json([
                    'status' => 201,
                    'message' => 'data berhasil diubah',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function getById()
    {
        $data = CompanyList::where('id', request()->id)->first();

        if($data) {
            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'something wrong'
        ]);
    }

}
