<?php

namespace App\Repositories\ContentLibrary;

//use Your Model

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use App\Models\ContentLibrary\ContentLibrary;
use App\Models\ContentLibrary\ContentLibraryMedia;
use App\Models\Favorite;
use App\Models\FileHistory;
use App\Models\Tag;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class ContentLibraryRepository.
 */
class ContentLibraryRepository extends Repository
{
    public function index(Request $request)
    {


        $tab = $request->tab;
        $queryBuilder = new ContentLibraryMedia();
        $queryBuilder = $this->filterMedia($request, $queryBuilder, true);
        $queryBuilder = $queryBuilder->whereHas('contentLibrary', function ($query) use ($request) {
            $query = $this->filterMedia($request, $query);
            return $query;
        });
        $queryBuilder->with(['contentLibrary', 'labels', 'contentLibrary.country:id,name,iso2', 'contentLibrary.company:id,name,logo']);
        // $queryBuilder->setWithTrashed();
        if ($request->filter_type == 'history') {
            $queryBuilder = $queryBuilder->join('file_histories', 'content_library_media.id', '=', 'file_histories.media_id')->orderBy('file_histories.created_at', 'desc')->select('content_library_media.*');

            if ($request->date) {
                $date = json_decode($request->date);
                $start_date = Carbon::parse($date->start_date);
                $end_date = Carbon::parse($date->end_date);
                if ($start_date->gt($end_date)) {
                    $queryBuilder = $queryBuilder->whereBetween(DB::raw('DATE(file_histories.created_at)'),  [$end_date, $start_date]);
                } else {
                    $queryBuilder = $queryBuilder->whereBetween(DB::raw('DATE(file_histories.created_at)'),  [$start_date, $end_date]);
                }
            }
        }
        $extraData = [];
        if ($request->filter_type == 'media') {
            $queryBuilder = $queryBuilder->latest('created_at')->withCount('isFavorite');
            $extraData = DB::table('content_library_media')->select('status',DB::raw('count(status) as count'))->whereNull('deleted_at')->groupBy('status')->get();
        }
        if ($request->filter_type == 'favorite') {
            $queryBuilder = $queryBuilder->latest('created_at')->withCount('isFavorite');
            $queryBuilder = $queryBuilder->has('isFavorite', '>', 0);
        }

        $queryBuilder = self::searchContentData($queryBuilder, $request);
        if ($tab && $tab != 'all') {
            $queryBuilder = $queryBuilder->where('status', $tab);
        }

        $result = $queryBuilder->paginate($request->itemsPerPage);
        $data = $result->items();
        $total = $result->total();
        $result = [
            "data" => $data,
            "total" => $total,
            "extraData" => $extraData,
        ];

        return response()->json($result);
    }

    public  function getProductMedia(Request $request)
    {
        $query = new ContentLibraryMedia();
        $query = $this->filterMedia($request, $query, true);
        $query =  $query->with('contentLibrary')->whereHas('contentLibrary', function ($query) use ($request) {
            $query = $query->where('item_code', $request->item_code);
            $query = $this->filterMedia($request, $query);
            return $query;
        });
        $media = $query->orderBy('created_at', 'desc')->get();
        return response()->json($media);
    }

