<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private InvoiceService $invoices) {}

    // الأدمن يشوف كل الفواتير، وولي الأمر يشوف فواتيره بس
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return InvoiceResource::collection($this->invoices->paginateForAdmin());
        }

        $guardian = $user->guardianProfile;

        abort_if($guardian === null, 403);

        return InvoiceResource::collection($this->invoices->paginateForGuardian($guardian));
    }

    // الأدمن بس (الـ route عليه role:admin)
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoices->create($request->validated());

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        $this->authorize('view', $invoice);

        return new InvoiceResource($invoice);
    }

    public function cancel(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($this->invoices->cancel($invoice));
    }
}
