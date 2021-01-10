<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PostMedia;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{

    public function getNotifications()
    {
        return [
            'read'      => auth()->check() ? auth()->user()->readNotifications : collect([]),
            'unread'    => auth()->check() ? auth()->user()->unreadNotifications : collect([]),
            'usertype'  => auth()->check() ? auth()->user()->roles->first()->name : collect([]),
        ];
    }

    public function markAsRead(Request $request)
    {
        return auth()->user()->notifications->where('id', $request->id)->markAsRead();
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = auth()->user()->notifications->where('id', $id)->first();
        $notification->markAsRead();

        if (auth()->user()->roles->first()->name == 'user') {

            if ($notification->type == 'App\Notifications\NewCommentForPostOwnerNotify') {
                return redirect()->route('users.comment.edit', $notification->data['id']);
            } else {
                return redirect()->back();
            }
        }

    }



}
