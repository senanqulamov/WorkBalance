<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Request;
use Illuminate\Support\Collection;

class QuoteComparisonService
{
    /**
     * Get all quotes for an RFQ
     *
     * @param Request $rfq The RFQ
     * @return Collection The quotes
     */
    public function getQuotesForRfq(Request $rfq): Collection
    {
        return Quote::where('request_id', $rfq->id)
            ->with(['supplier', 'items.requestItem.product'])
            ->get();
    }

    /**
     * Get the best quote for an RFQ based on total price
     *
     * @param Request $rfq The RFQ
     * @return Quote|null The best quote, or null if no quotes exist
     */
    public function getBestQuote(Request $rfq): ?Quote
    {
        return Quote::where('request_id', $rfq->id)
            ->orderBy('total_price', 'asc')
            ->first();
    }

    /**
     * Generate comparison data for quotes
     *
     * @param Request $rfq The RFQ
     * @return array The comparison data
     */
    public function generateComparisonData(Request $rfq): array
    {
        $quotes = $this->getQuotesForRfq($rfq);

        if ($quotes->isEmpty()) {
            return [
                'quotes' => [],
                'items' => [],
                'suppliers' => [],
                'best_quote' => null,
                'price_range' => [
                    'min' => 0,
                    'max' => 0,
                    'avg' => 0,
                ],
            ];
        }

        // Extract suppliers
        $suppliers = $quotes->pluck('supplier')->unique('id')->values();

        // Group quotes by item for comparison
        $itemComparisons = [];
        foreach ($rfq->items as $requestItem) {
            $itemComparisons[$requestItem->id] = [
                'request_item' => $requestItem,
                'product_name' => $requestItem->product_name,
                'quotes' => [],
                'price_range' => [
                    'min' => PHP_FLOAT_MAX,
                    'max' => 0,
                    'avg' => 0,
                ],
            ];
        }

        // Populate item comparisons with quote data
        foreach ($quotes as $quote) {
            foreach ($quote->items as $quoteItem) {
                if (isset($itemComparisons[$quoteItem->request_item_id])) {
                    $itemComparisons[$quoteItem->request_item_id]['quotes'][] = [
                        'quote_id' => $quote->id,
                        'supplier_id' => $quote->supplier_id,
                        'supplier_name' => $quote->supplier->name,
                        'unit_price' => $quoteItem->unit_price,
                        'quantity' => $quoteItem->quantity,
                        'total_price' => $quoteItem->total_price,
                        'notes' => $quoteItem->notes,
                    ];

                    // Update price range
                    $itemComparisons[$quoteItem->request_item_id]['price_range']['min'] =
                        min($itemComparisons[$quoteItem->request_item_id]['price_range']['min'], $quoteItem->unit_price);
                    $itemComparisons[$quoteItem->request_item_id]['price_range']['max'] =
                        max($itemComparisons[$quoteItem->request_item_id]['price_range']['max'], $quoteItem->unit_price);
                }
            }
        }

        // Calculate averages
        foreach ($itemComparisons as &$comparison) {
            if (!empty($comparison['quotes'])) {
                $sum = array_sum(array_column($comparison['quotes'], 'unit_price'));
                $comparison['price_range']['avg'] = $sum / count($comparison['quotes']);
            }

            // If min is still PHP_FLOAT_MAX, no quotes were found
            if ($comparison['price_range']['min'] === PHP_FLOAT_MAX) {
                $comparison['price_range']['min'] = 0;
            }
        }

        // Calculate overall price range
        $priceRange = [
            'min' => $quotes->min('total_price'),
            'max' => $quotes->max('total_price'),
            'avg' => $quotes->avg('total_price'),
        ];

        // Get the best quote
        $bestQuote = $this->getBestQuote($rfq);

        return [
            'quotes' => $quotes,
            'items' => $itemComparisons,
            'suppliers' => $suppliers,
            'best_quote' => $bestQuote,
            'price_range' => $priceRange,
        ];
    }

    /**
     * Calculate savings compared to the highest quote
     *
     * @param Request $rfq The RFQ
     * @param Quote|null $selectedQuote The selected quote (defaults to the best quote)
     * @return array The savings data
     */
    public function calculateSavings(Request $rfq, ?Quote $selectedQuote = null): array
    {
        $quotes = $this->getQuotesForRfq($rfq);

        if ($quotes->isEmpty()) {
            return [
                'amount' => 0,
                'percentage' => 0,
                'highest_quote' => null,
                'selected_quote' => null,
            ];
        }

        // Get the highest quote
        $highestQuote = $quotes->sortByDesc('total_price')->first();

        // Use the provided quote or get the best quote
        $selectedQuote = $selectedQuote ?? $this->getBestQuote($rfq);

        if (!$selectedQuote || !$highestQuote) {
            return [
                'amount' => 0,
                'percentage' => 0,
                'highest_quote' => $highestQuote,
                'selected_quote' => $selectedQuote,
            ];
        }

        // Calculate savings
        $savingsAmount = $highestQuote->total_price - $selectedQuote->total_price;
        $savingsPercentage = ($savingsAmount / $highestQuote->total_price) * 100;

        return [
            'amount' => $savingsAmount,
            'percentage' => $savingsPercentage,
            'highest_quote' => $highestQuote,
            'selected_quote' => $selectedQuote,
        ];
    }
}
