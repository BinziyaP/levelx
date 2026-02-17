<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MergeCartOnLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        $sessionCart = session()->get('cart', []);
        
        $dbCartRecord = \App\Models\Cart::firstOrNew(['user_id' => $user->id]);
        $dbCart = $dbCartRecord->items ?? [];

        // Merge Strategy:
        // 1. If session has items, merge them into DB cart (sum quantities)
        // 2. Save back to DB
        // 3. Update session with the FULL merged cart
        
        foreach ($sessionCart as $id => $item) {
            if (isset($dbCart[$id])) {
                $dbCart[$id]['quantity'] += $item['quantity'];
                 // Update price/name/image in case it changed
                 $dbCart[$id]['price'] = $item['price'];
                 $dbCart[$id]['name'] = $item['name'];
                 $dbCart[$id]['image'] = $item['image'] ?? null;
            } else {
                $dbCart[$id] = $item;
            }
        }

        // Save merged cart to DB (only if session had items to update, OR just always sync)
        if (!empty($sessionCart)) {
             $dbCartRecord->items = $dbCart;
             $dbCartRecord->save();
        }

        // IMPORTANT: Restore DB cart to Session
        // This handles the case where Session is empty but DB has items (e.g. fresh login)
        session()->put('cart', $dbCart);
    }
}
