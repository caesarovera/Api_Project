<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Project;

class ProjectController extends Controller
{   
    private $sucess_status = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = array();
        $user     = Auth::user();
        $projects = project::with("children")->where("user_id", $user->id)->whereNull("parent_id")->get();
        if(count($projects) > 0) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "count" => count($projects), "data" => $projects]);
        }

        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! no todo found"]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user      = Auth::user();
        $validator = Validator::make($request->all(),
            [
                "project_title"  => "required",
                "description" => "required",
            ]
        );

        if($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $project_array = array(
            "project_title" => $request->project_title,
            "description"   => $request->description,
            "status"        => $request->status,
            "user_id"       => $user->id,
            "parent_id"        => $request->parent_id
        );

        $project_id = $request->project_id;

        if($project_id != "") {
            $project_status = project::where("id", $project_id)->update($project_array);

            if($project_status == 1) {
                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "Todo updated successfully", "data" => $project_array]);
            }

            else {
                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "Todo not updated"]);
            }

        }

        $project = project::create($project_array);

        if(!is_null($project)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $project]);
        }

        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! project not created."]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id)
    {
        if($project_id == 'undefined' || $project_id == "") {
            return response()->json(["status" => "failed", "success" => false, "message" => "Alert! enter the project id"]);
        }

        $project = project::find($project_id);

        if(!is_null($project)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $project]);
        }

        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! no todo found"]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id)
    {
        if($project_id == 'undefined' || $project_id == "") {
            return response()->json(["status" => "failed", "success" => false, "message" => "Alert! enter the project id"]);
        }

        $project = project::find($project_id);

        if(!is_null($project)) {

            $delete_status = project::where("id", $project_id)->delete();

            if($delete_status == 1) {

                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "Success! todo deleted"]);
            }

            else {
                return response()->json(["status" => "failed", "success" => false, "message" => "Alert! todo not deleted"]);
            }
        }

        else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Alert! todo not found"]);
        }
    }
}
