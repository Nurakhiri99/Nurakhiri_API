<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//import resource PostResource
use App\Http\Resources\NurakhiriResource;
//import facade Validator
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class NurakhiriController extends Controller
{
    //
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $posts = User::latest()->paginate(5);

        //return collection of posts as a resource
        return new NurakhiriResource(true, 'List Data User', $posts);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [            
            'email' => 'required|email|unique:users,email,regex:/(.+)@(.+)\.(.+)/i',
            'password'     => 'required|min:8',
            'name'     => 'required|min:3|max:50',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create post
        $post = User::create([
            'email'     => $request->email,
            'password'   => Hash::make($request->password),
            'name'     => $request->name,
            'active'     => $request->active,
        ]);      

        //return response
        return new NurakhiriResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find post by ID
        $post = User::find($id);

        //return single post as a resource
        return new NurakhiriResource(true, 'Detail Data Post!', $post);
    }
}
