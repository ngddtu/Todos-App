<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = auth('api')->user()->id;
        // $id = $request->get('id');
        $todos = Todo::where('user_id', '=', $id)->get();
        return $todos;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $user_id = $request->get('user_id');
        // $id = auth('api')->user()->id;
        $data = $request->validate([
            'title' => 'required|min:10',
            // 'user_id' => 'required|integer'
        ]);
        $data['user_id'] = auth('api')->user()->id;
        $data['is_done']  = 0;
        $data['deadline'] = date('Y-m-d');

        Todo::create($data);

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        //
    }
}
