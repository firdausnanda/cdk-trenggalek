<?php

namespace Modules\UserAvatar\Services;

class UserAvatarHooks
{
    public function register(): void
    {
        ld_add_filter('after_username_field', [$this, 'addAvatarField'], 10, 2);
        ld_add_filter('user_store_before_save', [$this, 'appendAvatarInUser'], 10, 2);
        ld_add_filter('user_update_before_save', [$this, 'appendAvatarInUser'], 10, 2);
        ld_add_filter('user_list_page_avatar_item', [$this, 'appendAvatarInUserList'], 10, 2);
    }

    public function addAvatarField($html = '', $user = null): string
    {
        ob_start();
        ?>
        <div>
            <label for="avatar" class="mb-2 block">Avatar</label>

            <?php if ($user && $user->avatar) { ?>
                <img src="<?php echo asset('storage/'.$user->avatar); ?>" width="50" alt="User Avatar" class="border rounded">
            <?php } ?>
            <input type="file" class="" id="avatar" name="avatar" accept="image/*" />
        </div>

        <?php
        $html .= ob_get_clean();

        return $html;
    }

    public function appendAvatarInUser($user, $request): \App\Models\User
    {
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
        }

        return $user;
    }

    public function appendAvatarInUserList(string $avatar, \App\Models\User $user): string
    {
        // Check if user avatar exist or not.
        if ($user->avatar) {
            $avatar = asset('storage/'.$user->avatar);
        }

        return $avatar;
    }
}
