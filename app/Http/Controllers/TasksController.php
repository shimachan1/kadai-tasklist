<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    // getでtasklist/にアクセスされた場合の「一覧表示処理」
    {
        //ログインしているかしていないかを判断する

        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];

            return view('task.index',$data);
        }else{
            return view('welcome');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tasks = new Task;

        return view('task.create', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // postでtasklist/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:191',
        ]);

        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);


        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   // getでtasklist/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        $tasks = Task::find($id);
        if (\Auth::id() === $tasks->user_id) {
            return view('task.show', [
                'tasks' => $tasks,
            ]);
        }else{
            return redirect('/');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        $tasks = Task::find($id);
        if (\Auth::id() === $tasks->user_id) {
            return view('task.edit', [
                'tasks' => $tasks,
            ]);
        }else{
            return redirect('/');
        }

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    // putまたはpatchでtasklist/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
//        $this->validate($request, [
//            'status' => 'required|max:191',   // 追加
//            'content' => 'required|max:191',
//        ]);

        $tasks = Task::find($id);
        $tasks->status = $request->status;    // 追加
        $tasks->content = $request->content;
        $tasks->save();

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでtasklist/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        $tasks = Task::find($id);
        $tasks->delete();

        return redirect('/');    }
}
