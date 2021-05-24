<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Landing;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\Landing as LandingResource;

class LandingController extends BaseController
{
    public function getMyLanding()
    {
        $user = Auth::user();

        $landing = Landing::query()
            ->where('user_id', '=', $user->id)
            ->first();

        if (is_null($landing)) {
            return $this->sendResponse(['landing' => null], 'no landing');
        }

        return $this->sendResponse(new LandingResource($landing), 'Landing retrieved successfully.');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $landings = Landing::all();

        return $this->sendResponse(LandingResource::collection($landings), 'Landings retrieved successfully.');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $user = Auth::user()->id;

        $validator = Validator::make($input, [
            'first_header' => 'required',
            'second_header' => 'required',
            'content' => 'required',
            'template' => 'required',
            'domen' => 'required',
            'font_color' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

         else $landing = Landing::create(array_merge($input, ['user_id' => $user, 'image' => ' ']));

        Landing::addLanding($input);

        return $this->sendResponse(new LandingResource($landing), 'Landing created successfully.');
    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)
    {
        $landing = Landing::find($id);

        if (is_null($landing)) {
            return $this->sendError('Landing not found.');
        }

        return $this->sendResponse(new LandingResource($landing), 'Landing retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Landing $landing)
    {
        $user = Auth::user();

        if ($landing->user_id != $user->id) {
            return $this->sendError('Unable landing', $validator->errors());
        }

        $input = $request->all();
        $validator = Validator::make($input, [
            'first_header' => 'required',
            'second_header' => 'required',
            'content' => 'required',
            'template' => 'required',
            'domen' => 'required',
            'font_color' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $landing->first_header = $input['first_header'];
        $landing->second_header = $input['second_header'];
        $landing->content = $input['content'];
        $landing->template = $input['template'];
        $landing->font_color = $input['font_color'];
        $landing->save();

        $root = Landing::getLandingRoot($landing->domen);

        $indexData = [
            'first_header' => $input['first_header'],
            'second_header' => $input['second_header'],
            'content' => $input['content'],
            'image' => $landing->image
        ];

        $styleData = [
            'font_color' => $input['font_color']
        ];

        Landing::setLandingIndex($root . '/', $indexData);

        Landing::setLandingStyles($root.'/', $styleData);

        return $this->sendResponse(new LandingResource($landing), 'Landing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Landing $landing)
    {
        $landing->delete();

        return $this->sendResponse([], 'Landing deleted successfully.');
    }

    public function getDomains()
    {
        return array_diff(scandir('/etc/nginx/sites-enabled/'), array('..', '.'));
    }

    public function getTemplates()
    {
        return array_diff(scandir(base_path().'/resources/views/landingLayouts/'), array('..', '.'));
    }

    public function addImage()
    {
        $user = Auth::user();
        $landing = Landing::query()->where('user_id', '=', $user->id)->first();

        $projectDir = Landing::getLandingRoot($landing->domen);
        if(isset($_FILES['file']['name'])){
            Landing::addLandingImage($_FILES, $projectDir, $landing);
        }
    }
}
