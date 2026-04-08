# Real-Time Notifications (Unread Count + List Without Page Refresh)

You can use **Pusher** (or Laravel broadcasting in general) so the notification icon updates in real time: **unread count** and **list** change without refreshing the page.

## How It Works

1. **Backend**: When a notification is created (e.g. new contact), `SystemNotification` is sent on both `database` and `broadcast` channels. The broadcast is pushed to a **private channel** per user (`App.Models.User.{id}`).
2. **Frontend**: Laravel **Echo** subscribes to that private channel and listens for `.notification()` events. When one is received, the script updates the bell badge count and prepends the new item to the dropdown list—**no page reload**.

## What’s Already in Place

- `App\Notifications\SystemNotification` implements `ShouldBroadcast` and uses a private channel.
- `routes/channels.php` authorizes `App.Models.User.{id}` for the logged-in user.
- `BroadcastServiceProvider` registers `/broadcasting/auth` (with `auth` middleware).
- `resources/js/bootstrap.js` configures Echo with Pusher and `/broadcasting/auth`.
- `resources/js/crm/notification/global-notification.js` listens for notifications and updates the badge + dropdown.
- CRM layout includes `bootstrap.js` so `window.Echo` is available.

## Setup (Pusher)

### 1. Install dependencies

```bash
# Backend (if not already installed)
composer require pusher/pusher-php-server

# Frontend
npm install pusher-js laravel-echo
```

### 2. Create a Pusher app

- Go to [pusher.com](https://pusher.com), create an app, and choose a cluster (e.g. `mt1`, `ap1`).
- Copy **App ID**, **Key**, **Secret**, and **Cluster**.

### 3. Configure environment

In `.env`:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=mt1

# Required for Vite/frontend (Echo uses these)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 4. Run the queue worker

Broadcast notifications are queued. Run a worker so they are sent:

```bash
php artisan queue:work
```

Keep this running (or use Supervisor in production).

### 5. Rebuild frontend

```bash
npm run dev
# or
npm run build
```

## Result

- **New notification** (e.g. new contact): bell badge count increases and the new item appears in the dropdown **without refresh**.
- **Click a notification**: it is marked as read and the count decreases (already implemented via AJAX).
- **Mark all as read**: count goes to zero and list updates (already implemented via AJAX).

## Alternative: Laravel Reverb

If you prefer a self-hosted WebSocket server instead of Pusher:

1. Install and configure [Laravel Reverb](https://laravel.com/docs/reverb).
2. In `.env` set `BROADCAST_DRIVER=reverb` and configure Reverb.
3. In `resources/js/bootstrap.js` switch the Echo `broadcaster` to `reverb` and use the Reverb client config from the Laravel docs.

The same backend notification and `global-notification.js` logic will work; only the broadcaster and front-end Echo config change.
