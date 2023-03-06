<?php


namespace App\Http\Controllers\ContentLibrary;

use App\Http\Controllers\Controller;
use App\Models\Advertisement\ProductProfileInfo;
use App\Models\ContentLibrary\ContentLibrary;
use App\Repositories\ContentLibrary\ContentLibraryRepository;
use Illuminate\Http\Request;

class ContentLibraryController extends Controller
{
    private ContentLibraryRepository $repository;
    public function __construct()
    {
        $this->repository = new ContentLibraryRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->repository->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->repository->validationRule());
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContentLibrary\ContentLibrary  $contentLibrary
     * @return \Illuminate\Http\Response
     */
    public function show(ContentLibrary $contentLibrary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContentLibrary\ContentLibrary  $contentLibrary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContentLibrary $contentLibrary)
    {
        //
        Configuration::instance([
            'cloud' => [
                'cloud_name' => 'my_cloud_name',
                'api_key' => 'my_key',
                'api_secret' => 'my_secret'
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContentLibrary\ContentLibrary  $contentLibrary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $ids = explode(',', $id);
        return $this->repository->destroy($request, $ids);
    }

    public function getContentMedia(Request $request)
    {
        return $this->repository->getContentMedia($request);
    }
    public function getProductMedia(Request $request)
    {
        return $this->repository->getProductMedia($request);
    }
    public function getProjectUrl(Request $request)
    {
        return $this->repository->getProjectUrl($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->repository->changetatus($request);
    }
    public function assignTags(Request $request)
    {
        return $this->repository->assignTags($request);
    }
    public  function getItemCodes(Request $request)
    {
        return  ContentLibrary::select('item_code')->get()->pluck('item_code');
    }


    public  function addToFavorite(Request $request)
    {
        return  $this->repository->addToFavorite($request);
    }

    public  function editProperties(Request $request)
    {
        return  $this->repository->editProperties($request);
    }
}