    public  function getProjectUrl(Request $request)
    {
        $status = $request->status == 'all' ? ['publish', 'not publish'] : [$request->status];
        $projectUrl =  ContentLibraryMedia::with(['contentLibrary', 'contentLibrary.country:id,name,iso2', 'contentLibrary.company:id,name,logo'])->whereHas('contentLibrary', function ($query) use ($request) {
            return $query->where('item_code', $request->item_code)
                ->where('country_id', $request->country_id)
                ->where('company_id', $request->company_id);
        })->get()->groupBy('project_url');
        // ->where('status','publish')
        return response()->json($projectUrl);
    }
    public  function searchContentData($query, Request $request)
    {
        $searchValue = $request->searchValue;
        if ($searchValue) {

            $tags =  Tag::where('name', 'LIKE', '%' . $request->searchValue . '%')->get()->pluck('id');

            $query = $query->where(function ($query) use ($searchValue, $tags) {
                return $query->where('media_size', 'LIKE', '%' . $searchValue . '%')
                    ->orWhereHas('tags', function ($q) use ($tags) {
                        return $q->whereIn('tag_id', $tags);
                    })
                    ->orWhere('project_url', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('status',  $searchValue)
                    ->orWhereHas('contentLibrary', function ($query2) use ($searchValue) {
                        return  $query2->where(function ($query3) use ($searchValue) {
                            return $query3->orWhere('item_code', $searchValue)
                                ->orWhere('item_name', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('requested_when', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('content_source', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('content_type', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('content_language', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('created_at', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('risk_palicy', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('content_direction', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('music', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('shooting', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('people', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('graphics', 'LIKE', '%' . $searchValue . '%')
                                ->orWhere('info_offer', 'LIKE', '%' . $searchValue . '%')
                                ->orWhereHas('country', function ($query4) use ($searchValue) {
                                    return $query4->Where('name', 'LIKE', '%' . $searchValue . '%');
                                })
                                ->orWhereHas('company', function ($query5) use ($searchValue) {
                                    return $query5->Where('name', 'LIKE', '%' . $searchValue . '%');
                                });
                        });
                    });
            });
        }
        return $query;
    }

    public function filterMedia($request, $query, $media = false)
    {

        if ($media) {
            if ($request->date && $request->filter_type != 'history') {
                $date = json_decode($request->date);
                $start_date = Carbon::parse($date->start_date);
                $end_date = Carbon::parse($date->end_date);
                if ($start_date->gt($end_date)) {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'),  [$end_date, $start_date]);
                } else {
                    $query = $query->whereBetween(DB::raw('DATE(created_at)'),  [$start_date, $end_date]);
                }
            }
        }

        if ($request->filter_data) {
            $filters = $request->filter_data;
            if ($request->tab) {
                $filters = [];
                foreach ($request->filter_data as $key => $value) {
                    $value =  json_decode($value, true);
                    $filters[$value['column']][] = $value['value'];
                }
            }

            if ($media) {
                if (array_key_exists("media_size", $filters)) {
                    $query = $query->whereIn('media_size',  $filters['media_size']);
                }
            } else {
                unset($filters['media_size']);
                foreach ($filters as $key => $value) {
                    if ($key == 'countries') $key = 'country_id';
                    if ($key == 'companies') $key = 'company_id';
                    $query = $query->whereIn($key, $value);
                }
            }
        }
        return $query;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $contentLibrary = new ContentLibrary();
            $attributes         = $request->only($contentLibrary->getFillable());
            $attributes['code'] = rand(1000000, 9999999999);
            $result =  $contentLibrary->create($attributes);
            foreach ($request->media as  $value) {
                $contentLibraryMedia = new ContentLibraryMedia();
                $attr['project_url'] = $value['project_url'];
                $attr['media_size'] = $value['media_size'];
                $attr['content_library_id'] = $result->id;
                $contentLibraryMedia->create($attr);
            }
            $result = $result->load('country:id,name,iso2', 'company:id,name,logo');
            $media = ContentLibraryMedia::where('content_library_id', $result->id)->get();
            $data = $media->map(function ($item) use ($result) {
                $item['content_library'] = $result;
                return $item;
            });
            DB::commit();
            return response()->json($data, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->errorResponse($exception->getMessage());
        }
    }
    public function getRelations()
    {
        return [
            'country:id,name,iso2',
            'company:id,name,logo'
        ];
    }

    public function validationRule(): array
    {
        return [
            'item_code' => 'required',
            'item_name' => 'required',
            'requested_when' => 'required',
            'content_source' => 'required',
            'content_type' => 'required',
            'content_language' => 'required',
            'season' => 'required',
            'risk_palicy' => 'required',
            'main_or_customer' => 'required',
            'info_offer' => 'required',
            'content_direction' => 'required',
            'voice_over' => 'required',
            'music' => 'required',
            'shooting' => 'required',
            'people' => 'required',
            'graphics' => 'required',
            'country_id' => 'required',
            'company_id' => 'required',
            'media' => 'required|array',
        ];
    }
    public function getContentMedia(Request $request)
    {
        $content_id = $request->contentId;
        $media = ContentLibraryMedia::with('tags')->where('content_library_id', $content_id)->get();
        $media = $media->map(function ($item, $key) {
            $data = collect($item);
            $data['tags'] = collect($data['tags'])->pluck('name');
            return  $data;
        });
        $tags = Tag::get()->pluck('name');
        $this->getHistory($request);
        return response()->json(["media" => $media, "tags" => $tags]);
    }
    public function getHistory($request)
    {
        if ($request->filterType == 'media') {
            FileHistory::where(['user_id' => Auth::user()->id, 'media_id' => $request->mediaId])->delete();
            FileHistory::create(['user_id' => Auth::user()->id, 'media_id' => $request->mediaId]);
        }
    }

    public function changetatus(Request $request)
    {
        $model = ContentLibraryMedia::where('id', $request->item_id)->first();
        if ($model) {
            $uuid = Str::uuid();
            ContentLibraryMedia::where('id', $request->item_id)->update(["status" => $request->status]);
            $result = $model->reasonables()->attach($request->reason_id, ['created_by' => $request->user()->id, "status" => $request->status, 'uuid' => $uuid, 'tab' => $request->tab, 'created_at' => Carbon::now()]);
            return response()->json(['data' => $result], 200);
        }
    }

    public function destroy($request, $ids)
    {
        try {
            DB::beginTransaction();

            if ($request->filter_type == 'media') {
                $result = ContentLibraryMedia::whereIn('id', $ids)->delete();
            } else {
                $result =  FileHistory::where('user_id', Auth::user()->id)->whereIn('media_id', $ids)->delete();
            }

            DB::commit();
            return response()->json($result, Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json($exception->getMessage(), 500);
        }
    }

    public function assignTags($request)
    {
        try {
            DB::beginTransaction();
            $model = ContentLibraryMedia::find($request->id);
            foreach ($request->tags as $tag) {
                $tags = Tag::firstOrCreate(
                    ['name' => $tag]
                );
            }
            $tags = Tag::whereIn('name', $request->tags)->get()->pluck('id');
            $model =  $model->tags();
            $model->sync($tags);
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function addToFavorite($request)
    {
        try {
            if (!$request->favorite == 0) {
                $media =  ContentLibraryMedia::find($request->id);
                $media->favorite()->create(['user_id' => Auth::user()->id]);
                return response()->json(["result" => true, "status" => 1]);
            } else {
                $favorite =   Favorite::where(['favorable_id' => $request->id, 'user_id' => Auth::user()->id])->delete();
                return response()->json(["result" => true, "status" => 0]);
            }
        } catch (\Throwable $th) {
            return response()->json(["result" => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function editProperties($request)
    {
        try {
            //code...

            if ($request->property == 'media_size') {
                $content =  ContentLibraryMedia::find($request->media_id);
            } else {
                $content =  ContentLibrary::find($request->id);
            }
            $content->update([$request->property => $request->value]);
            return response()->json($content, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }
}
