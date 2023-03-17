<?php

namespace App\Repositories\SingleSales;

use App\Models\Reason;
use App\Models\SingleSales\Question;
use App\Repositories\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\QueryBuilder\UriQueryBuilder;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class QuestionRepository.
 */
class QuestionRepository extends Repository
{
    /**
     * @return string
     *  Return the model
     */
    public function paginate(Request $request)
    {
        $key = $request->query->get("key");
        $queryBuilder = new UriQueryBuilder(new Question(), $request);
        $queryBuilder->setRelations($this->getRelations());

        $searchInColumns        = [];
        $queryBuilder->query    = $this->filterRecords($queryBuilder->query, $request, $searchInColumns);
        $statusQuery            = clone  $queryBuilder->query;
        $queryBuilder->query    = $this->fetchDataAccordingToStatus($queryBuilder->query, $key);

        $queryBuilder->query->orderBy("created_at", 'desc');
        $question         = $queryBuilder->build()->paginate()->getData();
        $extraData = [];
        $question = $this->getStatusCount($statusQuery, $question, $extraData);
        return response()->json($question);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $question['question'] = $request->input("question");
            $attrModel = Question::create($question);
            $data[] = $attrModel->load($this->getRelations())->refresh();

            DB::commit();
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        if ($question) {
            return $this->successResponse($question);
        }
        return $this->errorResponse("Not Found");
    }

    public function update(Request $request)
    {
        try {
            $questionId = $request->input("id");
            $question = Question::findOrFail($questionId);
            $attributes['question'] = $request->input("question");

            $question->update($attributes);
            return $this->successResponse($question);
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function getRelations(): array
    {
        return [];
    }

    public function destroy(array $ids, $reason_ids)
    {
        try {
            //code...
            DB::beginTransaction();
            $question = Question::withTrashed()->whereIn('id', $ids)->get();
            // $reasons = Reason::whereIn('id', $reason_ids)->get();
            // ADD REASON
            foreach ($question as $s) {
                if (!$s->trashed()) {
                    // $s->reasons()->syncWithoutDetaching($reasons);
                    $s->delete();
                } else {
                    $s->forceDelete();
                }
            }
            DB::commit();
            return response()->json(['result' => true], Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $th;
        }
    }

    public function storeRules()
    {
        return [

            'question' => ['required'],
        ];
    }

    public function updateRules()
    {
        return [
            "id" => ['required'],
            'question' => ['required'],
        ];
    }

    public function search(Request $request)
    {
    }
}
