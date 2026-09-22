<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Referral;
use App\Models\ReferralEarning;
use App\Services\Referral\ReferralService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralsController extends Controller
{
    public function __construct(
        private readonly ReferralService $referralService,
    ) {}

    public function attach(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string']);

        $masterId = (int) $request->header('X-Master-Id');

        $master = Master::query()->findOrFail($masterId);

        $referral = $this->referralService->registerReferral($master, $data['code']);
        if (!$referral) {
            return response()->json(
                [
                    'ok' => false,
                    'message' => 'Неверный код или попытка привязать себя.',
                ],
                422
            );
        }

        return response()->json(
            [
                'ok' => true,
                'referral' => [
                    'id' => $referral->id,
                    'referrer_master_id' => $referral->referrer_master_id,
                    'status' => $referral->status,
                ]
            ]
        );
    }

    public function my(Request $request): JsonResponse
    {
        $masterId = (int) $request->header('X-Master-Id');

        $referrals = Referral::with(['referredMaster', 'earnings'])
            ->where('referrer_master_id', $masterId)
            ->get();

        return response()->json(
            $referrals->map(function (Referral $r) {
                $earned = $r->earnings->sum('amount');

                return [
                    'referred_master_name' => $r->referredMaster->name,
                    'attached_at' => $r->created_at,
                    'is_rewarded' => $r->status === Referral::STATUS_REWARDED,
                    'earned' => $earned,
                ];
            })
        );
    }

    public function earnings(Request $request): JsonResponse
    {
        $masterId = (int) $request->header('X-Master-Id');

        $base = ReferralEarning::query()->where('referrer_master_id', $masterId);

        return response()->json(
            [
                'total' => (clone $base)->sum('amount'),
                'pending' => (clone $base)
                    ->where('status', ReferralEarning::STATUS_PENDING)
                    ->sum('amount'),
                'paid' => (clone $base)
                    ->where('status', ReferralEarning::STATUS_PAID)
                    ->sum('amount'),
                'rewarded_referrals_count' => Referral::query()
                    ->where('referrer_master_id', $masterId)
                    ->where('status', Referral::STATUS_REWARDED)
                    ->count(),
            ]
        );
    }
}
