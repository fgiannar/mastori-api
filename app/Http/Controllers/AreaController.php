<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Area;

class AreaController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/areas",
     *     description="Returns all the areas",
     *     operationId="getareas",
     *     tags={"areas"},
     *     produces={"application/json"}
     *     @SWG\Response(
     *         response=200,
     *         description="All areas response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/profession")
     *         ),
     *     )
     * )
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Area::all();
    }
}
