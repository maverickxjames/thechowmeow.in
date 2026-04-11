<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->search, fn($q, $v) =>
                $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$v}%"))
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$v}%")))
            ->when($request->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($request->status === 'pending',  fn($q) => $q->where('is_approved', false))
            ->when($request->rating, fn($q, $v) => $q->where('rating', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function export(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->search, fn($q, $v) =>
                $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$v}%"))
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$v}%")))
            ->when($request->status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($request->status === 'pending',  fn($q) => $q->where('is_approved', false))
            ->when($request->rating, fn($q, $v) => $q->where('rating', $v))
            ->latest()
            ->get();

        $filename = 'reviews_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($reviews) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Customer', 'Product', 'Rating', 'Comment', 'Status', 'Date']);
            foreach ($reviews as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->user->name ?? 'N/A',
                    $r->product->name ?? 'N/A',
                    $r->rating,
                    $r->comment,
                    $r->is_approved ? 'Approved' : 'Pending',
                    $r->created_at->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
