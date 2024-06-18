<?php

namespace App\Http\Controllers\Bsi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UMKMGroupList;
use App\Models\UMKMEvaluations;
use App\Models\UMKMInfrastructures;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bsi\UMKMEvaluationStoreRequest;
use App\Http\Requests\Bsi\UMKMEvaluationUpdateRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\UMKMEvaluationInfrastructure;

class UMKMEvaluationController extends Controller
{
    public function index()
    {
        $umkmEvaluations = UMKMEvaluations::orderBy('created_at', 'desc')
            ->with('umkmProfile')
            ->with('umkmGroupName')
            ->paginate(10);

        return response([
            'message' => "success",
            'data' => $umkmEvaluations
        ], 200);
    }

    public function store(UMKMEvaluationStoreRequest $request)
    {
        $validated = $request->validated();

        $umkm = User::where('id_role', 2)->where('id', $request->id_user)->first();

        if (!$umkm) {
            return response([
                'status' => 404,
                'message' => "UMKM not found",
            ], 404);
        }

        if (UMKMEvaluations::where('id_user', $request->id_user)->first()) {
            return response([
                'status' => 403,
                'message' => "You cant create evaluation more than one!",
            ], 403);
        }

        $umkmEvaluation = new UMKMEvaluations();
        $umkmEvaluation->id_user = $validated['id_user'];
        $umkmEvaluation->gross_expenses = $validated['gross_expenses'];
        $umkmEvaluation->gross_incomes = $validated['gross_incomes'];
        $umkmEvaluation->net_income = $validated['net_income'];
        $umkmEvaluation->market_uptake = $validated['market_uptake'];
        $umkmEvaluation->equipment_total = $validated['equipment_total'];
        $umkmEvaluation->is_sustain = $validated['is_sustain'];
        $umkmEvaluation->is_mustahiq = $validated['is_mustahiq'];
        $umkmEvaluation->is_licensed = $validated['is_licensed'];
        $umkmEvaluation->is_proper = $validated['is_proper'];
        $umkmEvaluation->loan_amount = $validated['loan_amount'];

        if (!$umkmEvaluation->save()) {
            return response([
                'message' => "Failed to create UMKM Evaluation",
                'data' => null
            ], 400);
        }

        if ($request->input('infrastructures')) {
            $infrastructures = $request->input('infrastructures');
            foreach ($infrastructures as $infrastructure) {
                $searchInfrastructures = UMKMInfrastructures::where('id', $infrastructure)->first();
                if ($searchInfrastructures) {
                    UMKMEvaluationInfrastructure::create([
                        'id_umkm_evaluation' => $umkmEvaluation->id,
                        'id_infrastructure' => $searchInfrastructures->id,
                    ]);
                }
            }
        }

        $umkmEvaluation['umkm_name'] = $umkm->umkm_name;

        return response([
            'message' => "UMKM Evaluation created successfully",
            'data' => $umkmEvaluation
        ], 201);
    }

    public function update(UMKMEvaluationUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $umkmEvaluation = UMKMEvaluations::where('id', $id)->first();

        if (!$umkmEvaluation) {
            return response([
                'status' => 404,
                'message' => "UMKM Evaluation not found",
            ], 404);
        }

        $umkmEvaluation->gross_expenses = $validated['gross_expenses'];
        $umkmEvaluation->gross_incomes = $validated['gross_incomes'];
        $umkmEvaluation->net_income = $validated['net_income'];
        $umkmEvaluation->market_uptake = $validated['market_uptake'];
        $umkmEvaluation->equipment_total = $validated['equipment_total'];
        $umkmEvaluation->is_sustain = $validated['is_sustain'];
        $umkmEvaluation->is_mustahiq = $validated['is_mustahiq'];
        $umkmEvaluation->is_licensed = $validated['is_licensed'];
        $umkmEvaluation->is_proper = $validated['is_proper'];
        $umkmEvaluation->loan_amount = $validated['loan_amount'];

        if (!$umkmEvaluation->save()) {
            return response([
                'message' => "Failed to update UMKM Evaluation",
                'data' => null
            ], 400);
        }


        $umkm = User::where('id', $umkmEvaluation->id_user)->first();

        if (!$umkm) {
            return response([
                'status' => 404,
                'message' => "UMKM not found",
            ], 404);
        }

        if ($request->input('infrastructures')) {
            UMKMEvaluationInfrastructure::where('id_umkm_evaluation', $umkmEvaluation->id)->delete();
            $infrastructures = $request->input('infrastructures');
            foreach ($infrastructures as $infrastructure) {
                $searchInfrastructures = UMKMInfrastructures::where('id', $infrastructure)->first();
                if ($searchInfrastructures) {
                    UMKMEvaluationInfrastructure::create([
                        'id_umkm_evaluation' => $umkmEvaluation->id,
                        'id_infrastructure' => $searchInfrastructures->id,
                    ]);
                }
            }
        }

        $infrastructureData = $umkmEvaluation->evaluationInfrastructures->map(function ($evaluationInfrastructure) {
            return [
                'id' => $evaluationInfrastructure->infrastructure->id,
                'name' => $evaluationInfrastructure->infrastructure->name,
            ];
        })->toArray();

        $umkmEvaluation->infrastructures = $infrastructureData;
        $umkmEvaluation['umkm_name'] = $umkm->umkm_name;

        unset($umkmEvaluation->evaluationInfrastructures);

        return response([
            'message' => "UMKM Evaluation updated successfully",
            'data' => $umkmEvaluation
        ], 200);
    }

