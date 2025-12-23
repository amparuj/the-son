<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OrderSubmission;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
  public function submissions(Request $request)
  {
    // OPEN | DONE | CLOSED
    $statusGroup = $request->query('status', 'OPEN');

    $query = OrderSubmission::query()
      ->with([
        'order:id,order_no,channel,status,table_id,total',
        'order.table:id,number',
        'items:id,order_submission_id,product_name,qty,line_total,note',
      ]);

    if ($statusGroup === 'CLOSED') {
      // CLOSE = PAID/CANCELLED only (order-level close)
      $query->whereHas('order', fn($q) => $q->whereIn('status', ['PAID', 'CANCELLED']));
      $query->orderByDesc('submitted_at')->orderByDesc('id');
    } elseif ($statusGroup === 'DONE') {
      // DONE = submission done, order still not closed (OPEN or DONE at order-level if you use it)
      $query->where('status', 'DONE')
            ->whereHas('order', fn($q) => $q->whereNotIn('status', ['PAID', 'CANCELLED']));
      $query->orderByDesc('submitted_at')->orderByDesc('id');
    } else {
      // OPEN = submission open, order not closed
      $query->where('status', 'OPEN')
            ->whereHas('order', fn($q) => $q->whereNotIn('status', ['PAID', 'CANCELLED']));
      // OPEN: oldest -> newest (queue)
      $query->orderBy('submitted_at')->orderBy('id');
    }

    $submissions = $query
      ->paginate(60)
      ->withQueryString();

    return view('staff.monitor.submissions', [
      'statusGroup' => $statusGroup,
      'submissions' => $submissions,
    ]);
  }

  // Mark DONE per submission (NOT per order)
  public function markSubmissionDone(Request $request, OrderSubmission $submission)
  {
    if ($submission->status !== 'OPEN') {
      return back()->withErrors(['status' => 'Submission is not OPEN.']);
    }

    $submission->status = 'DONE';
    $submission->done_at = now();
    $submission->save();

    // Lock related order items (cannot delete after DONE)
    OrderItem::where('order_submission_id', $submission->id)
      ->update(['status' => 'DONE', 'done_at' => now()]);

    return back()->with('success', 'Marked submission as DONE.');
  }
}
