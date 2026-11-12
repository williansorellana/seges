<?php

namespace App\Http\Middleware;

use App\Models\Rendition;
use App\Models\RenditionExpense;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenditionMutationTransaction
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->routeIs('renditions.new-attempt')) {
            return $next($request);
        }
        $rendition = $request->route('rendition');
        if (is_numeric($rendition)) {
            $rendition = Rendition::findOrFail($rendition);
        }
        if (! $rendition && ($expense = $request->route('expense'))) {
            $expense = $expense instanceof RenditionExpense ? $expense : RenditionExpense::findOrFail($expense);
            $rendition = $expense->rendition;
        }
        if (! $rendition && $request->routeIs('renditions.reject-transfer')) {
            $rendition = Rendition::findOrFail($request->route('id'));
        }
        if (! $rendition instanceof Rendition) {
            return $next($request);
        }

        return DB::transaction(function () use ($rendition, $request, $next) {
            // Shared with creation/replacement: uploads and reviews cannot race
            // a rejection and change a document after its snapshot is frozen.
            User::whereKey($rendition->user_id)->lockForUpdate()->firstOrFail();
            $rendition->refresh();
            abort_if($rendition->status === 'rejected', 403, 'El intento rechazado está cerrado y es inalterable.');

            return $next($request);
        });
    }
}
