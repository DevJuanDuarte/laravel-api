<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

    /**
     * List sales with optional filters.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Sale::with(['user', 'customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(15);

        return SaleResource::collection($sales);
    }


    /**
     * Create a new sale.
     * @param StoreSaleRequest $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $items = [];

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }

                    $itemSubtotal = $product->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                $tax = $request->input('tax', 0);
                $discount = $request->input('discount', 0);
                $total = $subtotal + $tax - $discount;

                $sale = Sale::create([
                    'user_id' => $request->user()->id,
                    'customer_id' => $request->customer_id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                    'notes' => $request->notes,
                ]);

                $sale->items()->createMany($items);
                $sale->load(['user', 'customer', 'items.product']);

                return $sale;
            });

            return (new SaleResource($sale))
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Show a specific sale.
     * @param Sale $sale
     * @return SaleResource
     */
    public function show(Sale $sale): SaleResource
    {
        $sale->load(['user', 'customer', 'items.product']);

        return new SaleResource($sale);
    }

    /**
     * Update a specific sale.
     * @param UpdateSaleRequest $request
     * @param Sale $sale
     * @return SaleResource
     */
    public function update(UpdateSaleRequest $request, Sale $sale): SaleResource
    {
        $sale->update($request->validated());
        $sale->load(['user', 'customer', 'items.product']);

        return new SaleResource($sale);
    }

    /**
     * Delete a specific sale.
     * @param Sale $sale
     * @return JsonResponse
     */
    public function destroy(Sale $sale): JsonResponse
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $sale->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * Generate a unique invoice number.
     * @return string
     */
    private function generateInvoiceNumber(): string
    {
        $lastSale = Sale::latest('id')->first();
        $number = $lastSale ? $lastSale->id + 1 : 1;
        
        return 'INV-' . date('Ymd') . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
