<?php

use App\Models\PersonalFile;
use Carbon\Carbon;
use App\Repositories\Files\FileUtils;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('update.{modelName}', function ($user, $modelName) {
    switch ($modelName) {
        case 'user': {
                if ($user->tokenCan('uv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'team': {
                if ($user->tokenCan('tv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'role': {
                if ($user->tokenCan('rv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'department': {
                if ($user->tokenCan('dpv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'company': {
                if ($user->tokenCan('cmv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'design-request': {
                if ($user->tokenCan('drv')) {
                    return true;
                }
                return false;
            }
            break;

        case 'country': {
                if ($user->tokenCan('cnv')) {
                    return true;
                }
                return false;
            }
            break;
        case 'request-storage': {
                return true;
            }
            break;
            // case 'file_management': {
            //         if ($user->tokenCan('cnv')) {
            //             return true;
            //         }
            //         return false;
            //     }
            //     break;

    }
});

Broadcast::channel('notifications.{user_id}', function ($user, $user_id) {
    if ($user->id == $user_id) {
        return true;
    }
    return false;
});

Broadcast::channel(
    'personal_file.{user_id}',
    function ($user, $user_id) {
        if ($user->tokenCan("mdv") && $user->tokenCan("sfv") && $user->tokenCan("mshv") && $user->id == $user_id) {
            return true;
        }
        return false;
    }
);
Broadcast::channel(
    'personal_file_comment.{file_id}',
    function ($user, $file_id) {
        $file = PersonalFile::where('id', $file_id)->select('id', 'name', 'user_id', 'sharedBy_id')->first();
        if ($file) {
            $fileShares = FileUtils::checkIfParentisShared($file->id, $user->id, PersonalFile::class);
            if (
                $file->sharedBy_id == $user->id ||
                $file->user_id == $user->id ||
                ($fileShares['result'] && $fileShares['fileShare']->shared_to == $user->id)
            ) {
                return true;
            }
        }
        return false;
    }
);

Broadcast::channel('online', function ($user) {
    return [
        'id' => $user->id,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
        'image' => $user->image,
        'time' => Carbon::now(),
        'location' => getAddress(
            $user->coords ? (array_key_exists('latitude', $user->coords) ? $user->coords['latitude'] : '') : '',
            $user->coords ? (array_key_exists('longtitude', $user->coords) ? $user->coords['longtitude'] : '') : ''
        ),
        'roles' => $user->load('roles:id,name,code')->roles,
        "coords" => $user->coords
    ];
});


Broadcast::channel('refresh-ads.{user_id}', function ($_, $user_id) {
    $users = UserRepository::getUsersByPermissions("adv", "c");
    if ($users->contains("id", $user_id)) {
        return true;
    }
    return false;
});

Broadcast::channel('refresh-crm.{user_id}', function ($user) {
    // $users = UserRepository::getUsersByPermissions("adv", "c");
    // if ($users->contains("id", $user_id)) {
    //     return true;
    // }
    return true;
});
