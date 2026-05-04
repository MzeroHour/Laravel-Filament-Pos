@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold text-center">Receipt</h1>
                <p class="text-center text-blue-100">Sale #{{ $sale->id }}</p>
            </div>

            <div class="px-6 py-4">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold mb-2">Sale Details</h2>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-medium">Date:</span> {{ $sale->created_at->format('M d, Y H:i') }}</p>
                        @if ($sale->customer)
                            <p><span class="font-medium">Customer:</span> {{ $sale->customer->name }}</p>
                        @endif
                        @if ($sale->paymentMethod)
                            <p><span class="font-medium">Payment Method:</span> {{ $sale->paymentMethod->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="text-lg font-semibold mb-2">Items</h2>
                    <div class="space-y-2">
                        @foreach ($sale->saleItems as $item)
                            <div class="flex justify-between text-sm">
                                <span>{{ $item->item->name }} (x{{ $item->quantity }})</span>
                                <span>${{ number_format($item->total_amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${{ number_format($sale->total_amount - $sale->tax_amount + $sale->discount_amount, 2) }}</span>
                        </div>
                        @if ($sale->tax_amount > 0)
                            <div class="flex justify-between">
                                <span>Tax:</span>
                                <span>${{ number_format($sale->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        @if ($sale->discount_amount > 0)
                            <div class="flex justify-between">
                                <span>Discount:</span>
                                <span>-${{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-semibold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span>${{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Paid:</span>
                            <span>${{ number_format($sale->paid_amount, 2) }}</span>
                        </div>
                        @if ($sale->paid_amount > $sale->total_amount)
                            <div class="flex justify-between">
                                <span>Change:</span>
                                <span>${{ number_format($sale->paid_amount - $sale->total_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-600">
                <p>Thank you for your business!</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