    public function show(string $id)
    {
        $umkmEvaluation = UMKMEvaluations::where('id', $id)
            ->with('umkmProfile')
            ->with('umkmGroupName')
            ->first();

        if (!$umkmEvaluation) {
            return response([
                'message' => "There is no evaluation with that id",
                'data' => null
            ], 400);
        }

        $infrastructureData = $umkmEvaluation->evaluationInfrastructures->map(function ($evaluationInfrastructure) {
            return [
                'id' => $evaluationInfrastructure->infrastructure->id,
                'name' => $evaluationInfrastructure->infrastructure->name,
            ];
        })->toArray();

        $umkmEvaluation->infrastructures = $infrastructureData;

        unset($umkmEvaluation->evaluationInfrastructures);

        return response([
            'message' => "success",
            'data' => $umkmEvaluation
        ], 200);
    }

    public function destroy(string $id)
    {
        $umkmEvaluation = UMKMEvaluations::where('id', $id)->first();

        if (!$umkmEvaluation) {
            return response([
                'message' => "There is no evaluation with that id",
                'data' => null
            ], 400);
        }

        UMKMEvaluationInfrastructure::where('id_umkm_evaluation', $umkmEvaluation->id)->delete();
        $result = UMKMEvaluations::destroy($id);

        if (!$result) {
            return response([
                'message' => "Failed to delete product",
                'data' => null
            ], 400);
        }

        return response([
            'message' => "Evaluation deleted",
            'data' => $umkmEvaluation
        ], 200);
    }

    public function searchEvaluations(Request $request)
    {
        $query = $request->input('query');

        $umkmEvaluations = UMKMEvaluations::orderBy('created_at', 'desc')
            ->with('umkmProfile')
            ->with('umkmGroupName')
            ->where(function ($q) use ($query) {
                $q->whereHas('umkmProfile', function ($subq) use ($query) {
                    $subq->where('umkm_name', 'like', '%' . $query . '%');
                })->orWhereHas('umkmGroupName.umkmGroup', function ($subq) use ($query) {
                    $subq->where('name', 'like', '%' . $query . '%');
                });
            })
            ->paginate(10);

        return response([
            'message' => "success",
            'data' => $umkmEvaluations
        ], 200);
    }

    public function searchEvaluationsByIdUser(string $id)
    {

        $umkmEvaluation = UMKMEvaluations::where('id_user', $id)
            ->with('umkmProfile')
            ->with('umkmGroupName')
            ->first();

        if (!$umkmEvaluation) {
            return response([
                'message' => "There is no user evaluation with that id",
                'data' => null
            ], 400);
        }

        $infrastructureData = $umkmEvaluation->evaluationInfrastructures->map(function ($evaluationInfrastructure) {
            return [
                'id' => $evaluationInfrastructure->infrastructure->id,
                'name' => $evaluationInfrastructure->infrastructure->name,
            ];
        })->toArray();

        $umkmEvaluation->infrastructures = $infrastructureData;

        unset($umkmEvaluation->evaluationInfrastructures);

        return response([
            'message' => "success",
            'data' => $umkmEvaluation
        ], 200);
    }
}
