# OrderItemOption fix (Laravel)

This package adds a missing Eloquent model so calls like:

    OrderItemOption::create([...])

work correctly and no longer throw:

    Class "App\Services\OrderItemOption" not found

## What’s included
- `app/Models/OrderItemOption.php` — Eloquent model with a default `$fillable` set.

## How to apply
1) Copy `app/Models/OrderItemOption.php` into your Laravel project at:
   `app/Models/OrderItemOption.php`

2) In the file where you call `OrderItemOption::create([...])`, add (or fix) the import:

   ```php
   use App\Models\OrderItemOption;
   ```

   Alternatively, you can reference the class fully-qualified:
   `\App\Models\OrderItemOption::create([...]);`

3) Run:

   ```bash
   composer dump-autoload
   ```

## Notes
- If your database table is not named `order_item_options`, set `protected $table`.
- Adjust `$fillable` to match your actual columns.
