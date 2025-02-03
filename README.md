
# WordPress Bootstrap

This repository provides a basic bootstrap setup designed to be placed at the very top of `wp-config.php`. It allows you to add custom code that needs to run at the earliest stage of WordPress initialization. This approach ensures that your code executes before any plugins, mu-plugins, or themes, making it ideal for functionalities that require priority execution.

## How to Use

### Step 1: Add the following code to the **top** of your `wp-config.php` file

Insert this snippet at the very top of your `wp-config.php` file:

```php
define('WPB_DIR', __DIR__ . '/wp-bootstrap'); // Path to the bootstrap directory
require_once WPB_DIR . '/main.php';           // Load the bootstrap main file
wpb_inc('top.php');                           // Include the 'top.php' script
```

### Step 2: Add the following code to the very end of wp-config.php, just before the line that reads, “That’s all, stop editing! Happy publishing.”

```php
wpb_inc('end.php'); // Include 'end.php'
```

## File and folder structure

### `main.php`

This file serves as the core of the WordPress bootstrap application. It initializes key constants and functions required for custom configurations and dynamic environment handling.

#### Key Functionalities
1. **Bootstrap Status**:
   - `WPB_ACTIVE` determines whether the bootstrap functionality is enabled. If set to `false`, none of the custom include (`wpb_inc`) functions will execute.

2. **Dynamic Directory Paths**:
   - The functions `wpb_wp_dir` and `wpb_root_dir` dynamically calculate directory paths relative to the bootstrap directory (`WPB_DIR`), allowing modular and scalable file includes.

3. **File Includes with Environment Handling**:
   - The `wpb_inc` function allows conditional file inclusion based on the current environment (e.g., `local`, `staging`, `production`). It dynamically resolves environment-specific file paths when applicable.

4. **Path Normalization**:
   - The `wpb_trailingslashit` function ensures all directory paths are properly formatted with a trailing slash.

5. **Error Handling**:
   - Provides utility functions (`wpb_enable_errors` and `wpb_disable_errors`) to toggle PHP error reporting dynamically.

6. **Debugging Utilities**:
   - Includes a helper function (`wpb_printr`) to pretty-print arrays and variables for debugging.

#### Use Cases
- Include `main.php` at the start of `wp-config.php` to initialize environment-specific configurations and custom logic.
- Use `wpb_inc` to load additional files dynamically, tailored to your environment (e.g., staging or production constants).
- Toggle PHP error reporting during development with `wpb_enable_errors` and `wpb_disable_errors`.

The constant WPB_ACTIVE controls the bootstrap’s functionality. When true, it enables all features, and when false, it disables them, stopping file inclusion and custom logic execution.



### top.php

Add files to the $top_includes array that should be included at the top of wp-config.php. You can also add custom code to execute at the top of the file.

### Default files in top.php

```php
$top_includes = array(
  'vendor/autoload.php',
  'inc/top-constants.php',
  'inc/functions.php',
);
```

### `end.php`

Add files to the `$end_includes` array that should be included at the end of `wp-config.php`. You can also add custom code to execute at the end of the file.

### Default files in `end.php`

```php
$end_includes = array(
  'inc/end-constants.php',
  'inc/class-inits.php',
);
```


### `envs.php`

### `envs.php`

This file contains a map of different environments, such as production, staging, and local. You can extend it to include additional environments as needed.

#### Additional Details

The function `wpb_envs` in `inc/functions.php` retrieves environment details from either `envs.php` or `envs.local.php`. If `envs.local.php` exists, it will take precedence over `envs.php`. 

- **Parameters**:
  - `$name` *(optional)*: The name of the environment (e.g., production, staging, local). If not provided, the function returns the entire environment map.

- **Return Value**:
  - Returns an array of environment details if `$name` is `null`.
  - Returns details for the specified environment if `$name` matches a key in the map.
  - Returns `null` if the specified environment does not exist.

This allows for flexible handling of environment-specific configurations.

### `./src` folder

This folder serves as the PSR-4 autoload directory, used for organizing and loading classes automatically.

### `./src/WpDebug.php`

This file is an optional wrapper class for handling fatal errors in WordPress using the [Whoops library](https://filp.github.io/whoops/).

- To enable it, comment out the constants `WP_DEBUG`, `SCRIPT_DEBUG`, `WP_DEBUG_LOG`, and `WP_DEBUG_DISPLAY` in your `wp-config.php`.
- In the `inc/class-inits.php` file, uncomment the line `WpBootstrap\WpDebug::get_instance();`.

Make sure to run `composer install` before enabling this feature.

### `../app/src` folder

In addition to the WpBootstrap namespace, another PSR-4 autoload directory is available in the app folder located in the document root. This folder can exist inside the `wp-content` directory or even one level above the web root.

Currently, the `app` folder is replacing the default `wp-content` directory, and this is handled dynamically using the ``./src/MoveFolders.php` class.

### `./src/MoveFolders.php`

This class is responsible for adding custom directories for uploads, plugins, and themes. You can rename and relocate the uploads and plugins directories, but the themes folder can only be moved to a different location, it cannot be renamed. 

To enable the custom uploads, plugins & themes folder you can uncomment the line `new WpBootstrap\MoveFolders(false);` in `./inc/class-inits.php` file, and then go to the `mu-plugins` folder and create a new file, such as MoveThemesFolder.php. Inside this file, add this line `new WpBootstrap\MoveFolders(true);` to apply the changes and set up the custom themes directory.


This class is responsible for adding custom directories for uploads, plugins, and themes. You can rename and relocate the uploads and plugins directories, but the themes folder can only be moved to a different location; it cannot be renamed. 

To enable the custom uploads, plugins & themes folder you can, uncomment the line `new WpBootstrap\MoveFolders(false);` in the `./inc/class-inits.php` file. Then, go to the `mu-plugins` folder and create a new file, such as `MoveThemesFolder.php`. Inside this file, add the line `new WpBootstrap\MoveFolders(true);` to apply the changes and set up the custom themes directory.

You can modify the `./src/MoveFolders.php` file according to your needs and update the `$folder_names` array variable to match your desired custom folder names. This allows you to customize the locations of the uploads, plugins, and themes directories based on your preferred structure. You can also comment out specific parts of the class. i.e, if you only want to rename the wp-content folder while keeping the plugins and themes folders unchanged.

### `inc/class-inits.php`

This file contains all class initializations, such as WpBootstrap\WpDebug::get_instance() or any other new classes added to the PSR-4 directories defined in composer.json. It serves as a central place for managing and instantiating classes, ensuring they are properly loaded and available throughout the application.

### `inc/end-constants.php`

This file contains all the constant definitions that should be added at the end of `wp-config.php`.

### `inc/functions.php`

This file contains custom functions as needed. Make sure to prefix function names with `wpb_` to avoid any naming conflicts.

### `inc/top-constants.php`

This file contains constant definitions that should be added at the very top of the `wp-config.php` file.


