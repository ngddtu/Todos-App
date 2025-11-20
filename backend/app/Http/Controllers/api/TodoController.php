<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = auth('api')->user()->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $query = Todo::where('user_id', $id);

        // Filter theo thời gian
        $timeFilter = $request->get('time_filter', 'today');
        
        switch ($timeFilter) {
            case 'alltime':
                // Không filter theo deadline - lấy tất cả
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                $query->whereBetween('deadline', [$start, $end]);
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $query->whereBetween('deadline', [$start, $end]);
                break;
            case 'today':
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $query->whereBetween('deadline', [$start, $end]);
                break;
        }

        $timeFilteredQuery = clone $query;

        // Filter theo trạng thái (is_done)
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'completed') {
                $query->where('is_done', 1);
            } elseif ($status === 'pending') {
                $query->where('is_done', 0);
            }
        }

        $perPage = (int) $request->get('per_page', 5);
        $perPage = $perPage > 0 ? $perPage : 5;

        $todos = $query->orderBy('id', 'desc')->paginate($perPage);

        $completedCount = (clone $timeFilteredQuery)->where('is_done', 1)->count();
        $pendingCount = (clone $timeFilteredQuery)->where('is_done', 0)->count();
        $totalCount = (clone $timeFilteredQuery)->count();
        
        return response()->json([
            'todos' => $todos->items(),
            'stats' => [
                'total' => $totalCount,
                'completed' => $completedCount,
                'pending' => $pendingCount
            ],
            'pagination' => [
                'current_page' => $todos->currentPage(),
                'last_page' => $todos->lastPage(),
                'per_page' => $todos->perPage(),
                'total' => $todos->total()
            ]
        ]);
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
            'title' => 'required|min:3',
            // 'user_id' => 'required|integer'
        ]);
        $data['user_id'] = auth('api')->user()->id;
        // $data['user_id'] = $request->get('user_id');
        $data['is_done']  = 0;
        $data['deadline'] = $request->get('deadline');
        // return $data; 
        // die();
        // dd(env('APP_TIMEZONE'));

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
        // Kiểm tra todo có tồn tại không
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy todo'
            ], 404);
        }

        // Kiểm tra quyền
        if ($todo->user_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật mục này'
            ], 403);
        }

        try {
            $data = $request->validate([
                'title' => 'sometimes|required|min:3',
                'deadline' => 'sometimes|required|date',
                'is_done' => 'sometimes|in:0,1' // Chấp nhận 0 hoặc 1
            ]);

            // Đảm bảo is_done là integer (0 hoặc 1)
            if (isset($data['is_done'])) {
                $data['is_done'] = (int) $data['is_done'];
            }

            $todo->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'todo' => $todo->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        // Kiểm tra todo có tồn tại không
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy todo'
            ], 404);
        }

        // Kiểm tra quyền
        if ($todo->user_id !== auth('api')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa mục này'
            ], 403);
        }

        $todo->delete();
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thành công'
        ]);
    }
}
