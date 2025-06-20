<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Models\Tenant::class, // vertelt waar de tenant model staat
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class, // elk tenant krijgt een unieke id
    'domain_model' => Domain::class, // vertelt waar de domain model staat

    /**
     * The list of domains hosting your central app.
     *
     * Only relevant if you're using the domain or subdomain identification middleware.
     */
    'central_domains' => [
        '127.0.0.1',
        'digimarket.be',
    ],

    /**
     * Tenancy bootstrappers are executed when tenancy is initialized.
     * Their responsibility is making Laravel features tenant-aware.
     *
     * To configure their behavior, see the config keys below.
     */
    'bootstrappers' => [
        // Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class, // uitgeschakeld = geen aparte database per tenant
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
        // Stancl\Tenancy\Bootstrappers\RedisTenancyBootstrapper::class, // alleen als je Redis gebruikt
    ],

    /**
     * Database tenancy config. Used by DatabaseTenancyBootstrapper.
     */
    // is de hoofd-database voor algemene data
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'central'),

        /**
         * Connection used as a "template" for the dynamically created tenant database connection.
         * Note: don't name your template connection tenant. That name is reserved by package.
         */
        //Als je tenants automatisch een kopie wilt geven van een bestaande databaseconfiguratie, kun je hier een template-verbinding invullen. Nu is het uitgeschakeld (null).
        'template_tenant_connection' => null,

        /**
         * Tenant database names are created like this:
         * prefix + tenant_id + suffix.
         */
        // bepaalt hoe tenant-databases heten
        'prefix' => 'tenant',
        'suffix' => '',

        /**
         * TenantDatabaseManagers are classes that handle the creation & deletion of tenant databases.
         */
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,

            /**
             * Use this database manager for MySQL to have a DB user created for each tenant database.
             * You can customize the grants given to these users by changing the $grants property.
             */
            // 'mysql' => Stancl\Tenancy\TenantDatabaseManagers\PermissionControlledMySQLDatabaseManager::class,

            /**
             * Disable the pgsql manager above, and enable the one below if you
             * want to separate tenant DBs by schemas rather than databases.
             */
            // 'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLSchemaManager::class, // Separate by schema instead of database
        ],
    ],

    /**
     * Cache tenancy config. Used by CacheTenancyBootstrapper.
     *
     * This works for all Cache facade calls, cache() helper
     * calls and direct calls to injected cache stores.
     *
     * Each key in cache will have a tag applied on it. This tag is used to
     * scope the cache both when writing to it and when reading from it.
     *
     * You can clear cache selectively by specifying the tag.
     */
    'cache' => [
        // Zorgt dat de cache apart wordt gehouden voor elke tenant, dus geen verwarring tussen gebruikers.
        'tag_base' => 'tenant',
    ],

    /**
     * Filesystem tenancy config. Used by FilesystemTenancyBootstrapper.
     * https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper.
     */
    'filesystem' => [
        /**
         * Each disk listed in the 'disks' array will be suffixed by the suffix_base, followed by the tenant_id.
         */
        // Bestanden (zoals afbeeldingen of PDF’s) worden opgeslagen in mappen met de tenant-ID, zodat ze gescheiden blijven.
        'suffix_base' => 'tenant',
        // Dit zijn de opslaglocaties die tenant-afhankelijk worden gemaakt. Elk bestand dat je hier opslaat wordt per tenant gescheiden.
        'disks' => [
            'local',
            'public',
            // 's3',
        ],

        /**
         * Use this for local disks.
         *
         * See https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/#filesystem-tenancy-boostrapper
         */
        'root_override' => [
            // Bestanden van tenant met ID abc123 worden opgeslagen in bijv. /storage/app/public/...
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],

        /**
         * Should storage_path() be suffixed.
         *
         * Note: Disabling this will likely break local disk tenancy. Only disable this if you're using an external file storage service like S3.
         *
         * For the vast majority of applications, this feature should be enabled. But in some
         * edge cases, it can cause issues (like using Passport with Vapor - see #196), so
         * you may want to disable this if you are experiencing these edge case issues.
         */
        // Laravel verandert de opslagmap niet. Als je dit op true zet, krijgt elke tenant echt zijn eigen map.
        'suffix_storage_path' => false,

        /**
         * By default, asset() calls are made multi-tenant too. You can use global_asset() and mix()
         * for global, non-tenant-specific assets. However, you might have some issues when using
         * packages that use asset() calls inside the tenant app. To avoid such issues, you can
         * disable asset() helper tenancy and explicitly use tenant_asset() calls in places
         * where you want to use tenant-specific assets (product images, avatars, etc).
         */
        // Je gebruikt gewone bestanden zoals CSS of JS voor alle tenants samen. Zet dit op true als je per tenant unieke bestanden wilt.
        'asset_helper_tenancy' => false,
    ],

    /**
     * Redis tenancy config. Used by RedisTenancyBootstrapper.
     *
     * Note: You need phpredis to use Redis tenancy.
     *
     * Note: You don't need to use this if you're using Redis only for cache.
     * Redis tenancy is only relevant if you're making direct Redis calls,
     * either using the Redis facade or by injecting it as a dependency.
     */
    'redis' => [
        // Redis is een supersnelle opslag. Deze prefix zorgt dat elke tenant zijn eigen Redis-sleutels heeft.
        'prefix_base' => 'tenant', // Each key in Redis will be prepended by this prefix_base, followed by the tenant id.
        'prefixed_connections' => [ // Redis connections whose keys are prefixed, to separate one tenant's keys from another.
            // 'default',
        ],
    ],

    /**
     * Features are classes that provide additional functionality
     * not needed for tenancy to be bootstrapped. They are run
     * regardless of whether tenancy has been initialized.
     *
     * See the documentation page for each class to
     * understand which ones you want to enable.
     */
    'features' => [
        // Stancl\Tenancy\Features\TelescopeTags::class,  // je gebruikt dit alleen als je Laravel Telescope gebruikt.
        // Stancl\Tenancy\Features\UniversalRoutes::class, // activeer dit alleen als je je routes één keer wilt schrijven voor alle tenants tegelijk.

        Stancl\Tenancy\Features\UserImpersonation::class, // word gebruikt voor tenantswitcher. https://tenancyforlaravel.com/docs/v3/features/user-impersonation
        Stancl\Tenancy\Features\TenantConfig::class, // TenantConfig: zet tenant-info automatisch in Laravel’s config. https://tenancyforlaravel.com/docs/v3/features/tenant-config
        Stancl\Tenancy\Features\CrossDomainRedirect::class, // CrossDomainRedirect: helpt met doorverwijzen tussen domeinen. https://tenancyforlaravel.com/docs/v3/features/cross-domain-redirect
        Stancl\Tenancy\Features\ViteBundler::class, // ViteBundler: ondersteunt Vite als je dat gebruikt voor assets.
    ],

    /**
     * Should tenancy routes be registered.
     *
     * Tenancy routes include tenant asset routes. By default, this route is
     * enabled. But it may be useful to disable them if you use external
     * storage (e.g. S3 / Dropbox) or have a custom asset controller.
     */
    'routes' => true, // Zorgt dat de speciale routes voor tenants automatisch worden aangemaakt.


    /**
     * Parameters used by the tenants:migrate command.
     */
    // Als je php artisan tenants:migrate uitvoert, weet Laravel waar de migratiebestanden van tenants staan.
    'migration_parameters' => [
        '--force' => true, // dit moet true zijn om te kunnen migreren in productie
        '--path' => [database_path('migrations/tenant')], // hierdoor kan je gescheiden migraties uitvoeren
        '--realpath' => true,
    ],

    /**
     * Parameters used by the tenants:seed command.
     */
    // Dit zegt welke seeder gebruikt moet worden bij php artisan tenants:seed
    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder', // root seeder class
        // '--force' => true, // dit moet true zijn om te kunnen seeden in productie
    ],
];
