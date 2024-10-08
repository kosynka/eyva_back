<?php

namespace App\Http\Resources;

use App\Models;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $userServiceSchedule = $service = null;

        // if ($this->type === Models\Transaction::TYPE_PURCHASE_SERVICE) {
        //     $userServiceSchedule = $this->related;

        //     if ($userServiceSchedule) {
        //         $userServiceSchedule->load('schedule.service');

        //         /** @var Models\Service|null $service */
        //         $service = $userServiceSchedule->schedule->service;
        //     }
        // }

        return [
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,

            'type_text' => $this->getType(),
            'status_text' => $this->getStatus(),
            'amount_text' => $this->getAmount(),

            // 'related_service_type' => $service?->type,
            // 'related_service_type_text' => $service?->getType(true),

            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
